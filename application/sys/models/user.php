<?php
/*********************************
 * 模块：   系统模块(SYS)
 * 机能：   用户信息(user)
 * 作成者：周义
 * 作成日：2010/10/14
 * 更新履历：
 *********************************/
class sys_models_user extends Common_Model_Base {
	/**
	 * 取得列表数据
	 *
	 * @param unknown_type $filter
	 * @return unknown
	 */
    public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "YHID", "XINGMING", "YHZHT", "YHLX","DYMCH","ZHDL","ZCHRQ","ZCHZH" ); 

		//检索SQL
		$sql = "SELECT YHID,XINGMING,DECODE(YHZHT,'0','禁用','1','可用','2','锁定'),
		               DECODE(YHLX,'0','员工','1','客户') AS YHLX,
		               DECODE(YHLX,'0',YGXM,'1',DWMCH) AS DYMCH,
		               ' ' AS ZHDL,TO_CHAR(ZCHRQ,'YYYY-MM-DD HH24:MI:SS'),
		               ZCHZHXM
		        FROM H01VIEW012107
		        WHERE QYBH = :QYBH";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( YHID LIKE '%' || :SEARCHKEY || '%' OR  lower(XINGMING) LIKE '%' || :SEARCHKEY || '%'
			          OR  lower(YGXM) LIKE '%' || :SEARCHKEY || '%' OR  lower(DWMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}

		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",YHID";
		
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
	 * 取得用户基本信息
	 */
	public function getBaseInfo($yhid,$flg="current",$filter){
		if($flg=="current"){
			$sql_single ="SELECT QYBH,YHID,YHZHT,YHLX,YGBH,YGXM,DWBH,DWMCH,XINGMING,DZYJ,DHHM,SHJHM,
			              TO_CHAR(BGRQ,'YYYY-MM-DD HH24:MI:SS') AS BGRQ FROM H01VIEW012107
			              WHERE QYBH = :QYBH AND YHID = :YHID";
			//绑定查询条件
		    $bind ['QYBH'] = $_SESSION ['auth']->qybh;
		   	$bind["YHID"] = $yhid; //用户id
		    return $this->_db->fetchRow ( $sql_single, $bind );
		    
		}else{
			//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
			//排序用字段名
			$fields = array ("", "YHID", "XINGMING", "YHZHT", "YHLX","DYMCH","ZHDL","ZCHRQ","ZCHZH" ); 
			$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",YHID) AS NEXTROWID,".
			            "              LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,YHID) AS PREVROWID".
			            " ,YHID".
			            " FROM H01VIEW012107 " . 
			            " WHERE QYBH = :QYBH";
	       //绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			
			if($filter['searchParams']['SEARCHKEY']!=""){
				$sql_list .= " AND( YHID LIKE '%' || :SEARCHKEY || '%' OR  lower(XINGMING) LIKE '%' || :SEARCHKEY || '%'
				          OR  lower(YGXM) LIKE '%' || :SEARCHKEY || '%' OR  lower(DWMCH) LIKE '%' || :SEARCHKEY || '%')";
				$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
			}
	
			//单条查询
			$sql_single ="SELECT QYBH,YHID,YHZHT,YHLX,YGBH,YGXM,DWBH,DWMCH,XINGMING,DZYJ,DHHM,SHJHM,
			      TO_CHAR(BGRQ,'YYYY-MM-DD HH24:MI:SS') AS BGRQ FROM H01VIEW012107 ";
	
			if ($flg == 'next') {//下一条
				$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,YHID FROM ( $sql_list ) WHERE YHID = :YHID))";		
			} else if ($flg == 'prev') {//前一条
				$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,YHID FROM ( $sql_list ) WHERE YHID = :YHID))";		
			}
			
			$bind["YHID"] = $yhid; //当前用户id
			return $this->_db->fetchRow ( $sql_single, $bind );
		}
	}
	/*
	 * 用户Id按规则自动分配
	 */
	public function getYhid($yhlx,$yhbh){
		//内部员工 
		if($yhlx=="0"){
			$sql = "SELECT COUNT(*) FROM H01DB012107 WHERE QYBH = :QYBH AND YHLX = '0' AND YGBH = :YGBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['YGBH'] = $yhbh;
			$cnt = $this->_db->fetchOne($sql,$bind);
			$cnt = $cnt + 1;
			$yhid = $cnt==1? "Y_".$yhbh : "Y_".$yhbh."_".str_pad($cnt,2,"0",STR_PAD_LEFT);	
		}else{//外部客户 自动加1
			$sql = "SELECT COUNT(*) FROM H01DB012107 WHERE QYBH = :QYBH AND YHLX = '1' AND DWBH = :DWBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $yhbh;
			$cnt = $this->_db->fetchOne($sql,$bind);
			$cnt = $cnt + 1;
			$yhid = "K_".$yhbh."_".str_pad($cnt,2,"0",STR_PAD_LEFT);		
		}
		
		return $yhid;
	}	
	/*
	 * 取得该用户所有可用权限信息
	 */
	public function getAllRoles($yhid){
		$sql = "SELECT ROLEID,ROLENAME FROM ACL_ROLE
		        WHERE QYBH = :QYBH AND ROLEID NOT IN(
		           SELECT ROLEID FROM ACL_USER_ROLE WHERE QYBH =:QYBH AND USERID = :USERID)		
		        ORDER BY ROLEID";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['USERID'] = $yhid;
		$roles = $this->_db->fetchPairs($sql,$bind);
		return $roles;
	}
	/*
	 * 取得已分配给该用户的权限信息
	 */
	public function getAssignedRoles($yhid){
		$sql = "SELECT A.ROLEID,B.ROLENAME FROM ACL_USER_ROLE A
		        JOIN ACL_ROLE B ON A.QYBH = B.QYBH AND A.ROLEID = B.ROLEID
		        WHERE A.QYBH = :QYBH AND A.USERID = :USERID
		        ORDER BY A.ROLEID";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['USERID'] = $yhid;
		$roles = $this->_db->fetchPairs($sql,$bind);
		return $roles;
	}
	/*
	 * 判别该员工编号是否已经建立用户
	 */
	public function checkYgbh($ygbh){
		$sql = "SELECT COUNT(*) FROM H01DB012107 WHERE QYBH = :QYBH AND YHLX = '0' AND YGBH = :YGBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YGBH'] = $ygbh;
		return $this->_db->fetchOne($sql,$bind);
	}
	/*
	 * 必须输入项目验证
	 */
	public function inputCheck($data){
		if($data["action"]=="new"){
			if($data["YHLX"]==""|| $data["YHBH"]=="" ||$data["YHID"]=="" || $data["XINGMING"]=="" || $data["MIMA"]=="" ){
		   	   return false;
		    }
		}elseif ($data["action"]=="update"){
			if($data["XINGMING"]=="" || ($data["#resetpassword"]=="1" && $data["MIMA"]=="")){
		   	   return false;
		    }
		}
		
		return true;
	}
	
	/*
	 * 项目逻辑性验证
	 */
	public function logicCheck($data){
		$result["status"] = "0";
		if($data["MIMA"] != $data["V_MIMA"]){
			$result["status"] = "1";
			$result["message"] = "用户口令与验证口令不一致，请重新输入。";
		}
		
		return $result;
	}
	/*
	 * 建立用户信息
	 */
	public function createUser($data){
		//用户基本信息
		$yh["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$yh["YHID"] = $data["YHID"];//用户id
		$yh["MIMA"] = md5($data["MIMA"]);//密码
		$yh["YHZHT"] = $data["SDZHT"]; //用户状态
		//$yh["SDZHT"] = $data["SDZHT"];//锁定状态
		$yh["LXDLCHCCSH"] = 0; //连续登陆出错次数
		$yh["YXQKSH"] = null; //有效期开始
		$yh["YXQJSH"] = null; //有效期结束
		$yh["YHLX"] = $data["YHLX"]; //用户类型
		$yh["YGBH"] = $data["YHLX"]=="0"? $data["YHBH"]:""; //员工编号
		$yh["DWBH"] = $data["YHLX"]=="1"? $data["YHBH"]:""; //单位编号
		$yh["XINGMING"] = $data["XINGMING"];//姓名
		$yh["XINGBIE"] = "";
		$yh["CHSHRQ"] = "";
		$yh["SHFZHH"] = "";
		$yh["DZYJ"] = $data["DZYJ"];//电子邮件
		$yh["DHHM"] = $data["DHHM"];//电话号码
		$yh["SHJHM"] = $data["SHJHM"];//手机号码
		$yh["JINGCHENG"] = "";
		$yh["ZHZH"] = "";
		$yh["MDBH"] = "";
		$yh["BGZH"] = $_SESSION ['auth']->userId;
		$yh["BGRQ"] = new Zend_Db_Expr ( "SYSDATE" );
		$yh["ZCHZH"] = $_SESSION ['auth']->userId;
		$yh["ZCHRQ"] = new Zend_Db_Expr ( "SYSDATE" ); 
		
		$this->_db->insert("H01DB012107",$yh);
		
		//用户角色信息
		if(isset($data["assignedroles"])){
			$role["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$role["USERID"] = $data["YHID"]; //用户id
			foreach ($data["assignedroles"] as $assignedrole){
				$role["ROLEID"] = $assignedrole;
				$this->_db->insert("ACL_USER_ROLE",$role);
			}
		}
		
	}
	/*
	 * 编辑用户信息
	 */
	public function modifyUser($data){
		//更新用户基本信息
		$yh["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
		$yh["YHID"] = $data["YHID"];//用户id
		$yh["MIMA"] = md5($data["MIMA"]);//密码
		$yh["YHZHT"] = $data["SDZHT"]; //用户状态
		//$yh["SDZHT"] = $data["SDZHT"];//锁定状态
		$yh["XINGMING"] = $data["XINGMING"];//姓名
		$yh["DZYJ"] = $data["DZYJ"];//电子邮件
		$yh["DHHM"] = $data["DHHM"];//电话号码
		$yh["SHJHM"] = $data["SHJHM"];//手机号码
		$yh["BGZH"] = $_SESSION ['auth']->userId;
		$yh["BGRQ"] = new Zend_Db_Expr ( "SYSDATE" );
		$sql = "UPDATE H01DB012107 
		        SET YHZHT = :YHZHT,
		        XINGMING = :XINGMING,
		        DZYJ = :DZYJ,
		        DHHM = :DHHM,
		        SHJHM = :SHJHM,
		        BGZH = :BGZH,
		        BGRQ = SYSDATE ";
		if($data["resetpassword"]=="1"){
			$sql .= ",MIMA = :MIMA "; 
			$bind ["MIMA"] = md5($data["MIMA"]);
		}
		
	    $sql .= "WHERE QYBH = :QYBH AND YHID = :YHID";
	    
	   	$bind ["QYBH"] = $_SESSION ['auth']->qybh;
	   	$bind ["YHID"] = $data["YHID"];
	   	$bind ["YHZHT"] = $data["SDZHT"];
	   	$bind ["XINGMING"] = $data["XINGMING"];
	    $bind ["DZYJ"] = $data["DZYJ"];
	   	$bind ["DHHM"] = $data["DHHM"];
	   	$bind ["SHJHM"] = $data["SHJHM"];	   		    
	   	$bind ["BGZH"] = $_SESSION ['auth']->userId;

	   	$this->_db->query($sql,$bind);
	   
	   	//删除原有角色信息
	   	$sql = "DELETE FROM ACL_USER_ROLE WHERE QYBH = :QYBH AND USERID = :USERID";
	   	unset($bind);
	   	$bind ["QYBH"] = $_SESSION ['auth']->qybh;
	   	$bind ["USERID"] = $data["YHID"];
	   	$this->_db->query($sql,$bind);
	   	
	   	//插入新的角色信息
		if(isset($data["assignedroles"])){
			$role["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$role["USERID"] = $data["YHID"]; //用户id
			foreach ($data["assignedroles"] as $assignedrole){
				$role["ROLEID"] = $assignedrole;
				$this->_db->insert("ACL_USER_ROLE",$role);
			}
		}
		
		return true;
	}
	/*
	 * 更改用户状态
	 */
	public function changeStatus($yhid,$action){
		$sql = "UPDATE H01DB012107 SET YHZHT = :YHZHT WHERE QYBH = :QYBH AND YHID IN 
		        (SELECT * FROM TABLE(STR2VARLIST(:YHID)))";
		
		$bind ["QYBH"] = $_SESSION ['auth']->qybh;
	   	$bind ["YHZHT"] = ($action=="unlock")? "1":"2";
	   	$bind["YHID"]="";
	   	for($i=0;$i<count($yhid);$i++){
	   		$bind["YHID"] .= $yhid[$i]."," ;
	   	}
	   	
	   	$this->_db->query($sql,$bind);
		        
		
	}

}
