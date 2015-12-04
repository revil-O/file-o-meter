<?php
	/**
	 * salting of passwords
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * salting of passwords
	 * @package file-o-meter
	 * @subpackage class
	 */
	class CryptPw
	{
		/**
		 * Dateipfad zum Salt_array
		 *
		 * @var string
		 */
		public $salt_file = '';

		/**
		 * Gibt die Stringlaenge an mit der gemischt werden soll. Max 32
		 *
		 * @var int
		 */
		private $salt_block_len = 0;

		/**
		 * Laed diverse Grundeinstellungen
		 * @return void
		 */
		public function __construct()
		{
			$this->salt_file = FOM_ABS_PFAD.'config/cryptpw_salt.php';
			$this->salt_block_len = 32;
		}

		/**
		 * Erstellt aus einem Passwortstring einen gesalzenen md5 Hash
		 *
		 * @param string $pw
		 * @return string
		 */
		public function encode_pw($pw)
		{
			$salt_array = $this->get_salt_array();

			if(isset($salt_array[strtolower($pw{0})]))
			{
				$salt_array = $salt_array[strtolower($pw{0})];
			}
			else
			{
				$salt_array = $salt_array['sz'];
			}
			$salt_index_int = 0;
			$newpw_string = '';

			for($i = 0; $i < strlen($pw); $i++)
			{
				$newpw_string .= $pw{$i}.$salt_array[$salt_index_int];

				if($salt_index_int == 9)
				{
					$salt_index_int = 0;
				}
				else
				{
					$salt_index_int++;
				}
			}
			return md5($newpw_string);
		}

		/**
		 * Gibt ein Array mit stringbluecken zurueck mit denen das PW gemischt wird
		 * @return array
		 */
		public function get_salt_array()
		{
			$salt_array_exists = false;
			//Pruefen ob Datei bereits existiert
			if(file_exists($this->salt_file))
			{
				require($this->salt_file);
				if(isset($cryptpw_salt_array) and is_array($cryptpw_salt_array) and count($cryptpw_salt_array) > 0)
				{
					$salt_array_exists = true;
				}
			}
			//Datei existiert bereits
			if($salt_array_exists)
			{
				return $cryptpw_salt_array;
			}
			else
			{
				$cryptpw_salt_array_string = '<?php
				$cryptpw_salt_array[\'a\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'b\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'c\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'d\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'e\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'f\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'g\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'h\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'i\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'j\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'k\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'l\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'m\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'n\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'o\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'p\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'q\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'r\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'s\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'t\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'u\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'v\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'w\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'x\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'y\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'z\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'0\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'1\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'2\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'3\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'4\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'5\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'6\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'7\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'8\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'9\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\');'."\r\n".'
				$cryptpw_salt_array[\'sz\'] = array(\''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\', \''.$this->get_uniq_string($this->salt_block_len).'\'); ?>';

				if ($salt_file = fopen($this->salt_file, 'w'))
				{
					fwrite($salt_file, $cryptpw_salt_array_string);
					fclose($salt_file);
				}
				//Pruefen ob Datei jetzt existiert
				if(file_exists($this->salt_file))
				{
					require($this->salt_file);
					if(isset($cryptpw_salt_array) and is_array($cryptpw_salt_array) and count($cryptpw_salt_array) > 0)
					{
						$salt_array_exists = true;
					}
				}
				//Datei existiert bereits
				if($salt_array_exists)
				{
					return $cryptpw_salt_array;
				}
				else
				{
					return array();
				}
			}
		}

		/**
		 * Erstellt einen unique String max Laenge 32 Zeichen
		 * @param int $len
		 * @return string
		 */
		private function get_uniq_string($len = 32)
		{
			if($len > 32 or $len < 0)
			{
				$len = 32;
			}
			return substr(md5(uniqid(rand(), true)), 0, $len);
		}
	}
?>