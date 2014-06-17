<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       补货上架确认新(ckqrx)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/09/21
 ***** 更新履历：
 ******************************************************************/

class cc_models_ckqrx extends Common_Model_Base {

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
	public function getGridData($djbh){

		//检索SQL
		$sql = "SELECT * FROM "
				."(SELECT DYTM, ZHXBH AS XH, DJBH, DECODE(ZHUANGTAI,'2','已出库确认','未出库确认'), "
				."CASE ZHUANGTAI "
				."WHEN '2' "
				."THEN '' "
				."ELSE '确认^javascript:QueRen(' || '\"' || DYTM || '\"' || ',' || '\"' || ZHXBH || '\"' || ',' || '\"' || DJBH || '\"' || ')^_self' END,"
				."QYBH FROM H01DB012457 "
				."UNION ALL " 
				."SELECT DYTM, FENXIANGHAO AS XH, DJBH, DECODE(ZHUANGTAI,'4','已出库确认','未出库确认'),"
				."CASE ZHUANGTAI "
				."WHEN '4' "
				."THEN '' "
				."ELSE '确认^javascript:QueRen(' || '\"' || DYTM || '\"' || ',' || '\"' || FENXIANGHAO || '\"' || ',' || '\"' || DJBH || '\"' || ')^_self' END,"
				."QYBH FROM H01DB012431) "
				."WHERE QYBH = :QYBH AND DJBH = :DJBH ";
			
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		
		$recs = $this->_db->fetchAll($sql,$bind);
		
		return Common_Tool::createXml ( $recs, true );
	}
	
	
	/*
	 * 根据画面中的对应条码获取相应的单据编号
	 */
	public function getDJBH( $dytm ){
		
		if( substr($dytm,15,4) == "0000" ){
			//检索SQL
			$sql = "SELECT DJBH "                                //单据编号
					."FROM H01DB012457 "
					."WHERE QYBH = :QYBH "
					."AND DYTM = :DYTM ";
		}else{
			//检索SQL
			$sql = "SELECT DJBH "                               //单据编号
					."FROM H01DB012431 "
					."WHERE QYBH = :QYBH "
					."AND DYTM = :DYTM ";
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DYTM'] = $dytm;
		
		$recs = $this->_db->fetchOne( $sql, $bind );
		
		return $recs;
	}
	
	
	/*
	 * 更新出库单已出库确认商品的数量
	 */
	public function updateShl($filter) {
		
		if( substr( $filter['dytm'], 15, 4) == "0000" ){
			//获取对应箱中的商品信息
			$sql = "SELECT SHPBH,CKBH,KWBH,PIHAO,SHULIANG,ZHZHXH,DJBH "
					."FROM H01DB012458 "
					."WHERE QYBH = :QYBH "
					."AND CKBH = :CKBH "
					."AND DJBH = :DJBH "
					."AND ZHXBH = :ZHXBH ";
					
			//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
			$bind ['DJBH'] = $filter['djbh'];
			$bind ['ZHXBH'] = $filter['xianghao'];
			
			$recs = $this->_db->fetchAll( $sql, $bind );
			
		}else{
			//获取对应箱中的商品信息
			$sql = "SELECT SHPBH,CKBH,KWBH,SHULIANG,PIHAO,DJBH "
					."FROM H01DB012431 "
					."WHERE QYBH = :QYBH "
					."AND DYTM = :DYTM ";
					
			//绑定查询条件
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DYTM'] = $filter['dytm'];
			
			$recs = $this->_db->fetchAll( $sql, $bind );		
			
		}
		
		foreach ( $recs as $rec ) {
			
			$sql2 = "SELECT CHKDBH,XUHAO,SHPBH,SHULIANG,YQRSHL,RKDBH ".
			       " FROM H01DB012409 " .
			       " WHERE QYBH = :QYBH " .          //区域编号
				   " AND CHKDBH = :CHKDBH".          //出库单编号
                   " AND CKBH = :CKBH " .            //仓库编号
                   " AND KWBH = :KWBH " .            //库位编号
                   " AND SHPBH = :SHPBH " .          //商品编号
                   " AND PIHAO = :PIHAO " .          //批号
                   " AND CHHQRZHT != '2' " .         //出库确认状态
                   " ORDER BY XUHAO ";
			
			$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind2 ['CHKDBH'] = $rec['DJBH'];
			$bind2 ['SHPBH'] = $rec['SHPBH'];
			$bind2 ['CKBH'] = $rec['CKBH'];
			$bind2 ['KWBH'] = $rec['KWBH'];
			$bind2 ['PIHAO'] = $rec['PIHAO'];
			
			$datas = $this->_db->fetchAll( $sql2, $bind2 );
			
			$CKSL = $rec['SHULIANG'];          //商品出库确认数量
			
			foreach ( $datas as $data ) {
				//销售数量  = 已确认数量  + 本次出库数量
				if ( (int)$data['SHULIANG'] == (int)$data['YQRSHL'] + (int)$CKSL ) {
					
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
					$bind_update ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind_update ['BGZH'] = $_SESSION ['auth']->userid;
					$bind_update ['CHKDBH'] = $data['CHKDBH'];
					$bind_update ['XUHAO'] = $data['XUHAO'];
					
					$this->_db->query( $sql_update, $bind_update );
			
					$CKSL = 0;
				}
				
				
				//销售数量  > 已确认数量  + 本次出库数量
				if ( (int)$data['SHULIANG'] > (int)$data['YQRSHL'] + (int)$CKSL ) {
					
					//更新出库单对应明细的出库确认状态和已确认数量
					$sql_update = "UPDATE H01DB012409 ".
								   " AND YQRSHL = :YQRSHL " .
								   " AND BGRQ = SYSDATE " .
								   " AND BGZH = :BGZH " .
							       " WHERE QYBH = :QYBH " .          //区域编号
								   " AND CHKDBH = :CHKDBH".          //出库单编号
				                   " AND XUHAO = :XUHAO " ;          //序号
					
					//绑定查询变量
					$bind_update ['YQRSHL'] = (int)$data['YQRSHL'] + (int)$CKSL;
					$bind_update ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind_update ['BGZH'] = $_SESSION ['auth']->userid;
					$bind_update ['CHKDBH'] = $data['CHKDBH'];
					$bind_update ['XUHAO'] = $data['XUHAO'];
					
					$this->_db->query( $sql_update, $bind_update );
			
					$CKSL = 0;
				}
				
				
				//销售数量  < 已确认数量  + 本次出库数量
				if ( (int)$data['SHULIANG'] < (int)$data['YQRSHL'] + (int)$CKSL ) {
					
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
					$bind_update ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind_update ['BGZH'] = $_SESSION ['auth']->userid;
					$bind_update ['CHKDBH'] = $data['CHKDBH'];
					$bind_update ['XUHAO'] = $data['XUHAO'];
					
					$this->_db->query( $sql_update, $bind_update );
			
					$CKSL = (int)$CKSL - (int)$data['SHULIANG'] + (int)$data['YQRSHL'];
				}
				
				if ( $CKSL <= 0 ) break;
			}
			
		}
	}

	
	/*
	 * 确认修改当前箱状态
	 */
	public function updateZht($filter) {
		
		if( substr( $filter['dytm'], 15, 4) == "0000" ){
			
			//更新SQL
			$sql_update = "UPDATE H01DB012457 ".
					       " SET ZHUANGTAI = '2' " .
					       " WHERE QYBH = :QYBH " .      //区域编号
						   " AND CKBH = :CKBH".          //出库单编号
		                   " AND DJBH = :DJBH".          //序号
						   " AND DYTM = :DYTM";          //对应条码
						
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
			$bind ['DJBH'] = $filter['djbh'];
			$bind ['DYTM'] = $filter['dytm'];
			
			$this->_db->query( $sql_update,$bind );
			
		}else{
			
			//更新SQL
			$sql_update = "UPDATE H01DB012431 ".
					       " SET ZHUANGTAI = '4' " .
						   " AND BGRQ = SYSDATE " .
						   " AND BGZH = :BGZH " .
					       " WHERE QYBH = :QYBH " .          //区域编号
		                   " AND DYTM = :DYTM " ;            //对应条码
			
			//绑定查询变量
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['BGZH'] = $_SESSION ['auth']->userid;
			$bind ['DYTM'] = $filter['dytm'];
			
			$this->_db->query( $sql_update,$bind );
			
		}
		
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
		
		//return $result;
	}

}