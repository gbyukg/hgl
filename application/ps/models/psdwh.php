<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：   配送订单生成(psdwh)
 * 作成者：梁兆新
 * 作成日：2011/1/21
 * 更新履历：
 *********************************/
class ps_models_psdwh extends Common_Model_Base {
	/**
	 *的到维护清单
	 *
	 * @param array $filter
	 * @return string xml
	 */	
	//回执确认页面的 出库单信息
	private  $idx_hzqrqshr=12;//签收人
	private  $idx_hzqrqshrq=13;//签收日期
	private  $idx_hzqrchkdh=1;//出库单号
	private  $idx_hzqrdhke=11;//带回款额
	private  $idx_hzqrxshdh=3;//销售单号
	//回执确认页面的 出库单信息-end
	
	
	private $_psdscbh = null; //配送
	private $idx_ROWNUM = 0; //行号
	private $idx_CHHBH = 1; //出库单号,
	private $idx_CHHRQ = 2; //出库日期,
	private $idx_XSHDH = 3; //销售单号,
	private $idx_XSRQ = 4; //销售日期,
	private $idx_DWMCH = 5; //单位名称,
	private $idx_DWDH = 6; //单位电话,
	private $idx_SHDZH = 7; //送货地址,
	private $idx_DSHHK = 8; //代收货款,
	private $idx_JINE = 9; //金额
	private $idx_HSHJE = 10; //,含税金额,
	private $idx_BZH = 11; //备注
	private $idx_SHUIE = 12; //税额
	//G3表格的 idx定义
	private $g3idx_CHHBH = 2; //出库单号,
	private $g3idx_SHPBH = 3; //商品编号
	private $g3idx_XUHAO = 27; //序号
	private $g3idx_PIHAO = 7; //批号
	private $g3idx_SHCHRQ = 8; //生产日期,
	private $g3idx_BZHSHL = 9; //包装数量,
	private $g3idx_LSSHL = 10; //零散数量,
	private $g3idx_SHULIANG = 11; //数量,
	private $g3idx_DANJIA = 23; //单价
	private $g3idx_HSHJ = 24; //含税价,
	private $g3idx_KOULV = 25; //扣率
	private $g3idx_BEIZHU = 12; //备注
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ('', 'T1.PSDH', 'T1.PSZHT' , 'T1.PSLX' , 'T1.PSRQ' , 'T1.CHYRMCH' , 'T1.LXDH' , 'T1.CHPHM' , 'T1.FCHRQ' , 'T1.DDRQ' , 'T1.ZSHL' , 'T1.ZPSH' , 'T1.HKZE' , 'T1.DHKE','T2.YGXM','T1.BEIZHU' ); //查询排序字段
		//检索SQL
		$sql = 'SELECT  T1.PSDH,(CASE WHEN T1.PSZHT=0 THEN \'未发车\' WHEN T1.PSZHT=1 THEN \'已发车\' WHEN T1.PSZHT=2 THEN \'已到达\' END)STRPSZHT, '.
		       ' (CASE WHEN T1.PSLX=0 THEN \'公司配送\' WHEN T1.PSLX=1 THEN \'第三方物流\' END)PSLX,TO_CHAR(T1.PSRQ,\'YYYY-MM-DD\'),T1.CHYRMCH,T1.LXDH,T1.CHPHM,TO_CHAR(T1.FCHRQ,\'YYYY-MM-DD\'),TO_CHAR(T1.DDRQ,\'YYYY-MM-DD\'),T1.ZSHL,T1.ZPSH,T1.HKZE,T1.DHKE,T2.YGXM,T1.BEIZHU,PSZHT '.
		      ' FROM H01DB012601 T1 LEFT OUTER JOIN H01DB012113 T2 ON T2.YGBH=T1.ZHDR WHERE T1.QYBH=T2.QYBH AND  T1.QYBH = :QYBH ';
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查找条件  车牌号码
		
