<?php

/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 19.04.2016
 * Time: 18:10
 *
 * Определяет настройки доступа к базе данных.
 * Создаёт подключение к базе данных.
 * Задаёт метод для отравки запроса к базе данных, который возвращает запрошенные данные.
 *
 */
namespace plugins\mysql_connect;

use app\Application;

class Model{
    private $default_sets = [
        'host' => 'localhost',
        'dbname' => 'passiflora',
        'user' => 'root',
        'pass' => ''
    ];
    private $DBH;
    protected static $_instance;

    public static function load(){
        self::getInstance();
    }
    private function setDbSets(){
        foreach($this->default_sets as $name => $val){
            if(key_exists($name, Application::$app_config)){
                $this->default_sets[$name] = Application::$app_config[$name];
            }
        }
    }
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private  function __construct() {
        $this->setDbSets();
        $opt = [
	        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
	        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
	        \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->DBH = new \PDO(
            "mysql:host=".$this->default_sets['host'].
            ";dbname=".$this->default_sets['dbname'].";charset=utf8",
            $this->default_sets['user'],
            $this->default_sets['pass'],
            $opt
        );
        $this->DBH->exec('SET NAMES utf8');
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    public static function query($sql){
	    $result = [];
	    if($sql !== null){
		    $model = self::getInstance();
		    $STH = $model->DBH->query($sql);
		    if($STH !== false){
			    $result = $STH->FETCHAll(\PDO::FETCH_ASSOC);
		    }
	    }
        return $result;
    }
}