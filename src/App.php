<?php namespace Pshift;

/**
 * コントローラーの基本クラス
 * @since 2019年05月02日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group 基本
 * @access エンドユーザーだけ
 */
class App extends Request {
    
    /**
     * 新しいコントローラのインスタンスを作る時に、権限が設定されます。
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();  
        
        $this->init();
    }
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * 
     * @return void
     */
    public function init() {
        try {
            $controllerInstance = new $this->namespace;
            if (!method_exists($controllerInstance, $this->actionName)) {
                $this->actionName = $this->setting['APP_ACTION'];
            }
            $methodName = $this->actionName;   

            $refMethod = new \ReflectionMethod($controllerInstance,  $methodName); 
            $params = $refMethod->getParameters(); 

            $re_args = array();     
            foreach($params as $param) {     
                $obj = $param->getClass();
                if ($obj) {                 
                    $objName = $obj->name;
                    $re_args[] = new $objName;
                } else {
                    $re_args[] = $param->getDefaultValue();
                }
            } 
            call_user_func_array(array($controllerInstance, $methodName), $re_args);                        
        } catch (Exception $ex) {
            throw $ex->getMessage();
        }
    }
}
if (!function_exists('dd')) {
    function dd($rs) {
        echo '<pre>';
        var_dump($rs);
        echo '</pre>';
    }
}