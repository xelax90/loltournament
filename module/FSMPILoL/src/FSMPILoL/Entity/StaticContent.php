<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;


/**
 * Static site content
 *
 * @ORM\Entity
 * @ORM\Table(name="static_content")
 * @property int $id
 * @property string $site
 * @property int $position
 * @property string $content
 */
class StaticContent implements JsonSerializable{
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
		
	/**
	 * @ORM\Column(type="string");
	 */
	protected $site;
		
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $position;
		
	/**
	 * @ORM\Column(type="text");
	 */
	protected $content;
	
	public function getId() {
		return $this->id;
	}

	public function getSite() {
		return $this->site;
	}

	public function getPosition() {
		return $this->position;
	}

	public function getContent() {
		return $this->content;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setSite($site) {
		$this->site = $site;
		return $this;
	}

	public function setPosition($position) {
		$this->position = $position;
		return $this;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize(){
		$data = array(
			"id" => $this->getId(),
			"site" => $this->getSite(),
			"position" => $this->getPosition(),
			"content" => $this->getContent()
		);
		return $data;
	}

}
