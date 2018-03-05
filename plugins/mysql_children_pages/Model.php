<?php

/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 15.08.2017
 * Time: 18:29
 */
namespace plugins\mysql_children_pages;


use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model
{
	private static $child_pages_lib_key = "child_pages_lib";
	private static $child_pages_query_key = "child_pages";
	private static $config_key_pages_name = "pages_name_key";
	private static $pages_key_name = "name";
	private static $pages_settings;
	private static $request_pages;
	public static function load(){
		self::set_up();
		$parent_page = null;
		foreach(self::$request_pages as $request_order => $requested_name){
			if(key_exists($request_order-1, self::$request_pages)){
				$parent_page = self::$request_pages[$request_order-1];
			}
			for($page_id = 0; $page_id < sizeof(self::$pages_settings); $page_id++){
				if(key_exists($page_id, self::$pages_settings)){
					if(is_array(self::$pages_settings[$page_id])){
						$page_setting = self::$pages_settings[$page_id];
						if($page_setting[self::$pages_key_name] === $requested_name){
							if($page_setting["parent"] === $parent_page || $request_order === 0){
								if(key_exists(self::$child_pages_query_key, $page_setting)){
									self::add_child_pages($page_setting[self::$child_pages_query_key], $page_setting[self::$pages_key_name]);
								}
							}
						}
					}
				}
			}
		}
		ModelApi::setPagesSettings(self::$pages_settings);
	}
	private static function set_up(){
		self::get_child_pages_query_key();
		self::get_pages_key_name();
		self::$pages_settings = ModelApi::getPagesSettings();
		self::$request_pages = ModelApi::getRequestPages();
	}
	private static function get_child_pages_query_key(){
		if(key_exists(self::$child_pages_lib_key, Application::$app_config)){
			self::$child_pages_query_key = Application::$app_config[self::$child_pages_lib_key];
		}
	}
	private static function get_pages_key_name(){
		if(key_exists(self::$config_key_pages_name, Application::$app_config)){
			self::$pages_key_name = Application::$app_config[self::$config_key_pages_name];
		}
	}
	private static function add_child_pages($pages_query, $parent_page_name){
		$pages = BD::query($pages_query);
		if(is_array($pages)){
			foreach($pages as $page_id => $page_settings){
				if(key_exists(self::$pages_key_name, $page_settings)){
					$page_settings["parent"] = $parent_page_name;
					self::$pages_settings[]=$page_settings;
				}
			}
		}

		/*while($row = $STH->fetch()) {
			if(key_exists(self::$pages_key_name, $row)){
				$row["parent"] = $parent_page_name;
				self::$pages_settings[]=$row;
			}
		}*/
	}
}