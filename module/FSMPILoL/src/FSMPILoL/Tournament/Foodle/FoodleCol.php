<?php
namespace FSMPILoL\Tournament\Foodle;

class FoodleCol{
	public $date;
	public $times;
	
	public function __construct($date, $times){
		$this->date = $date;
		$this->times = $times;
	}
	
	public function __toString(){
		$ret = $this->date;
		if(!empty($this->times)){
			$ret .= '('.implode(',', $this->times).')';
		}
		return $ret;
	}
}