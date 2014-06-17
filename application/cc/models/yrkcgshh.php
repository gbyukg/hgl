<?php
/*********************************
 * 模块：   仓储模块(JC)
 * 机能：   预入库采购审核(yrkcgshh)
 * 作成者：ZhangZeliang
 * 作成日：2011/06/03
 * 更新履历：
 * 2011/08/17修改--DLTTSUXUN
 * 针对预入库可以部分入库，不需要完全符合采购订单明细
 * 修改处：搜索(2011/08/17修改)
 *********************************/

class cc_models_yrkcgshh extends Common_Model_Base
{
	/**
     * 取得列表数据
     *
     * @param array_type $filter
     * @return unknown
     */
	public function getGridData($filter)
	{
		//排序用字段名
        $fields = array ("", 'YRKDBH', 'CKDBH', 'SHQDH', 'KPRQ', 'DWBH', 'DWMCH', 'BMMCH', 'YWYXM', 'ZCHZHXM', 'DYCGYXM' ); //编号，姓名，所属部门,性别

        //检索SQL
        $sql = 'SELECT YRKDBH,CKDBH,SHQDH,TO_CHAR(KPRQ,\'YYYY-MM-DD\') AS KPRQ,DWBH,DWMCH,BMMCH,YWYXM,ZCHZHXM,DYCGYXM FROM H01VIEW012427 '
               . ' WHERE QYBH = :QYBH AND DYCGY = :DYCGY AND ZHUANGTAI = 1';

        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['DYCGY'] = $_SESSION ['auth']->userId;
        
	    //查询条件(开始日期<=开票日期<=终止日期)
        if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "") {
            $sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
            $bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == "" ? "1900-01-01" : $filter ['searchParams']["KSRQKEY"];
            $bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == "" ? "9999-12-31" : $filter ['searchParams']["ZZRQKEY"];
        }
        
        //查询条件(单位编号输入)
        if ($filter ['searchParams']["DWBHKEY"] != "") {
            $sql .= " AND DWBH = :DWBH";
            $bind ['DWBH'] = $filter ['searchParams']["DWBHKEY"];
        }
        
        //查询条件(单位编号没输入,只输入单位名称)
        if ($filter ['searchParams']["DWBHKEY"] == "" && $filter ['searchParams']["DWMCHKEY"] != "") {
            $sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
            $bind ['DWMCH'] = $filter ['searchParams']["DWMCHKEY"];
        }
        
