<?php
	/**
	 * this file contains all actions for the user_group-folder
	 * @package file-o-meter
	 * @subpackage user_group
	 */

	$reload = new Reload;
	if ($_POST['job_string'] == 'add_usergroup')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Gruppenbezeichnung
			if (!isset($_POST['groupnname_string']) or empty($_POST['groupnname_string'])){$pflichtfelder++;}
			if ($pflichtfelder == 0)
			{
				$post_array = array();

				//Nicht Projektbezogene Zugriffsmoeglichkeiten
				if (isset($_POST['other_options']))
				{
					$post_array['other_options'] = $_POST['other_options'];
				}
				//Projektbezogene Zugriffsmoeglichkeiten
				if (isset($_POST['project']))
				{
					$post_array['project'] = $_POST['project'];
				}

				//Usergruppenname eintrageb
				if ($cdb->insert("INSERT INTO fom_user_group (usergroup) VALUES ('".$_POST['groupnname_string']."')"))
				{
					if ($cdb->get_affected_rows() == 1)
					{
						//Usergruppen ID
						$user_group_id = $cdb->get_last_insert_id();

						//errorzaehler
						$access_error_count = 0;

						//Alternative Zugriffsoptionen vorhanden
						if (isset($post_array['other_options']) and count($post_array['other_options']) > 0)
						{
							//ein Array mit allen zur Verfuegung stehen alternativen Zugriffsmoeglichkeiten
							$other_access_options = $ac->get_other_access_options();

							//Zugriffsrechte fuer nicht Projekt, Verzeichnis, Dateibezogene Objekte
							foreach($post_array['other_options'] as $other => $access)
							{
								//dient nur zur Sicherheit damit hier nicht irgendein bloedsin eingetragen wird
								if (isset($other_access_options[$other]))
								{
									//Zugriffsrechte eintragen
									if (!$ac->insert($other, 0, 0, $user_group_id, $access))
									{
										$access_error_count++;
									}
								}
							}
						}

						//Projektbezogene Zugriffsmoeglichkeiten
						if (isset($post_array['project']) and count($post_array['project']) > 0)
						{
							//Projektspezifische Zugriffsrechte
							foreach($post_array['project'] as $project_id => $access)
							{
								if (is_numeric($project_id))
								{
									//Pruefen ob es das Projekt auch gibt
									$sql = $cdb->select("SELECT projekt_id FROM fom_projekte WHERE projekt_id=$project_id");
									$result = $cdb->fetch_array($sql);

									if (isset($result['projekt_id']) and $result['projekt_id'] > 0)
									{
										//Zugriffsrechte eintragen
										if (!$ac->insert('PROJECT', $project_id, 0, $user_group_id, $access))
										{
											$access_error_count++;
										}
										else
										{
											//Trigger Events eintragen
											if (isset($access['mn']) and $access['mn'] == 1)
											{
												$mn_sql = $cdb->select('SELECT user_id FROM fom_user_membership WHERE usergroup_id='.$user_group_id);
												while ($mn_result = $cdb->fetch_array($mn_sql))
												{
													$mn->insert_all_trigger_events($project_id, $mn_result['user_id']);
												}
											}
										}
									}
								}
							}
						}

						if ($access_error_count == 0)
						{
							$meldung['ok'][] = get_text(96,'return');//The dataset was created.
						}
						else
						{
							$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
						}
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'edit_usergroup')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Usergruppenname vorhanden
			if (!isset($_POST['groupnname_string']) or empty($_POST['groupnname_string'])){$pflichtfelder++;}
			//UsergruppenID vorhanden
			if (!isset($_POST['ugid_int'])){$pflichtfelder++;}
			if ($pflichtfelder == 0)
			{
				$post_array = array();

				//Nicht Projektbezogene Zugriffsmoeglichkeiten
				if (isset($_POST['other_options']))
				{
					$post_array['other_options'] = $_POST['other_options'];
				}
				//Projektbezogene Zugriffsmoeglichkeiten
				if (isset($_POST['project']))
				{
					$post_array['project'] = $_POST['project'];
				}

				//Usergruppenname aendern
				if ($cdb->update("UPDATE fom_user_group SET usergroup='".$_POST['groupnname_string']."' WHERE usergroup_id=".$_POST['ugid_int']))
				{
					$user_group_id = $_POST['ugid_int'];

					//errorzaehler
					$access_error_count = 0;

					$other_access_array = $ac->get_other_access_options();

					foreach ($other_access_array as $type => $txt)
					{
						if (!isset($post_array['other_options'][$type]['r']))
						{
							$post_array['other_options'][$type]['r'] = 0;
						}

						if (!isset($post_array['other_options'][$type]['w']))
						{
							$post_array['other_options'][$type]['w'] = 0;
						}
					}

					//Alternative Zugriffsoptionen vorhanden
					if (isset($post_array['other_options']) and count($post_array['other_options']) > 0)
					{
						//ein Array mit allen zur Verfuegung stehen alternativen Zugriffsmoeglichkeiten
						$other_access_options = $ac->get_other_access_options();

						//Zugriffsrechte fuer nicht Projekt, Verzeichnis, Dateibezogene Objekte
						foreach($post_array['other_options'] as $other => $access)
						{
							//dient nur zur Sicherheit damit hier nicht irgendein bloedsin eingetragen wird
							if (isset($other_access_options[$other]))
							{
								//Zugriffsrechte eintragen
								if (!$ac->insert($other, 0, 0, $user_group_id, $access))
								{
									$access_error_count++;
								}
							}
						}
					}

					$edit_project_id_array = array();
					//Projektbezogene Zugriffsmoeglichkeiten
					if (isset($post_array['project']) and count($post_array['project']) > 0)
					{
						//Projektspezifische Zugriffsrechte
						foreach ($post_array['project'] as $project_id => $access)
						{
							if (is_numeric($project_id))
							{
								//Pruefen ob es das Projekt auch gibt
								$sql = $cdb->select("SELECT projekt_id FROM fom_projekte WHERE projekt_id=$project_id");
								$result = $cdb->fetch_array($sql);

								if (isset($result['projekt_id']) and $result['projekt_id'] > 0)
								{
									$edit_project_id_array[] = $project_id;

									//Zugriffsrechte eintragen
									if (!$ac->insert('PROJECT', $project_id, 0, $user_group_id, $access))
									{
										$access_error_count++;
									}
									else
									{
										//Trigger Events eintragen
										if (isset($access['mn']) and $access['mn'] == 1)
										{
											$mn_sql = $cdb->select('SELECT user_id FROM fom_user_membership WHERE usergroup_id='.$user_group_id);
											while ($mn_result = $cdb->fetch_array($mn_sql))
											{
												$mn->insert_all_trigger_events($project_id, $mn_result['user_id']);
											}
										}
										else
										{
											$mn_sql = $cdb->select('SELECT user_id FROM fom_user_membership WHERE usergroup_id='.$user_group_id);
											while ($mn_result = $cdb->fetch_array($mn_sql))
											{
												$mn_exists = false;
												//Alle anderen Usergruppen des Users Suchen
												$mn_g_sql = $cdb->select('SELECT usergroup_id FROM fom_user_membership WHERE user_id='.$mn_result['user_id'].' AND usergroup_id!='.$user_group_id);
												while ($mn_g_result = $cdb->fetch_array($mn_g_sql))
												{
													if ($ac->mn_exists($project_id, $mn_g_result['usergroup_id']))
													{
														$mn_exists = true;
													}
												}

												if ($mn_exists == false)
												{
													$mn->delete_trigger_events($project_id, $mn_result['user_id']);
												}
											}
										}
									}
								}
							}
						}
					}

					//Entfernt von allen Projekten die nicht bearbeitet wurden die zugriffsrechte
					$sql = $cdb->select('SELECT projekt_id FROM fom_projekte');
					while ($result = $cdb->fetch_array($sql))
					{
						if (!in_array($result['projekt_id'], $edit_project_id_array))
						{
							$cdb->delete("DELETE FROM fom_access WHERE type='PROJECT' AND id=".$result['projekt_id'].' AND usergroup_id='.$user_group_id);
						}
					}

					if ($access_error_count == 0)
					{
						$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
					}
					else
					{
						$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
					}
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'edit_usergroup_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Pflichtangabe
			if (!isset($_POST['access_array']) or empty($_POST['access_array'])){$pflichtfelder++;}
			if (!isset($_POST['usergroup_id_int']) or empty($_POST['usergroup_id_int'])){$pflichtfelder++;}
			if (!isset($_POST['id_int']) or empty($_POST['id_int'])){$pflichtfelder++;}
			if (!isset($_POST['type_string']) or empty($_POST['type_string'])){$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$access_array = $ac->verify_access($_POST['access_array']);
				if ($ac->insert($_POST['type_string'], $_POST['id_int'], 0, $_POST['usergroup_id_int'], $access_array))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'del_usergroup_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Pflichtangabe
			if (!isset($_POST['usergroup_id_int']) or empty($_POST['usergroup_id_int'])){$pflichtfelder++;}
			if (!isset($_POST['id_int']) or empty($_POST['id_int'])){$pflichtfelder++;}
			if (!isset($_POST['type_string']) or empty($_POST['type_string'])){$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($ac->delete($_POST['type_string'], $_POST['id_int'], 0, $_POST['usergroup_id_int']))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'edit_user_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Pflichtangabe
			if (!isset($_POST['access_array']) or empty($_POST['access_array'])){$pflichtfelder++;}
			if (!isset($_POST['user_id_int']) or empty($_POST['user_id_int'])){$pflichtfelder++;}
			if (!isset($_POST['id_int']) or empty($_POST['id_int'])){$pflichtfelder++;}
			if (!isset($_POST['type_string']) or empty($_POST['type_string'])){$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$access_array = $ac->verify_access($_POST['access_array']);
				if ($ac->insert($_POST['type_string'], $_POST['id_int'], $_POST['user_id_int'], 0, $access_array))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
	elseif ($_POST['job_string'] == 'del_user_folder')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;

			//Pflichtangabe
			if (!isset($_POST['user_id_int']) or empty($_POST['user_id_int'])){$pflichtfelder++;}
			if (!isset($_POST['id_int']) or empty($_POST['id_int'])){$pflichtfelder++;}
			if (!isset($_POST['type_string']) or empty($_POST['type_string'])){$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($ac->delete($_POST['type_string'], $_POST['id_int'], $_POST['user_id_int'], 0))
				{
					$meldung['ok'][] = get_text(97,'return');//The changes were successfully saved.
				}
				else
				{
					$meldung['error'][] = setError(get_text('error','return'), ERROR, __LINE__);//An error has occurred!
				}
			}
			else
			{
				$meldung['error'][] = setError(get_text(95,'return'), WARNING, __LINE__);//Please complete all mandatory fields! //PFLICHTFELDER
			}
		}
		else
		{
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
		$GLOBALS['FOM_VAR']['fileinc'] = '';
	}
?>