		//起止日期
		if ($filter ['serchstime'] != '') {
			$sql .= " AND PSRQ>=TO_DATE(:STIME,'YYYY-MM-DD')";
			$bind['STIME']=$filter['serchstime']; 
		}
	    //终止日期
		if(!empty($filter['serchetime'])){
			$sql .= " AND PSRQ<=TO_DATE(:ETIME,'YYYY-MM-DD')";
			$bind['ETIME'] =$filter['serchetime']; 
		}
		//查找条件车牌号
		if ($filter ["serchchphm"] !='') {
			$sql .= " AND (CHPHM LIKE '%' || :CHPHM || '%' )";
			$bind ['CHPHM'] = $filter ["serchchphm"];
		}
	   //查找条件承运人
		if ($filter ["serchchyrmch"] !='') {
			$sql .= " AND (CHPHM LIKE '%' || :CHYRMCH || '%' )";
			$bind ['CHYRMCH'] = $filter ["serchchyrmch"];
		}
		
		if(!isset($filter ['orderby'])&& !empty($filter ['orderby'])){
			//排序
			if(empty($filter ['direction'])&& $filter ['direction']=='ASC'){
				$sql .= ' AND  ' . $fields [$filter ['orderby']] . ">0" ;
			}else{
				$sql .= ' ORDER BY ' . $fields [$filter ['orderby']] . ' ' . $filter ['direction'];
			}
		}
	
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ['sql_count'], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ['sql_page'], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ['posStart'] );
	}	
	
	//删除信息
	public function del($filter){
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['PSDH'] = $filter['psdh'];
		$sql = 'UPDATE H01DB012601 SET QXBZH=\'X\' WHERE QYBH = :QYBH AND PSDH=:PSDH';
		$this->_db->query ($sql,$bind );
		$result['status']='1';
		return json_encode($result);
	}
	
    /*
	 * 得到配送订单详细的 中的配送订单部分
	 */
	function getpsxxlist1($filter) {
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter['psdh']; //配送单号
		//检索SQL
		$sql = 'SELECT :QYBH AS QYBH,:PSDH AS PSDH,TO_CHAR(PSRQ,\'YYYY-MM-DD\') AS PSRQ,(CASE WHEN PSZHT=0 THEN \'未发车\' WHEN PSZHT=1 THEN \'已发车\' WHEN PSZHT=2 THEN \'已到达\' END)PSZHT,(CASE WHEN PSLX=0 THEN \'公司配送\' WHEN PSLX=1 THEN \'第三方物流\' END)PSLX,CHYRMCH,CHPHM,LXRXM,LXDZH,LXDH,TO_CHAR(FCHRQ ,\'YYYY-MM-DD\') AS FCHRQ,TO_CHAR(DDRQ,\'YYYY-MM-DD\') AS DDRQ,ZSHL,ZPSH,HKZE,DHKE,BEIZHU,TO_CHAR(BGRQ,\'YYYY-MM-DD\') AS BGRQ'.
		       ' FROM H01DB012601'.
		       ' WHERE QYBH=:QYBH'.
			   ' AND PSDH=:PSDH';
		$recs = $this->_db->fetchRow($sql,$bind);
		Common_Logger::logMessage($sql);
		return $recs;
	}
	
    /*
	 *的到修改页面额配送信息
	 */
	function getpseditlist($filter) {
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter['psdh']; //配送单号
		//检索SQL
		$sql = 'SELECT :QYBH AS QYBH,:PSDH AS PSDH,TO_CHAR(PSRQ,\'YYYY-MM-DD\') AS PSRQ,(CASE WHEN PSZHT=0 THEN \'未发车\' WHEN PSZHT=1 THEN \'已发车\' WHEN PSZHT=2 THEN \'已到达\' END)STRPSZHT,PSZHT,PSLX,CHYRMCH,CHPHM,LXRXM,LXDZH,LXDH,TO_CHAR(FCHRQ ,\'YYYY-MM-DD\') AS FCHRQ,TO_CHAR(DDRQ,\'YYYY-MM-DD\') AS DDRQ,ZSHL,ZPSH,HKZE,DHKE,BEIZHU,TO_CHAR(BGRQ,\'YYYY-MM-DD\') AS BGRQ'.
		       ' FROM H01DB012601'.
		       ' WHERE QYBH=:QYBH'.
			   ' AND PSDH=:PSDH';
		$recs = $this->_db->fetchRow($sql,$bind);
		Common_Logger::logMessage($sql);
		return $recs;
	}
     /*
	 * 配送单信息（xml格式）//得到出库单列表 为了修改 操作 
	 */
	function getpseditlist2($filter){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter ['psdh']; //区域编号
	    $sql = "SELECT T1.CHKDBH,TO_CHAR(T1.KPRQ,'yyyy-MM-dd'),T2.XSHDBH,TO_CHAR(T2.KPRQ,'yyyy-MM-dd') T2KPRQ,T4.DWMCH,T2.DHHM,T2.DIZHI,T5.SHFDSHHK,T5.JINE,T5.HSHJE,T5.BEIZHU,T5.SHUIE".
		       " FROM H01DB012602 T5 LEFT OUTER JOIN H01DB012113 T6 ON T6.YGBH=T5.QSHR, H01DB012408 T1,H01DB012201 T2 LEFT OUTER JOIN H01DB012106 T4 ON T2.DWBH=T4.DWBH ".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=T4.QYBH  AND T1.QYBH=:QYBH AND T1.QYBH=T5.QYBH".
			   " AND T1.CKDBH=T2.XSHDBH".
			   " AND T1.CHKDBH=T5.CHKDBH".
	  		   " AND T5.PSDH=:PSDH".
			   " AND T2.SHFPS='1'";
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return Common_Tool::createXml ( $recs, true);
	}
	
	
     /*
	 * 配送单信息（xml格式）//得到回执确认的 出库单列表信息
	 */
	function gethzqrlist($filter){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter ['psdh']; //配送单号
	    $sql = "SELECT T1.CHKDBH,TO_CHAR(T1.KPRQ,'yyyy-MM-dd'),T2.XSHDBH,TO_CHAR(T2.KPRQ,'yyyy-MM-dd') T2KPRQ,T4.DWMCH,T2.DHHM,T2.DIZHI,(CASE WHEN T5.SHFDSHHK=0 THEN '未代收' WHEN T5.SHFDSHHK=1 THEN '代收'  END)T5SHFDSHHK,T5.JINE,T5.HSHJE,T5.DHKE,T5.QSHR,TO_CHAR(T5.QSHRQ,'yyyy-MM-dd') T5QSHRQ,T5.BEIZHU,T5.SHUIE".
		       " FROM H01DB012602 T5 LEFT OUTER JOIN H01DB012113 T6 ON T6.YGBH=T5.QSHR, H01DB012408 T1,H01DB012201 T2 LEFT OUTER JOIN H01DB012106 T4 ON T2.DWBH=T4.DWBH ".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=T4.QYBH  AND T1.QYBH=:QYBH AND T1.QYBH=T5.QYBH".
			   " AND T1.CKDBH=T2.XSHDBH".
			   " AND T1.CHKDBH=T5.CHKDBH".
	  		   " AND T5.PSDH=:PSDH".
			   " AND T2.SHFPS='1'";
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return Common_Tool::createXml ( $recs, true);
	}
	
	 /*
	 * 配送单信息（xml格式）//获得商品的详细情况-为了修改页面
	 */
	function getpseditlist3($filter){
			$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter ['psdh']; //配送单号
	    $sql = "SELECT '0',T1.CHKDBH,T1.SHPBH,T2.SHPMCH,T2.GUIGE,T2.BZHGG,T1.PIHAO,TO_CHAR(T1.SHCHRQ,'YYYY-MM-DD'),T1.BZHSHL,T1.LSSHL,T1.SHULIANG,T1.BEIZHU,T2.JLGG,T4.DWMCH,".
	    	    "((SELECT SUM(T5.SHULIANG) FROM H01DB012409 T5 WHERE T1.QYBH=T5.QYBH AND T1.CHKDBH=T5.CHKDBH AND T1.SHPBH=T5.SHPBH AND T1.PIHAO=T5.PIHAO AND T1.SHCHRQ=T5.SHCHRQ )-NVL((SELECT SUM(T4.SHULIANG) FROM H01DB012603 T4 WHERE T1.QYBH=T4.QYBH AND T1.CHKDBH=T4.CHKDBH AND T1.SHPBH=T4.SHPBH AND T1.PIHAO=T4.PIHAO AND T1.SHCHRQ=T4.SHCHRQ ),0))SHYSHL"	.
	           ", '金额' AS T1JINE,'含税金额' AS T1HSHJINE,T5.DIZHI,T5.DHHM,TO_CHAR(T3.KPRQ ,'YYYY-MM-DD') T3KPRQ ,T5.XSHDBH,TO_CHAR(T5.KPRQ,'YYYY-MM-DD') T5KPRQ ,T1.DANJIA,T1.HSHJ,T1.KOULV,'税额' AS T1SHUIE,T1.XUHAO ".
		       " FROM H01DB012101 T2,H01DB012603 T1 LEFT OUTER JOIN H01DB012408 T3 ON T1.CHKDBH=T3.CHKDBH LEFT OUTER JOIN H01DB012201 T5 ON T3.CKDBH=T5.XSHDBH LEFT OUTER JOIN H01DB012106 T4 ON T3.DWBH=T4.DWBH".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=T3.QYBH  AND T1.QYBH=:QYBH ".
			   " AND T1.SHPBH=T2.SHPBH".
	           " AND T1.PSDH=:PSDH ";
	    
	    
		$recs = $this->_db->fetchAll ( $sql, $bind );
	    $recsa=Array();
		foreach ($recs as $key=>$val){
			$val['T1JINE']=floatval(($val['SHULIANG']*$val['DANJIA'])*($val['KOULV']/100));
			$val['T1HSHJINE']=floatval(($val['SHULIANG']*$val['HSHJ'])*($val['KOULV']/100));
			$val['T1SHUIE']=$val['T1HSHJINE']-$val['T1JINE'];
			$val['KZZSHL']=$val['SHYSHL']+$val['SHULIANG'];//得到可操作的总数量
			$recsa[]=$val;
		}
		return Common_Tool::createXml ( $recsa, true);
	}
	 /*
	 * 配送单信息（xml格式）//得到出库单列表  
	 */
	function getxxlist2($filter){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter ['psdh']; //区域编号
	    $sql = "SELECT T1.CHKDBH,TO_CHAR(T1.KPRQ,'yyyy-MM-dd'),T2.XSHDBH,TO_CHAR(T2.KPRQ,'yyyy-MM-dd') T2KPRQ,T4.DWMCH,T2.DHHM,T2.DIZHI,T5.SHFDSHHK,T5.JINE,T5.HSHJE,T5.DHKE,T6.YGXM,TO_CHAR(T5.QSHRQ,'yyyy-MM-dd') T5QSHRQ,T5.BEIZHU".
		       " FROM H01DB012602 T5 LEFT OUTER JOIN H01DB012113 T6 ON T6.YGBH=T5.QSHR, H01DB012408 T1,H01DB012201 T2 LEFT OUTER JOIN H01DB012106 T4 ON T2.DWBH=T4.DWBH ".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=T4.QYBH  AND T1.QYBH=:QYBH AND T1.QYBH=T5.QYBH".
			   " AND T1.CKDBH=T2.XSHDBH".
			   " AND T1.CHKDBH=T5.CHKDBH".
	  		   " AND T5.PSDH=:PSDH".
			   " AND T2.SHFPS='1'";
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return Common_Tool::createXml ( $recs, true);
		
		
	}
	 /*
	 * 配送单信息（xml格式）//获得商品的详细情况
	 */
	function getxxlist3($filter){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['PSDH'] =$filter ['psdh']; //区域编号
	    $sql = "SELECT T1.CHKDBH,T1.SHPBH,T2.SHPMCH,T2.GUIGE,T1.PIHAO,T1.SHCHRQ,T1.BZHSHL,T1.LSSHL,T1.SHULIANG,T1.DANJIA,T1.HSHJ ".
		       " FROM H01DB012603 T1 LEFT OUTER JOIN H01DB012101 T2 ON T2.SHPBH=T1.SHPBH".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=:QYBH ".
			   " AND T1.SHPBH=T2.SHPBH".
	           " AND T1.PSDH=:PSDH ";
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return Common_Tool::createXml ( $recs, true);
		
		
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {

		if ($_POST ["PSRQ"] == "" || //开票日期
            $_POST ["PSLX"] == "" || //配送类型
            $_POST ["CHYRMCH"] == "" || //承运方名称
            $_POST ["#grid_mingxi"] == ""||
            $_POST ["#grid_mingxi2"] == "") { //明细表格
			return false;
		}
			return true;
	}
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
		return true;
	}
	/*
	 * 删除原有的记录数据
	 */
	function  delold_date($psdscbh){
		// 创建一个 $db对象, 然后...
		// 设定需要删除数据的表
		if (!get_magic_quotes_gpc()) {
		    $qybh=addslashes($_SESSION ['auth']->qybh);
		} else {
		    $qybh=$_SESSION ['auth']->qybh;
		}
		if (!get_magic_quotes_gpc()) {
		   $psdscbh=addslashes($psdscbh);
		} else {
		    $qybh=$psdscbh;
		}
		$sql="DELETE FROM H01DB012602 where QYBH='{$qybh}' AND PSDH='{$psdscbh}'";
		$this->_db->query($sql);
		
		$sql="DELETE FROM H01DB012603 where QYBH='{$qybh}' AND PSDH='{$psdscbh}'";
		$this->_db->query($sql);
		
		
	}
	
	/*
	 * 配送保存
	 */
	public function savePshdMain($psdscbh){
			$sql='UPDATE H01DB012601 SET PSRQ=TO_DATE(:PSRQ,\'YYYY-MM-DD\'),PSZHT=:PSZHT,PSLX=:PSLX,CHYRMCH=:CHYRMCH,CHPHM=:CHPHM'.
				' ,LXRXM=:LXRXM,LXDZH=:LXDZH,LXDH=:LXDH,FCHRQ=TO_DATE(:FCHRQ,\'YYYY-MM-DD\'),DDRQ=TO_DATE(:DDRQ,\'YYYY-MM-DD\')'.
				',ZSHL=:ZSHL,ZPSH=:ZPSH,HKZE=:HKZE,BEIZHU=:BEIZHU,QXBZH=:QXBZH,BGRQ=TO_DATE(:BGRQ,\'YYYY-MM-DD\')'.
			    ',BGZH=:BGZH WHERE QYBH=:QYBH AND PSDH=:PSDH';
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['PSDH'] = $psdscbh; //配送单号
			$data ['PSRQ'] =$_POST['PSRQ']; //配送日期
			$data ['PSZHT'] = $_POST['PSZHT']; //配送状态
			$data ['PSLX'] = $_POST['PSLX']; //配送类型
			$data ['CHYRMCH'] = $_POST['CHYRMCH']; //承运方名称
			$data ['CHPHM'] = $_POST['CHPHM']; //车牌号码
			$data ['LXRXM'] = $_POST['LXRXM']; //联系人员
			$data ['LXDZH'] = $_POST['LXDZH']; //联系地址
			$data ['LXDH'] = $_POST['LXDH'];  //联系电话
			$data ['FCHRQ'] =$_POST['FCHRQ']; //发车日期
			$data ['DDRQ'] =$_POST['DDRQ'];  //到达日期
			$data ['ZSHL'] = $_POST['ZSHL']; //总数量
			$data ['ZPSH'] = $_POST['ZPSH']; //总票数
			$data ['HKZE'] = $_POST['HKZE']; //货款总额
			$data ['DHKE'] = $_POST['DHKE']; //带回总额
			$data ['BEIZHU'] = $_POST['BEIZHU']; //备注
			$data ['QXBZH'] = '0';//取消标志
			$data ['BGRQ'] = date('Y-m-d'); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			//销售订单明细表
			$this->_db->query( $sql, $data );	
	}
	/*
	 * 配送单明细保存
	 */
	public function savePsMingX($psdscbh){
			foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			    $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$data ['PSDH'] = $psdscbh; //配送单号
				$data ['CHKDBH'] =$grid [$this->idx_CHHBH]; //出库单号
				$data ['SHFDSHHK'] = $grid [$this->idx_DSHHK]; //是否代收货款
				$data ['YSHJE'] = $grid [$this->idx_HSHJE]; //应收金额目前和含税金额相同
				$data ['DHKE'] = 0; //带回金额 默认存储为0数字类型
				$data ['JINE'] = (empty($grid [$this->idx_JINE])) ? 0 : $grid [$this->idx_JINE]; //金额
				$data ['HSHJE'] = (empty($grid [$this->idx_HSHJE])) ? 0 : $grid [$this->idx_HSHJE]; //含税金额
				$data ['SHUIE'] = (empty($grid [$this->idx_SHUIE])) ? 0 : $grid [$this->idx_SHUIE]; //税额
				$data ['QSHZHT'] ='0';  //签收状态0 表示为签收 1表示已签收
				$data ['BEIZHU'] = $grid [$this->idx_BZH]; //备注
				$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
				$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者				$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
				//配送单明细表
				$this->_db->insert ( "H01DB012602", $data );	
			}
			
	}
	
    /*
	 * 配送商品单明细保存
	 */
	public function savePsspMingX($psdscbh){		
			foreach ( $_POST ["#grid_mingxi2"] as $grid ) {
			    $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$data ['PSDH'] = $psdscbh; //配送单号
				$data ['CHKDBH'] =$grid [$this->g3idx_CHHBH]; //出库单号
				$data ['SHPBH'] = $grid [$this->g3idx_SHPBH]; //商品编号
				$data ['XUHAO'] = $grid [$this->g3idx_XUHAO]; //序号
				$data ['PIHAO'] = $grid [$this->g3idx_PIHAO]; //批号
				$data ['SHCHRQ'] =new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->g3idx_SHCHRQ]. "','YYYY-MM-DD')" ); //生产日期
				$data ['BZHSHL'] = (empty($grid [$this->g3idx_BZHSHL])) ? 0 :$grid [$this->g3idx_BZHSHL]; //包装数量
				$data ['LSSHL'] = (empty($grid [$this->g3idx_LSSHL])) ? 0 :$grid [$this->g3idx_LSSHL]; //零散数量
				$data ['SHULIANG'] =(empty($grid [$this->g3idx_SHULIANG])) ? 0 :$grid [$this->g3idx_SHULIANG];  //数量
				$data ['DANJIA'] =(empty($grid [$this->g3idx_DANJIA])) ? 0 :$grid [$this->g3idx_DANJIA];  //单价
				$data ['HSHJ'] =empty($grid [$this->g3idx_HSHJ]) ? 0 :$grid [$this->g3idx_HSHJ];  //含税价
				$data ['KOULV'] =empty($grid [$this->g3idx_KOULV]) ? 0 :$grid [$this->g3idx_KOULV];  //扣率
				$data ['BEIZHU'] = $grid [$this->g3idx_BEIZHU]; //备注
				$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
				$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者				$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
				//配送单明细表
				$this->_db->insert ( "H01DB012603", $data );	
				
			}
	}
	/*配送单回执确认-执行函数*/
