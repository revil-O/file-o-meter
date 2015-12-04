<?php
	//Klasse fuer Webservice Login
	require_once('access_client.php');

	class WebServiceLink
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

		public function add_link($folder_id = 0, $project_id = 0, $link_string = '', $protokoll_string = '', $link_name_string = '', $tagging_string = '', $linkcomment_string = '')
		{
			//Webservice Login
			$wsa = new WebServiceLogin();
			$ws_key = $wsa->get_ws_key();

			if ($ws_key !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->add_link($ws_key, $folder_id, $project_id, $link_string, $protokoll_string, $link_name_string, $tagging_string, $linkcomment_string);

					//keine Daten oder Fehler
					if ($result_files != 'false')
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

		public function get_links($project_id = 0, $folder_id = 0, $link_comment = '', $order_by = 'name_asc', $return_type = 'array', $recursive = false)
		{
			//Webservice Login
			$wsa = new WebServiceLogin();
			$ws_key = $wsa->get_ws_key();

			if ($ws_key !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_links($ws_key, $project_id, $folder_id, $link_comment, $order_by, $return_type, $recursive);

					//echo $result_files;
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

		public function get_link_exists($project_id = 0, $folder_id = 0)
		{
			//Webservice Login
			$wsa = new WebServiceLogin();
			$ws_key = $wsa->get_ws_key();

			if ($ws_key !== false)
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$result_files = $client->get_link_exists($ws_key, $project_id, $folder_id);

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
	}
?>