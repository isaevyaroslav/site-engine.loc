<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 19:33
 *
 * Определяет название подбиблиотеки ключей запросов в библилиотеке страницы.
 *
 */

namespace plugins\mysql_page_queries;

use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model{
	private static $page_queries_list_name = "queries_names_lib";
	private static $config_queries_list_key = "queries_lib_name";
	private static $pages_key_name = "name";
	private static $config_key_pages_name = "pages_name_key";
	public static function load(){
		self::get_pages_key_name();
		self::get_queries_list_name();
		$pages_settings = ModelApi::getPagesSettings();
		$requested_pages = ModelApi::getRequestPages();
		$requested_page = $requested_pages[sizeof($requested_pages)-1];
		if(is_array($pages_settings)){
			foreach($pages_settings as $page_id => $page_setting){

				if(
					key_exists(self::$pages_key_name, $page_setting)
					&& key_exists(self::$page_queries_list_name, $page_setting)
					&& key_exists("parent", $page_setting)
				){
					if($page_setting[self::$pages_key_name] === $requested_page){
						if(
							$page_setting["parent"] === $requested_pages[sizeof($requested_pages)-2]
							|| $page_setting["parent"] === null
						){
							$queries_names = $page_setting[self::$page_queries_list_name];
							if(is_string($queries_names)){
								$queries_names = explode(",", $queries_names);
							}
							if(is_array($queries_names)){
								foreach($queries_names as $query_number => $query_name){
									if($pages_settings[$page_id][$query_name] !== null){
										$pages_settings[$page_id][$query_name] = self::get_page_list($pages_settings[$page_id][$query_name]);
									}
								}
							}
						}
					}
				}
			}
		}
		ModelApi::setPagesSettings($pages_settings);
	}
	private static function get_pages_key_name(){
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
		$pages = BD::query($page_query);
		if(is_array($pages)){
			foreach($pages as $page_id => $page_settings){
				if(key_exists(self::$pages_key_name, $page_settings)){
					$page_list[]=$page_settings;
				}
			}
		}
		return $page_list;
	}
}