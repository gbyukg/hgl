<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库区登陆(kqdl)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/18
 * 更新履历：
 *********************************/
class cc_models_kqdl extends Common_Model_Base
{
    /**
     * 获取仓库信息
     *
     * @param unknown_type $filter
     * @return unknown
     */
    public function get_ckxx()
    {
        //查询语句
        $sql="SELECT CKBH,CKMCH FROM H01DB012401 WHERE QYBH = :QYBH AND CKZHT = '1'";
        //绑定条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        return $this->_db->fetchAll($sql, $bind);
    }
    
/**
     * 获取库区状态
     *
     * @param $filter
     * @return string
     */
    public function get_status($filter)
    {
        //查询语句
        $sql="SELECT DLZHT FROM H01DB012402 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH";
        //绑定条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['KQBH'] = $filter['kqbh'];
        return $this->_db->fetchOne($sql, $bind);
    }
    
/**
     * 更新库区登陆信息
     *
     * @param $filter
     * @return 
     */
    public function updateKqdlxx($filter)
    {
        //更新语句
        $sql="UPDATE H01DB012449 SET DCHSHJ = SYSDATE WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND DLSHJ = "
        . "(SELECT MAX(DLSHJ) FROM H01DB012449 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH )";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['KQBH'] = $filter['kqbh'];
        $this->_db->query($sql, $bind);
        return true;
    }
    
    /**
     * 保存库区登陆信息
     *
     * @param $filter
     * @return array
     */
    public function saveKq($filter)
    {
        $data['QYBH'] = $_SESSION['auth']->qybh;  //区域编号
        $data['CKBH'] = $filter['ckbh'];  //仓库编号
        $data['KQBH'] = $filter['kqbh'];  //库区编号
        $data ['DLZH'] = $_SESSION ['auth']->userId; //登陆者
        $data ['DLSHJ'] = new Zend_Db_Expr ( "SYSDATE" ); //登陆日期
        $this->_db->insert ( "H01DB012449", $data );
        return true;
    }
    
    /**
     * 更改库区信息
     *
     * @param $filter
     * @return 
     */
    public function updateKqxx($filter)
    {
        //更新语句
        $sql="UPDATE H01DB012402 SET DLZHT = '1' WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['KQBH'] = $filter['kqbh'];
        $this->_db->query($sql, $bind);
        return true;
    }
}
?>