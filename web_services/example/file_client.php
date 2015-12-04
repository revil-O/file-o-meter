<?php
	//Klasse fuer Webservice Login
	require_once('access_client.php');

	class WebServiceFile
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

		public function get_files($folder_id = 0, $project_id = 0, $doctype_id = 0, $file_comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_files($this->setup_array['ws_key'], $project_id, $folder_id, $doctype_id, $file_comment, $order_by, $return_type, $recursive);

					//keine Daten oder Fehler
					if ($result_files != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result_files);
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
							return $result_files;
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

		public function get_file_exists($project_id = 0, $folder_id = 0)
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_file_exists($this->setup_array['ws_key'], $project_id, $folder_id);

					//keine Daten oder Fehler
					if ($result_files == false)
					{
						return false;
					}
					else
					{
						return true;
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

		public function get_file_data($file_id)
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_file_data = $client->get_file_data($this->setup_array['ws_key'], $file_id);

					//keine Daten oder Fehler
					if ($result_file_data != 'false' and !empty($result_file_data))
					{
						//return $result_file_data;

						//Image
						$im_data = base64_decode($result_file_data);
						//FIXME Daten muessen natuerlich noch geschrieben werden
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

		public function add_files($folder_id = 0, $project_id = 0, $file_name = '', $file_data = '', $file_id = 0, $file_type = 'PRIMARY', $comment = '', $search_string = '', $document_type = '', $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->add_files($this->setup_array['ws_key'], $folder_id, $project_id, $file_name, $file_data, $file_id, $file_type, $comment, $search_string, $document_type, $return_type);

					//keine Daten oder Fehler
					if ($result_files != 'false')
					{
						if ($return_type == 'array')
						{
							$return_array = @unserialize($result_files);
							if (is_array($return_array))
							{
								return $return_array;
							}
							else
							{
								return false;
							}
						}
						elseif ($return_type == 'json')
						{
							return $result_files;
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

		public function get_one_file($file_id, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_one_file($this->setup_array['ws_key'], $file_id, $return_type);

					//keine Daten oder Fehler
					if ($result_files != 'false')
					{
						if ($return_type == 'array')
						{
							$return_array = @unserialize($result_files);
							if (is_array($return_array))
							{
								return $return_array;
							}
							else
							{
								return false;
							}
						}
						elseif ($return_type == 'json')
						{
							return $result_files;
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