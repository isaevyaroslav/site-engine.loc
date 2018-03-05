<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 28.10.2016
 * Time: 14:17
 *
 * Определяет ключ для активации аякс запроса и получет его пользовательское значение, если оно определено в файле конфигурации приложения.
 * Получает модель страницы.
 * Если модель содержит ключ для активации аякс запроса, то переводит модель страницы в json формат, выводит её на экран и выходит из работы приложения.
 *
 */

namespace plugins\ajax;


use app\Application;
use pluginsapi\ViewApi;

class View
{
	private static $ajax_flag_name = "ajax";
	public static function load(){
		if(array_key_exists("ajax_flag_name", Application::$app_config)){
			self::$ajax_flag_name = Application::$app_config["ajax_flag_name"];
		}
		$page_model = ViewApi::getPageModel();
		if($page_model === null){
			$page_model = [];
		}
		if(array_key_exists(self::$ajax_flag_name, $page_model)){
//			var_dump($page_model);
			echo json_encode($page_model);
			exit();
		}
	}
}