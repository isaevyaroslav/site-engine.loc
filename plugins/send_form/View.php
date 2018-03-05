<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 24.05.2016
 * Time: 21:21
 */

namespace plugins\send_form;


use pluginsapi\ViewApi;

class View
{
	private static $mail_model;
	public static function load(){
		$page_model = ViewApi::getPageModel();
		if(is_array($page_model)){
			if(key_exists("send_form", $page_model)){
				self::$mail_model = $page_model["send_form"];
				self::sendMail();
			}
		}
	}
	private static function sendMail(){
		$mail_message = self::renderMail();
		if($mail_message != null && key_exists("form_lib", self::$mail_model)){
			$mail_lib = self::$mail_model["form_lib"];
			$sabject = self::$mail_model["form_lib"]["sabject"];
//			$heads = self::$mail_model["form_lib"]["heads"].'From:'.self::$mail_model["args"]["fio"].' <'.self::$mail_model["args"]["email"].'>\r\n';
			$heads= "MIME-Version: 1.0\r\n";
			$heads .= "Content-type: text/html; charset=utf8\r\n";

			/* дополнительные шапки */
			$heads .= "From: ".self::$mail_model["args"]["fio"]." <".self::$mail_model["args"]["email"].">\r\n";
//			var_dump($heads);
//			$heads .= ;
			$to_mails = [];
			if(key_exists("sabject", $mail_lib)){
				$sabject = $mail_lib['sabject'];
			}
			if(key_exists("heads", $mail_lib)){
//				$heads = $mail_lib['heads'];
			}
			if(key_exists("to", $mail_lib)){
				$to_sets = explode("$", $mail_lib["to"]);
				if(sizeof($to_sets)>1 && key_exists("args", self::$mail_model)){
					array_shift($to_sets);
					foreach($to_sets as $arg_num => $arg_name){
						$to_mails[] = self::$mail_model["args"][$arg_name];
					}
				}else{
					$to_mails = explode(",", $to_sets["0"]);
				}
			}
			foreach($to_mails as $mail_num => $to_mail){
				mail($to_mail,$sabject,$mail_message,$heads);
			}
//			var_dump($mail_lib);
			if(key_exists("success_location", $mail_lib)){
				header('Location: '.$mail_lib["success_location"]);
			}else{
//				header('Location: ../');
			}

		}
	}
	private static function renderMail(){
		$template_path = self::getTemplatePath();
		if(is_file($template_path)){
			ob_start();
				require $template_path;
				$mail_message = ob_get_contents();
			ob_end_clean();
			return $mail_message;
		}
		return null;
	}
	private static function getTemplatePath(){
//		$default_template_path = ViewApi::getTemplatesPath();
		$email_template_name = self::getTempName();
		if($email_template_name != null){
			$email_template_path = '../www/templates/'.$email_template_name.".php";
			return $email_template_path;
		}
		return null;
	}
	private static function getTempName(){
		$mail_model = self::$mail_model;
			if(key_exists("form_lib", $mail_model)){
				if(key_exists("template", $mail_model["form_lib"])){
					return $mail_model["form_lib"]["template"];
				}
			}
		return null;
	}
}