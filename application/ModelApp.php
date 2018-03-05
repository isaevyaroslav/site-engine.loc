<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 30.03.2016
 * Time: 14:55
 *
 * Принимает аргументы запроса от контроллера.
 * Определяет адреса библиотек по умолчанию при помощи роутера и передаёт их в модуль интерфейса плагинов.
 * Получает и передаёт настройки страниц из файла настройки страниц.
 * Запускает работу моделей плагинов.
 * Получает от плагинов обновлённые настройки страниц и список запрашиваемых страниц.
 * Проверяет существуют ли запрошенные страницы в настройках страниц.
 */

namespace app;
use pluginsapi\ModelApi;

class ModelApp
{

	private $request_pages;
	private $pages_settings = [];
	private $add_libs = [];
	private static $page_model = [];
	private static $config_key_pages_name = "pages_name_key";
	private static $pages_key_name = "name";
	private static $child_pages_lib_key = "child_pages_lib";
	private static $child_pages_query_key = "child_pages";
	function __construct($request_args){
		self::get_pages_key_name();
		self::get_child_pages_query_key();
		ModelApi::setRequestArgs($request_args);
		$libs_router = new LibsRouter('model');
		ModelApi::setDefaultLibsPath($libs_router->getDefaultLibsPath());
		ModelApi::setDefaultLibName($libs_router->getDefaultLibName());
		ModelApi::setPagesLibName($libs_router->getPathSetting('pages_set_up_name'));
		$pages_settings_path = $libs_router->getLibPathOf('pages_set_up_name');
		if(is_file($pages_settings_path)){
			ModelApi::setPagesSettings(require_once($pages_settings_path));
		}
		Application::loadPlugins('Model');
		$this->pages_settings = ModelApi::getPagesSettings();
		$this->request_pages = ModelApi::getRequestPages();
		$this->add_libs = ModelApi::getAddLib();

		$page_model = [
			"index"=>[],
			"parents"=>[],
			"page"=>[]
		];
		if(sizeof($this->add_libs) > 0){
			foreach($this->add_libs as $lib_name => $lib_content){
				$page_model[$lib_name] = $lib_content;
			}
		}
		foreach($this->request_pages as $request_num => $request_name){
			$page_found = false;
			$child_pages = [];
			foreach($this->pages_settings as $page_id => $page_settings){
				if(key_exists(self::$pages_key_name, $page_settings)){
					if(/*$request_num === sizeof($this->request_pages)-1 &&*/ $page_settings["parent"] === $request_name){
						$child_pages[] = $page_settings;
					}
					$lowcase_page_name = mb_strtolower($page_settings[self::$pages_key_name]);
					if($lowcase_page_name === $request_name){
						if($request_num === 0){
							$page_model["index"] = $page_settings;
							if(sizeof($this->request_pages) > 1){
								$page_model["parents"][$request_num] = $page_settings;
							}else{
								$page_model["page"] = $page_settings;
								$page_model["parents"][$request_num] = $page_settings;
							}
							$page_found = true;
						}else if(key_exists("parent", $page_settings)){
							if($page_settings["parent"] === $this->request_pages[$request_num-1]){
								if($request_num === sizeof($this->request_pages)-1){
									$page_model["page"] = $page_settings;
									$page_model["parents"][$request_num] = $page_settings;
									$page_found = true;
								}else{
									$page_model["parents"][$request_num] = $page_settings;
									$page_found = true;
								}
							}
						}
					}
				}
			}
			if($page_found === false){
//				header("Location: ../404.html");
			}else if(sizeof($child_pages)>0){
				if($request_num === 0){
					$page_model["index"][self::$child_pages_query_key] = $child_pages;
				}else{
					if($request_num === sizeof($this->request_pages)-1){
						$page_model["page"][self::$child_pages_query_key] = $child_pages;
					}
				}
				$page_model["parents"][$request_num][self::$child_pages_query_key] = $child_pages;
			}
		}
		self::$page_model = $page_model;
	}
	private static function get_pages_key_name(){
		if(key_exists(self::$config_key_pages_name, Application::$app_config)){
			self::$pages_key_name = Application::$app_config[self::$config_key_pages_name];
		}
	}
	private static function get_child_pages_query_key(){
		if(key_exists(self::$child_pages_lib_key, Application::$app_config)){
			self::$child_pages_query_key = Application::$app_config[self::$child_pages_lib_key];
		}
	}
	public function getPageModel(){
        return self::$page_model;
	}
}