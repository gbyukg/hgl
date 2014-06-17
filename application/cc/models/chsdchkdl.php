<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   传送带出口登陆(chsdchkdl)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/18
 * 更新履历：
 *********************************/
class cc_models_chsdchkdl extends Common_Model_Base
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
     * 获取传送带出口信息
     *
     * @param $ckbh 仓库编号
     * @return array
     */
    public function get_chsdchkxx($ckbh)
    {
        //查询语句
        $sql="SELECT CHSDCHK FROM H01DB012443 WHERE QYBH = :QYBH AND CKBH = :CKBH AND ZHUANGTAI = '1' ORDER BY CKBH ASC";
        //绑定条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind["CKBH"] = $ckbh;
        return $this->_db->fetchAll($sql, $bind);
    }
    
    /**
     * 获取传送带出货口状态
     *
     * @param $filter
     * @return string
     */
    public function get_status($filter)
    {
        //查询语句
        $sql="SELECT DLZHT FROM H01DB012443 WHERE QYBH = :QYBH AND CKBH = :CKBH AND CHSDCHK = :CHSDCHK";
        //绑定条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['CHSDCHK'] = $filter['chsdchk'];
        return $this->_db->fetchOne($sql, $bind);
    }
    
    /**
     * 保存传送带出货口登陆信息
     *
     * @param $filter
     * @return array
     */
    public function saveChsdchk($filter)
    {
        $data['QYBH'] = $_SESSION['auth']->qybh;  //区域编号
        $data['CKBH'] = $filter['ckbh'];  //仓库编号
        $data['CHSDCHK'] = $filter['chsdchk'];  //传送带出货口编号
        $data ['YHID'] = $_SESSION ['auth']->userId; //登陆者
        $data ['DLSHJ'] = new Zend_Db_Expr ( "SYSDATE" ); //登陆日期
        $this->_db->insert ( "H01DB012432", $data );
        return true;
    }
    
    /**
     * 更改传送带出货口信息
     *
     * @param $filter
     * @return 
     */
    public function updateChsdchk($filter)
    {
        //更新语句
        $sql="UPDATE H01DB012443 SET DLZHT = '1' WHERE QYBH = :QYBH AND CKBH = :CKBH AND CHSDCHK = :CHSDCHK";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['CHSDCHK'] = $filter['chsdchk'];
        $this->_db->query($sql, $bind);
        return true;
    }
    
    /**
     * 更新传送带出货口登陆信息
     *
     * @param $filter
     * @return 
     */
    public function updateChsdchkdlxx($filter)
    {
        //更新语句
        $sql="UPDATE H01DB012432 SET DCHSHJ = SYSDATE WHERE QYBH = :QYBH AND CKBH = :CKBH AND CHSDCHK = :CHSDCHK AND DLSHJ = "
        . "(SELECT MAX(DLSHJ) FROM H01DB012432 WHERE QYBH = :QYBH AND CKBH = :CKBH AND CHSDCHK = :CHSDCHK )";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['CHSDCHK'] = $filter['chsdchk'];
        $this->_db->query($sql, $bind);
        return true;
    }
}
?>