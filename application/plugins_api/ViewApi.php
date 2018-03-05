<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 11.04.2016
 * Time: 14:58
 */

namespace pluginsapi;


class ViewApi
{
    private static $page_model = null;
    private static $page_templates = null;
    private static $page_blocks = null;
    private static $page_layout = null;
    private static $templates_path = null;
    private static $blocks_path = null;

    public static function setTemplatesPath($templates_path)
    {
        self::$templates_path = $templates_path;
    }

    public static function getTemplatesPath()
    {
        return self::$templates_path;
    }
    public static function setPageModel($page_model){
        self::$page_model = $page_model;
    }
    public static function getPageModel(){
        return self::$page_model;
    }

    public static function setPageBlocks($page_blocks){
        self::$page_blocks = $page_blocks;
    }
    public static function getPageBlocks(){
        return self::$page_blocks;
    }

    public static function setPageTemplates($page_templates){
        self::$page_templates = $page_templates;
    }
    public static function getPluginsTemplates(){
        return self::$page_templates;
    }

    public static function setPageLayout($page_layout){
        self::$page_layout = $page_layout;
    }
    public static function getPluginsLayout(){
        return self::$page_layout;
    }

    public static function setBlocksPath($defaultBlocksPath)
    {
        self::$blocks_path = $defaultBlocksPath;
    }

    public static function getPlaginBlocksPath()
    {
        return self::$blocks_path;
    }
}