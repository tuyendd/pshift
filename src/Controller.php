<?php namespace Pshift;

/**
 * コントローラーの基本クラス
 * @since 2019年05月01日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group 基本
 * @access エンドユーザーだけ
 */
class Controller {
    
    public $title;
    public $active;
    
    public $moduleName;
    public $controllerName;
    public $actionName;
    
    /**
     * 新しいコントローラのインスタンスを作る時に、モデル名、コントローラ名、アクションが設定されます。
     * 
     * @return void
     */
    public function __construct() {
        $this->setBaseInfo();
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
    private function setBaseInfo() {
        $routeArray = app('request')->route()->getAction();
        list(, $this->moduleName, ) = explode('\\', $routeArray['uses']);
        
        $controllerAction = class_basename($routeArray['controller']);
        
        list($this->controllerName, $this->actionName) = explode('@', str_replace('Controller', '', $controllerAction));
    }
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @param  数字列   $view   view名
     * @param  配列     $data   パラメーター配列
     * 
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function view($view = null, $data = []) {        
        
        $mergeData = array_merge([
                'title'             => $this->title,
                'active'            => $this->active,
            
                'moduleName'        => $this->moduleName,
                'controllerName'    => $this->controllerName,
                'actionName'        => $this->actionName,
            ], 
            request()->all()
        );     
        $view = strtolower($this->controllerName) . ".{$view}";
        if (strcasecmp($view, 'tax.receipt')  == 0) {
            $layout = $view;
        } else {
            $layout = 'layout';
            $mergeData['content'] = view($view, $data, $mergeData)->render();
        }        
        return view($layout, $data, $mergeData);
    }
}
