<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 27.04.2016
 * Time: 17:38
 */

namespace plugins\send_form;


use app\Application;
use pluginsapi\ModelApi;

class Model{
	private static $forms_lib_name = 'forms';
	private static $forms_name_key = 'form_name';
	private static $form_lib = null;
	private static $form_errors = [];
	public static function load(){
		$request = ModelApi::getRequest();
		$mail_model = self::getMailModel($request["post"]);
		if($mail_model != null){
			$mail_model["send_form"] = $mail_model;
			ModelApi::addLib($mail_model);
		}
	}
	private static function getMailModel($post_args){
		self::setFormsNameKey();
		$email_model = null;
		$forms_name_key = self::getFormsNameKey();
		if(key_exists($forms_name_key, $post_args)){
			$form_name = $post_args[$forms_name_key];
			self::setFormLib($form_name);
			self::checkForm($post_args);
			if(sizeof(self::$form_errors) === 0){
				$email_model["form_lib"] = self::$form_lib;
				$email_model["args"] = $post_args;
			}else{
				self::returnRightValues($post_args);
				$email_model["errors"] = self::$form_errors;
			}
			return $email_model;
		}else{
			return null;
		}
	}

	private static function setFormsLibName(){
		if(key_exists('forms_lib_name', Application::$app_config)){
			self::$forms_lib_name = Application::$app_config['forms_lib_name'];
		}
	}

	public static function getFormsNameKey(){
		return self::$forms_name_key;
	}
	private static function setFormsNameKey(){
		if(key_exists('forms_name_key', Application::$app_config)){
			self::$forms_name_key = Application::$app_config['forms_name_key'];
		}
	}

	public static function getFormsLibName(){
		return self::$forms_lib_name;
	}
	private static function getFormsLibPath(){
		self::setFormsLibName();
		$libs_path = ModelApi::getDefaultLibsPath();
		$forms_lib_path = '../'.$libs_path.self::getFormsLibName().'.php';
		return $forms_lib_path;
	}
	private static function setFormLib($form_name){
		$form_lib = null;
		$add_lib = ModelApi::getAddLib();
		$forms_lib_name = self::getFormsLibName();
		if(is_array($add_lib)){
			if(isset($add_lib[$forms_lib_name][$form_name])){
				$form_lib = $add_lib[$forms_lib_name][$form_name];
			}
		}
		if($form_lib === null){
			$forms_lib_path = self::getFormsLibPath();
			if(is_file($forms_lib_path)){
				$forms_lib = require_once $forms_lib_path;
				if(key_exists($form_name, $forms_lib)){
					$form_lib = $forms_lib[$form_name];
				}
			}
		}
		self::$form_lib = $form_lib;
	}
	private static function checkForm($post_args){
		$error_sent = null;
		if(key_exists('required', self::$form_lib)){
			if(is_array(self::$form_lib['required'])){
				foreach(self::$form_lib['required'] as $num => $arg_name){
					if(key_exists($arg_name, $post_args)){
						if(!(is_scalar($post_args[$arg_name]) && strlen($post_args[$arg_name]))){
							self::$form_errors[$arg_name]['value'] = $post_args[$arg_name];
							self::$form_errors[$arg_name]['error'] = 'required_error';
						}
					}else{
						self::$form_errors[$arg_name]['value'] = $post_args[$arg_name];
						self::$form_errors[$arg_name]['error'] = 'required_error';
					}
				}
			}
		}
		if(key_exists('args_types', self::$form_lib)){
			if(is_array(self::$form_lib['args_types'])){
				foreach(self::$form_lib['args_types'] as $arg_name => $arg_type){
					if(key_exists($arg_name, $post_args) && !key_exists($arg_name, self::$form_errors)){
						if(strlen($post_args[$arg_name])){
							$post_args[$arg_name] = self::checkArgType($post_args[$arg_name], $arg_type, $arg_name);
						}
					}
				}
			}
		}
		return $post_args;
	}
	private  static function returnRightValues($post_args){
		foreach($post_args as $arg_name => $arg_value){
			if(!key_exists($arg_name, self::$form_errors)){
				self::$form_errors[$arg_name]['value'] = $arg_value;
//				self::$form_errors[$arg_name]['error'] = false;
			}
		}
	}
	private static function checkArgType($arg, $arg_type, $arg_name){
		$arg = trim($arg);
		switch($arg_type){
			case 'phone':
				$clear_arg = preg_replace('/[\D]+/', '', $arg);
				if(!preg_match('/^[\d]{5,}$/', $clear_arg)){
					self::$form_errors[$arg_name]['value'] = $arg;
					self::$form_errors[$arg_name]['error'] = 'syntax_error';
				}
				break;
			case 'fio':
				$clear_arg = preg_replace('/[\s\d]+/', ' ', $arg);
				$clear_arg = trim($clear_arg);
//				setlocale(LC_ALL, "ru_RU.UTF-8");
				if(!preg_match('/^[\wа-яё0-9]+$/iu', $clear_arg)){
					self::$form_errors[$arg_name]['value'] = $arg;
					self::$form_errors[$arg_name]['error'] = 'syntax_error';
				}
				break;
			case 'email':
				if(!preg_match('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/', $arg)){
					self::$form_errors[$arg_name]['value'] = $arg;
					self::$form_errors[$arg_name]['error'] = 'syntax_error';
				}
				break;
			case 'text':
				if(!preg_match('/[\w\dа-яё]+/iu', $arg)){
					self::$form_errors[$arg_name]['value'] = $arg;
					self::$form_errors[$arg_name]['error'] = 'syntax_error';
				}
			break;
			case 'int':
				$clear_arg = preg_replace('/[^\d]+/', '', $arg);
				if(!preg_match('/^[\d]+$/', $clear_arg)){
					self::$form_errors[$arg_name]['value'] = $arg;
					self::$form_errors[$arg_name]['error'] = 'syntax_error';
				}
				break;
			default:
				break;
		}
		return $arg;
	}
}