/**************************************************
 '　　[函数名]：updaehzqr_psd
 '　　[功  能]：更新配送单
 '　　[参  数]：str（配送单号）
 '　　[返回值]：无
 '**************************************************/
	public  function updaehzqr_psd($psdscbh){
		$bind['QYBH']=$_SESSION ['auth']->qybh; //区域编号
		$bind['PSDH']=$psdscbh; //区域编号
		$bind['BGZH']=$_SESSION ['auth']->userId; //变更者
		$bind['PSZHT']=$_POST['PSZHT']; //配送状态
		$bind['BGRQ']=date('Y-m-d'); //变更日期
		$sql="UPDATE H01DB012601 SET PSZHT=:PSZHT,BGRQ=TO_DATE(:BGRQ,'YYYY-MM-DD'),BGZH=:BGZH WHERE QYBH=:QYBH AND PSDH=:PSDH";
		$this->_db->query( $sql, $bind );	
		
	}
/**************************************************
 '　　[函数名]：updaehzqr_psdmx
 '　　[功  能]：配送单明细更新
 '　　[参  数]：str（配送单号）
 '　　[返回值]：无
 '**************************************************/
	public  function updaehzqr_psdmx($psdscbh){
		$bind['QYBH']=$_SESSION ['auth']->qybh; //区域编号
		$bind['PSDH']=$psdscbh; //配送单号
		$bind['BGZH']=$_SESSION ['auth']->userId; //变更者
		$bind['BGRQ']=date('Y-m-d'); //变更日期
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			$bind['CHKDBH']=$grid [$this->idx_hzqrchkdh]; //出库单号
			$bind['DHKE']=$grid [$this->idx_hzqrdhke]; //带回款额
			if(!empty($grid [$this->idx_hzqrqshr])){
				$bind['QSHR']=$grid [$this->idx_hzqrqshr]; //签收人
				$bind['QSHZHT']=1;
				$bind['QSHRQ']=$grid [$this->idx_hzqrqshrq]; //签收人日期
				$addsql="QSHR=:QSHR,QSHZHT=:QSHZHT,QSHRQ=TO_DATE(:QSHRQ,'YYYY-MM-DD'),";
				$sql="UPDATE H01DB012602 SET DHKE=:DHKE, {$addsql} BGRQ=TO_DATE(:BGRQ,'YYYY-MM-DD'),BGZH=:BGZH WHERE QYBH=:QYBH AND PSDH=:PSDH AND CHKDBH=:CHKDBH";
				$this->_db->query( $sql, $bind );	
			}else{
				unset($bind['QSHZHT']);
				unset($bind['QSHRQ']);
				unset($bind['QSHR']);
				$addsql='';
			}
			
			
		}
		
		
		
		
	}
/**************************************************
 '　　[函数名]：updaehzqr_saleorder
 '　　[功  能]：更新销售单
 '　　[参  数]：str（配送单号）
 '　　[返回值]：无
 '**************************************************/
	public  function updaehzqr_saleorder($psdscbh){
		$bind['QYBH']=$_SESSION ['auth']->qybh; //区域编号
		$bind['BGZH']=$_SESSION ['auth']->userId; //变更者
		$bind['BGRQ']=date('Y-m-d'); //变更日期
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			$bind['XSHDBH']=$grid [$this->idx_hzqrqshr]; //销售单号
			if(!empty($grid [$this->idx_hzqrqshr])){
				$bind['XSHDZHT']='3'; //销售单状态
				$addsql='XSHDZHT=：XSHDZHT,';
				$sql="UPDATE H01DB012602 SET  {$addsql} BGRQ=TO_DATE(:BGRQ,'YYYY-MM-DD'),BGZH=:BGZH WHERE QYBH=:QYBH AND XSHDBH=:XSHDBH";
				$this->_db->query( $sql, $bind );		
			}else{
				unset($bind['XSHDZHT']);
				$addsql='';
			}
		}
	}
	/*配送单回执确认-执行函数-end*/
}
	