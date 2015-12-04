<?php
	//Klasse fuer Webservice Login
	require_once('access_client.php');

	class WebServiceGetAZRegister
	{
		private $setup_array = array();

		public function __construct()
		{
			$this->setup_array['wsdl_pfad'] = 'http://www.yourdomain.com/fom/web_services/fom.wsdl';

			//Webservice Login
			$wsa = new WebServiceLogin();
			$this->setup_array['ws_key'] = $wsa->get_ws_key();
		}

		public function get_az_register_file($file_id, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result = $client->get_az_register_file($this->setup_array['ws_key'], $file_id, $return_type);

					//keine Daten oder Fehler
					if ($result != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result);
							if (is_array($result_array))
							{
								return $result_array;
							}
							else
							{
								return false;
							}
						}
						elseif ($return_type == 'json')
						{
							return $result;
						}
						return false;
					}
					else
					{
						return false;
					}
				}
				catch(Exception $e)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		public function get_az_register_folder($folder_id, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result = $client->get_az_register_folder($this->setup_array['ws_key'], $folder_id, $return_type);

					//keine Daten oder Fehler
					if ($result != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result);
							if (is_array($result_array))
							{
								return $result_array;
							}
							else
							{
								return false;
							}
						}
						elseif ($return_type == 'json')
						{
							return $result;
						}
						return false;
					}
					else
					{
						return false;
					}
				}
				catch(Exception $e)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}

		public function insert_az_register($file_id, $sign_array, $word_array, $is_subfile = 'false')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result = $client->insert_az_register($this->setup_array['ws_key'], $file_id, serialize($sign_array), serialize($word_array), $is_subfile);

					//keine Daten oder Fehler
					if ($result == 'true')
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				catch(Exception $e)
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
?>