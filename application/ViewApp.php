<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 30.03.2016
 * Time: 14:55
 */

namespace app;
use pluginsapi\ViewApi;



class ViewApp
{
    function __construct($page_model){
        ViewApi::setPageModel($page_model);
        $this->page_model = $page_model;
        $libs_router = new LibsRouter('view');
        ViewApi::setTemplatesPath($libs_router->getDefaultTempsKey());
        ViewApi::setBlocksPath($libs_router->getDefaultBlocksKey());
        Application::loadPlugins('View');

        $blocks_names = $this->getTempNames($libs_router->getDefaultBlocksKey());
        $this->blocks_paths = $libs_router->getTempPaths($blocks_names);

        $templates_names = $this->getTempNames($libs_router->getDefaultTempsKey());

        $this->templates_paths = $libs_router->getTempPaths($templates_names);

        if(key_exists('default_template', $this->page_model['page'])){
            $this->layout_path = $libs_router->getTempPathOf($page_model['page']['default_layout']);
        }else{
            $this->layout_path = $libs_router->getTempPathOf('default_layout');
        }
    }

    private function getTempNames($temps_lib_path){
        if(!is_array($this->page_model['page'])){
            $this->page_model['page'] = [];
        }
        if(key_exists($temps_lib_path, $this->page_model['page'])){
            return $this->page_model['page'][$temps_lib_path];
        }else{
            return [];
        }
    }
    private $templates_paths = [];
    private $blocks_paths = [];
    private $layout_path;

    private $page_model = [];
    private $page_templates = [];
    private $page_blocks = [];

//RENDER PAGE
    public function render(){
        $this->page_blocks = $this->getTemplates($this->blocks_paths);
        $plugin_blocks = ViewApi::getPageBlocks();
        if(is_array($plugin_blocks) && sizeof($plugin_blocks)>0){
            foreach($plugin_blocks as $block_name => $block_template){
                $this->page_blocks[$block_name] = $block_template;
            }
        }

        $this->page_templates = $this->getTemplates($this->templates_paths);

        $plugin_templates = ViewApi::getPluginsTemplates();
        if(is_array($plugin_templates) && sizeof($plugin_templates)>0){
            foreach($plugin_templates as $template_name => $plugin_template){
                $this->page_templates[$template_name] = $plugin_template;
            }
        }
        $plugin_layout = ViewApi::getPluginsLayout();
        if($plugin_layout != null){
            echo $plugin_layout;
        }else{
            if(is_file($this->layout_path)){
                require $this->layout_path;
            }else{
                throw new \Exception('Макет '. $this->layout_path .' не найден');
            }
        }
    }
    private function getTemplates($temp_paths){
        $templates = [];
        if(is_array($temp_paths) && sizeof($temp_paths)>0){
            ob_start();
            foreach($temp_paths as $temp_name => $temp_path){
                if(is_file($temp_path)){
                    require $temp_path;
                    $templates[$temp_name] = ob_get_contents();
                }
	            ob_clean();
            }
            ob_end_clean();
        }
        return $templates;
    }

//VIEW API
    public function getBlock($block_name){
        return $this->getFromLib($this->page_blocks, $block_name);
    }

    public function getTemplate($template_name){
        return $this->getFromLib($this->page_templates, $template_name);
    }
    private function getFromLib($array, $item_key){
        if(is_int($item_key)){
            return array_pop(array_slice($array, $item_key, 1));
        }elseif(key_exists($item_key, $array)){
            return $array[$item_key];
        }else{
            return '';
        }
    }

    public function getLibFromModel($lib_path){
        $lib = [];
        if(is_array($lib_path)){
            $search_in = $this->page_model;
            foreach($lib_path as $key_n => $key_val){
                if(key_exists($key_val, $search_in)){
                    $search_in = $search_in[$key_val];
                }else{
                    $search_in = [];
                    continue;
                }
            }
            if(!is_array($search_in)){
                $search_in = [array_pop($lib_path)=>$search_in];
            }
            $lib = $search_in;
        }else{
            if(key_exists($lib_path, $this->page_model)){
                $lib = $this->page_model[$lib_path];
            }
            if(!is_array($lib)){
                $lib = [$lib_path => $lib];
            }
        }
        return $lib;
    }

    public function getValFromModel($lib_path){
        $lib = '';
        if(is_array($lib_path)){
            $search_in = $this->page_model;
            foreach($lib_path as $key_n => $key_val){
                if(key_exists($key_val, $search_in)){
                    $search_in = $search_in[$key_val];
                }else{
                    $search_in = [];
                    continue;
                }
            }
            $lib = $search_in;
        }else{
            if(key_exists($lib_path, $this->page_model)){
                $lib = $this->page_model[$lib_path];
            }
        }
        if(is_array($lib)){
            $lib = '';
        }
        return $lib;
    }
}