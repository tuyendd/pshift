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
    
    // リクエストのURLを含む
    private $url = null;

    // リクエストの種類は「GET | POST」だけを含む
    private $type = null;

    //リクエストのセグメント
    private $segments = null;

    // リクエストのコントローラー名
    private $controller = null;
    
    // コントローラーのメソッド
    private $method = null;
    
    // パラメーター配列
    private $parameters = null;

    /**
     * 新しいコントローラのインスタンスを作る時に、モデル名、コントローラ名、アクションが設定されます。
     * 
     * @return void
     */
    public function __construct() {
        $this->url = Sanitizer::sanitizeURL(rtrim(substr($_SERVER["REQUEST_URI"], 1), '/'));
        $this->type = $_SERVER['REQUEST_METHOD'];
        $this->parameters = (object) array();

        self::parseURL();

        $postParameters = filter_input_array(INPUT_POST);
        $cookieParameters = filter_input_array(INPUT_COOKIE);

        if(!is_null($postParameters))
        {
            foreach($postParameters as $parameter => $value)
            {
                if(!isset($this->parameters->postParameters))
                {
                    $this->parameters->postParameters = (object) array();
                }

                $this->parameters->postParameters->{$parameter} = $value;
            }
        }

        if(!is_null($cookieParameters))
        {
            foreach($cookieParameters as $parameter => $value)
            {
                if(!isset($this->parameters->cookieParameters))
                {
                    $this->parameters->cookieParameters = (object) array();
                }

                $this->parameters->cookieParameters->{$parameter} = $value;
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
     * @return リクエストのオブジェクトを返る、その為、それによって使えますよ。
     */
    public function getRequest()
    {
        return new self;
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return ヌル
     */
    private function parseURL()
    {
        $url = $this->url;
        $this->segments = explode('/', $url);

        $this->parameters->getParameters = array_values(array_diff(array_slice($this->segments, 2), array('')));
        $this->parameters->controller = isset($this->segments[0]) && !empty($this->segments[0]) ? $this->segments[0] : 'index';
        $this->parameters->method = isset($this->segments[1]) && !empty($this->segments[1]) ? $this->segments[1] : 'index';
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return URLを返る
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return リクエストの種類を返る
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return 各パラメーター配列を返る。
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return リクエスト名を返る
     */
    public function getController()
    {
        return $this->parameters->controller;
    }

    /**
     * @since 2019年05月01日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return リクエストのメソッド名を返る。
     */
    public function getMethod()
    {
        return $this->parameters->method;
    }
}
