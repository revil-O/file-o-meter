<?php
	//Klasse fuer Webservice Login
	require_once('access_client.php');

	class WebServiceOther
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

		public function get_projects($project_id = 0, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_project = $client->get_projects($this->setup_array['ws_key'], $project_id, $return_type);

					//keine Daten oder Fehler
					if ($result_project != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result_project);
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
							return $result_project;
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

		public function get_files_links($project_id = 0, $folder_id = 0, $doctype_id = 0, $comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_files_and_links($this->setup_array['ws_key'], $project_id, $folder_id, $doctype_id, $comment, $order_by, $return_type, $recursive);

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

		public function get_file_link_exists($project_id = 0, $folder_id = 0)
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_file_link_exists($this->setup_array['ws_key'], $project_id, $folder_id);

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

		public function get_doctypes($doctype_id = 0, $return_type = 'array')
		{
			if ($this->setup_array['ws_key'] !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_doctype = $client->get_doctypes($this->setup_array['ws_key'], $doctype_id, $return_type);


					//keine Daten oder Fehler
					if ($result_doctype != 'false')
					{
						if ($return_type == 'array')
						{
							$result_array = unserialize($result_doctype);
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
							return $result_doctype;
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
/*
	$ws = new WebServiceOther();
	echo '<pre>';
	print_r($ws->get_projects(10));
	echo '</pre>';
*/
?>