<?php
/**
 * Created by PhpStorm.
 * User: iyaro
 * Date: 30.03.2016
 * Time: 14:54
 *
 * Принимет все переменные страницы:
 *  - запрос
 *  - файлы
 *  - переменные get
 *  - переменные post
 *  - переменные cookie
 *  - переменные session
 * Передаёт массив аргументов запроса в контроллер интерфейса плагинов.
 * Запускает контроллеры плагинов.
 * Загружает страницу:
 *  - Получает обработанные плагинами аргументы и добавляет в массив переменных страницы
 *  - Замускает модель приложения и получает из неё модель страницы.
 *  - Отправляет модель страницы на визуализацию
 *  - Запускает рендер страницы
 */

namespace app;
use pluginsapi\ControllerApi;
use pluginsapi\ModelApi;

class ControllerApp
{
    function __construct(){
        ControllerApi::setDefaultArgs();
        $this->request_args["page"] = $this->getPageRequest();
        $this->request_args["files"] = $_FILES;
        $this->request_args["get"] = $_GET;
        $this->request_args["post"] = $_POST;
        $this->request_args["cookie"] = $_COOKIE;
        $this->request_args["session"] = $_SESSION;
        ControllerApi::setRequest($this->request_args);
	    Application::loadPlugins('Controller');
    }
    private $request_args = [];
    public function setUpPage(){
        $this->pluginRequestArgs(ControllerApi::getArgsToModel());
        $request_args = $this->request_args;
        $app_model = new ModelApp($request_args);
        $page_model = $app_model->getPageModel();
        $app_view = new ViewApp($page_model);
        $app_view->render();
    }
    private function pluginRequestArgs($newArgs){
        if(is_array($newArgs)){
            foreach($newArgs as $arg_name => $arg_val){
                $this->request_args[$arg_name] = $arg_val;
            }
        }
    }
    public function getPageRequest(){
        $plugin_request = ControllerApi::getPageRequest();
        if($plugin_request == null){
            $url_path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
            $url_path = preg_replace("/([^\w_\/.-]+)/","",mb_strtolower($url_path));
            preg_match_all('/([\w_-]+)[\/.]/', $url_path, $page_request_args);
            $page_request_args = $page_request_args[1];
            array_unshift($page_request_args, '/');
        }else{
            $page_request_args = $plugin_request;
        }
        return $page_request_args;
    }

}