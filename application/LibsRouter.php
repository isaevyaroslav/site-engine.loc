<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 15.04.2016
 * Time: 21:54
 */

namespace app;


class LibsRouter
{
    private $default_path_settings =[
        'model' =>[
            'libs_path' => 'libraries/',
            'pages_set_up_name' => 'pages_set_up',
            'default_lib_name' => 'default_lib'
        ],
        'view' => [
            'templates_path' => 'www/templates/',
            'blocks_key' => 'blocks',
            'template_key' => 'templates',
            'default_layout' => 'default_layout'
        ]
    ];
    function __construct($mode){
        $default_path_settings = [];
        foreach($this->default_path_settings[$mode] as $setting_name => $setting_val){
            if(key_exists($setting_name, Application::$app_config)){
                $default_path_settings[$setting_name] = Application::$app_config[$setting_name];
            }else{
                $default_path_settings[$setting_name] = $setting_val;
            }
        }
        $this->default_path_settings = $default_path_settings;
    }
    public function getPathSetting($setting_name){
        return $this->default_path_settings[$setting_name];
    }
    public function getLibPathOf($lib_name){
        if(!is_array($lib_name)){
            if(key_exists($lib_name, $this->default_path_settings)){
                $lib_name = $this->default_path_settings[$lib_name];
            }
            return '../' . $this->default_path_settings['libs_path'] . $lib_name . '.php';
        }else{
            return null;
        }

    }
    public function getDefaultLibName(){
        return $this->default_path_settings['default_lib_name'];
    }
    public function getTempPathOf($temp_name){
        if(key_exists($temp_name, $this->default_path_settings)){
            $temp_name = $this->default_path_settings[$temp_name];
        }
        return '../' . $this->default_path_settings['templates_path'] . $temp_name . '.php';
    }
    public function getDefaultTempsKey(){
        return $this->default_path_settings['template_key'];
    }
    public function getDefaultBlocksKey(){
        return $this->default_path_settings['blocks_key'];
    }
    public function getTempPaths($temp_names){
        $temp_paths = [];
        if(is_array($temp_names)){
            foreach($temp_names as $temp_num => $temp_name){
                $temp_path = $this->getTempPathOf($temp_name);
                if(is_file($temp_path)){
                    $temp_paths[$temp_name] = $temp_path;
                }else{
                    throw new \Exception('Шаблон страницы '. $temp_name .' не найден');
                }
            }
        }else{
            $temp_path = $this->getTempPathOf($temp_names);
            if(is_file($temp_path)){
                $temp_paths[$temp_names] = $temp_path;
            }else{
                throw new \Exception('Шаблон страницы '. $temp_names .' не найден');
            }
        }
        return $temp_paths;
    }
    public function getDefaultLibsPath(){
        if(key_exists('libs_path', $this->default_path_settings)){
            return $this->default_path_settings['libs_path'];
        }else{
            return $this->default_path_settings['templates_path'];
        }
    }
}