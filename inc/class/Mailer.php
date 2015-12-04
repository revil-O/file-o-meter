<?php
	/**
	 * mailer-class for the submitting of e-mails
	 *
	 * @author Soeren Pieper <soeren.pieper@docemos.de> / Martin Ufer <martin.ufer@docemos.de>
	 * @copyright Copyright (C) 2009  docemos GmbH
	 * @package file-o-meter
	 */

	/**
	 * mailer-class for the submitting of e-mails
	 *
	 * ATTENTION: this class extends the PHPMailer class, so PHPMailer is mandatory
	 * see /PHPMailer/class.phpmailer.php or http://sourceforge.net/projects/phpmailer/
	 * @package file-o-meter
	 * @subpackage class
	 */
	class Mailer extends PHPMailer
	{
		/**
		 * Standardeinstellungen laden
		 */
		public function __construct()
		{
			/**
		   * Sets the From email address for the message.
		   * @var string
		   */
			$this->From = $GLOBALS['setup_array']['mail']['from'];

			/**
			* Sets the From name of the message.
			* @var string
			*/
			$this->FromName = $GLOBALS['setup_array']['mail']['fromname'];

			/**
			* Sets the text-only body of the message.  This automatically sets the
			* email to multipart/alternative.  This body can be read by mail
			* clients that do not have HTML email capability such as mutt. Clients
			* that can read HTML will view the normal Body.
			* @var string
			*/
			$this->AltBody = $GLOBALS['setup_array']['mail']['altbody'];

			/**
			* Method to send mail: ("mail", "sendmail", or "smtp").
			* @var string
			*/
			$this->Mailer = $GLOBALS['setup_array']['mail']['sendtype'];

			/**
			* Sets the path of the sendmail program.
			* @var string
			*/
			$this->Sendmail = html_entity_decode($GLOBALS['setup_array']['mail']['sendmail'], ENT_QUOTES, 'UTF-8');

			/////////////////////////////////////////////////
			// PROPERTIES FOR SMTP
			/////////////////////////////////////////////////

			/**
			* Sets the SMTP hosts.  All hosts must be separated by a
			* semicolon.  You can also specify a different port
			* for each host by using this format: [hostname:port]
			* (e.g. "smtp1.example.com:25;smtp2.example.com").
			* Hosts will be tried in order.
			* @var string
			*/
			$this->Host = $GLOBALS['setup_array']['mail']['smtphost'];

			/**
			* Sets the default SMTP server port.
			* @var int
			*/
			$this->Port = $GLOBALS['setup_array']['mail']['smtpport'];

			/**
			* Sets connection prefix.
			* Options are "", "ssl" or "tls"
			* @var string
			*/
			$this->SMTPSecure = $GLOBALS['setup_array']['mail']['smtpsecure'];

			/**
			* Sets SMTP authentication. Utilizes the Username and Password variables.
			* @var bool
			*/
			$this->SMTPAuth = $GLOBALS['setup_array']['mail']['smtpauth'];

			/**
			* Sets SMTP username.
			* @var string
			*/
			$this->Username = $GLOBALS['setup_array']['mail']['smtpuser'];

			/**
			* Sets SMTP password.
			* @var string
			*/
			$this->Password = $GLOBALS['setup_array']['mail']['smtppw'];
		}

		/**
		 * Sendet eine E-Mail
		 * @param string $type
		 * @param string $subject
		 * @param string $body
		 * @param mixed $to, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param array $attachment array(array('filepfad', 'filename'), array('filepfad')) filename muss nicht angegeben werden
		 * @param mixed $bcc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param mixed $cc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @return boole
		 */
		public function send_mail($type = 'text', $subject, $body, $to, $attachment = array(), $bcc = '', $cc = '')
		{
			$log = new Logbook();

			//Html Mail senden
			if ($type == 'text')
			{
				$this->IsHTML(false);
			}
			else
			{
				$this->IsHTML(true);
			}

			//Betreffzeile
			$this->Subject = $subject;

			//Mailbody
			$this->Body = $body;

			//Empfaenger
			if (is_array($to))
			{
				// 0 => Emailadresse, 1 => Empfaengername
				if (isset($to[1]))
				{
					$this->AddAddress($to[0], $to[1]);
				}
				else
				{
					$this->AddAddress($to[0]);
				}
			}
			else
			{
				$this->AddAddress($to);
			}

			//Dateianhaenge
			if (count($attachment) > 0)
			{
				for($i = 0; $i < count($attachment); $i++)
				{
					if (file_exists($attachment[$i][0]))
					{
						if (isset($attachment[$i][1]))
						{
							$this->AddAttachment($attachment[$i][0], $attachment[$i][1]);
						}
						else
						{
							$this->AddAttachment($attachment[$i][0]);
						}
					}
				}
			}

			//BCC Empfaenger
			if (is_array($bcc) and count($bcc) > 0)
			{
				if (isset($bcc[1]))
				{
					// 0 => Emailadresse, 1 => Empfaengername
					$this->AddBCC($bcc[0], $bcc[1]);
				}
				else
				{
					$this->AddBCC($bcc[0]);
				}
			}
			elseif (!empty($bcc))
			{
				$this->AddBCC($bcc);
			}

			//CC Empfaenger
			if (is_array($cc) and count($cc) > 0)
			{
				if (isset($cc[1]))
				{
					// 0 => Emailadresse, 1 => Empfaengername
					$this->AddCC($cc[0], $cc[1]);
				}
				else
				{
					$this->AddCC($cc[0]);
				}
			}
			elseif (!empty($cc))
			{
				$this->AddCC($cc);
			}

			//Mail Senden
			if ($this->Send())
			{
				$log->insert_log_mail($type, $subject, $body, $to, $attachment, $bcc, $cc);
				$this->clear_all();
				return true;
			}
			else
			{
				$this->clear_all();
				return false;
			}
		}

		/**
		 * Sendet eine text E-Mail
		 * @param string $subject
		 * @param string $body
		 * @param mixed $to, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param array $attachment array(array('filepfad', 'filename'), array('filepfad')) filename muss nicht angegeben werden
		 * @param mixed $bcc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param mixed $cc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @return boole
		 */
		public function send_text_mail($subject, $body, $to, $attachment = array(), $bcc = '', $cc = '')
		{
			$this->CharSet		= 'utf-8';
			$this->AltBody		= '';
			$this->ContentType	= 'text';

			return $this->send_mail('text', $subject, $body, $to, $attachment, $bcc, $cc);
		}

		/**
		 * Sendet eine html E-Mail
		 * @param string $subject
		 * @param string $body
		 * @param mixed $to, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param array $attachment array(array('filepfad', 'filename'), array('filepfad')) filename muss nicht angegeben werden
		 * @param mixed $bcc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @param mixed $cc, array('mustermann@domain.de', 'Frank Musterman') oder 'mustermann@domain.de'
		 * @return boole
		 */
		public function send_html_mail($subject, $body, $to, $attachment = array(), $bcc = '', $cc = '')
		{
			$this->CharSet		= 'utf-8';
			return $this->send_mail('html', $subject, $body, $to, $attachment, $bcc, $cc);
		}

		/**
		* Leert die Array in der Hauptklasse
		* @return void
		*/
		private function clear_all()
		{
			$this->ClearAddresses();
			$this->ClearAllRecipients();
			$this->ClearAttachments();
			$this->ClearBCCs();
			$this->ClearCCs();
			$this->ClearCustomHeaders();
			$this->ClearReplyTos();
		}

		/**
		 * Prueft ob der uebergebene String eine Korrekte E-Mailadresse ist
		 * @param string $mail
		 * @return boole
		 */
		public function is_mail($mail)
		{
			$mail = trim($mail);
			if (!empty($mail))
			{
				//Pruefen ob @ vorhanden ist
				if (substr_count($mail, '@') == 1)
				{
					//E-Mailadresse in lokalen und Domainbereich teilen
					$mail_array = explode('@', $mail);
					$local = strtolower($mail_array[0]);
					$domain = strtolower($mail_array[1]);

					if (!empty($local) and !empty($domain))
					{
						//Domainbereich muss min. einen Punkt haben
						if (substr_count($domain, '.') > 0)
						{
							//Domainbereich in td und Domainnamen teilen
							$ex_domain_array = explode('.', $domain);

							if (is_array($ex_domain_array))
							{
								$domain_count_int = count($ex_domain_array) - 1;
								$domain_name_string = '';
								$domain_td_string = '';
								for($i = 0; $i <= $domain_count_int; $i++)
								{
									if ($i == $domain_count_int)
									{
										$domain_td_string = $ex_domain_array[$i];
									}
									else
									{
										$domain_name_string .= $ex_domain_array[$i];
									}
								}
							}
							else
							{
								return false;
							}
							//Pruefen ob eine Domainname vorhanden ist
							//Einen weitere Pruefung ist kaum moeglich da jede td ihre eigenen bestimmungen erlassen kann so koennen zb. auch Regionale sonderzeichen erlaubt sein
							if (empty($domain_name_string))
							{
								return false;
							}

							//Pruefen ob die td existiert
							// lan und local existieren eigentlich nicht
							$domain_td_array = array('aero','asia','ac','ad','ae','af','ag','ai','al','am','an','ao','aq','ar',
													'arpa','as','at','au','aw','ax','az','ba','bb','bd','be','bf','bg','bh','bi',
													'bitnet','biz','bj','bm','bn','bo','br','bs','bt','bv','bw','by','bz','ca','cat',
													'cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','com','cr','cs','cu',
													'cv','cx','cy','cz','dd','de','dj','dk','dm','do','dz','edu','ec','ee','eg',
													'eh','er','es','et','eu','example','fi','fj','fk','fm','fo','fr','ga','gb','gd','ge',
													'gf','gg','gh','gi','gl','gm','gn','gov','gp','gq','gr','gs','gt','gu','gw',
													'gy','hk','hm','hn','hr','ht','hu','id','ie','il','im','in','info','int','invalid','io',
													'iq','ir','is','it','je','jm','jo','jobs','jp','ke','kg','kh','ki','km','kn','kp',
													'kr','kw','ky','kz','la','lb','lc','li','lk','local','localhost','lr','ls','lt','lu','lv','ly',
													'ma','mc','md','me','mg','mh','mil','mk','ml','mm','mn','mo','mobi','mp','mq','mr',
													'ms','mt','mu','museum','mv','mw','mx','my','mz','na','name','nato','nc','ne',
													'net','nf','ng','ni','nl','no','np','nr','nu','nz','om','org','pa','pe','pf',
													'pg','ph','pk','pl','pm','pn','post','pr','pro','ps','pt','pw','py','qa','re',
													'ro','root','rs','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sj','sk',
													'sl','sm','sn','so','sr','st','su','sv','sy','sz','tc','td','tel','test','tf','tg',
													'th','tj','tk','tl','tm','tn','to','tp','tr','travel','tt','tv','tw','tz','ua',
													'ug','uk','um','us','uucp','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye',
													'yt','yu','za','zm','zw','lan','local','localhost','xxx');

							if (!in_array($domain_td_string, $domain_td_array))
							{
								return false;
							}

							//erlaubt zeichen fuer den local bereich
							$local_sign_array = array('a','b','c','d','e','f','g','h','i','j',
														'k','l','m','n','o','p','q','r','s','t',
														'u','v','w','x','y','z','0','1','2','3',
														'4','5','6','7','8','9','.','!','#','$',
														'%','&','\'','*','+','-','/','=','?','^',
														'_','`','{','}','|','~',' ');
							for($i = 0; $i < strlen($local); $i++)
							{
								if (!in_array($local{$i}, $local_sign_array))
								{
									return false;
								}
							}

							return true;
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
	}
?>