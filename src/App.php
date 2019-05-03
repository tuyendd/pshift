<?php namespace Pshift;

use Pshift\Request;
use Pshift\Db;

/**
 * コントローラーの基本クラス
 * @since 2019年05月02日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group 基本
 * @access エンドユーザーだけ
 */
class App extends Request {
    public $setting;
    public $request;
    public $db;
    
    /**
     * 新しいコントローラのインスタンスを作る時に、権限が設定されます。
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();        
    }
    public function init() {
        
    }
}
