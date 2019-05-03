<?php namespace Pshift;

/**
 * @since 2019年05月03日
 * @author 杜低選 <tuyedd.itz@gmail.com>
 * 
 * @group システム
 * @access 管理者
 */
class Db {
    
    protected $conn;
    /**
     * 新しいコントローラのインスタンスを作る時に、権限が設定されます。
     * 
     * @return void
     */
    public function __construct() {
        $this->conn = $this->connect();
    }
    /**
     * @since 2019年05月03日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function connect() {
        try {
            return new PDO('mysql:dbname=mysql;host=localhost', 
                $this->user, 
                $this->password, 
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
                )
            );
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    /**
     * @since 2019年05月03日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function fetch($query, $fetch_style = PDO::FETCH_OBJ, $debug = FALSE) {
        if ($debug) {
            echo $query, "\n";
        }
        try {
            if (!$debug) {
                $sth = $this->conn->prepare($query);
                $sth->execute();

                return $sth->fetchAll($fetch_style);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage(), "\n", $query, "\n";
        }
        return false;
    }

    /**
     * @since 2019年05月03日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function insert($table, $arg = array(), $debug = FALSE) {
        if ($arg == null || $arg == '' || empty($arg)) {
            return false;
        }
        if(is_array($arg))
        {
            $query = "INSERT INTO {$table} (" . implode(', ',  array_keys($arg)) . ") VALUES ('" . implode("', '",  array_keys($arg)) . "')";
        } else {
            $query = "INSERT INTO {$table} VALUES ({$arg})";
        }

        if ($debug) {
            echo $query, "\n";
        }
        try {
            if (!$debug) {
                return $this->conn->query($query);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage(), "\n", $query, "\n";
        }
        return false;
    }
    /**
     * @since 2019年05月03日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function update($table, $arg = array(), $wh = array(), $debug = FALSE) {
        if ($arg == null || $arg == '' || empty($arg)) {
            return false;
        }
        $query = "UPDATE {$table} SET ";
        if (is_array($arg)) {
            $rs = [];
            foreach ($arg AS $key=>$val) {
                $rs[] = "{$key}='{$val}'";
            }
            $query .= implode(', ', $rs);
        } else {
            $query .= $arg;
        }
        if (is_array($wh)) {
            $rs = [];
            foreach ($wh AS $key=>$val) {
                $rs[] = "{$key}='{$val}'";
            }
            $query .= !empty($rs) ? (" WHERE " . implode(' AND ', $rs)) : "";
        } else {
            $query .= $wh ? " WHERE {$wh}" : "";
        }
        if ($debug) {
            echo $query, "; \n";
        }
        try {
            if (!$debug) {
                return $this->conn->query($query);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage(), "\n", $query, "\n";
        }
        return false;
    }

    /**
     * @since 2019年05月03日
     * @author 杜低選 <tuyedd.itz@gmail.com>
     * 
     * @group 基本
     * @category システム全体
     *
     * @return void
     */
    public function delete($table, $arg = '', $debug = FALSE) {
        $query = "DELETE FROM {$table} ";
        if (is_array($arg)) {
            $rs = [];
            foreach ($arg AS $key=>$val) {
                $rs[] = "{$key}='{$val}'";
            }
            $query .= !empty($rs) ? (" WHERE " . implode(' AND ', $rs)) : "";
        } else {
            $query .= $arg ? " WHERE {$arg}" : "";
        }
        if ($debug) {
            echo $query, "; \n";
        }
        try {
            if (!$debug) {
                return $this->conn->query($query);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage(), "\n", $query, "\n";
        }
        return false;
    }
    public static function query($q) {
        global $app;
        return $app->conn->query($q);
    }
}