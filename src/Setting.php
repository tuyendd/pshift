<?php namespace Pshift;

/**
 * Description of Setting
 *
 * @author TuyenDD
 */
class Setting {
    public static function get($key='') {
        if(file_exists('./env.php')) {
            include ROOT. '/env.php';
        }
        if (is_array($key)) {
            
        } else {
            
        }
    }
}
