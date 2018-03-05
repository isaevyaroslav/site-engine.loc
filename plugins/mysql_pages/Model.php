<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 19:33
 *
 * Определяет ключ к запросу страниц из библиотеки страницы
 *
 */

namespace plugins\mysql_pages;


use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model{
	private static $child_pages_lib_key = "child_pages_lib";
	private static $child_pages_query_key = "child_pages";
	private static $pages_settings;
	public static function load(){
		self::get_child_pages_query_key();
		self::$pages_settings = ModelApi::getPagesSettings();
		self::get_pages_childrens();
	}
	private static function get_child_pages_query_key(){
		if(key_exists(self::$child_pages_lib_key, Application::$app_config)){
			self::$child_pages_query_key = Application::$app_config[self::$child_pages_lib_key];
		}
	}
	private static function get_pages_childrens(){
		$new_pages = [];
		foreach(self::$pages_settings as $page_name => $page_settings){
			self::get_page_childrens($page_settings);
		}
	}
	private static function get_page_childrens($page_settings){
		/*if(key_exists(self::$child_pages_query_key, $page_settings)){

		}*/
	}
	/*private static $config;
	private static $libs_path;
	private static $pages_key_name = "name";
	private static $child_pages_query_key = "child_pages";
	private static $child_pages_lib_key = "child_pages_lib";
	public static function load(){
		self::getConfig();
		self::get_pages_key_name();
		self::get_libs_path();
		$pages_query = self::get_pages_query();
		$pages_libs = self::get_pages_libs($pages_query);

	}
	private static function getConfig (){
		self::$config = Application::$app_config;
	}
	private static function get_pages_key_name(){
		if(key_exists("pages_key_name", self::$config)){
			self::$pages_key_name = self::$config["pages_key_name"];
		}
	}
	private static function get_libs_path(){
		self::$libs_path = ModelApi::getDefaultLibsPath();
	}
	private static function get_pages_query(){
		$config = self::$config;
		$mysql_pages_query = NULL;
		if(key_exists("mysql_pages_query", $config)){
			$mysql_pages_query = $config["mysql_pages_query"];
		}
		return $mysql_pages_query;
	}
	private static function get_pages_libs($pages_query){
		$pages_libs = self::query_libs_from_bd($pages_query);
		if(key_exists("pages_set_up_name", self::$config)){
			$pages_libs = self::get_libs_from_files($pages_libs);
		}
		if($pages_libs > 0){
			$pages_libs = self::add_child_pages($pages_libs);
		}
		return $pages_libs;
	}
	private static function query_libs_from_bd($pages_query){
		$pages_key_name = self::$pages_key_name;
		$pages_libs = [];
		if($pages_query !== NULL){
			$STH = BD::query($pages_query);
			while($row = $STH->fetch()) {
				if(key_exists($pages_key_name, $row)){
					$pages_libs[$row[$pages_key_name]]=$row;
				}
			}
		}
		return $pages_libs;
	}
	private static function get_libs_from_files($pages_libs){
		$pages_set_up_path = "../".self::$libs_path.self::$config["pages_set_up_name"].".php";

		if(is_file($pages_set_up_path)){
			$pages_set_up = require $pages_set_up_path;
			foreach($pages_set_up as $page_name => $lib_name){
				$page_lib = self::get_lib_from_file($lib_name);
				if(key_exists($page_name, $pages_libs) && sizeof($page_lib)>0){
					foreach($page_lib as $arg_key => $arg_value){
						$pages_libs[$page_name][$arg_key] = $arg_value;
					}
				}else{
					$pages_libs[$page_name] = $page_lib;
				}
			}
		}
		return $pages_libs;
	}
	private static function get_lib_from_file($lib_name){
		$page_lib = [];
		$lib_path = "../".self::$libs_path.$lib_name.".php";
		if(is_file($lib_path)){
			$page_lib = require $lib_path;
		}
		return $page_lib;
	}
	private static function add_child_pages($pages_libs){
		if(sizeof($pages_libs) > 0){
			$child_pages_libs = self::get_child_pages_libs($pages_libs);
			if(sizeof($child_pages_libs) > 0){
				foreach($child_pages_libs as $page_name => $page_lib){
					if(!key_exists($page_name, $pages_libs)){
						$pages_libs[$page_name] = $page_lib;
					}
				}
			}
		}
		return $pages_libs;
	}
	private  static function get_child_pages_libs($pages_libs){
		$child_pages_libs = [];
		foreach($pages_libs as $page_name => $page_lib){
			if(key_exists(self::$child_pages_query_key, $page_lib)){
				$child_pages_query = $page_lib[self::$child_pages_query_key];
				$child_pages_libs = self::query_libs_from_bd($child_pages_query);
			}
			if(key_exists(self::$child_pages_lib_key, $page_lib)){
				$child_pages_file_libs = self::get_lib_from_file($page_lib[self::$child_pages_lib_key]);
				foreach($child_pages_file_libs as $child_page_name => $child_page_lib){
					if(!key_exists($child_page_name, $child_pages_libs)){
						$child_pages_libs[$child_page_name] = $child_page_lib;
					}
				}
			}
		}
		return $child_pages_libs;
	}*/





    /*protected static $_instance;

    private function __construct(){
        $this->setPageRequest();
        $this->setPagesLibName();
        $this->setPageLibsNames();
    }
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    private $page_request;
    private $page_libs_names;
    private $pages_lib_name;

    private function setPageLibsNames(){
        if(key_exists('page_libs_names', Application::$app_config)){
            $this->page_libs_names = Application::$app_config['page_libs_names'];
        }else{
            $this->page_libs_names = [];
        }
    }
    private function setPagesLibName()
    {
        $this->pages_lib_name = ModelApi::getPagesLibName();
    }

    private function setPageRequest(){
        $this->page_request = ModelApi::getRequestPages();
    }
    public static function load(){
        $model = self::getInstance();
        $where = " WHERE ";
        foreach($model->page_request as $request_n => $page_name){
            if($request_n > 0){
                $where .= " OR ";
            }
            $where .= "name = '".$page_name."'";
        }
        $join = "";
        if(sizeof($model->page_libs_names)>0){
            foreach($model->page_libs_names as $n => $names){
                $join .= ' JOIN '.$names.' ON ('.$model->pages_lib_name.'.id = '.$names.'.page_id) ';
            }
        }
        $query = 'SELECT * from '.$model->pages_lib_name.$join.$where;
        $STH = BD::query($query);
        $pages_libs = [];
        while($row = $STH->fetch()) {
            if(key_exists('name', $row)){
                $pages_libs[$row['name']]=$row;
            }
        }
//        var_dump($pages_libs);
        ModelApi::setPagesSettings($pages_libs);
        ModelApi::setPagesLibs($pages_libs);
//        var_dump(ModelApi::getPagesLibName());

//        $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);


    }*/
}