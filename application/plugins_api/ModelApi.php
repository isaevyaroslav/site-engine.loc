<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 14:58
 */

namespace pluginsapi;



class ModelApi
{
    private static $pages_model;
    private static $pages_lib_name;
    private static $request;
    private static $pages_settings = null;
    private static $pages_libs = null;
    private static $default_lib = null;
    private static $add_lib = null;
    private static $default_lib_name;
    private static $default_libs_path;

    public static function setDefaultLibsPath($default_libs_path){
        self::$default_libs_path = $default_libs_path;
    }
    public static function getDefaultLibsPath(){
        return self::$default_libs_path;
    }
    public static function setPagesLibName($pages_lib_name)
    {
        self::$pages_lib_name = $pages_lib_name;
    }

    public static function getPagesLibName()
    {
        return self::$pages_lib_name;
    }
    public static function setRequestArgs($request_args){
        self::$request = $request_args;
    }

    public static function setAddLib($add_lib)
    {
	    if(is_array($add_lib)){
		    foreach($add_lib as $key => $value){
			    self::$add_lib[$key] = $value;
		    }
	    }
    }

    public static function setDefaultLib($default_lib)
    {
        self::$default_lib = $default_lib;
    }

    public static function setPagesModel($pages_model)
    {
        self::$pages_model = $pages_model;
    }

    public static function setPagesSettings($pages_settings)
    {
        foreach($pages_settings as $k => $v){
            self::$pages_settings[$k] = $v;
        }
    }
    public static function getPagesModel()
    {
        return self::$pages_model;
    }
    public static function getRequest(){
        return self::$request;
    }
    public static function getRequestPages(){
        return self::$request["page"];
    }
    public static function getPagesSettings(){
        return self::$pages_settings;
    }

    public static function getDefaultLib()
    {
        return self::$default_lib;
    }

    public static function getPageLib($page_name)
    {
        if(is_array(self::$pages_libs)){
            if(key_exists($page_name, self::$pages_libs)){
                return self::$pages_libs[$page_name];
            }
        }
        return null;
    }

    public static function getAddLib(){
        return self::$add_lib;
    }
    public static function addLib($lib_to_add){
        foreach($lib_to_add as $sub_lib_name => $sub_lib){
            self::$add_lib[$sub_lib_name] = $sub_lib;
        }
    }

    public static function setDefaultLibName($default_lib_name){
        self::$default_lib_name = $default_lib_name;
    }

    public static function getDefaultLibName()
    {
        return self::$default_lib_name;
    }
}