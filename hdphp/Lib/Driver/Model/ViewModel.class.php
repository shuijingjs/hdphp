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
 * 视图模型处理类
 * @package     Model
 * @subpackage  Driver
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
defined("INNER_JOIN") or define("INNER_JOIN", "INNER JOIN");
defined("LEFT_JOIN") or define("LEFT_JOIN", "LEFT JOIN");
defined("RIGHT_JOIN") or define("RIGHT_JOIN", "RIGHT JOIN");

class ViewModel extends Model
{
    public $view = array();

    //本次需要关联的表
    private function check_join($table)
    {
        //验证表
        if (is_null($this->joinTable)) {
            return false;
        } else if (is_array($this->joinTable) && !empty($this->joinTable) && !in_array($table, $this->joinTable)) {
            return false;
        } else {
            return true;
        }

    }

    //验证关联定义
    private function checkViewSet($set)
    {
        if (empty($set['type']) || !in_array($set['type'], array(INNER_JOIN, LEFT_JOIN, RIGHT_JOIN))
        ) {
            error("关联定义规则[type]设置错误");
            return false;
        }
        if (empty($set['on'])) {
            error("关联定义规则[on]设置错误");
            return false;
        }
        return true;
    }

    //查询
    public function select($data = array())
    {
        //不存在关联定义或不关联时
        if (is_null($this->joinTable) || empty($this->view)) {
            $this->init();
            return call_user_func(array($this->db, __FUNCTION__), $data);
        }
        //条件
        $this->where = $data;
        //主表查询字段
        $field = $this->db->opt['field'];
        //字段
        if (empty($field)) {
            $field = "*";
        }
        //关联from 语句
        $from = " " . $this->tableFull . " ";
        //处理关联
        foreach ($this->view as $table => $set) {
            //表是否需要关联
            if (!$this->check_join($table)) continue;
            //验证关联定义
            if (!$this->checkViewSet($set)) continue;
            //加表前缀
            $_table = C("DB_PREFIX") . $table;
            $from .= $set['type'] . " " . $_table . " ";
            $from .= " ON " . $set['on'] . " ";
        }
        $sql = "SELECT " . $field . " FROM " . $from .
            $this->db->opt['where'] . $this->db->opt['group'] . $this->db->opt['having'] .
            $this->db->opt['order'] . $this->db->opt['limit'];
        $sql = $this->addTableFix($sql);
        $result = $this->query($sql);
        $this->init();
        return $result;
    }

    /**
     * 添加表前缀
     * @access public
     * @param string $sql
     * @return string   格式化后的SQL
     */
    private function addTableFix($sql)
    {
        return preg_replace("@(?<=[\s,=><])(\w+?\.[a-z]+?)@i", C("DB_PREFIX") . '\1', $sql);
    }
}


























