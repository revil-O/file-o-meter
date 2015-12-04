<?php
	/*
	 * Gibt den WsKex zurueck der allen anderen WebServices mit uebergeben werden muss
	 */
	class WebServiceLogin
	{
		private $setup_array = array();

		/*
		 * Setzt die Grundeinstellungen
		 */
		public function __construct()
		{
			//FIXME: Der Pfad Username und Passwort müssen angepasst werden!
			$this->setup_array['wsdl_pfad']		= 'http://www.yourdomain.com/fom/web_services/access.wsdl';
			$this->setup_array['ws_loginname']	= 'username';
			$this->setup_array['ws_loginpw']	= 'pw';
		}

		/*
		 * Gibt den WebService Key zurueck den Alle anderen WebService Abfragen benoetigen
		 * @return mixed
		 */
		public function get_ws_key()
		{
			$ws_key = $this->read_ws_key_cache();

			//WsKey aus lokalem Speicher vorhanden
			if (!empty($ws_key))
			{
				try
				{
					// Soap-Client initialisieren
					$client = @new SoapClient($this->setup_array['wsdl_pfad']);
					$ws_check = $client->chk_ws_key($ws_key);

					if ($ws_check == 'true')
					{
						return $ws_key;
					}
				}
				catch(Exception $e)
				{
					//nix machen
				}
			}

			//Kein WsKey vorhanden oder abgelaufen neuen Loginvorgang starten
			try
			{
				// Soap-Client initialisieren
				$client = @new SoapClient($this->setup_array['wsdl_pfad']);
				//Einloggen
				$ws_key = $client->ws_login($this->setup_array['ws_loginpw'], $this->setup_array['ws_loginname']);

				//keine Fehler
				if ($ws_key != 'false')
				{
					$this->insert_ws_key_cache($ws_key);
					return $ws_key;
				}
			}
			catch(Exception $e)
			{
				return false;
			}
			return false;
		}

		/*
		 * Liest den WsKey aus einem Lokalen Speicher
		 * @return string
		 */
		private function read_ws_key_cache()
		{
			//Hier koennte der WsKey von einer Session oder DB kommen
			//Halt irgend etwas um nicht mit jedem Seitenaufruf sich neu Einloggen zu muessen
			return '';
		}

		/*
		 * Schreibt den WsKey in einen Lokalen Speicher
		 * @param string $ws_key
		 * @return boole
		 */
		private function insert_ws_key_cache($ws_key)
		{
			//hier kann der WsKey in die Session oder Db gespeichert werden
			return true;
		}
	}
?>