<?php
/*********************************
 * 模块：仓储模块(cc)
 * 机能：库间调拨出库(kjdbck)
 * 作成者：sunmingming
 * 作成日：2010/12/27
 * 更新履历：

 *********************************/
class cc_models_kjdbck extends Common_Model_Base {
	private $idx_ROWNUM=0;// 行号
	private $idx_SHPBH=1;// 商品编号
	private $idx_SHPMCH=2;// 商品名称
	private $idx_GUIGE=3;// 规格
	private $idx_BZHDWBH=4;// 包装单位编号
	private $idx_DCHCK=5;// 调出仓库
	private $idx_PIHAO=6;// 批号
	private $idx_SHCHRQ=7;// 生产日期
	private $idx_BZHQZH=8;// 保质期至
	private $idx_BZHSHL=9;// 包装数量
	private $idx_LSSHL=10;// 零散数量
	private $idx_SHULIANG=11;// 数量
	private $idx_ZENGPIN=12;// 赠品
	private $idx_CHANDI=13;// 产地
	private $idx_BEIZHU=14;// 备注
	private $idx_BZHDWBH_H=15;// 包装单位编号_H
	private $idx_DCHCK_H=16;// 调出仓库_H
	private $idx_SHFSHKW=17; //是否为散货库存
	
