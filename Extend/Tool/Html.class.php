<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <houdunwangxj@gmail.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * 生成静态文件处理类
 * @package     tools_class
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
final class html{

    public $error; //错误信息
    static $obj = null; //生成静态对象

    public function __construct() {

    }
    /**
     * 生成静态页面
     * <code>
     * array(控制器名，方法名，表态数据，保存表态文件路径）
     * array(news,show,1,'h/b/Hd.html');表示生成news控制器中的show方法生成ID为1的文章
     * </code>
     * @param $control
     * @param $method
     * @param $data
     * @return bool
     */
    static public function create($control, $method, $data) {
        //创建控制器对象
        if (is_null(self::$obj)) {
            $obj = control($control);
            if (!method_exists($obj, $method)) {
                error("方法{$method}不存在，请查看HD手册", false);
            }
        }
        foreach ($data as $d) {
            //************创建GET数据****************
            $_GET = array_merge($_GET, $d);
            $htmlPath = dirname($d['html_file']); //生成静态目录
            if (!dir_create($htmlPath)) {//创建生成静态的目录
                throw_exception(L("html_create_error2"));
                return false;
            }
            ob_start();
            $obj->$method(); //执行控制器方法
            $content = ob_get_clean();
            file_put_contents($d['html_file'], $content);
        }
        return true;
    }

    /**
     * 删除表态文件
     * @param void $name 目录名或者HTML文件
     * @return boolean
     */
    static public function del($name) {
        if (is_array($name)) {
            foreach ($name as $v) {
                if (is_file($v)) {
                    unlink($v);
                    continue;
                }
                Dir::del($v);
            }
        } else {
            if (is_file($name)) {
                unlink($name);
            } else {
                Dir::del($name);
            }
        }
        return true;
    }

}

?>