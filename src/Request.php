<?php namespace Pshift;

use Pshift\Db;

/**
 * コントローラーの基本クラス
 * @since 2019年05月01日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group 基本
 * @access エンドユーザーだけ
 */
class Request {
    /**
     * @var string
     */
    public $method;

    /**
     * Query string parameters ($_GET).
     *
     */
    public $params;
    /**
     * Server and execution environment parameters ($_SERVER).
     *
     */
    public $server;
    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $basePath;
    
    /**
     * @var string
     */
    public $moduleName;
    /**
     * @var string
     */
    public $controllerName;
    /**
     * @var string
     */
    public $actionName;
    /**
     * 新しいコントローラのインスタンスを作る時に、モデル名、コントローラ名、アクションが設定されます。
     * 
     * @return void
     */
    public function __construct() {    
        $this->initialize();        
        $this->setURLMapper();
    }
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    private function initialize() {
        $this->method   = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->basePath = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
        $this->baseUrl  = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
        $this->params   = $_REQUEST;
        
        if ('POST' == $this->method) {
            foreach($_POST as $key => $val) {
                $this->params[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }     
    }
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    private function setURLMapper() {        
        $classLoaders = [];
        $data = require ROOT. '/vendor/composer/autoload_classmap.php' ;
        foreach ($data AS $key=>$path) {
            $elements = explode('\\', $key);
            if (trim(strtolower($elements[0])) == 'modules' && trim(strtolower($elements[2])) == 'controllers') {
                $classLoaders[strtolower($elements[1])] = [$elements[1], $elements[3]];
            }
        }
        $requestURI = explode('/', $_SERVER['REQUEST_URI']); 
        unset($requestURI[0]);
        $key = strtolower($requestURI[1]);
        if (isset($classLoaders[$key])) {
            list($this->moduleName, $controllerName) = $classLoaders[$key]; 
            if (strcasecmp(str_replace('Controller', '', $controllerName), $requestURI[2]) == 0) {
                $this->controllerName   = $controllerName;
                $this->actionName       = $requestURI[3]; 
            } 
        } else {
            foreach ($classLoaders AS $key=>$el) {
                list($this->moduleName, $controllerName) = $el;
                if (strcasecmp(str_replace('Controller', '', $controllerName), $requestURI[1]) == 0) {
                    $this->controllerName   = $controllerName;
                    $this->actionName       = $requestURI[2]; 
                    break;
                }
            }
        }
        $this->actionName = str_replace(' ', '', 
            preg_replace('/\b(\w)/e', 'strtoupper("$1")', 
                    preg_replace("/[^A-Za-z0-9?\s]/"," ", 
                        str_replace(array('.html', '.htm'), '', $this->actionName)
                    )
                )
            ); 
    }    
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function url($mod="") { 
        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
        $query = explode("&", $_SERVER['QUERY_STRING']);
        $queryStart = !$_SERVER['QUERY_STRING'] ? "?" : "&";
        
        // modify/delete data 
        foreach($query as $q) { 
            list($key, $value) = explode("=", $q); 
            if(array_key_exists($key, $mod)) { 
                if($mod[$key]) { 
                    $url = preg_replace("/{$key}={$value}/", "{$key}=" . $mod[$key], $url); 
                } else { 
                    $url = preg_replace('/&?' . "{$key}={$value}/", '', $url); 
                } 
            } 
        } 
        
        // add new data 
        if (is_array($mod)) {
            foreach($mod as $key => $value) { 
                if($value && !preg_match("/{$key}=/", $url)) { 
                    $url .= "{$queryStart}{$key}={$value}"; 
                } 
            } 
        }
        return $url; 
    } 
}
if (!function_exists('dd')) {
    function dd($rs) {
        echo '<pre>';
        var_dump($rs);
        echo '</pre>';
    }
}