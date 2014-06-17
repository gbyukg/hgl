<?php
/*********************************
 * 模块：    仓储模块(cc)
 * 机能：    库间调拨返库(kjdbfk)
 * 作成者：姚磊
 * 作成日：2010/12/27
 * 更新履历：

 *********************************/
class cc_models_kjdbfk extends Common_Model_Base {


		private $idxx_ROWNUM=0;// 行号
		private $idxx_SHPBH=1;// 商品编号
		private $idxx_SHPMCH=2;// 商品名称
		private $idxx_GUIGE=3;// 规格
		private $idxx_BZHDWBH=4;// 包装单位
		private $idxx_PIHAO=5;// 批号
		private $idxx_SHCHRQ=6;// 生产日期
		private $idxx_BZHQZH=7;// 保质期至
		private $idxx_JLGG = 8;// 计量规格
		private $idxx_BZHSHL=9;// 包装数量
		private $idxx_LSSHL=10;// 零散数量
		private $idxx_SHULIANG=11;// 数量
		private $idxx_WSHHSHL=12; //未收货数量
		private $idxx_CHANDI=13;// 产地
		private $idxx_BEIZHU=14;// 备注
		private $idxx_TONGYONGMING = 15; // 通用名
		private $idxx_DCHKW=16;// 调出库位编号
		private $idxx_DRKW = 17;//调入库位编号
		private $idxx_DCHKQ = 18;//调出库区编号
		private $idxx_DRKQ = 19;//调入库区编号
		private $idxx_BZHDW=20;// 包装单位编号_
		private $idxx_SHFSHKW=21; //是否为散货库存
		
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
		" A.JLGG ," . //计量规格
		" A.BZHDWMCH,". //包装单位编号
		" A.TYMCH " . //通用名
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

		" H2.CKZHT,". //库位状态

		" H3.KQZHT". //库位状态

		" FROM H01DB012403 H1 ".
		" LEFT JOIN H01DB012401 H2 ON  H1.QYBH =  H2.QYBH And  H1.CKBH =  H2.CKBH " . 
		" LEFT JOIN H01DB012402 H3 ON  H1.QYBH =  H3.QYBH And  H1.CKBH =  H3.CKBH And  H1.KQBH =  H3.KQBH " .
		" WHERE A.QYBH = :QYBH " . " AND H1.CKBH = :CKBH " . " AND H1.KQBH = :KQBH AND H1.KWBH = :KWBH ";

		$bind ['CKBH'] = $filter ['ckbh']; //仓库编号
		$bind ['KQBH'] = $filter ['kqbh']; //库区编号
		$bind ['KWBH'] = $filter ['kwbh']; //库位编号
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		

