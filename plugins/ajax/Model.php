<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 28.10.2016
 * Time: 17:18
 *
 * Определяет имя для активации аякс запроса и получет его пользовательское значение, если оно определено в файле конфигурации приложения.
 * Получает GET запросы из модуля интерфейса для плагинов.
 * Если в GET запросе есть определённый флаг аякс, то передаёт его значение в модель страницы.
 *
 */

namespace plugins\ajax;


use app\Application;
use pluginsapi\ModelApi;

class Model
{
	private static $ajax_flag_name = "ajax";
	public static function load(){
		if(array_key_exists("ajax_flag_name", Application::$app_config)){
			self::$ajax_flag_name = Application::$app_config["ajax_flag_name"];
		}
		$get_request = ModelApi::getRequest()["get"];
//		$post_request = ModelApi::getRequest()["post"];
		if(array_key_exists(self::$ajax_flag_name, $get_request) /* || array_key_exists(self::$ajax_flag_name, $post_request)*/){
			$ajax_flag[self::$ajax_flag_name] = $get_request[self::$ajax_flag_name];
			ModelApi::setAddLib($ajax_flag);
		}
	}
}