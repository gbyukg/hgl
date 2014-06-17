<?php
/*********************************
 * 模块：仓储模块(cc)
 * 机能：待验区库位信息(dyqkwxx)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/06
 * 更新履历：

 *********************************/

class cc_models_dyqkwxx extends Common_Model_Base 
{
	/*
	 * 获取仓库状态
	 */
	function ckstatusCheck($ckbh)
	{
		//检索SQL
		$sql = "SELECT CKZHT FROM H01DB012401 WHERE QYBH = :QYBH AND CKBH = :CKBH";
		//绑定查询条件
		$bind["QYBH"] = $_SESSION['auth']->qybh;
		$bind["CKBH"] = $ckbh;
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 获取验证区状态
	 */
	function dyqbhChec($dyqbh)
	{
		//检索SQL
		$sql = "SELECT ZHUANGTAI FROM H01DB012435 WHERE QYBH = :QYBH AND DYQBH = :DYQBH";
		//绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind["DYQBH"] = $dyqbh;
        return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 验证待验区库位信息登陆 待验区库位编号是否存在
	 */
	function dyqkwbhCheck($filter)
	{
		//检索SQL
//		if($filter['actions'] == 'addnew')
//		{
			$sql = "SELECT COUNT(1) AS CON FROM H01DB012439 WHERE QYBH = :QYBH AND DYQKWBH = :DYQKWBH AND CKBH = :CKBH AND DYQBH = :DYQBH";
//		}
//		else if($filter['actions'] == 'updatedata')
//		{
//			$sql = "SELECT COUNT(1) AS CON FROM H01DB012439 WHERE QYBH = :QYBH AND DYQKWBH = :DYQKWBH AND CKBH = :CKBH AND DYQBH = :DYQBH"
//			. " AND ROWID <> (SELECT ROWID FROM H01DB012439 WHERE QYBH = :QYBH AND DYQKWBH = :YDYQKWBH AND CKBH = :YCKBH AND DYQBH = :YDYQBH)";
//			$bind["QYBH"] = $_SESSION['auth']->qybh;    //区域编号
//	        $bind["YDYQKWBH"] = str_pad($filter['ydyqkwbh'], 6);  //待验区库位编号
//	        $bind["YCKBH"] = str_pad($filter['yckbh'], 6);    //仓库编号
//	        $bind["YDYQBH"] = str_pad($filter['ydyqbh'], 6);  //待验区编号
//		}
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;    //区域编号
        $bind["DYQKWBH"] = str_pad($filter['dyqkwbh'], 6);  //待验区库位编号
        $bind["CKBH"] = str_pad($filter['ckbh'], 6);    //仓库编号
        $bind["DYQBH"] = str_pad($filter['dyqbh'], 6);  //待验区编号
        return $this->_db->fetchRow ( $sql, $bind );
	}
	
    /*
     * 画面必须输入项验证
     */
    public function inputCheck() {
    
        //必须输入项
        $arrInput = array("CKBH","DYQBH","DYQKWBH","DYQKWMCH");
        
        foreach($arrInput as $input){
            if ($_POST [$input] == "") {
                return false;
            }
        }
        return true;
    }
    
    /*
     * 保存待验区库位信息
     */
    public function save()
    {
    	$data['QYBH'] = $_SESSION['auth']->qybh;   //区域编号
    	$data['CKBH'] = $_POST['CKBH'];    //仓库编号
    	$data['DYQBH'] = $_POST['DYQBH'];  //待验区编号
    	$data['DYQKWBH'] = $_POST['DYQKWBH'];    //待验区库位编号
    	$data['DYQKWMCH'] = $_POST['DYQKWMCH'];    //待验区库位名称
    	$data['ZHUANGTAI'] = '1';    //状态
    	$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
        $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
        $data ['BGZH'] = $_SESSION ['auth']->userId; //变更者 
        $data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
    	//主表保存
        return $this->_db->insert ( "H01DB012439", $data );
    }
    
    /*
     * 获取待验区信息
     */
    public function getDyqListData($filter)
    {
    	$fields=array("","DYQBH","CKBH","DYQMCH","KQLX");
    	//检索SQL
    	$sql="SELECT DYQBH,CKBH,DYQMCH,KQLX FROM H01DB012435 WHERE QYBH = :QYBH AND CKBH = :CKBH AND ZHUANGTAI = '1'";
    	//绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind["CKBH"] = $filter['ckbh'];
        
        if($filter['searchParams']['SEARCHKEY']!=""){
            $sql .= " AND( DYQBH LIKE '%' || :SEARCHKEY || '%'".
                    "      OR  lower(DYQMCH) LIKE '%' || :SEARCHKEY || '%')";
            $bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_DYQ",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
//        $sql .=",DYQBH";
        $bind["CKBH"] = $filter['ckbh'];
        //翻页表格用SQL生成(总行数与单页记录)
        $pagedSql = Common_Tool::getPageSql ( $sql, $filter );
        
        //总行数
        $totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
        
        //当前页数据
        $recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
        
        //调用表格xml生成函数
        return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
    }
    
    /*
     * 获取待验区库位信息
     */
    public function getGridData($filter)
    {
    	//排序用字段名
        $fields = array ("", "ZHUANGTAI","CKBH","CKMCH","DYQBH","DYQMCH","DYQKWBH","DYQKWMCH","BGRQ","BGZHXM"); //状态，仓库编号，仓库名称，待验区编号，待验区名称，待验区库位编号，待验区库位名称，变更者，变更日期

        //检索SQL
        $sql = "SELECT DECODE(ZHUANGTAI,'0','冻结','1','可用','X','删除','未知') AS ZHUANGTAI,CKBH,CKMCH,DYQBH,DYQMCH,DYQKWBH,DYQKWMCH,BGRQ,BGZHXM" .
               " FROM H01VIEW012439 " .  
               " WHERE QYBH = :QYBH ";

        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        
        //仓库查询
        if($filter['searchParams']['CANGKU']!=""){
            $sql .= " AND( CKBH LIKE '%' || :CANGKU || '%'".
                    "      OR  lower(CKMCH) LIKE '%' || LOWER(:CANGKU) || '%')";
            $bind ['CANGKU'] = strtolower($filter ["searchParams"]['CANGKU']);
        }
        //待验区查询
        if($filter['searchParams']['DAIYANQU']!=""){
            $sql .= " AND( LOWER(DYQBH) LIKE '%' || LOWER(:DAIYANQU) || '%'".
                    "      OR  lower(DYQMCH) LIKE '%' || LOWER(:DAIYANQU) || '%')";
            $bind ['DAIYANQU'] = strtolower($filter ["searchParams"]['DAIYANQU']);
        }
        //待验区查询
        if($filter['searchParams']['DYQKW']!=""){
            $sql .= " AND( LOWER(DYQKWBH) LIKE '%' || LOWER(:DYQKW) || '%'".
                    "      OR  lower(DYQKWMCH) LIKE '%' || LOWER(:DYQKW) || '%')";
            $bind ['DYQKW'] = strtolower($filter ["searchParams"]['DYQKW']);
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_DYQKWXXWH",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
        $sql .=",DYQKWBH";
        
        //翻页表格用SQL生成(总行数与单页记录)
        $pagedSql = Common_Tool::getPageSql ( $sql, $filter );
        
        //总行数
        $totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
        
        //当前页数据
        $recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
        
        //调用表格xml生成函数
        return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
    }
    
    /**
     * 取得员工信息
     * @param string $dyqkwbh 待验区库位编号
     * @param array $filter  查询条件
     * @param string $direction 查找方向  current,next,prev
     * @return array 
     */
    public function getDyqkwxx($filter=null, $flg = 'current')
    {
    	//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
        //排序用字段名
        //状态，仓库名称，待验区名称，待验区库位编号，待验区库位名称
        $fields = array ("","ZHUANGTAI","CKBH","CKMCH","DYQBH","DYQMCH","DYQKWBH","DYQKWMCH","BGZH","BGRQ"); 

        $sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",DYQKWBH) AS NEXTROWID,".
                    "  LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,DYQKWBH) AS PREVROWID".
                    " ,DYQKWBH".
                    " FROM H01VIEW012439" . 
                    " WHERE QYBH = :QYBH";
        
       //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        
        //仓库查询
        if($filter['searchParams']['CANGKU']!=""){
            $sql_list .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
                    "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
            $bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CANGKU']);
        }
        //待验区查询
        if($filter['searchParams']['DAIYANQU']!=""){
            $sql_list .= " AND( DYQBH LIKE '%' || :SEARCHKEY || '%'".
                    "      OR  lower(DYQMCH) LIKE '%' || :SEARCHKEY || '%')";
            $bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DAIYANQU']);
        }
        //待验区库位查询
        if($filter['searchParams']['DYQKW']!=""){
            $sql_list .= " AND( DYQKWBH LIKE '%' || :SEARCHKEY || '%'".
                    "      OR  lower(DYQKWMCH) LIKE '%' || :SEARCHKEY || '%')";
            $bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['DYQKW']);
        }
        
        //自动生成精确查询用Sql
        $sql_list .= Common_Tool::createFilterSql("CC_DYQKWXXWH",$filter['filterParams'],$bind);

        //待验区库位信息单条查询
        $sql_single = "SELECT CKBH,CKMCH,DYQBH,DYQMCH,DYQKWBH,DYQKWMCH,BGZH,TO_CHAR(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ "
                      . " FROM H01VIEW012439 ";
        //当前
        if ($flg == 'current') {
            $sql_single .= " WHERE QYBH = :QYBH AND DYQKWBH = :DYQKWBH";
//            unset($bind['SEARCHKEY']);
        } else if ($flg == 'next') {//下一条
            $sql_single .= "WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DYQKWBH FROM ( $sql_list ) WHERE DYQKWBH = :DYQKWBH))";     
        } else if ($flg == 'prev') {//前一条
            $sql_single .= "WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,DYQKWBH FROM ( $sql_list ) WHERE DYQKWBH = :DYQKWBH))";     
        }
        $bind['DYQKWBH'] = str_pad($filter['dyqkwbh'], 6); //当前待验区库位编号
        return $this->_db->fetchRow ( $sql_single, $bind );
    }
    
    /*
     * 时间戳判断
     */
    public function getTime($filter)
    {
    	//查询语句
    	$sql="SELECT TO_CHAR(BGRQ, 'yyyy-mm-dd hh24:mi:ss') AS BGRQ FROM H01DB012439 WHERE CKBH = :CKBH AND DYQBH = :DYQBH AND DYQKWBH = :DYQKWBH AND QYBH = :QYBH ";
    	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
    	$bind['DYQKWBH'] = str_pad($filter['dyqkwbh'], 6); //当前待验区库位编号
        $bind['CKBH'] = str_pad($filter['ckbh'], 6); //当前仓库编号
        $bind['DYQBH'] = str_pad($filter['dyqbh'], 6); //当前待验区编号
        $timestamp = $this->_db->fetchOne($sql, $bind);
        $bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
        if($timestamp != $filter['timestamp'])
        {
        	return false;
        }else
        {
        	return true;
        }
    }
    
    /*
     * 更新待验区库位信息
     */
    public function updateData($filter)
    {
    	$sql="UPDATE H01DB012439 SET DYQKWMCH = :DYQKWMCH,BGZH = :BGZH,BGRQ = SYSDATE WHERE QYBH = :QYBH AND CKBH = :CKBH AND DYQBH = :DYQBH AND DYQKWBH = :DYQKWBH";
    	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
    	$bind['DYQKWBH'] = str_pad($filter['dyqkwbh'], 6); //当前待验区库位编号
        $bind['CKBH'] = str_pad($filter['ckbh'], 6); //当前仓库编号
        $bind['DYQBH'] = str_pad($filter['dyqbh'], 6); //当前待验区编号
        $bind['DYQKWMCH'] = $filter['dyqkwmch'];
        $bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
        $this->_db->query($sql, $bind);
        return true;
    }
    
    /*
     * 获取选中行的待验区库位信息的状态
     */
    public function getStatus($filter)
    {
    	//检索语句
    	$sql = "SELECT ZHUANGTAI FROM H01DB012439 WHERE CKBH = :CKBH AND DYQBH = :DYQBH AND DYQKWBH = :DYQKWBH AND QYBH = :QYBH";
    	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['DYQKWBH'] = str_pad($filter['dyqkwbh'], 6); //当前待验区库位编号
        $bind['CKBH'] = str_pad($filter['ckbh'], 6); //当前仓库编号
        $bind['DYQBH'] = str_pad($filter['dyqbh'], 6); //当前待验区编号
        return $this->_db->fetchOne($sql, $bind);
    }
    
    /*
     * 获取选中行的待验区库位信息所在待验区状态
     */
    public function getDyqStatus($filter)
    {
    	$sql = "SELECT ZHUANGTAI FROM H01DB012435 WHERE QYBH = :QYBH AND DYQBH = ：DYQBH AND CKBH = :CKBH";
    	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['CKBH'] = str_pad($filter['ckbh'], 6); //当前仓库编号
        $bind['DYQBH'] = str_pad($filter['dyqbh'], 6); //当前待验区编号
//        return $this->_db->fetchOne($sql, $bind);
        return $this->_db->fetchOne($sql, $bind);
    }
    
    /*
     * 更新待验区库位信息状态
     */
    public function updateStatus($filter)
    {
    	//检索语句
    	$sql = "UPDATE H01DB012439 SET ZHUANGTAI = :ZHT WHERE QYBH = :QYBH AND CKBH = :CKBH AND DYQBH = :DYQBH AND DYQKWBH = :DYQKWBH";
    	//绑定查询条件
    	$bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['DYQKWBH'] = str_pad($filter['dyqkwbh'], 6); //当前待验区库位编号
        $bind['CKBH'] = str_pad($filter['ckbh'], 6); //当前仓库编号
        $bind['DYQBH'] = str_pad($filter['dyqbh'], 6); //当前待验区编号
        $bind['ZHT'] = $filter['dyqkwzht'];
        $this->_db->query($sql, $bind);
        return true;
    }
}
?>