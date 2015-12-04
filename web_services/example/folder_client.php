<?php
	//Klasse fuer Webservice Login
	require_once('access_client.php');

	class WebServiceFolder
	{
		private $setup_array = array();

		public function __construct()
		{
			//FIXME: Der Pfad muss angepasst werden!
			$this->setup_array['wsdl_pfad'] = 'http://www.yourdomain.com/fom/web_services/fom.wsdl';

			//Webservice Login
			$wsa = new WebServiceLogin();
			$this->setup_array['ws_key'] = $wsa->get_ws_key();
		}

		public function edit_folder($folder_id = 0, $folder_name = '', $folder_desc = '')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_folder = $client->edit_folder($this->setup_array['ws_key'], $folder_id, $folder_name, $folder_desc);

					//keine Daten oder Fehler
					if ($result_folder == 'true')
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

		public function add_folder($project_id = 0, $folder_id = 0, $folder_name = '', $folder_desc = '', $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_folder = $client->add_folder($this->setup_array['ws_key'], $project_id, $folder_id, $folder_name, $folder_desc, $return_type);

					//keine Daten oder Fehler
					if ($result_folder != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result_folder);
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
							return $result_folder;
						}
						else
						{
							return false;
						}
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

		public function get_folder($project_id = 0, $folder_id = 0, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_folder = $client->get_folder($this->setup_array['ws_key'], $project_id, $folder_id, $return_type);

					//keine Daten oder Fehler
					if ($result_folder != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result_folder);
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
							return $result_folder;
						}
						else
						{
							return false;
						}
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