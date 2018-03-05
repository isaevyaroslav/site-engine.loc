<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 19:33
 */

namespace plugins\mysql_default;

use pluginsapi\ModelApi, \plugins\mysql_connect\Model as BD;

class Model{
    protected static $_instance;

    private $default_lib_name;

    private function __construct(){
        $this->setDefaultLibName();
    }
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    private function setDefaultLibName(){
        $this->default_lib_name = ModelApi::getDefaultLibName();
    }
    public static function load(){
        $model = self::getInstance();
        $query = 'SELECT * from '.$model->default_lib_name;
        $STH = BD::query($query);
        $default_libs = [];
        while($row = $STH->fetch()) {
            if($row['query'] == null){
                $inner_query = 'SELECT * from '.$row['lib_name'];
            }else{
                $inner_query = $row['query'];
            }
            $inner_STH = BD::query($inner_query);
            while($inner_row = $inner_STH->fetch()){
                $default_libs[$row['lib_name']][] = $inner_row;
            }
        }
        ModelApi::setDefaultLib($default_libs);
    }
}