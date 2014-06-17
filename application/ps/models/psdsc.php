<?php
/*********************************
 * 模块：   配送模块(PS)
 * 机能：   配送订单生成(psdsc)
 * 作成者：梁兆新
 * 作成日：2011/1/7
 * 更新履历：
 *********************************/
class ps_models_psdsc extends Common_Model_Base {
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

	
	/*
	 * 得到发获取信息
	 */
	function getquhao(){
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$sql='SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH=:QYBH AND FHQZHT=\'1\'';
		$recs = $this->_db->fetchAll ($sql,$bind);
		return $recs;
		
	}
	
	/*
	 * 列表数据取得（xml格式）//出库信息
	 */
	function getListData($filter) {
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		//检索SQL
		$sql = "SELECT T1.CHKDBH,to_char(T1.KPRQ,'yyyy-MM-dd'),T2.XSHDBH,to_char(T2.KPRQ,'yyyy-MM-dd') T2KPRQ,T4.DWMCH,T2.DIZHI,T2.DHHM,".
				"(SELECT SUM(JINE)FROM H01DB012409 T3 WHERE T1.CHKDBH=T3.CHKDBH) AS JINE,".
				"(SELECT SUM(HSHJE)FROM H01DB012409 T3 WHERE T1.CHKDBH=T3.CHKDBH) AS HSHJE, ".
				"T1.BEIZHU ".
		       " FROM H01DB012408 T1,H01DB012201 T2 LEFT OUTER JOIN H01DB012106 T4 ON T2.DWBH=T4.DWBH ".
		       " WHERE T1.QYBH=T2.QYBH AND T1.QYBH=T4.QYBH  AND T1.QYBH=:QYBH ".
			   " AND T1.CKDBH=T2.XSHDBH".
			   " AND T2.SHFPS='1'";
				//不确定 t1表中没有是否标志的字段			   "AND T1.QXBZH='1'".
		//出库编号
		if(!empty($filter['fhqbh'])){
			$sql .= " AND T1.FHQBH=:FHQBH";
			$bind['FHQBH'] =$filter['fhqbh']; //出库编号
		}
		//开始日期
		if(!empty($filter['stime'])){
			$sql .= " AND T1.KPRQ>=TO_DATE(:STIME,'YYYY-MM-DD')";
			$bind['STIME']=$filter['stime']; 
			
		}
		//终止日期
		if(!empty($filter['etime'])){
			$sql .= " AND T1.KPRQ<=TO_DATE(:ETIME,'YYYY-MM-DD')";
			$bind['ETIME'] =$filter['etime']; 
		}
		//当前页数据
		
		$recs = $this->_db->fetchAll ($sql,$bind);
		Common_Logger::logMessage($sql);
		return Common_Tool::createXml ($recs);
	}
	/*
	 * 列表数据取得（xml格式）//待配出库单明细信息
	 */
	function getdpsListData($filter) {
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
		$bind['CHKDBH'] =$filter['chdh']; //出库编号
		$bind['DWMCH'] =$filter['dwmch']; //单位名称
		$bind['DIZHI'] =$filter['dizhi']; //地址
		$bind['DIANHUA'] =$filter['dianhua']; //电话
		$bind['CHKRQ'] =$filter['chkrq']; //出库日期
		$bind['XSHDH'] =$filter['xshdh']; //销售单号
		$bind['XSHRQ'] =$filter['xshrq']; //销售日期
		
		
		//检索SQL
		$sql = "SELECT '',:CHKDBH,T1.SHPBH,T2.SHPMCH,T2.GUIGE,T3.NEIRONG,T1.PIHAO,to_char(T1.SHCHRQ,'yyyy-MM-dd'),'' AS BZHSHL,'' AS LSSHL,".
				"((SELECT SUM(T5.SHULIANG) FROM H01DB012409 T5 WHERE T1.QYBH=T5.QYBH AND T1.CHKDBH=T5.CHKDBH AND T1.SHPBH=T5.SHPBH AND T1.PIHAO=T5.PIHAO AND T1.SHCHRQ=T5.SHCHRQ )-".
				"NVL((SELECT SUM(T4.SHULIANG) FROM H01DB012603 T4 WHERE T1.QYBH=T4.QYBH AND T1.CHKDBH=T4.CHKDBH AND T1.SHPBH=T4.SHPBH AND T1.PIHAO=T4.PIHAO AND T1.SHCHRQ=T4.SHCHRQ ),0))DPSHULIANG".
			   ",T1.BEIZHU,T2.JLGG,:DWMCH AS T4DWMCH,'' AS SHYSHL,T1.JINE,T1.HSHJE,:DIZHI AS DIZHI,:DIANHUA AS DIANHUA,:CHKRQ AS CHKRQ,:XSHDH AS XSHDH,:XSHRQ AS XSHRQ,T1.DANJIA AS T1DANJIA,T1.HSHJ AS T1HSHJ,T1.KOULV AS T1KOULV,T1.SHUIE AS T1SHUIE,T1.XUHAO AS T1XUHAO,'可操作数量' AS T1KZZSHL".
		       " FROM H01DB012409 T1".
			   " LEFT JOIN H01DB012101 T2 ON T1.QYBH = T2.QYBH AND T1.SHPBH = T2.SHPBH ".
		       " LEFT JOIN H01DB012001 T3 ON T2.BZHDWBH=T3.ZIHAOMA AND T3.CHLID='DW' ".
		       " WHERE T1.QYBH=:QYBH ".
			   " AND T1.CHKDBH=:CHKDBH".
		
		//当前页数据
		
		$recs = $this->_db->fetchAll ($sql,$bind);
		$recsa=Array();
		foreach ($recs as $key=>$val){
			$val['BZHSHL']=intval($val['DPSHULIANG']/$val['JLGG']);
			$val['LSSHL']=$val['DPSHULIANG']%$val['JLGG'];
			$val['YSHZH']=$val['DPSHULIANG'];
			$val['SHYSHL']=$val['DPSHULIANG'];
			$val['T1KZZSHL']=$val['DPSHULIANG'];
			
			$val['JINE']=$val['DPSHULIANG']*$val['T1DANJIA']*($val['T1KOULV']/100);
			$val['HSHJE']=$val['DPSHULIANG']*$val['T1HSHJ']*($val['T1KOULV']/100);
			$val['T1SHUIE']=$val['HSHJE']-$val['JINE'];
			$recsa[]=$val;
		}
		Common_Logger::logMessage($sql);
		return Common_Tool::createXml ($recsa);
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
	 * 配送保存
	 */
	public function savePshdMain($psdscbh){
		    $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['PSDH'] = $psdscbh; //配送单号
			$data ['PSRQ'] =new Zend_Db_Expr ( "TO_DATE('" . $_POST['PSRQ']. "','YYYY-MM-DD')" ); //配送日期
			$data ['PSZHT'] = $_POST['PSZHT']; //配送状态
			$data ['PSLX'] = $_POST['PSLX']; //配送类型
			$data ['CHYRMCH'] = $_POST['CHYRMCH']; //承运方名称
			$data ['CHPHM'] = $_POST['CHPHM']; //车牌号码
			$data ['LXRXM'] = $_POST['LXRXM']; //联系人员
			$data ['LXDZH'] = $_POST['LXDZH']; //联系地址
			$data ['LXDH'] = $_POST['LXDH'];  //联系电话
			$data ['FCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['FCHRQ']. "','YYYY-MM-DD')" ); //发车日期
			$data ['DDRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST['DDRQ']. "','YYYY-MM-DD')" );  //到达日期
			$data ['ZSHL'] = $_POST['ZSHL']; //总数量
			$data ['ZPSH'] = $_POST['ZPSH']; //总票数
			$data ['HKZE'] = $_POST['HKZE']; //货款总额
			$data ['DHKE'] = $_POST['DHKE']; //带回总额
			$data ['ZHDR'] = $_SESSION ['auth']->userId;//制单人
			$data ['BEIZHU'] = $_POST['BEIZHU']; //备注
			$data ['QXBZH'] = '0';//取消标志
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//销售订单明细表
			$this->_db->insert ( "H01DB012601", $data );	
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
	
	
	
	
}
	