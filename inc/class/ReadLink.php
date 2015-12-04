<?php
	class ReadLink
	{
		private $setup_array = array();

		public function __construct()
		{
			$this->setup_array['min_len'] = $GLOBALS['setup_array']['index_min_len'];
		}

		/**
		 * Traegt die Woerter ind die DB ein und ordnet diese dem Link zu.
		 * @param array $word_array
		 * @param int $link_id
		 * @return void
		 */
		public function insert_link_word_array($word_array, $link_id, $tagging = true)
		{
			$cdb = new MySql;

			foreach($word_array as $word)
			{
				$sql = $cdb->select("SELECT word_id FROM fom_search_word WHERE word='$word'");
				$result = $cdb->fetch_array($sql);

				$word_id = 0;
				$word_file_exists = false;

				//Das Wort existiert bereits
				if ($result['word_id'] > 0)
				{
					$word_id = $result['word_id'];

					//Doppelte eintraege verhindern
					$sql = $cdb->select('SELECT word_id, tagging FROM fom_search_word_link WHERE word_id='.$result['word_id'].' AND link_id='.$link_id);
					$sub_result = $cdb->fetch_array($sql);

					if ($sub_result['word_id'] > 0)
					{
						$word_file_exists = true;
					}
				}
				else
				{
					//Neues Wort eintragen
					if ($cdb->insert("INSERT INTO fom_search_word (word) VALUES ('$word')"))
					{
						if ($cdb->get_affected_rows() == 1)
						{
							$last_id = $cdb->get_last_insert_id();
							if ($last_id > 0)
							{
								$word_id = $last_id;
							}
						}
					}
				}

				//Wort vorhanden
				if ($word_id > 0)
				{
					//Suchbegriff kommt aus Dateiinhalt
					if ($tagging == false)
					{
						//Keine Doppelten zuordnungen
						if ($word_file_exists == false)
						{
							$cdb->insert("INSERT INTO fom_search_word_link (word_id, link_id, tagging) VALUES ($word_id, $link_id, '0')");
						}
					}
					//Suchbegriff kommt aus tagging
					elseif ($tagging == true)
					{
						//keine Doppelten zuordnungen
						if ($word_file_exists == false)
						{
							$cdb->insert("INSERT INTO fom_search_word_link (word_id, link_id, tagging) VALUES ($word_id, $link_id, '1')");
						}
						elseif ($sub_result['tagging'] == '0')
						{
							$cdb->update("UPDATE fom_search_word_link SET tagging='1' WHERE word_id=$word_id AND link_id=$link_id");
						}
					}
				}
			}
		}

		/**
		 * Bereinigt den String von Sonderzeichen und Stopwoertern
		 * @param string $link_string
		 * @return array
		 */
		public function clear_string($link_string)
		{
			$rf = new ReadFile;
			return $rf->clear_string($link_string);
		}
	}
?>