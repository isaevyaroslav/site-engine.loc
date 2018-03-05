<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 14:57
 *
 * Определяет методы работы плагинов с приложением
 */

namespace pluginsapi;


class ControllerApi
{
    function __construct(){
        $this->setDefaultArgs();
    }

    private static $get_args;
    private static $post_args;
    private static $cookie_args;
    private static $files_args;
    private static $session_args;
    private static $session_name = null;
    private static $page_request = [];
    private static $args_to_model;

    public static function getCookieArgs()
    {
        return self::$cookie_args;
    }

    public static function getFilesArgs()
    {
        return self::$files_args;
    }

    public static function getGetArgs()
    {
        return self::$get_args;
    }

    public static function getPostArgs()
    {
        return self::$post_args;
    }

    public static function getSessionArgs()
    {
        return self::$session_args;
    }

    public static function setArgsToModel($args_to_model)
    {
        self::$args_to_model = $args_to_model;
    }

    public static function setCookieArgs($cookie_args)
    {
        self::$cookie_args = $cookie_args;
    }

    public static function setFilesArgs($files_args)
    {
        self::$files_args = $files_args;
    }

    public static function setGetArgs($get_args)
    {
        self::$get_args = $get_args;
    }

    public static function setRequest($page_request)
    {
        self::$page_request = $page_request;
    }

    public static function setPostArgs($post_args)
    {
        self::$post_args = $post_args;
    }

    public static function setSessionArgs($session_args)
    {
        self::$session_args = $session_args;
    }


    public static function setSessionName($session_name)
    {
        self::$session_name = $session_name;
    }

    public static function setDefaultArgs(){
        if(self::$session_name != null){
            ControllerApi::getSessionName();
        }
        session_start();
        ControllerApi::$files_args = $_FILES;
        ControllerApi::$get_args = $_GET;
        ControllerApi::$post_args = $_POST;
        ControllerApi::$cookie_args = $_COOKIE;
        ControllerApi::$session_args = $_SESSION;
    }
    public static function getSessionName(){
        return session_name(self::$session_name);
    }
    public static function getPageRequest(){
        if(is_array(self::$page_request) && sizeof(self::$page_request)>0){
            return self::$page_request;
        }else{
            return null;
        }
    }
    public static function getArgsToModel (){
        return self::$args_to_model;
    }
}