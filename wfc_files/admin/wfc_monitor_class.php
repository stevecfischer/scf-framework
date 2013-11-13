<?php
if(!class_exists('Monitor'))
{
	/**
	* Class Monitor 
	* Time to load page, memory used, etc..
	*
	* @author Thibault Miclo
	* @version 1.1
	* @package wfc-framework
	* @since 5.2
	*/
	class Monitor
	{
		protected $start=0,
					$end=0;

		/**
		* Launch the timer
		*
		* @since 1.0
		*/
		function StartTimer() {
			$this->start=microtime(true);
			$this->end=0;
		}
		/**
		* Stop the timer
		*
		* @since 1.0
		*/
		function StopTimer() {
			$this->end=microtime(true);
		}
		/**
		* Get elapsed time between start and stop
		*
		* @since 1.0
		* @return int time in microseconds
		*/
		function GetElapsedTime() {
			return $this->end-$this->start;
		}
		/**
		* Format a microsecond time into a human readable time
		*
		* @param int $time in microseconds
		* @since 1.1
		* @return string formated time
		*/
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
		/**
		* Format a memory size into a human readable size
		*
		* @param int $size in bytes
		* @since 1.1
		* @return string formated size
		*/
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
		/**
		* Return the memory used by the script at the time you call this method
		*
		* @since 1.0
		* @return int $memory in bytes
		*/
		function GetMemoryUsage() {
			return memory_get_usage();
		}
		/**
		* Return the max memory used by the script
		* Make sure to call this at the end of your scripts
		*
		* @since 1.0
		* @return int $memory in bytes
		*/
		function GetMemoryPeak() {
			return memory_get_peak_usage();
		}
		/**
		* UNIX ONLY - Equivalent of UNIX's getrusage
		*
		* @since 1.0
		* @return array $data various infos
		*/
		function GetRessourceUsage() {
			return getrusage();
		}

	}
}