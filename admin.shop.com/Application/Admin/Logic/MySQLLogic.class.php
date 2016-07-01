<?php
/**
 * Created by PhpStorm.
 * User: leiliang
 * Date: 2016/6/28
 * Time: 12:48
 */

namespace Admin\Logic;

class MySQLLogic implements DbMysql{
    public function connect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function disconnect()
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function free($result)
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 查询一条数据
     * @param string $sql
     * @param array $args
     * @return false|int
     */
    public function query($sql, array $args = array())
    {
        //获取所有实参
        $args = func_get_args();
        //分离出sql语句
        $sql = array_shift($args);
        //拼接sql语句
        $params = preg_split('/\?[NFT]/',$sql);
        //删除最后一个空元素
        array_pop($params);
        $sql = '';
        foreach($params as $key=>$value){
            $sql .= $value . $args[$key];
        }
        return M()->execute($sql);
    }

    /**
     * 插入一条数据
     * @param string $sql
     * @param array $args
     * @return bool|string
     */
    public function insert($sql, array $args = array())
    {

        $args = func_get_args();
        $sql = $args[0];
        $table_name = $args[1];
        $params = $args[2];
        $sql = str_replace('?T',$table_name,$sql);
        $tmp = [];
        foreach($params as $key => $value){
            $tmp[] = $key .'="'.$value.'"';
        }
        $sql = str_replace('?%',implode(',',$tmp),$sql);
        if(M()->execute($sql) !== false){
            return M()->getLastInsID();
        }else{
            return false;
        }

    }

    public function update($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAll($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    public function getAssoc($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 获取一条数据
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getRow($sql, array $args = array())
    {

        //获取所有实参
        $args = func_get_args();
        //分离出sql语句
        $sql = array_shift($args);
        //拼接sql语句
        $params = preg_split('/\?[NFT]/',$sql);
        //删除最后一个空元素
        array_pop($params);
        $sql = '';
        foreach($params as $key=>$value){
            $sql .= $value . $args[$key];
        }
        $rows = M()->query($sql);
        return array_shift($rows);



    }

    public function getCol($sql, array $args = array())
    {
        echo __METHOD__;
        dump(func_get_args());
        echo '<hr />';
    }

    /**
     * 获取一行数据
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getOne($sql, array $args = array())
    {
        //获取所有实参
        $args = func_get_args();
        //分离出sql语句
        $sql = array_shift($args);
        //拼接sql语句
        $params = preg_split('/\?[NFT]/',$sql);
        array_pop($params);
        $sql = '';
        foreach($params as $key=>$value){
            $sql .= $value . $args[$key];
        }
        $rows = M()->query($sql);
        return array_shift($rows);
    }

}