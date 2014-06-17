<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   打包台登陆(dbtdl)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/17
 * 更新履历：
 *********************************/
class cc_models_dbtdl extends Common_Model_Base
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
     * 获取打包台信息
     *
     * @param $ckbh 仓库编号
     * @return array
     */
	public function get_dbtxx($ckbh)
	{
		//查询语句
		$sql="SELECT DBTBH,DBTMCH FROM H01DB012442 WHERE QYBH = :QYBH AND CKBH = :CKBH AND ZHUANGTAI = '1' ORDER BY DBTBH ASC";
		//绑定条件
		$bind["QYBH"] = $_SESSION['auth']->qybh;
		$bind["CKBH"] = $ckbh;
		return $this->_db->fetchAll($sql, $bind);
	}
	
	/**
     * 获取打包台状态
     *
     * @param $filter
     * @return string
     */
	public function get_status($filter)
	{
		//查询语句
		$sql="SELECT DLZHT FROM H01DB012442 WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH";
		//绑定条件
		$bind["QYBH"] = $_SESSION['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh'];
		$bind['DBTBH'] = $filter['dbt'];
		return $this->_db->fetchOne($sql, $bind);
	}
	
	/**
     * 保存打包台登陆信息
     *
     * @param $filter
     * @return array
     */
	public function saveDbt($filter)
	{
		$data['QYBH'] = $_SESSION['auth']->qybh;  //区域编号
		$data['CKBH'] = $filter['ckbh'];  //仓库编号
		$data['DBTBH'] = $filter['dbt'];  //打包台编号
		$data ['DLZH'] = $_SESSION ['auth']->userId; //登陆者
		$data ['DLSHJ'] = new Zend_Db_Expr ( "SYSDATE" ); //登陆日期
        $this->_db->insert ( "H01DB012447", $data );
        return true;
	}
	
	/**
     * 更改打包台信息
     *
     * @param $filter
     * @return 
     */
	public function updateDbtxx($filter)
	{
		//更新语句
        $sql="UPDATE H01DB012442 SET DLZHT = '1' WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['DBTBH'] = $filter['dbt'];
        $this->_db->query($sql, $bind);
        return true;
	}
	
	/**
     * 更新打包台登陆信息
     *
     * @param $filter
     * @return 
     */
	public function updateDbtdlxx($filter)
	{
		//更新语句
		$sql="UPDATE H01DB012447 SET DCHSHJ = SYSDATE WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH AND DLSHJ = "
		. "(SELECT MAX(DLSHJ) FROM H01DB012447 WHERE QYBH = :QYBH AND CKBH = :CKBH AND DBTBH = :DBTBH )";
		//绑定查询条件
		$bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind['CKBH'] = $filter['ckbh'];
        $bind['DBTBH'] = $filter['dbt'];
        $this->_db->query($sql, $bind);
        return true;
	}
}
?>