	private $idx_CKBH_H = 18;//仓库编号
	private $idx_KQBH_H = 19;//库区编号
	private $idx_KWBH_H = 20;//库位编号
	private $idx_KWSHULIANG = 21; //在库数量
	/*
	 * 根据商品编号取得商品信息
	 */
	public function getShangpinInfo($filter) {
	
		//检索SQL
		$sql = "SELECT " . " A.SHPBH,". //商品编号
		" A.SHPMCH,". //商品名称
		" A.GUIGE,". //商品规格
		" A.BEIZHU,". //备注
		" A.CHANDI,". //产地
		" A.BZHDWBH,". //包装单位编号
		" A.BZHDWMCH,". //包装单位名称
		" A.JLGG". //计量规格
		" FROM H01VIEW012001 A " . //商品指定客户信息
		" WHERE A.QYBH = :QYBH " . " AND A.SHPBH = :SHPBH " . " AND A.SHPZHT = '1'";
		
		$bind ['SHPBH'] = $filter ['shpbh']; //商品编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	 /* 根据仓库、库区、库位编号获得库位状态
	
	 */
	public function getKwInfo($filter) {
	
		//检索SQL
		$sql = "SELECT " .
		" H1.KWZHT,". //库位状态
		
		" H2.CKZHT,". //仓库状态
		
		" H3.KQZHT". //库区状态
		
		" FROM H01DB012403 H1 ".
		" LEFT JOIN H01DB012401 H2 ON H1.QYBH =H2.QYBH And H1.CKBH =H2.CKBH " . 
		" LEFT JOIN H01DB012402 H3 ON H1.QYBH =H3.QYBH And H1.CKBH =H3.CKBH And H1.KQBH =H3.KQBH " .
		" WHERE H1.QYBH = :QYBH " . " AND H1.CKBH = :CKBH " . " AND H1.KQBH = :KQBH AND H1.KWBH = :KWBH ";
		
		$bind ['CKBH'] = $filter ['ckbh']; //仓库编号
		$bind ['KQBH'] = $filter ['kqbh']; //库区编号
		$bind ['KWBH'] = $filter ['kwbh']; //库位编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
	
		//必须输入项
		 $arrInput = array("KPRQ","BMBH","YWYBH","DCHCK");
		
		foreach($arrInput as $input){
			if ($_POST [$input] == "") {
				return false;
			}
		}
		 //明细必须输入项
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
			}
		}
		//一条明细也没有输入
		if (! $isHasMingxi) {
			return false;
		}
		return true;
	}
	
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck() {
	
		$idx = 1; //序号自增
		//循环所有明细行，保存明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			//(1)以明细信息项目调出库位为条件，抽取仓库/库区/库位表。
			$filter ['ckbh'] = $grid [$this->idx_CKBH_H] ; //仓库编号
			$filter ['kqbh'] = $grid [$this->idx_KQBH_H] ; //库区编号
			$filter ['kwbh'] = $grid [$this->idx_KWBH_H] ; //库位编号
			
			//a.判断对象库位状态，如果有处于删除状态的库位（库位状态 = X：删除），则弹出警告信息。
			$ret = $this->getKwInfo($filter);
			
			if ($ret['KWZHT'] == "X") {
			
			return '2';
		}
		//b.判断对象调出仓库/库区/库位状态，如果有处于冻结状态的仓库/库区/库位（状态 = 0：冻结 9：盘点冻结），则弹出警告信息。
		if (($ret['KWZHT']="0" || $ret['KWZHT']=="9")||($ret['CKZHT']="0" || $ret['CKZHT']=="9")||($ret['KQZHT']="0" || $ret['KQZHT']=="9")){
		
		return '3';
		 }
		$idx ++ ;
		}
		
		
		return '0';
	}
	/*
	 * 主表保存
	 */
	public function saveMain($autobh) {
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DJBH'] = $autobh; //单据编号
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期 
		$data ['YWYBH'] = $_POST ['YWYBH_H']; //业务员编号
		$data ['BMBH'] = $_POST ['BMBH_H']; //部门编号
		$data ['DCHCK'] = $_POST ['DCHCK_H']; //调出仓库
		$data ['DRCK'] = $_POST ['DRCK_H']; //调入仓库
		$data ['DRCKDZH'] = $_POST ['DRCKDZH']; //调入仓库地址
		$data ['SHFPS'] = $_POST ['SHFPS']; //是否配送
		$data ['DHHM'] = $_POST ['DHHM']; //电话号码
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['CHKDZHT'] = '1';//出库单状态
		$data ['SHLHJ'] = str_replace(",","",$_POST ['SHLHJ']); //数量合计
		$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者 
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//主表保存
		return $this->_db->insert ( "H01DB012410", $data );
	}
	
	/*
	 * 明细保存
	 */
	public function saveMingxi($autobh) {
		$idx = 1; //序号自增
		//循环所有明细行，保存明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == '')continue;
			
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['DJBH'] = $autobh; //编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idx_SHPBH]; //商品编号
			//$data ['GUIGE'] = $grid [$this->idx_GUIGE]; //规格
			$data ['BZHDWBH'] = $grid [$this->idx_BZHDWBH_H]; //包装单位
			$data ['DCHKQ'] = $grid [$this->idx_KQBH_H]; //调出库区
			$data ['DCHKW'] = $grid [$this->idx_KWBH_H]; //调出库位
			
			$data ['PIHAO'] = $grid [$this->idx_PIHAO]; //批号
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" ); //生产日期
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH]   . "','YYYY-MM')" );  //保质期至
			$data ['BZHSHL'] = ($grid [$this->idx_BZHSHL] == null) ? 0 : $grid [$this->idx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idx_LSSHL] == null) ? 0 : $grid [$this->idx_LSSHL]; //零散数量
			$data ['SHULIANG'] = $grid [$this->idx_SHULIANG]; //数量
			$data ['WSHHSHL'] = $data ['SHULIANG']; //未收货数量
			$data ['THSHL'] = 0; //退货数量
			$data ['THZHSHL'] = 0; //退货中数量
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
	
			
			//明细表
			$this->_db->insert ( "H01DB012411", $data );

		}
	}
	/*
	 * 保存处理
	 */
	public function updateKucun($autobh) {
		$result ['status'] = '0';
		
		//循环所有明细行进行库存数量检验
		foreach ( $_POST ["#grid_mingxi"] as $row ) {
			if ($row [$this->idx_SHPBH] == '')continue;
			//取得即时库存信息
			$sql = "SELECT A.QYBH,A.CKBH,A.KQBH,A.KWBH,A.SHPBH,A.PIHAO,A.RKDBH,A.ZKZHT,A.BZHDWBH,A.SHULIANG,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH " . 
			 "FROM H01DB012404 A " .
			 "WHERE A.QYBH = :QYBH" . //区域编号
			 " AND A.CKBH = :CKBH " . //仓库编号
			 " AND A.KQBH = :KQBH " . //库区编号
			 " AND A.KWBH = :KWBH " . //库位编号
			 " AND A.SHPBH = :SHPBH " . //商品编号
			 " AND A.PIHAO = :PIHAO " . //批号
			 " AND A.ZKZHT IN ('0','1')" . //在库状态
			 " AND A.BZHDWBH = :BZHDWBH " . //包装单位
			 " AND A.SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') " . //生成日期
			 " AND A.SHULIANG > 0 " . //数量
			 " ORDER BY ZKZHT DESC,RKDBH" . //在库状态 降序，入库单升序
			 " FOR UPDATE OF A.SHULIANG WAIT 10"; //对象库存数据锁定
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $row [$this->idx_CKBH_H];
			$bind ['KQBH'] = $row [$this->idx_KQBH_H];
			$bind ['KWBH'] = $row [$this->idx_KWBH_H];
			$bind ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind ['PIHAO'] = $row [$this->idx_PIHAO];
			$bind ['BZHDWBH'] = $row [$this->idx_BZHDWBH_H];
			$bind ['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			
			//当前明细行在库信息
			$recs = $this->_db->fetchAll ( $sql, $bind );
			$shuliang_zaiku = 0; //累计在库数量
			foreach ( $recs as $rec ) {
				$shuliang_zaiku += ( int ) $rec ['SHULIANG'];
			}
			
			//当前库存数量不足
			if ($shuliang_zaiku < ( int ) $row [$this->idx_SHULIANG]) {
				$result ['status'] = '1'; //库存不足
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				$result ['data']['shuliang'] = $shuliang_zaiku; //最新在库数量
				$kucunModel = new gt_models_kucun ( );//库存不足时取得最新库存数据，返回页面用
				$result ['data']['kucundata'] = $kucunModel->getKucunData ( array('shpbh'=>$row [$this->idx_SHPBH],'shfshkw'=> $row [$this->idx_SHFSHKW]));
			}
			
			//库存数量充足
			if($result['status']=='0'){
			//更新在库和移动履历信息
				$this->updateZaiku ( $row, $recs, $autobh );
			}
		}
		
		return $result;
	}
	/*
	 * 更新在库和移动履历信息
	 */
	public function updateZaiku($row,$kucuns, $autobh) {
		//同一货位批号 按照催销，先入先出（入库单）原则进行分摊出库
		$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG]; //数量
		$idx = 0; //在库移动履历序号
		foreach ( $kucuns as $kucun ) {
			$shuliang = 0; //在库更新数量
			
			//部分出库时 
			if ($shuliang_shengyu <= ( int ) $kucun ['SHULIANG']) {
				$shuliang = ( int ) $kucun ['SHULIANG'] - $shuliang_shengyu;
				$shuliang_yidong = $shuliang_shengyu;
				$shuliang_shengyu = 0;
				
			} else { //全部出库
				$shuliang = 0;
				$shuliang_yidong = ( int ) $kucun ['SHULIANG'];
				$shuliang_shengyu = $shuliang_shengyu - ( int ) $kucun ['SHULIANG'];
			}
			
			//更新在库信息
			$sql_zaiku = "UPDATE H01DB012404 ".
			 "SET SHULIANG = :SHULIANG " .
			 (($shuliang == 0) ? ",ZZHCHKRQ = SYSDATE " : "").
			 " WHERE QYBH = :QYBH ".
			 " AND CKBH = :CKBH " .
			 " AND KQBH = :KQBH ".
			 " AND KWBH = :KWBH ".
			 " AND SHPBH = :SHPBH " .
			 " AND PIHAO = :PIHAO " .
			 " AND ZKZHT = :ZKZHT " .
			 " AND RKDBH = :RKDBH " .
			 " AND SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') " . //生成日期
			 " AND BZHDWBH = :BZHDWBH";
			 
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $kucun ['CKBH']; 
			$bind ['KQBH'] = $kucun ['KQBH']; 
			$bind ['KWBH'] = $kucun ['KWBH']; 
			$bind ['SHPBH'] = $kucun ['SHPBH']; 
			$bind ['PIHAO'] = $kucun ['PIHAO']; 
			$bind ['BZHDWBH'] = $kucun ['BZHDWBH']; 
			$bind ['RKDBH'] = $kucun ['RKDBH']; 
			$bind ['ZKZHT'] = $kucun ['ZKZHT'];
			$bind ['SHCHRQ'] = $kucun['SHCHRQ'];
			$bind ['SHULIANG'] = $shuliang; 
			$this->_db->query ( $sql_zaiku,$bind );
			
			//生成在库移动履历
			$lvli ["QYBH"] = $_SESSION ['auth']->qybh; //区域编号
			$lvli ["CKBH"] = $kucun ['CKBH']; //仓库编号
			$lvli ["KQBH"] = $kucun ['KQBH'];; //库区编号
			$lvli ["KWBH"] = $kucun ['KWBH'];; //库位编号
			$lvli ["SHPBH"] = $kucun ['SHPBH'];; //商品编号
			$lvli ["PIHAO"] = $kucun ['PIHAO'];; //批号
			$lvli ["RKDBH"] = $kucun ['RKDBH']; //入库单号
			$lvli ["YDDH"] = $autobh; //移动单号
			$lvli ["XUHAO"] = $idx ++; //序号
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$kucun['SHCHRQ']."','YYYY-MM-DD')"); //生产日期
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$kucun['BZHQZH']."','YYYY-MM')"); //保质期至
			$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
			$lvli ["SHULIANG"] = $shuliang_yidong * - 1; //移动数量
			$lvli ["ZHYZHL"] = '31'; //转移种类 [出库]
			$lvli ["BZHDWBH"] = $kucun ['BZHDWBH']; //包装单位编号
			$lvli ["BEIZHU"] = $autobh; //备注
			$lvli ["ZKZHT"] = $kucun ['ZKZHT'];//在库状态
			$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
			$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( 'H01DB012405', $lvli );
			
			//所有数量均出库完毕，不再继续循环
			if ($shuliang_shengyu <= 0) break;
			}
		}
	}
?>