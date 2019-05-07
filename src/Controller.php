<?php namespace Pshift;

use Pshift\Request;

/**
 * コントローラーの基本クラス
 * @since 2019年05月01日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group 基本
 * @access エンドユーザーだけ
 */
class Controller extends Request {
    public $themePath;
    public $js;
    /**
     * @var string
     */
    public $setting;
    /**
     * 新しいコントローラのインスタンスを作る時に、モデル名、コントローラ名、アクションが設定されます。
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
        $this->themePath = ROOT . DIRECTORY_SEPARATOR . "themes" . DIRECTORY_SEPARATOR . $this->setting['APP_THEME'];
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
    public function view($view = null, $params = array()) {   
        $jsFileName = $this->baseUrl("themes/itz/assets/js/pages/" . str_replace('controller', '', strtolower($this->controllerName)) . ".js");
        if ($this->isUrlExists($jsFileName)) {
            $this->js[] = $jsFileName;
        }
        $params['scripts'] = $this->js;
        $params['content'] = $this->fetch($view, $params);
        
        extract($params); 
        $path = $this->themePath . DIRECTORY_SEPARATOR . 'page.php';
        if (file_exists($path)) {
            include_once $path;
        }
    }
    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @param  数字列   $view   view名
     * @param  配列     $params   パラメーター配列
     * 
     * @return 空白
     */
    public function fetch($view, $params) {
        extract($params);
        
        $tpl  = implode(DIRECTORY_SEPARATOR, array(ROOT, 'modules', $this->moduleName, 'Views'));
        $tpl .= DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . ".php";
        
        if (file_exists($tpl)) {
            ob_start();
            include $tpl;
            return ob_get_clean();
        }
        return '';    
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
    public function api($params=array()) {
        if (!is_array($params)) {
            $params = array($params);
        }
        $data = array(
            'status' => true
        );
        $params = array_merge($data, $params);
        
        exit(json_encode($params)); 
    }
    public function addJS($filename) {       
        if ($this->isUrlExists($this->baseUrl("themes/itz/assets/js/pages/{$filename}"))) {
            $this->js[] = $this->baseUrl("themes/itz/assets/js/pages/{$filename}");
        }
    }
}
