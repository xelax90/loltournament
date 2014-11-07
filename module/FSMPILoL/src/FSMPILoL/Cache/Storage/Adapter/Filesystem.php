<?php
namespace FSMPILoL\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\Filesystem AS ZendFilesystem;
use Zend\Cache\Exception;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Stdlib\ErrorHandler;
use ArrayObject;

class Filesystem extends ZendFilesystem{
	
    /**
     * Internal method to test if an item exists.
     *
     * @param  string $normalizedKey
     * @return bool
     * @throws Exception\ExceptionInterface
     */
    protected function internalHasItem(& $normalizedKey)
    {
		return $this->internalHasItem_($normalizedKey);
    }


    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws Exception\ExceptionInterface
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
    public function itemHasExpired($key)
    {
		if(!$this->hasItem($key))
			return true;
		
        $options = $this->getOptions();
        if ($options->getReadable() && $options->getClearStatCache()) {
            clearstatcache();
        }

        if (!$this->getOptions()->getReadable()) {
            return false;
        }

        $this->normalizeKey($key);
        $args = new ArrayObject(array(
            'key' => & $key,
        ));

        try {
            $eventRs = $this->triggerPre(__FUNCTION__, $args);
            if ($eventRs->stopped()) {
                return $eventRs->last();
            }

            $result = !$this->internalHasItem_($args['key'], true);
            return $this->triggerPost(__FUNCTION__, $args, $result);
        } catch (\Exception $e) {
            $result = false;
            return $this->triggerException(__FUNCTION__, $args, $result, $e);
        }
    }	


    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws Exception\ExceptionInterface
     *
     * @triggers hasItem.pre(PreEvent)
     * @triggers hasItem.post(PostEvent)
     * @triggers hasItem.exception(ExceptionEvent)
     */
 	protected function internalHasItem_(& $normalizedKey, $checkTime = false)
    {
        $file = $this->getFileSpec($normalizedKey) . '.dat';
        if (!file_exists($file)) {
            return false;
        }

        $ttl = $this->getOptions()->getTtl();
        if ($ttl) {
            ErrorHandler::start();
            $mtime = filemtime($file);
            $error = ErrorHandler::stop();
            if (!$mtime) {
                throw new Exception\RuntimeException("Error getting mtime of file '{$file}'", 0, $error);
            }

            if ((!$this->getCapabilities()->getExpiredRead() || $checkTime) && time() >= ($mtime + $ttl)) {
                return false;
            }
        }

        return true;
    }	
}