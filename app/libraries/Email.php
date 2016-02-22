<?php

namespace App\Lib;


class Email 
{
	var $to;
	var $to_name = '';
    var $cc;
    var $cc_name = '';
    var $subject;
	var $body;
	var $attach = NULL;

	function Email($to = NULL, $subject = NULL, $body = NULL)
	{
		$this->to = $to;
		$this->subject = $subject;
		$this->body = $body;
	}

	function sendWithSmtpConfig($cfg)
	{
		$mail = new PHPMailer();
        $mail->IsSMTP();
		$mail->CharSet = 'UTF-8';
        $mail->IsHTML(true);
        $mail->SMTPSecure = 'ssl'; // $cfg['smtp_secure'];
        $mail->SMTPAuth = $cfg['smtp_auth'];
		$mail->Host = $cfg['smtp_server'];
		$mail->Port = $cfg['smtp_port'];
        $mail->Username = $cfg['smtp_usr'];
        $mail->Password = $cfg['smtp_psw'];
        $mail->SMTPDebug = empty($cfg['smtp_debug']) ? 0 : $cfg['smtp_debug'];

		if (!empty($cfg['smtp_from_email']))
		{
			$mail->SetFrom ($cfg['smtp_from_email'], $cfg['smtp_from_name']);
		} else 
		{
			$mail->SetFrom ($cfg['smtp_server'], $cfg['smtp_usr'] );
		}
		
		if (is_array($this->to))
		{
			foreach ( $this->to as $key => $val ) 
			{
				$name = is_numeric ( $key ) ? "" : $key;
				$mail->AddAddress ( $val, $name );
			}
		} else 
		{
			$mail->AddAddress($this->to, $this->to_name);
		}
		
		if (!empty($cfg['smtp_reply_email']))
		{
			$mail->AddReplyTo($cfg['smtp_reply_email'], $cfg['smtp_reply_name'] );
		}
		
		if ($this->cc) 
		{
			if (is_array ( $this->cc )) {
				foreach ( $this->cc as $keyc => $valcc ) {
					$name = is_numeric ( $keyc ) ? "" : $keyc;
					$mail->AddCC($valcc, $name);
				}
			} else {
				$mail->AddCC ($this->cc, $this->cc_name);
			}
		}
		
		if ($this->attach) 
		{
			if (is_array ( $this->attach )) 
			{
				foreach ( $this->attach as $key => $val ) 
				{
					$mail->AddAttachment($val);
				}
			} else 
			{
				$mail->AddAttachment ($this->attach);
			}
		}

		$mail->WordWrap = 50;
		$mail->Subject = $this->subject;
		$mail->Body = $this->body;
		$mail->AltBody = "";

        if(!$mail->Send()) {
            // -- Sent mail error --
            return $mail->ErrorInfo;
        } else {
            // -- Sent mail successfully --
            return true;
        }
	}
}
?>