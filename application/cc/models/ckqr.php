<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       补货上架确认(ckqr)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/07/21
 ***** 更新履历：
 ******************************************************************/

class cc_models_ckqr extends Common_Model_Base {

	private $idx_ROWNUM = 0;             //行号
	private $idx_SHPBH = 1;              //商品编号
	private $idx_SHPMCH = 2;             //商品名称
	private $idx_PIHAO = 3;              //批号
	private $idx_SHULIANG = 4;           //数量
	private $idx_DJBH = 5;               //单据编号
	private $idx_XIANGHAO = 6;           //箱号 
	private $idx_CKBH = 7;               //仓库编号
	private $idx_KWBH = 8;               //库位编号
	
	
	/**
	 * GRID列表XML数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($dytm){
		
		if( substr($dytm,15,4) == "0000" ){
			
			//检索SQL
			$sql = "SELECT "
				."A.SHPBH,"                               //商品编号
				."C.SHPMCH,"                              //商品名称
				."A.PIHAO,"                               //批号
				."A.SHULIANG,"                            //数量
				//."C.SHCHRQ,"                              //生产日期
				."A.DJBH,"                                //单据编号
				."A.ZHXBH,"                               //纸箱编号
				."A.CKBH,"                                //仓库编号
				."A.KWBH "                                //库位编号
				."FROM H01DB012458 A "
				."LEFT JOIN H01DB012457 B "
				."ON A.QYBH = B.QYBH "
				."AND A.CKBH = B.CKBH "
				."AND A.DJBH = B.DJBH "
				."AND A.ZHXBH = B.ZHXBH "
				."LEFT JOIN H01DB012101 C "
				."ON A.QYBH = C.QYBH "
				."AND A.SHPBH = C.SHPBH "
				."WHERE A.QYBH = :QYBH "
				."AND B.DYTM = :DYTM "
				."ORDER BY A.XUHAO";
			
		}else{
			
			//检索SQL
			$sql = "SELECT "
				."A.SHPBH,"                               //商品编号
				."B.SHPMCH,"                              //商品名称
				."A.PIHAO,"                               //批号
				."A.SHULIANG,"                            //数量
				//."B.SHCHRQ,"                              //生产日期
				."A.DJBH,"                                //单据编号
				."A.FENXIANGHAO,"                         //分箱编号
				."A.CKBH,"                                //仓库编号
				."A.KWBH "                                //库位编号
				."FROM H01DB012431 A "
				."LEFT JOIN H01DB012101 B "
				."ON A.QYBH = B.QYBH "
				."AND A.SHPBH = B.SHPBH "
				."WHERE A.QYBH = :QYBH "
				."AND A.DYTM = :DYTM ";
			
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DYTM'] = $dytm;
		
		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml ( $recs, true );
	}
	
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		
		$isHasMingxi = false;      //是否存在至少一条明细
		foreach ( $_POST ["#grid_main"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
			}
		}
		
		//一条明细也没有输入
		if ( !$isHasMingxi ) {
			return false;
		}
		
		return true;
	}

	
	/*
	 * 出库确认处理
	 */
	public function updateZht() {
		$result ['status'] = '0';
		
		//循环所有商品信息行进行出库确认处理
		foreach ( $_POST ["#grid_main"] as $row ) {
			
			if ($row [$this->idx_SHPBH] == '')continue;
			
			//取得即时库存信息
			$sql = "SELECT CHKDBH,XUHAO,SHPBH,SHULIANG,YQRSHL,RKDBH ".
			       " FROM H01DB012409 " .
			       " WHERE QYBH = :QYBH " .          //区域编号
				   " AND CHKDBH = :CHKDBH".          //出库单编号
                   " AND CKBH = :CKBH " .            //仓库编号
                   " AND KWBH = :KWBH " .            //库位编号
                   " AND SHPBH = :SHPBH " .          //商品编号
                   " AND PIHAO = :PIHAO " .          //批号
                   " AND CHHQRZHT != '2' " .         //出库确认状态
                   " ORDER BY XUHAO ";               
			
			//绑定查询变量
			$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind1 ['CHKDBH'] = $row [$this->idx_DJBH];
			$bind1 ['CKBH'] = $row [$this->idx_CKBH];
			$bind1 ['KWBH'] = $row [$this->idx_KWBH];
			$bind1 ['SHPBH'] = $row [$this->idx_SHPBH];
			$bind1 ['PIHAO'] = $row [$this->idx_PIHAO];

			//根据当前行商品信息获取对应出库单明细中的商品信息
			$recs = $this->_db->fetchAll( $sql, $bind1 );

			$CKSL = $row [$this->idx_SHULIANG];                //出库数量
			foreach ( $recs as $rec ) {
				//销售数量  = 已确认数量  + 本次出库数量
				if ( (int)$rec['SHULIANG'] == (int)$rec['YQRSHL'] + (int)$CKSL ) {
					
					//更新出库单对应明细的出库确认状态和已确认数量
					$sql_update = "UPDATE H01DB012409 ".
							       " SET CHHQRZHT = '2' " .
								   " AND YQRSHL = SHULIANG " .
								   " AND BGRQ = SYSDATE " .
								   " AND BGZH = :BGZH " .
							       " WHERE QYBH = :QYBH " .          //区域编号
								   " AND CHKDBH = :CHKDBH".          //出库单编号
				                   " AND XUHAO = :XUHAO " ;          //序号
					
					//绑定查询变量
					$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind2 ['BGZH'] = $_SESSION ['auth']->userid;
					$bind2 ['CHKDBH'] = $rec['CHKDBH'];
					$bind2 ['XUHAO'] = $rec['XUHAO'];
					
					$this->_db->query( $sql_update,$bind2 );
			
					$CKSL = 0;
				}
				
				
				//销售数量  > 已确认数量  + 本次出库数量
				if ( (int)$rec['SHULIANG'] > (int)$rec['YQRSHL'] + (int)$CKSL ) {
					
					//更新出库单对应明细的出库确认状态和已确认数量
					$sql_update = "UPDATE H01DB012409 ".
								   " AND YQRSHL = :YQRSHL " .
								   " AND BGRQ = SYSDATE " .
								   " AND BGZH = :BGZH " .
							       " WHERE QYBH = :QYBH " .          //区域编号
								   " AND CHKDBH = :CHKDBH".          //出库单编号
				                   " AND XUHAO = :XUHAO " ;          //序号
					
					//绑定查询变量
					$bind2 ['YQRSHL'] = (int)$rec['YQRSHL'] + (int)$CKSL;
					$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind2 ['BGZH'] = $_SESSION ['auth']->userid;
					$bind2 ['CHKDBH'] = $rec['CHKDBH'];
					$bind2 ['XUHAO'] = $rec['XUHAO'];
					
					$this->_db->query( $sql_update,$bind2 );
			
					$CKSL = 0;
				}
				
				
				//销售数量  > 已确认数量  + 本次出库数量
				if ( (int)$rec['SHULIANG'] < (int)$rec['YQRSHL'] + (int)$CKSL ) {
					
					//更新出库单对应明细的出库确认状态和已确认数量
					$sql_update = "UPDATE H01DB012409 ".
							       " SET CHHQRZHT = '2' " .
								   " AND YQRSHL = SHULIANG " .
								   " AND BGRQ = SYSDATE " .
								   " AND BGZH = :BGZH " .
							       " WHERE QYBH = :QYBH " .          //区域编号
								   " AND CHKDBH = :CHKDBH".          //出库单编号
				                   " AND XUHAO = :XUHAO " ;          //序号
					
					//绑定查询变量
					$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind2 ['BGZH'] = $_SESSION ['auth']->userid;
					$bind2 ['CHKDBH'] = $rec['CHKDBH'];
					$bind2 ['XUHAO'] = $rec['XUHAO'];
					
					$this->_db->query( $sql_update,$bind2 );
			
					$CKSL = (int)$CKSL - (int)$rec['SHULIANG'] + (int)$rec['YQRSHL'];
				}
				
				if ( $CKSL <= 0 ) break;
			}
			
		}

		return $result;
	}
	
	
	/*
	 * 判断销售单下的所有明细的状态
	 */
	public function selectZht() {
		
		$result ['status'] = '0';
		
		$sql = "SELECT XUHAO,CHHQRZHT ".
		       " FROM H01DB012409 " .
		       " WHERE QYBH = :QYBH " .          //区域编号
			   " AND CHKDBH = :CHKDBH";          //出库单编号

		//绑定查询变量
		$bind1['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1['CHKDBH'] = $_POST ["#grid_main"]['CHKDBH'];

		//根据当前行商品信息获取对应出库单明细中的商品信息
		$recs = $this->_db->fetchAll( $sql, $bind1 );
		
		foreach ( $recs as $rec ) {
			if( $rec['CHHQRZHT'] == '1' ){
				$result ['status'] = '1';
			}
		}
		
		if( $result ['status'] == '0' ){
			
			//更新出库单的出库确认状态
			$sql_update = "UPDATE H01DB012408 ".
					       " SET CHKDZHT = '2' " .
					       " WHERE QYBH = :QYBH " .          //区域编号
						   " AND CHKDBH = :CHKDBH";          //出库单编号
			
			//绑定查询变量
			$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind2 ['CHKDBH'] = $_POST ["#grid_main"]['CHKDBH'];

			$this->_db->query( $sql_update,$bind2 );
			
			$result ['data'] = $_POST ["#grid_main"]['CHKDBH'];
		}
		
		return $result;
	}

}