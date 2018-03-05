<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 28.10.2016
 * Time: 17:18
 *
 * Получает настройки страниц по умолчанию и загружает их библиотеки при наличии.
 * Возвращает в модель приложения настройки страниц уже с библиотеками.
 *
 */

namespace plugins\get_all_pages_lib;


use app\Application;
use pluginsapi\ModelApi;

class Model
{
	private static $config_key_pages_name = "pages_name_key";
	private static $pages_key_name = "name";
	public static function load(){
		self::get_pages_key_name();
		$pages_settings = ModelApi::getPagesSettings();
		$all_pages_lib = [];
		foreach($pages_settings as $page_id => $page_set_up){
			$lib_adres = "../".ModelApi::getDefaultLibsPath().$page_set_up["lib_name"].".php";
			if(is_file($lib_adres)){
				$all_pages_lib[$page_id] = require $lib_adres;

				$all_pages_lib[$page_id][self::$pages_key_name] = $page_set_up[self::$pages_key_name];
				if(!key_exists("parent", $all_pages_lib[$page_id]) && $page_set_up[self::$pages_key_name]!=="/"){
					$all_pages_lib[$page_id]["parent"] = "/";
				}
				if($page_set_up[self::$pages_key_name]==="/"){
					$all_pages_lib[$page_id]["parent"] = null;
				}
			}else{
				$all_pages_lib[$page_id] = $page_set_up;
			}
		}
		ModelApi::setPagesSettings($all_pages_lib);
	}
	private static function get_pages_key_name(){
		if(key_exists(self::$config_key_pages_name, Application::$app_config)){
			self::$pages_key_name = Application::$app_config[self::$config_key_pages_name];
		}
	}
}