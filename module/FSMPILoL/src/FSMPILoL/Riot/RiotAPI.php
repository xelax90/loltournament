<?php
namespace FSMPILoL\Riot;

use FSMPILoL\Options\APIOptions;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class RiotAPi {
	private static $apiProtocol = 'https';
	private static $apiUrl = "api.pvp.net";
	private static $requestCount = 0;
	
	protected $serviceLocator;
	protected $config;
	protected $entityManager;
	protected $cache;
	
	protected function getConfig(){
		if (null === $this->config) {
			$this->config = $this->getServiceLocator()->get('FSMPILoL\Options\API');
		}
		return $this->config;
	}
	
	protected function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->entityManager;
	}
	
	protected function getCache(){
		if (null === $this->cache) {
			$this->cache = $this->getServiceLocator()->get('FSMPILoL\RiotCache');
		}
		return $this->cache;
	}
	
	protected function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function __construct(ServiceLocatorInterface $sl){
		$this->serviceLocator = $sl;
	}
	
	/**
	 * Returns array of summoners with standardized name as key.
	 * @param $anmeldungen Array of Anmeldung
	 */
	public function getSummoners($anmeldungen){
		// TODO Benchmark!!
		
		// Split names in 40-element chunks
		$nameChunks = array();
		$idChunks = array();
		$names = array();
		$ids = array();
		
		/** @var FSMPILoL\Entity\Anmeldung $anmeldung */
		foreach($anmeldungen as $anmeldung){
			if(count($names) == 40){
				$nameChunks[] = $names;
				$names = array();
			}
			if(count($ids) == 40){
				$idChunks[] = $ids;
				$ids = array();
			}
			
			if($anmeldung->getPlayer() && $anmeldung->getPlayer()->getSummonerId()){
				$ids[] = $anmeldung->getPlayer()->getSummonerId();
			} else {
				$names[] = self::getStandardName($anmeldung->getSummonerName());
			}
		}
		$nameChunks[] = $names;
		$idChunks[] = $ids;
		// request all chunks
		$results = array();
		foreach($nameChunks as $names){
			$results[] = $this->getSummoner(implode(',', $names));
		}
		$idResults = array();
		foreach($idChunks as $ids){
			$idResults[] = $this->getSummonerById(implode(',', $ids));
		}
		
		$summoners = array();
		$summonersID = array();
		
		foreach($idResults as $idResult){ // for each chunk
			if(!is_numeric($idResult)){ // if no error
				$res = get_object_vars($idResult); // api gives object
				foreach($res as $key => $val){
					$summoners[self::getStandardName($val->name)] = $val;
					$summonersID[$val->id] = $val;
				}
			}
		}
		
		// join chunks into one array
		foreach($results as $result){
			if(!is_numeric($result)){ // if no error
				$res = get_object_vars($result); // api gives object
				foreach($res as $key => $val){
					$summoners[$key] = $val;
					$summonersID[$val->id] = $val;
				}
			}
		}
		
		// update summoner ids and names
		foreach($anmeldungen as $anmeldung){
			if($anmeldung->getPlayer()){
				$name = self::getStandardName($anmeldung->getSummonerName());
				$id = $anmeldung->getPlayer()->getSummonerId();
				// if player not found, continue
				if(empty($summoners[$name]) && empty($summonersID[$id])) 
					continue;
				
				if(empty($id))
					$anmeldung->getPlayer()->setSummonerId($summoners[$name]->id);
				else
					$anmeldung->setSummonerName($summonersID[$id]->name);
			}
		}
		$this->getEntityManager()->flush();
		
		return $summoners;
	}
	
	public function getSummoner($summonername){
		$url = "/api/lol/euw/v1.4/summoner/by-name/".rawurlencode($summonername);
		$result = $this->request($url);
		return $result;
	}

	public function getSummonerById($summonername){
		$url = "/api/lol/euw/v1.4/summoner/".rawurlencode($summonername);
		$result = $this->request($url);
		return $result;
	}

	public function getStats($summonerID, $season = "SEASON2015"){
		$url = "/api/lol/euw/v1.3/stats/by-summoner/".$summonerID."/summary?season=".$season;
		$result = $this->request($url);
		return $result;
	}
	
	public static function getStandardName($summonername){
		$res = mb_strtolower($summonername, 'UTF-8');
		$res = str_replace(array(","," ","&"), "", $res);
		$res = str_replace(array("Ä","Ö","Ü", "Ø"), array("ä", "ö", "ü", "ø"), $res);
		return $res;
	}
	
	public function getLeagueEntry($summonerID){
		$url="/api/lol/euw/v2.5/league/by-summoner/".$summonerID."/entry";
		$result = $this->request($url);
		if(is_array($result))
			return $result;
		return $result;
	}
	
	private static function getCacheKey($url){
		return hash("sha256", $url);
	}
	
	private function request($url){
		$config = $this->getConfig();
		$glue = "?";
		if(strpos($url,'?') !== false)
			$glue = "&";
		$request = self::$apiProtocol . '://'.$config->getRegion().'.'.self::$apiUrl.$url.$glue."api_key=".$config->getKey();
		
		$cache = $this->getCache();
		
		$cacheKey = self::getCacheKey($request);
		$contents = null;
		if($cache->hasItem($cacheKey)){
			//echo 'cached';
			$contents = $cache->getItem($cacheKey);
		}
		
		// new request if no cache or if expired cache and stil requests left
		if(($contents != null && $cache->itemHasExpired($cacheKey) && self::$requestCount <= $config->getMaxRequests()) || $contents == null) {
			@$requestContent = file_get_contents($request);
		    // Retrieve HTTP status code
		    list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);

		    // Check the HTTP Status code
		    switch($status_code) {
		        case 200:
		                $error_status="200: Success";
		                break;
		        case 401:
		                $error_status="401: Login failure.  Try logging out and back in.  Password are ONLY used when posting.";
		                break;
		        case 400:
		                $error_status="400: Invalid request.  You may have exceeded your rate limit.";
		                break;
		        case 404:
		                $error_status="404: Not found.  This shouldn't happen.  Please let me know what happened using the feedback link above.";
		                break;
		        case 404:
		                $error_status="429: Rate Limit Exceeded.";
		                break;
		        case 500:
		                $error_status="500: Twitter servers replied with an error. Hopefully they'll be OK soon!";
		                break;
		        case 502:
		                $error_status="502: Twitter servers may be down or being upgraded. Hopefully they'll be OK soon!";
		                break;
		        case 503:
		                $error_status="503: Twitter service unavailable. Hopefully they'll be OK soon!";
		                break;
		        default:
		                $error_status="Undocumented error: " . $status_code;
		                break;
		    }
			
			if($status_code == 200){
				//echo 'not cached';
				$cache->addItem($cacheKey, $requestContent);
				$contents = $requestContent;
				self::$requestCount++;
			} elseif($status_code == 429 && !empty($cache)){ // rate limit exceeded
				// use cache
			} elseif($status_code == 404){ // 404 is sometimes a valid response
				$cache->addItem($cacheKey, '404');
				return 404;
			} else {
				if($contents != null)
					return json_decode($contents);
				return $status_code;
			}
		}
		
		$obj = json_decode($contents);
		return $obj;
	}
}