		return $this->_db->fetchRow ( $sql, $bind );
	}
	/*
	 * 对应调拨出库单双击单据
	 */
	
	public function savedjxx($kjdbfkbh,$dydbchkd){
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DJBH'] = $kjdbfkbh; //单据编号		
		$data ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$data ['DYDBCHKD'] = $dydbchkd;//对应调拨出库单号
		$data ['BMBH'] = $_POST ['BMBH']; //部门编号
		$data ['YWYBH'] = $_POST ['YWYBH']; //业务员编号
		$data ['DCHCK'] = $_POST ['DCHCKBH']; //调出仓库
		$data ['DRCK'] = $_POST ['DRCKBH']; //调入仓库
		$data ['DRCKDZH'] = $_POST ['DRCKDZH']; //调入仓库地址
		$data ['DHHM'] = $_POST ['DHHM']; //电话
		$data ['SHFPS'] = ($_POST ['SHFPS'] == NULL ? 0 : 1); //是否配送
		$shuling =  str_replace(",","",$_POST ['SHLHEJ']); 
		$data ['SHLHJ'] = $shuling; //数量合计
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['CHKDZHT'] = '1'; //退货单状态		
		$data ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		return $this->_db->insert ( "H01DB012423", $data );
		
	}
	
	/*
	 * 对应调拨出库单双击明细
	 */
	public function dydbckdb($filter){
			$fields = array ("", "DJBH", "DCHCK", "DRCK", "KPRQ", "BMMCH","BMBH","YGXM","YWYBH");
			$sql = "SELECT DJBH ,DCHCKMCH AS DCHCK,DRCKMCH AS DRCK,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,BMMCH,BMBH,YWYXM AS YGXM ,YWYBH,
				BGZH ,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,DCHCK AS DCHCKBH,DRCK AS DRCKBH 
				FROM H01VIEW012410 "
				. " WHERE QYBH =:QYBH AND CHKDZHT <> '3' ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			if ($filter ['searchParams']["SERCHKSRQ"] != "" || $filter ['searchParams']["SERCHKSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter ['searchParams']["SERCHKSRQ"] == ""?"1900-01-01":$filter ['searchParams']["SERCHKSRQ"];
			$bind ['SERCHJSRQ'] = $filter ['searchParams']["SERCHJSRQ"] == ""?"9999-12-31":$filter ['searchParams']["SERCHJSRQ"];
		}
		
		if ($filter ['searchParams']["DCCKBH"] != "") {
			$sql .= " AND( 	DCHCK LIKE '%' || :serchdwbh || '%')";
			$bind ['SERCHDWBH'] = $filter ['searchParams']["DCCKBH"];
		}
		
		if ($filter ['searchParams']["DRCKBH"] != "") {
			$sql .= " AND( DRCK LIKE '%' || :serchdwmch || '%')";
			$bind ['SERCHDWMCH'] = $filter ['searchParams']["DRCKBH"];
		}
		
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_KJDBCKD_XZ",$filter['filterParams'],$bind);
		
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DJBH";
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
				
	}
	
	/*
	 * 对应调拨出库单双击
	 */
	public function getmingxilistdata($djbh){
		


		$sql=" SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ ,
                  TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,JLGG,0,0.00,0.000,
                  SUM(WSHHSHL),CHANDI,null,TYMCH , BZHDWBH 
                  FROM H01VIEW012411 
                  WHERE QYBH = :QYBH AND DJBH = :DJBH  AND WSHHSHL >0
                      group BY SHPBH,SHPMCH,GUIGE,BZHDWMCH,JLGG,SHCHRQ,PIHAO,BZHQZH,CHANDI,TYMCH,BZHDWBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DJBH'] = $djbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
	}
	
	/*
	 * 弹出页面明细
	 */
	public function dbckkwxx($djbh){
		
				$sql = " SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,DCHKWMCH,DRKQMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ ,
				    TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,JLGG, BZHSHL,LSSHL,SHULIANG,
		 		    WSHHSHL,THSHL,CHANDI,BEIZHU,TYMCH 
				    FROM H01VIEW012411 "
		 		    . " WHERE QYBH = :QYBH AND DJBH = :DJBH  ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DJBH'] = $djbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
	}
	/*
	 * 加载页面显示明细
	 */
	public function loadming($djbh){
		


		$sql = " SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,DCHKWMCH,DRKQMCH,PIHAO,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ ,
                    TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,JLGG, BZHSHL,LSSHL,SHULIANG,
                    WSHHSHL,THSHL,CHANDI,BEIZHU,TYMCH 
                    FROM H01VIEW012411 "
                    . " WHERE QYBH = :QYBH AND DJBH = :DJBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		$recs = $this->_db->fetchAll($sql,$bind);
		   return Common_Tool::createXml ( $recs, true );
		
	}
	
	/*
	 * 判断更新时间是否相同
	 */
	public function checktime($dydbchkd,$file){
		
 		$sql =" SELECT TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH FROM H01DB012410 WHERE QYBH=:QYBH AND DJBH = :DJBH FOR UPDATE ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $dydbchkd;
		$recs = $this->_db->fetchAll($sql,$bind);
		if($recs[0]['BGRQ'] == $file['BGRQ'] && $recs[0]['BGZH'] == $file['BGZH']){
			return true;
		}else{
			return false;
		}		
	}
	/*
	 * 获取配送，备注，电话信息
	 */
	public function getkjdjxx($djbh){
		


		$sql = "SELECT DJBH ,SHFPS,"
                . " DHHM ,BEIZHU, DRCKDZH ,DCHCK AS DCHCKBH ,DRCK AS DRCKBH "
                . " FROM H01VIEW012410 "
                . " WHERE QYBH =:QYBH  AND DJBH=:DJBH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DJBH'] = $djbh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return $recs;
	}
	
	/*
	 * 获取调拨库间出库明细
	 */
	public function getkjdbfkMx($djbh,$shpbh,$pihao,$shchrq){
		
		
		$sql = "SELECT WSHHSHL, XUHAO FROM H01DB012411 WHERE QYBH=:QYBH AND DJBH =:DJBH ".
			   " PIHAO=:PIHAO AND SHCHRQ =:SHCHRQ AND SHPBH=:SHPBH ORDER BY XYHAO DESC FOR UPDATE";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		$bind ['SHPBH'] = $shpbh;
		$bind ['PIHAO'] = $pihao;
		$bind ['SHCHRQ'] = $shchrq;
		$recs = $this->_db->fetchAll($sql,$bind);
		return $recs;
				
	}
	/*
	 * 保存调拨返库明细
	 */

	public function savekjdbfkMingxi($kjdbfkbh) {
		$idx = 1; //序号自增
		//循环所有明细行，保存销售订单明细
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idxx_SHPBH] == '')
				continue;
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['DJBH'] = $kjdbfkbh; //采购挂账单编号
			$data ['XUHAO'] = $idx ++; //序号
			$data ['SHPBH'] = $grid [$this->idxx_SHPBH]; //商品编号
			$data ['BZHSHL'] = ($grid [$this->idxx_BZHSHL] == null) ? 0 : $grid [$this->idxx_BZHSHL]; //包装数量
			$data ['LSSHL'] = ($grid [$this->idxx_LSSHL] == null) ? 0 : $grid [$this->idxx_LSSHL]; //零散数量
			$shuling =  str_replace(",","",$grid [$this->idxx_SHULIANG]);   
			$data ['SHULIANG'] =$shuling; //数量
			$data ['BZHDWBH'] = $grid [$this->idxx_BZHDW]; //包装单位编号
			$data ['PIHAO'] = $grid [$this->idxx_PIHAO]; //批号
			if ($grid [$this->idxx_SHCHRQ] != ""){
			$data ['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idxx_SHCHRQ] . "','YYYY-MM-DD')" );//生产日期
			}
			if ($grid [$this->idxx_BZHQZH] != ""){
			$data ['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idxx_BZHQZH] . "','YYYY-MM-DD')" );//保质期至
			}
			$data ['BEIZHU'] = $grid [$this->idxx_BEIZHU]; //备注						
			//采购开票单明细表
			$this->_db->insert ( "H01DB012424", $data );
		}
	}
	/**
	 * 库间调拨入库单信息获取
	 *
	 * @param string $bh
	 * @return array[]
	 */
	function getinfoData($bh){
		//检索SQL
		$sql = "SELECT TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,"           //开票日期
                ."DJBH,"                //单据编号(对应调拨出库单编号)
                ."BMMCH,"               //部门名称
                ."YWYXM,"                //员工名称
                ."DCHCKMCH,"    //调出仓库
                ."DRCKMCH,"    //调入仓库
                ."DRCKDZH,"             //调入仓库地址
                ."SHFPS,"               //是否配送
                ."DHHM,"                //电话号码
                ."BEIZHU,"              //备注
                ."DCHCK,"               //调出仓库编号
                ."DRCK,"                //调入仓库编号
                ."TO_CHAR(BGRQ,'YYYY-MM-DD HH:mm:ss') AS BGRQ "    //变更日期
              ."FROM H01VIEW012410 "
              ."WHERE QYBH = :QYBH "
              ."AND DJBH = :DJBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $bh;                         //单据编号

		return $this->_db->fetchRow( $sql, $bind );
	}
	
	/**
	 * 更新库间调拨出库单明细信息的未入库数量及退货中数量															
	 * 
	 * 
	 */
	function updatadbckdxx($djbh,$shpbh,$pihao,$shchrq){
		
		//循环所有明细行，保存调拨返库明细
		$sql = "SELECT WSHHSHL, XUHAO,THZHSHL FROM H01DB012411 WHERE QYBH=:QYBH AND DJBH =:DJBH AND ".
			   " PIHAO=:PIHAO AND  SHCHRQ = TO_DATE(:SHCHRQ,'YYYY-MM-DD') AND SHPBH=:SHPBH ORDER BY XUHAO DESC FOR UPDATE";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		$bind ['SHPBH'] = $shpbh;
		$bind ['PIHAO'] = $pihao;
		$bind ['SHCHRQ'] = $shchrq;
		$recs = $this->_db->fetchAll($sql,$bind);
		
		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if($recs['0']['WSHHSHL'] < $grid [$this->idxx_SHULIANG]){
				
				$sql_list = " UPDATE H01DB012411 SET WSHHSHL = '0' , THZHSHL =:THZHSHL  WHERE QYBH =:QYBH AND DJBH=:DJBH AND XUHAO=:XUHAO ";
				$bind ['QYBH'] = $_SESSION ['auth']->qybh;
				$bind ['THZHSHL'] = $recs['0']['THZHSHL'] + $recs['0']['WSHHSHL'];
				$bind ['XUHAO'] = $recs['0']['XUHAO'];
				$bind ['DJBH'] = $djbh;
				$grid [$this->idxx_SHULIANG] =  $grid [$this->idxx_SHULIANG] - $recs['0']['WSHHSHL'];
				 $this->_db->query ( $sql_list, $bind ) ;				
				
				
			}else{
				$sql_list = " UPDATE H01DB012411 SET WSHHSHL = :WSHHSHL , THZHSHL =:THZHSHL  WHERE QYBH =:QYBH AND DJBH=:DJBH AND XUHAO=:XUHAO ";
				$bindarry ['WSHHSHL'] = $recs['0']['WSHHSHL'] - $grid [$this->idxx_SHULIANG];
				$bindarry ['THZHSHL'] = $recs['0']['THZHSHL'] + $grid [$this->idxx_SHULIANG];
				$bindarry ['QYBH'] = $_SESSION ['auth']->qybh;
				$bindarry ['DJBH'] = $djbh;
				$bindarry ['XUHAO'] = $recs['0']['XUHAO'];
				$this->_db->query ( $sql_list, $bindarry );
			
			}
		
		}
		
	}
	
	/**
	 * 出库单状态更新				
	 * 
	 */
	function updateckzhtai($djbh){
		//循环所有明细行，保存销售订单明细
		$sql_list = " UPDATE H01DB012410 SET CHKDZHT = '2' ,  BGRQ = sysdate,BGZH = :BGZH  WHERE QYBH =:QYBH AND DJBH=:DJBH  ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$this->_db->query ( $sql_list, $bind );
	}
}
