<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 30.03.2016
 * Time: 17:05
 *
 * Определяет карту имён (список адресов папок) для загрузки классов приложения, интерфейса для плагинов и плагинов.
 * Определяет метод загрузки всех классов составляющих приложение, интерфейс для плагинов и плагины.
 *
 */

namespace app;

class Loader
{
    public static $namespaces_map = [
    'app' => '../application/',
    'pluginsapi' => '../application/plugins_api/',
    'plugins' => '../plugins/'
    ];
    public function loadClass($namesPath){
        $namesMap = self::$namespaces_map;

        $names = explode('\\', $namesPath);
        $main_name = array_shift($names);

        if(is_array($namesMap) && isset($namesMap[$main_name])){
            $main_folder = $namesMap[$main_name];
            $file_path = $main_folder.implode("/", $names).'.php';
            if(is_file($file_path)){
                require_once $file_path;
            }else{
                echo "ошибка в подключении $main_name по пути $file_path";
            }
        }else{
            echo "ошибка в подключении $main_name." . (is_array($namesMap))?"Нет карты имён":"";
        }
    }
}