	    //查询条件(送货清单号)
        if ($filter ['searchParams']["SHQDHKEY"] != "" ) {
            $sql .= " AND SHQDH LIKE '%' || :SHQDH || '%'";
            $bind ['SHQDH'] = $filter ['searchParams']["SHQDHKEY"];
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_YRKCGSHH",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
        $sql .=",YRKDBH";
        
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
     * 取得列表数据
     *
     * @param string $yrkdbh
     * @return unknown
     */
	public function getmxdata($yrkdbh)
	{
		$sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,TO_CHAR(SHCHRQ,\'YYYY-MM-DD\'),TO_CHAR(BZHQZH,\'YYYY-MM-DD\'),'
			 . 'DYQKWMCH,HGPSHL,BHGPSHL,JLGG,DANJIA,HSHJ,KOULV,SHUILV,JINE,HSHJE,SHUIE,LSHJ,CHANDI,BZHDWBH,ZHDKQLX,ZHDKQLXMCH,TYMCH,CHBJS'
             . ' FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
               
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	/**
     * 获取单位信息
     *
     * @param string $dwbh
     * @return unknown
     */
	public function getDanweiInfo($dwbh)
	{
		//检索语句
		$sql = 'SELECT DWMCH FROM H01VIEW012106 WHERE QYBH = :QYBH AND DWBH = :DWBH';
		//绑定查询语句
		$bind['QYBH'] = $_SESSION['auth']->qybh;
		$bind['DWBH'] = $dwbh;
		return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
     * 获取预入库单信息
     *
     * @param string $yrkdbh
     * @return unknown
     */
	public function getYrkdInfo($yrkdbh)
	{
		//查询语句
		$sql = 'SELECT TO_CHAR(KPRQ, \'YYYY-MM-DD\') AS KPRQ,SHFZZHSH,CKDBH,CKDBH,DWBH,DWMCH,DHHM,DIZHI,KOULV,SHQDH,FPBH,BMMCH,YWYXM,BEIZHU,DYCGYXM '
		. 'FROM H01VIEW012427 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
		//绑定条件
		$bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
     * 获取比较数据 grid_match
     *
     * @param string $yrkdbh
     * @return unknown
     */
	public function getMatch($yrkdbh)
	{
		$sql = 'SELECT A.SHPBH,A.SHPMCH,NVL(C.SHULIANG, 0),NVL(SUM(A.HGPSHL), 0) AS HGPSHL,NVL(A.DANJIA, 0),NVL(C.DANJIA, 0) AS QDJG '
                . 'FROM H01VIEW012428 A '
				. 'LEFT JOIN H01VIEW012427 B ON A.QYBH = B.QYBH AND A.YRKDBH = B.YRKDBH '
				. 'LEFT JOIN H01VIEW012307 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH AND C.CGDBH = B.CKDBH '
				. 'WHERE B.YRKDBH = :YRKDBH '
				. 'GROUP BY A.SHPBH,A.SHPMCH,A.DANJIA,C.SHULIANG,C.DANJIA '
				. 'UNION '
				. 'SELECT C.SHPBH,C.SHPMCH,NVL(C.SHULIANG, 0),NVL(SUM(A.HGPSHL), 0) AS HGPSHL,NVL(A.DANJIA, 0),NVL(C.DANJIA, 0) AS QDJG '
				. 'FROM H01VIEW012307 C '
				. 'LEFT JOIN H01VIEW012427 B ON C.QYBH = B.QYBH AND B.CKDBH = C.CGDBH '
				. 'LEFT JOIN H01VIEW012428 A ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH AND B.YRKDBH = A.YRKDBH '
				. 'WHERE B.YRKDBH = :YRKDBH '
				. 'GROUP BY C.SHPBH,C.SHPMCH,A.DANJIA,C.SHULIANG,C.DANJIA';
				
	   $bind['YRKDBH'] = $yrkdbh;
	   
	   //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	/**
     *获取商品报警信息 grid_alarm
     *
     * @param array $filter
     * @return unknown
     */
	public function getAlarm($filter)
	{
		//检索语句
		$sql = 'SELECT YRKJGXX FROM H01DB012476 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
		//绑定条件
		$bind['QYBH'] = $_SESSION['auth']->qybh;
		$bind['YRKDBH'] = $filter['yrkdbh'];
		
		if($filter['shpbh'] != '0')
		{
			$sql .= ' AND SHPBH = :SHPBH';
			$bind['SHPBH'] = $filter['shpbh'];
		}
		
		//调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
	}
	
	/**
     *新建采购订单
     *
     * @param array $filter
     * @return unknown
     */
	public function newCgdd($filter)
	{
		//检索原订单是否存在入库记录--入库状态1：未入库；2：已入库；3：已预入库
	    $sql_cgmx = "SELECT SHPBH FROM H01DB012307 WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND RKZHT ='2'";
	    
	    $bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['CGDBH'] = $filter['cgdbh'];
        
        if($this->_db->fetchAll($sql_cgmx, $bind)==false){
        	$hasQitaRk = false;	//采购订单没有已经入库的商品
        }else{
        	$hasQitaRk = true;  //采购订单已有已经入库的商品
        }
        
        
		$sql_oricgd = 'SELECT BMBH,YWYBH,DWBH,DIZHI,DHHM,SHFZZHSH,YDHRQ,FKFSH,KOULV,BEIZHU FROM H01DB012306 '
        	 . 'WHERE QYBH = :QYBH AND CGDBH = :CGDBH';

        $arr_cgdinfo = $this->_db->fetchRow($sql_oricgd, $bind);

        //保存新采购订单信息
        $cgd['QYBH'] = $_SESSION ['auth']->qybh;
        $cgd['CGDBH'] = $filter['new_cgdbh'];
        $cgd['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
        $cgd['BMBH'] = $arr_cgdinfo["BMBH"];
        $cgd['YWYBH'] = $arr_cgdinfo["YWYBH"];
        $cgd['DWBH'] = $arr_cgdinfo["DWBH"];
        $cgd['DIZHI'] = $arr_cgdinfo["DIZHI"];
        $cgd['DHHM'] = $arr_cgdinfo["DHHM"];
        $cgd['SHFZZHSH'] = $arr_cgdinfo["SHFZZHSH"];
        $cgd['YDHRQ'] = $arr_cgdinfo["YDHRQ"];       
        $cgd['KOULV'] = $arr_cgdinfo["KOULV"];
        $cgd['SHPZHT'] = '0';   //审批状态
        $cgd['CGDZHT'] = '0';   //采购单状态
        $cgd['BEIZHU'] = $arr_cgdinfo["BEIZHU"]; 
        $cgd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); 	//变更日期
        $cgd ['BGZH'] = $_SESSION ['auth']->userId;     	//变更者
        $cgd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); 	//作成日期
        $cgd ['ZCHZH'] = $_SESSION ['auth']->userId; 		//作成者
        
       /**
        * 2011/08/17修改--DLTTSUXUN
     	* 付款方式，如果不是预付款，付款方式按照原采购订单;
     	* 如果是预付款，需要检查是否原采购订单已有入库，若有，新生成订单付款方式为<QA:账期>,新订单不需要生成结算信息，新订单入库时再生成，
     	* 若原采购订单以前没有过入库，新订单付款方式为预付款，需要重新生成新订单对应的结算信息
    	*/
        if($arr_cgdinfo['FKFSH'] != '4'){
        	$cgd['FKFSH'] = $arr_cgdinfo['FKFSH'];//如果不是预付款，付款方式按照原采购订单
        }else{
        	if($hasQitaRk){
        		$cgd['FKFSH'] = '1';//付款方式：1:账期；2:现金；3:货到付款；4:预付款 -----QA:账期吗？
        	}else{
        		$cgd['FKFSH'] = '4';//预付款
        	}
        }        
        $this->_db->insert ( "H01DB012306", $cgd );
             
       /**
        * 2011/08/17修改--DLTTSUXUN
     	* 关于新订单的采购明细
     	* 如果原采购订单以前没有入库,按照本次预入库的明细生成新采购明细
     	* 如果原采购订单以前已经有入库，除了本次预入库明细生成采购明细外，还需要生成原订单中尚未来货商品的采购订单明细
    	*/
        //采购明细重新做成--按照预入库明细
        $xuhao = 1;
        $this->newCgdmx($filter,$xuhao);
        //如果该采购单已经有入库，除了本次重做订单对应的预入库明细中的商品，还需要生成原订单中尚未来货商品的采购订单明细
        if($hasQitaRk){
        	$sql_cgmx = "SELECT * FROM H01DB012307 WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND RKZHT <> '2'"
        			  . " AND SHPBH NOT IN (SELECT DISTINCT SHPBH FROM H01DB012428 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH)";
        			  
        	$bind_yrk['QYBH'] = $_SESSION['auth']->qybh;
        	$bind_yrk['CGDBH'] = $filter['cgdbh'];
        	$bind_yrk['YRKDBH'] = $filter['yrkdbh'];
        	
        	$arrhasrk_cgdmx = $this->_db->fetchAll($sql_cgmx, $bind_yrk);
        	
        	foreach ($arrhasrk_cgdmx as $row){
	        	$ins_cgmx['QYBH'] = $_SESSION ['auth']->qybh;
		        $ins_cgmx['CGDBH'] = $filter['new_cgdbh'];
		        $ins_cgmx['XUHAO'] = $xuhao;
		        $ins_cgmx['SHPBH'] = $row ['SHPBH'];
		        $ins_cgmx['BZHSHL'] = $row['BZHSHL'];
		        $ins_cgmx['LSSHL'] = $row['LSSHL'];
		        $ins_cgmx['SHULIANG'] = $row ['SHULIANG'];
		        $ins_cgmx['DANJIA'] = $row ['DANJIA'];
		        $ins_cgmx['HSHJ'] = $row ['HSHJ'];
		        $ins_cgmx['KOULV'] = $row ['KOULV'];
		        $ins_cgmx['JINE'] = $row ['JINE'];
		        $ins_cgmx['HSHJE'] = $row ['HSHJE'];
		        $ins_cgmx['SHUIE'] = $row ['SHUIE'];
		        $ins_cgmx['BEIZHU'] = $row ['BEIZHU'];
		        $ins_cgmx['RKZHT'] = '1';//未入库
		        $ins_cgmx['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		        $ins_cgmx['BGZH'] = $_SESSION ['auth']->userId; //变更者   
		        $ins_cgmx ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		        $ins_cgmx ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		        $this->_db->insert ( 'H01DB012307', $ins_cgmx );  
	            $xuhao++;
        	}

        }  
        
       /**
        * 2011/08/17修改--DLTTSUXUN
        *如果是预付款，需要检查是否原采购订单已有入库
        *若有，新生成订单付款方式为QA:账期,新订单不需要生成结算信息，新订单入库时再生成，原采购订单状态更新为已入库，原采购订单明细中未入库的明细更新为取消
     	*若原采购订单以前没有入库，新订单付款方式为预付款，需要重新生成新订单对应的结算信息，原采购结算信息更新为取消，原采购订单信息更新为取消
     	*/
        if($arr_cgdinfo['FKFSH'] == '4')
        {
        	if(!$hasQitaRk){
        		//重做采购结算信息,按照预入库
        		$this->updateCgjsxx($filter);
        		
        		//原采购结算信息更新为取消
        		$upd_cgjs = "UPDATE H01DB012310 SET ZHUANGTAI = 'X' WHERE QYBH = :QYBH AND CKDBH = :CGDBH";      		
        		$this->_db->query($upd_cgjs,$bind);
        		
        		//原采购订单信息取消标志更新为删除
        		$upd_cgdd = "UPDATE H01DB012306 SET QXBZH = 'X' WHERE QYBH = :QYBH AND CGDBH = :CGDBH";
        		$this->_db->query($upd_cgdd,$bind);
        		
        	}else{
        		//原采购订单状态更新为已入库--采购单状态：0：未确认；1：已确认；2:已入库；
        		$upd_cgdd = "UPDATE H01DB012306 SET CGDZHT = '2' WHERE QYBH = :QYBH AND CGDBH = :CGDBH";
        		$this->_db->query($upd_cgdd,$bind);
        		
        		//原采购订单明细中未入库的明细更新为取消--入库状态:1：未入库；2：已入库；3：已预入库
        		$upd_cgmx = "UPDATE H01DB012307 SET QXBZH = 'X' WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND RKZHT <> '2'";
        		$this->_db->query($upd_cgmx,$bind);
        	}    	
        }
	}
	
	/**
     *新建采购订单明细信息
     *
     * @param array $filter
     * @return unknown
     */
	private function newCgdmx($filter,&$xuhao)
	{
		$sql = 'SELECT SHPBH,GUIGE,JLGG,BZHDWBH,SUM(HGPSHL) AS HGPSHL,DANJIA,HSHJ,KOULV,SHUILV,SUM(JINE) AS JINE,SUM(HSHJE) AS HSHJE,SUM(SHUIE) AS SHUIE,CHANDI,BEIZHU '
        	 . 'FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH '
        	 . 'GROUP BY SHPBH,GUIGE,JLGG,BZHDWBH,DANJIA,HSHJ,KOULV,SHUILV,CHANDI,BEIZHU';
        $bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['YRKDBH'] = $filter['yrkdbh'];
        $arr_cgdmx = $this->_db->fetchAll($sql, $bind);
        foreach ($arr_cgdmx as $row)
        {
        	$bzhshl = (int) $row['HGPSHL'] / (int) $row['JLGG'];
        	$lsshl = (int) $row['HGPSHL'] % (int) $row['JLGG'];
        	$data['QYBH'] = $_SESSION ['auth']->qybh;
	        $data['CGDBH'] = $filter['new_cgdbh'];
	        $data['XUHAO'] = $xuhao;
	        $data['SHPBH'] = $row ['SHPBH'];
	        $data['BZHSHL'] = $bzhshl;
	        $data['LSSHL'] = $lsshl;
	        $data['SHULIANG'] = $row ['HGPSHL'];
	        $data['DANJIA'] = $row ['DANJIA'];
	        $data['HSHJ'] = $row ['HSHJ'];
	        $data['KOULV'] = $row ['KOULV'];
	        $data['JINE'] = $row ['JINE'];
	        $data['HSHJE'] = $row ['HSHJE'];
	        $data['SHUIE'] = $row ['SHUIE'];
	        $data['BEIZHU'] = $row ['BEIZHU'];
	        $data['RKZHT'] = '1';//未入库
	        $data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
	        $data['BGZH'] = $_SESSION ['auth']->userId; //变更者   
	        $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
	        $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
	        $this->_db->insert ( 'H01DB012307', $data );  
            $xuhao++;
        }
	}
	
	/*
	 * 更新采购结算信息
	 * @param array $filter
     * @return unknown
	 */
	private function updateCgjsxx($filter)
	{
		//获取原采购订单结算信息
		$sql_cgdjs = 'SELECT JINE,HSHJE,YFJE,ZHFJE FROM H01DB012310 WHERE QYBH = :QYBH AND CKDBH = :CGDBH';
		$bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['CGDBH'] = $filter['cgdbh'];
        $o_cgjsxx = $this->_db->fetchRow($sql_cgdjs, $bind);
        unset($bind);
        //获取新订单中总金额于含税金额数
        $sql = 'SELECT SUM(JINE) AS JINE, SUM(HSHJE) AS HSHJE FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
        $bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['YRKDBH'] = $filter['yrkdbh'];
        $n_cgdmx = $this->_db->fetchRow($sql, $bind);
        unset($bind);
        //新规新采购订单结算信息
        $cgjs['QYBH'] = $_SESSION ['auth']->qybh;
        $cgjs['CKDBH'] = $filter['new_cgdbh'];
        $cgjs['JINE'] = $n_cgdmx['JINE'];   					//金额
        $cgjs['HSHJE'] = $n_cgdmx['HSHJE']; 					//含税金额
        $cgjs['ZHFJE'] = $o_cgjsxx['ZHFJE'];					//支付金额
        $cgjs['YFJE'] = $n_cgdmx['HSHJE']-$o_cgjsxx['ZHFJE'];	//应付金额
        $cgjs['FKFSH'] = '4';									//只有预付款时，重做结算信息
        $cgjs['ZHUANGTAI'] = '1';								//状态:'1'正常
        $this->_db->insert('H01DB012310', $cgjs);
	}
	
	/**
     *保存
     *
     * @param array $filter,string $zt
     * @return unknown
     */
	public function save($filter, $zt)
	{
		$sql = 'UPDATE H01DB012427 SET CHLFF = :CHLFF,ZHUANGTAI = :ZHUANGTAI,CGYBH = :CGYBH,CGYFHRQ = sysdate,CGDDBH=:CGDDBH WHERE QYBH = :QYBH AND YRKDBH=:YRKDBH';
		$bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['YRKDBH'] = $filter['yrkdbh'];
        $bind['CHLFF'] = $filter['clff'];//状态
        $bind['ZHUANGTAI'] = $zt;       //处理方法
        $bind['CGYBH'] = $_SESSION ['auth']->userId;
        $bind['CGDDBH'] = $filter['new_cgdbh'];
        
        $this->_db->query($sql, $bind);
	}
	
	/**
	 * 获取采购单信息
	 *
	 * @param string $cgdbh
	 */
	public function getCgdxx($cgdbh)
	{
		$sql = 'SELECT TO_CHAR(KPRQ,\'YYYY-MM-DD\') AS KPRQ,SHFZZHSH,KPYXM,DWBH,DWMCH,DIZHI,BMMCH,YWYXM,TO_CHAR(YDHRQ,\'YYYY-MM-DD\') AS YDHRQ,KOULV,BEIZHU '
        . 'FROM H01VIEW012306 WHERE QYBH = :QYBH AND CGDBH = :CGDBH';
        
        $bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['CGDBH'] = $cgdbh;
        return $this->_db->fetchRow($sql, $bind);
	}
	
   /**
     * 取得预入库单明细信息
     *
     * @param string $yrkdbh
     * @return unknown
     */
    public function getNewMxdata($yrkdbh)
    {
        $sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,SUM(HGPSHL) AS HGPSHL,DANJIA,HSHJ,KOULV,SHUILV,SUM(JINE),SUM(HSHJE),SUM(SHUIE),CHANDI '
		. 'FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH '
		. 'GROUP BY SHPBH,SHPMCH,GUIGE,BZHDWMCH,DANJIA,HSHJ,KOULV,SHUILV,CHANDI';
        
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
    }
    
   /**
     * 取得采购单明细信息
     *
     * @param string $cgdbh
     * @return unknown
     */
    public function getOldMxdata($cgdbh)
    {
        $sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDW,SHULIANG,DANJIA,HSHJ,KOULV,SHUILV,JINE,HSHJE,SHUIE,CHANDI '
        . 'FROM H01VIEW012307 WHERE QYBH = :QYBH AND CGDBH = :CGDBH';
        
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['CGDBH'] = $cgdbh;
        
        //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
    }
}
