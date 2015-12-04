<?php
	//FIXME: diese Klasse wird bisher nirgends genutzt. muss noch fertig gestellt und sinnvoll eingebunden werden!
	class StringFunctions
	{
		//currently used charset encoding
		private $encoding;
		
		public function __construct()
		{
			$this->encoding = '';
		}
		
		public function fom_strlen($str)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$strlen = mb_strlen($str, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$strlen = mb_strlen($str, 'UTF-8');
			}
			else
			{
				$strlen = strlen($str);
			}
			
			return $strlen;
		}
		
		public function fom_substr($str, $start, $length)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$substring = mb_substr($str, $start, $length, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$substring = mb_substr($str, $start, $length, 'UTF-8');
			}
			else
			{
				$substring = substr($str, $start, $length);
			}
			
			return $substring;
		}
		
		public function fom_substr_count($str, $needle)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$count = mb_substr_count($str, $needle, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$count = mb_substr_count($str, $needle, 'UTF-8');
			}
			else
			{
				$count = substr_count($str, $needle);
			}
			
			return $count;
		}
		
			
		public function fom_strtolower($str)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$lowercase = mb_strtolower($str, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$lowercase = mb_strtolower($str, 'UTF-8');
			}
			else
			{
				$lowercase = strtolower($str);
			}
			
			return $lowercase;
		}
		
		
		public function fom_strtoupper($str)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$uppercase = mb_strtoupper($str, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$uppercase = mb_strtoupper($str, 'UTF-8');
			}
			else
			{
				$uppercase = strtoupper($str);
			}
			
			return $uppercase;
		}
		
		
		public function fom_stripos($str, $needle, $start_position)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$position = mb_stripos($str, $needle, $start_position, 'UTF-16LE');
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$position = mb_stripos($str, $needle, $start_position, 'UTF-8');
			}
			else
			{
				$position = stripos($str, $needle, $start_position);
			}
			
			return $position;
		}
		
		
		
		public function fom_ereg_replace($str, $reg_exp, $replace)
		{
			if ($this->encoding == 'UTF-16LE')
			{
				$replaced_string = mb_ereg_replace($reg_exp, $replace, $str);
			}
			elseif ($this->encoding == 'UTF-8')
			{
				$replaced_string = mb_ereg_replace($reg_exp, $replace, $str);
			}
			else
			{
				$replaced_string = preg_replace($reg_exp, $replace, $str);
			}
			
			return $replaced_string;
		}
		
		
		
		
	}
?>