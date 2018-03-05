<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 19:33
 */

namespace plugins\mysql_pagelibs;

use app\Application;
use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model{
    protected static $_instance;

    private $libs_table_name;

    private function __construct(){
        $this->setLibsTableName();
        $this->setCurrentPage();
    }
    private $current_page_id;
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function setCurrentPage(){
        $page_settings = ModelApi::getPagesSettings();
        $current_page = array_pop(ModelApi::getRequestPages());
//	    if(is_array($page_settings)){
		    if(key_exists($current_page, $page_settings)){
			    if(is_array($page_settings[$current_page])){
				    if(key_exists('id', $page_settings[$current_page])){
					    $this->current_page_id = $page_settings[$current_page]['id'];
				    }
			    }
		    }else{
			    $this->current_page_id = null;
		    }
//	    }
    }
    private function setLibsTableName(){
        if(key_exists('libs_table_name', Application::$app_config)){
           $this->libs_table_name = Application::$app_config['libs_table_name'];
        }else{
            $this->libs_table_name = 'page_libs';
        }
        $this->default_lib_name = ModelApi::getDefaultLibName();
    }
    public static function load(){
        $model = self::getInstance();
        if($model->current_page_id != null) {
            $query = 'SELECT * from ' . $model->libs_table_name . ' WHERE page_id = '.$model->current_page_id;
            $STH = BD::query($query);
            $add_libs = [];
            while ($row = $STH->fetch()) {
                if ($row['query'] == null) {
                    $inner_query = 'SELECT * from ' . $row['lib_name'];
                } else {
                    $inner_query = $row['query'];
                }
                $inner_STH = BD::query($inner_query);
                while ($inner_row = $inner_STH->fetch()) {
                    $add_libs[$row['lib_name']][] = $inner_row;
                }
            }
            ModelApi::setAddLib($add_libs);
        }
    }
}