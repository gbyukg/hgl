<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：  盘点开始及盘点表生成(pdksjpdbsc)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_models_pdksjpdbsc extends Common_Model_Base {

	/**
	 * 查找对应库区的状态信息

	 * @param string $ckbh   仓库编号
	 * 

	 * @return bool
	 */
	function getCkstatus( $ckbh){
		
			$sql = "SELECT COUNT(1) AS BHCNT " 
			      . " FROM H01DB012401 " 
			      . " WHERE  QYBH=:QYBH AND   CKBH =:CKBH AND CKZHT <>:CKZHT ";

			      $bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'CKZHT' => 'X');
			
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs == 0){
				return false;
			}else{
				return true;
			}
	}
	
	/**
	 * 查找对应库位的状态信息

	 * @param string $ckbh   仓库编号
	 * @param string $kqbh   库区编号

	 * @return bool
	 */
	function getKcstatus($ckbh, $kqbh){

			$sql = "SELECT COUNT(1) FROM H01DB012402 WHERE QYBH=:QYBH AND  CKBH =:CKBH AND KQBH=:KQBH AND KQZHT <>:KQZHT";
			$bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'KQZHT' => 'X', 'KQBH'=>$kqbh);
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs == 0){
				return false;
			}else{
				return true;
			}
		
	}
	
		/**
	 * 查找对应库位的状态信息

	 * @param string $ckbh   仓库编号
	 * @param string $kqbh   库区编号
	 * @param string $kwbh   库位编号
	 * @return bool
	 */
	
	function getKwstatus( $ckbh, $kqbh ,$kwbh ){
			$sql = "SELECT COUNT(1) FROM H01DB012403 WHERE QYBH=:QYBH AND  CKBH =:CKBH AND KQBH = :KQBH AND KWBH=:KWBH AND  KWZHT <>:KWZHT";
			$bind = array('QYBH' =>$_SESSION ['auth']->qybh ,'CKBH' => $ckbh, 'KWZHT' => 'X', 'KQBH'=>$kqbh,'KWBH'=>$kwbh);
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs == 0){
				return false;
			}else{
				return true;
			}
	}
	
	/**
	 * 判断是否有对象库位处于盘点状态

	 * @param string $ckbh   仓库编号
	 * @param string $kqbh   库区编号
	 * @param string $kwbh   库位编号
	 * @return bool
	 */
	function getPdstatus ( $ckbh, $kqbh ,$kwbh ){
	$sql = "SELECT COUNT(1) FROM H01DB012403 WHERE QYBH=:QYBH AND  CKBH =:CKBH AND KQBH = :KQBH " 
	       . " AND  KWZHT =:KWZHT";
	       
	        if($kwbh !=""){
		   		$sql .= "AND KWBH=:KWBH" ;
		   		$bind ['KWBH'] = $kwbh;
		    }
		    
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $ckbh;
			$bind ['KQBH'] = $kqbh;
			$bind ['KWZHT'] = '9';
			$recs = $this->_db->fetchOne( $sql, $bind );
			
			if($recs == 0){
				return true ;
			}else{
				return false;
			}
	}
	
	/**
	 * 生成仓库信息
	 *
	 * @param unknown_type $ckbh   仓库编号
	 * @param unknown_type $kqbh   库区编号
	 * @param unknown_type $kwbh   库位编号
	 * @param unknown_type $pdjhsc 盘点生成单号
	 * @return bool
	 */
	function insertpdjhsc( $ckbh, $kqbh ,$kwbh,$pdjhsc) {
		
			//需要盘点商品信息查询

			$sql = "SELECT A.CKBH,A.KQBH,A.KWBH,A.SHPBH,A.PIHAO,A.BZHDWBH,A.SHCHRQ,"
				. "B.GUIGE,B.LSHJ,B.JLGG ,SUM(A.SHULIANG) AS SHULIANG ,A.BZHQZH "
				. "FROM "
				. "H01DB012404 A LEFT JOIN H01DB012101 B "
				. "ON A.SHPBH = B.SHPBH AND A.QYBH = B.QYBH "
				. "WHERE "
				. "A.QYBH=:QYBH "
				. "AND A.CKBH=:CKBH "
				. "AND A.KQBH = :KQBH AND A.BZHDWBH = B.BZHDWBH ";
			//查找条件 库位
			if ($_POST['KWBH_H'] != "") {
				$sql .= " AND A.KWBH = :KWBH ";
				$bind ['KWBH'] = $kwbh;
			}
				
			$sql .=  "GROUP BY " . "A.CKBH,A.KQBH,A.KWBH,A.SHPBH,A.PIHAO,A.BZHDWBH,A.SHCHRQ,B.GUIGE,B.LSHJ,B.JLGG ,A.BZHQZH ";
			
			if ($_POST['ZHMSHLTJ'] == "2") {
				$sql .= " having sum(shuliang) > 0 ";
			}
			
			if ($_POST['ZHMSHLTJ'] == "3") {
				$sql .= " having sum(shuliang) = 0 ";
			}
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['CKBH'] = $ckbh;
			$bind ['KQBH'] = $kqbh;
			
			//需要盘点商品信息查询
			$recs = $this->_db->fetchAll($sql,$bind);
			
			//在库商品信息不存在时
			if($recs ==false){
				
				return false;
			}
			
		    $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			
			$data ['DJBH'] = $pdjhsc; //单据编号
			$data ['PDLX'] = '1'; //盘点类型
			$data ['PDKSHSHJ'] = new Zend_Db_Expr ( 'SYSDATE' ); //盘点开始时间
			$data ['PDJHDH'] = $_POST ['PDJHDH_H']; //盘点计划单号
			$data ['CKBH'] = $_POST ['CKBH_H']; //仓库编号
			$data ['KQBH'] = $_POST ['KQBH_H']; //库区编号
			$data ['KWBH'] = $_POST ['KWBH_H']; //库位编号
			$data ['ZHMSHLTJ'] = $_POST ['ZHMSHLTJ']; //账面数量条件
			$data ['DJBZH'] = '1'; //冻结标志
			
			$data ['YWYBH'] = $_POST ['YEWUYUAN_H']; //业务员编号
			$data ['BMBH'] = $_POST ['BUMEN_H']; //部门编号
			
			$data ['PDZHT'] = '1'; //盘点状态
			$data ['JZHZHT'] = '0'; //记账状态
			//未决定
			$data ['ZHMJEHJ'] = 0; //账面金额合计
			$data ['SHPJEHJ'] = 0; //实盘金额合计
			$data ['SYJEHJ'] = 0; //损溢金额合计
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		    $data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期	    
		    $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//插入盘点表明细信息
			$this->_db->insert ( "H01DB012417", $data );

		    for($i = 0 ; $i < count($recs);$i++){
		    	
    		    $data1 ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
				$data1 ['DJBH'] = $data ['DJBH']; //单据编号
				$data1 ['SHPBH'] = $recs [$i]['SHPBH']; //商品编号
				//$data1 ['GUIGE'] = $recs [$i]['GUIGE']; //商品规格
				$data1 ['BZHDWBH'] = $recs [$i]['BZHDWBH']; //包装单位编号
				$data1 ['PIHAO'] = $recs [$i]['PIHAO']; //批号
				$data1 ['KWBH'] = $recs [$i]['KWBH']; //库位编号
				$data1 ['SHCHRQ'] = $recs [$i]['SHCHRQ']; //生产日期
				//2011.02.22追加
				$data1 ['BZHQZH'] = $recs [$i]['BZHQZH']; //保质期至
				
				//处理Ⅱ-1-3)里取到的数量/计量规格的整数部分
				//$data1 ['BZHSHL'] =floor($recs [$i]['SHULIANG']/$recs [$i]['JLGG']); //包装数量
				//处理Ⅱ-1-3)里取到的数量/计量规格的余数部分
				//$data1 ['LSSHL'] = $recs [$i]['SHULIANG']%$recs [$i]['JLGG']; //零散数量
				$data1 ['SHULIANG'] = $recs [$i]['SHULIANG']; //数量
				//$data1 ['JINE'] = $recs [$i]['SHULIANG'] * $recs [$i]['CHBDJ']; //金额
				$data1 ['JINE'] = 100; //金额
				//$data1 ['SHPSHL'] = $recs [$i]['SHPSHL']; //实盘数量
				//$data1 ['SHPJE'] = $recs [$i]['SHPJE']; //实盘金额
				//$data1 ['CHBDJ'] = $recs [$i]['CHBDJ']; //成本单价
				//$data1 ['PSSHL'] = $recs [$i]['PSSHL']; //盘损数量
				//$data1 ['PSJE'] = $recs [$i]['PSJE']; //盘损金额
				//$data1 ['LSHJ'] = $recs [$i]['LSHJ']; //零售价   被删除啦
				$data1 ['BGZH'] = $recs [$i]['BGZH']; //变更者
			    $data1 ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			    $data1 ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
				$data1 ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
				$data1 ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			    $this->_db->insert ( "H01DB012418", $data1 );
		    }
		    
		    $sql = "UPDATE H01DB012403 A "
			."SET YZHT = KWZHT , KWZHT = '9' " 
			."WHERE  " 
			."A.QYBH=:QYBH AND A.CKBH=:CKBH AND A.KQBH = :KQBH  " ;
			
			if ($_POST['KWBH_H'] != "") {
				$sql .= " AND A.KWBH = :KWBH ";
				$bind ['KWBH'] = $kwbh;
			}
			
		    $this->_db->query($sql,$bind);
			    
			return true;
	
	}

	/**
	 * 列表数据取得(xml格式)
	 *
	 * @param unknown_type $filter 关联页面内容
	 * @return unknown
	 */
	
	function getListData($filter) {
		
		//检索SQL
//		$sql = "SELECT A.PDJHDH,B.CKMCH,C.KQMCH,D.KWMCH,TO_CHAR(A.YJKSHRQ,'YYYY/MM/DD HH24:MI:SS'),TO_CHAR(A.YJJSHRQ,'YYYY/MM/DD HH24:MI:SS'),A.CKBH,A.KQBH,A.KWBH  " 
//				." FROM  H01DB012416 A "         
//		        ." LEFT JOIN H01DB012401 B ON A.CKBH = B.CKBH AND A.QYBH =B.QYBH "
//				." LEFT JOIN H01DB012402 C ON A.CKBH = C.CKBH AND A.KQBH = C.KQBH AND A.QYBH =B.QYBH "
//				." LEFT JOIN H01DB012403 D ON A.CKBH = D.CKBH AND A.KQBH = D.KQBH AND A.KWBH = D.KWBH  AND A.QYBH =D.QYBH "
//				." WHERE A.QYBH =:QYBH AND YJKSHRQ > ADD_MONTHS(SYSDATE, -1)  ";

		$sql = "SELECT PDJHDH,CKMCH,KQMCH,KWMCH,TO_CHAR(YJKSHRQ,'YYYY/MM/DD HH24:MI:SS'),TO_CHAR(YJJSHRQ,'YYYY/MM/DD HH24:MI:SS'),CKBH,KQBH,KWBH  " 
              ." FROM  H01VIEW012416"
              ." WHERE QYBH =:QYBH AND YJKSHRQ > ADD_MONTHS(SYSDATE, -1)  ";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		       
		//排序
		$sql .= " ORDER BY PDJHDH ASC";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	
	}
}
