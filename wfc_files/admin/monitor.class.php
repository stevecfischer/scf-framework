<?php

class Monitor
{
	protected $start=0,
				$end=0;

	function StartTimer() {
		$this->start=microtime(true);
		$this->end=0;
	}

	function StopTimer() {
		$this->end=microtime(true);
	}

	function GetElapsedTime() {
		return $this->end-$this->start;
	}

	public static function HumanTime($time) {
		$s=intval($time/1000);
		if($s>0) {
			$datetime1 = new DateTime('@'.time());
			$datetime2 = new DateTime('@'.(time()+$s));
			$interval = $datetime1->diff($datetime2);
			if(intval($interval->format('%h')>0))
				return $interval->format('%h hour'.($interval->format('%h')>1?'s':'s').', %I minute'.($interval->format('%i')>1?'s':'s').' and %S seconds'.($s>1?'s':''));
			else if(intval($interval->format('%I')>0))
				return $interval->format('%i minute'.($interval->format('%i')>1?'s':'').' and %S seconds'.($s>1?'s':''));
			else
				return $interval->format('%s second'.($s>1?'s':''));
		}
		else
			return round($time).' ms';
	}

	public static function HumanSize($size) {
		if(intval($size/1000)>0) //Kb
			if(intval($size/1000000)>0) //Mb
				if(intval($size/1000000000)>0) //Gb
					return intval($size/1000000000).' Gb';
				else
					return intval($size/1000000).' Mb';
			else
				return intval($size/1000).' Kb';
		else
			return $size.' b';
    }
	
	function GetMemoryUsage() {
		return memory_get_usage();
	}

	function GetMemoryPeak() {
		return memory_get_peak_usage();
	}

	function GetRessourceUsage() {
		return getrusage();
	}

}


?>