<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 30.03.2016
 * Time: 20:34
 *
 * Определяет адрес нахождения файла конфигурации приложения и папки с плагинами.
 * Определяет метод регистрации и запуска плагина
 * Определяет метод запуска приложения, который:
 *  - ловит ошибки;
 *  - загружает конфигурационный файл;
 *  - запускает контроллер приложения, который загружает страницу;
 */

namespace app;
use app;


class Application
{
    private static $config_path = '../application/config.php';
    private static $plugins_path = '../plugins/';
    public static $app_config;
    private static $registered_plugins;
    public static function start(){
        set_error_handler("self::show_errors");
        try{
            self::loadAppConfig();
            self::registerPlugins();
            $controller_app = new ControllerApp();
            $controller_app->setUpPage();

//            echo microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"].'</br>';
//            echo memory_get_peak_usage();
        }catch(\Exception $e){
            self::show_errors($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }
    private static function show_errors($errno, $errstr, $file, $line){
        echo '</br>Номер ошибки: '.$errno;
        echo '</br>Ошибка: '.$errstr;
        echo '</br>Файла: '.$file;
        echo '</br>Линии: '.$line;
    }
    private static function loadAppConfig(){
        if(is_file(self::$config_path)){
            self::$app_config = require_once self::$config_path;
        }else{
            throw new \Exception('Конфигурационный файл '. self::$config_path .' не найден');
        }
    }

    public static function loadPlugins($type){
        if(is_array(self::$registered_plugins)){
            foreach (self::$registered_plugins as $key => $value) {
                switch ($type) {
                    default:
                        $controller_path = self::$plugins_path . $value . '/' . $type . '.php';
                        if (is_file($controller_path)) {
                            require_once $controller_path;
                            call_user_func(array('\\plugins\\' . $value . '\\' . $type, 'load'));
                        }
                    break;
                }
            }
        }
    }

    private static function registerPlugins()
    {
        $registered_plugins_list = self::$plugins_path . 'registered_plugins.php';
        if(is_file($registered_plugins_list)) {
            self::$registered_plugins = require_once $registered_plugins_list;
        }
    }
}