<?php
	/**
	 * this file contains all actions for the logbook-folder
	 * @package file-o-meter
	 * @subpackage logbook
	 */

	if ($_POST['job_string'] == 'del_log')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['logid_int']) or !is_numeric($_POST['logid_int'])) {$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				if ($cdb->delete('DELETE FROM fom_log_login WHERE log_id='.$_POST['logid_int']))
				{
					$meldung['ok'][] = get_text(366, 'return');//Der Eintrag wurde gelöscht.
					$GLOBALS['FOM_VAR']['fileinc'] = 'main';
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
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}
	elseif ($_POST['job_string'] == 'del_log_all')
	{
		if ($reload->check($_POST['reload_sperre_string']))
		{
			$pflichtfelder = 0;
			if (!isset($_POST['logid_string']) or empty($_POST['logid_string'])) {$pflichtfelder++;}

			if ($pflichtfelder == 0)
			{
				$log_id_array = explode(',', $_POST['logid_string']);

				if (is_array($log_id_array))
				{
					$error_count = 0;

					for ($i=0; $i < count($log_id_array); $i++)
					{
						if (!empty($log_id_array[$i]) and is_numeric($log_id_array[$i]))
						{
							if (!$cdb->delete('DELETE FROM fom_log_login WHERE log_id='.$log_id_array[$i]))
							{
								$error_count++;
							}
						}
					}

					if ($error_count == 0)
					{
						$meldung['ok'][] = get_text(366, 'return');//Der Eintrag wurde gelöscht.
						$GLOBALS['FOM_VAR']['fileinc'] = 'main';
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
			$meldung['error'][] = setError(get_text('reload','return'), WARNING, __LINE__);//A reload blockade prevented double data entry!
		}
	}

?>