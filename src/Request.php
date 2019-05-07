<?php namespace Pshift;

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
    public $basePath;
    
    /**
     * @var string
     */
    public $namespace;
    
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
     * @var array
     */
    public $setting;
    
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
        $this->params   = $_REQUEST;
        
        if ('POST' == $this->method) {
            foreach($_POST as $key => $val) {
                $this->params[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }     
    }
    public function baseUrl($param) {
        $params = explode('/', $param);        
        if (strpos($params[count($params) - 1], '.') === false) {
            $param = str_replace(array('.html.html', '.htm.html', '.html.htm', '.htm.htm'), '.html', "{$param}.html");
        }
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/' . $param;
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
                $classLoaders[strtolower($elements[1])] = [$elements[1], $elements[3], $key];
            }
        }
        $requestURI = explode('?', $_SERVER['REQUEST_URI']);
        $requestURI = explode('/', $requestURI[0]); 
        
        unset($requestURI[0]);
        $key = strtolower($requestURI[1]);
        if (isset($classLoaders[$key])) {
            list($this->moduleName, $controllerName, $this->namespace) = $classLoaders[$key]; 
            if (strcasecmp(str_replace('Controller', '', $controllerName), $requestURI[2]) == 0) {
                $this->controllerName   = $controllerName;
                $this->actionName       = $requestURI[3]; 
            } 
        } else {
            foreach ($classLoaders AS $key=>$el) {
                list($this->moduleName, $controllerName, $this->namespace) = $el;
                if (strcasecmp(str_replace('Controller', '', $controllerName), $requestURI[1]) == 0) {
                    $this->controllerName   = $controllerName;
                    $this->actionName       = $requestURI[2]; 
                    break;
                }
            }
        }
        $this->setting = Request::env();
        if ($this->controllerName == null || $this->controllerName == '') {            
            $this->controllerName = isset($this->setting['APP_CONTROLLER']) ? $this->setting['APP_CONTROLLER'] : 'IndexController';
        }
        $this->controllerName = str_replace(' ', '', 
            preg_replace('/\b(\w)/e', 'strtoupper("$1")', 
                    preg_replace("/[^A-Za-z0-9?\s]/"," ", 
                        str_replace(array('.html', '.htm'), '', $this->controllerName)
                    )
                )
            );
        
        if ($this->actionName == null || $this->actionName == '') {            
            $this->actionName = isset($this->setting['APP_ACTION']) ? $this->setting['APP_ACTION'] : 'index';
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
    public static function env($key='') {
        if (!file_exists(ROOT. '/.env')) {
            return false;
        }        
        $Loader = (new \josegonzalez\Dotenv\Loader(ROOT. DIRECTORY_SEPARATOR. '.env'))
            ->parse()
            ->toArray();
        if ($key == null || $key == '') {
            return $Loader;
        }
        if (is_array($key)) {
            $rs = array();
            foreach ($key AS $el) {
                $rs[$el] = $Loader[$el];
            }
            return $rs;
        }
        return $Loader[$key];
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
    
    /**
     * Send an HTTP request to a the $url and check the header posted back.
     *
     * @param $url String url to which we must send the request.
     * @param $failCodeList Int array list of code for which the page is considered invalid.
     *
     * @return Boolean
     */
    public static function isUrlExists($url, array $failCodeList = array(404)){
        $exists = false;

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_USERAGENT, true);

        $headers = curl_exec($handle);
        curl_close($handle);

        if (empty($failCodeList) or !is_array($failCodeList)) {
            $failCodeList = array(404); 
        }

        if (!empty($headers)) {
            $exists = true;
            $headers = explode(PHP_EOL, $headers);
            foreach($failCodeList as $code){
                if (is_numeric($code) and strpos($headers[0], strval($code)) !== false){
                    $exists = false;
                    break;  
                }
            }
        }
        return $exists;
    }
}