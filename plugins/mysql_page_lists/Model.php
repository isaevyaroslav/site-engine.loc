<?php

/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 15.08.2017
 * Time: 18:29
 */
namespace mysql_page_lists;


/*use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;*/

class Model
{
	public static function load(){
	}
}

/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 28.08.2017
 * Time: 18:02
 */

/*namespace mysql_page_lists;

use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model
{
	/*private static $page_queries_list_name = "queries_names_lib";
	private static $config_queries_list_key = "queries_lib_name";
	private static $pages_key_name = "name";
	private static $config_key_pages_name = "pages_name_key";*/
//	public static function load(){
		/*self::get_pages_key_name();
		self::get_queries_list_name();
		$pages_settings = ModelApi::getPagesSettings();
		$requested_pages = ModelApi::getRequestPages();
		$requested_page = $requested_pages[sizeof($requested_pages)-1];
		if(is_array($pages_settings)){
			foreach($pages_settings as $page_id => $page_setting){
				if(
					key_exists(self::$pages_key_name, $page_setting)
					&& key_exists(self::$page_queries_list_name, $page_setting)
					&& key_exists("parent", $pages_settings)
				){
					if($page_setting[self::$pages_key_name] === $requested_page){
						if(
							$pages_settings["parent"] === $requested_pages[sizeof($requested_pages)-2]
							|| $pages_settings["parent"] === null
						){
							$queries_names = $page_setting[self::$page_queries_list_name];
							if(is_string($queries_names)){
								$queries_names = explode(",", $queries_names);
							}
							if(is_array($queries_names)){
								foreach($queries_names as $query_number => $query_name){
									$pages_settings[$page_id][$query_name] = self::get_page_list($pages_settings[$page_id][$query_name]);
								}
							}
						}
					}
				}
			}
		}
		ModelApi::setPagesSettings($pages_settings);*/
//	}
	/*private static function get_pages_key_name(){
		if(key_exists(self::$config_key_pages_name, Application::$app_config)){

			self::$pages_key_name = Application::$app_config[self::$config_key_pages_name];
		}
	}
	private static function get_queries_list_name(){
		if(key_exists(self::$config_queries_list_key, Application::$app_config)){
			self::$page_queries_list_name = Application::$app_config[self::$config_queries_list_key];
		}
	}
	private static function get_page_list($page_query){
		$page_list = [];
		$STH = BD::query($page_query);
		while($row = $STH->fetch()) {
			if(key_exists(self::$pages_key_name, $row)){
				$page_list[]=$row;
			}
		}
		return $page_list;
	}*/
//}*/