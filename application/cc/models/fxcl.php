<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：   分箱处理(fxcl)
 * 作成者：姚磊
 * 作成日：2011/3/14
 * 更新履历：

 *********************************/
class cc_models_fxcl extends Common_Model_Base {
	
	private $idx_ROWNUM = 0;// 行号
	private $idx_FENXIANGHAO = 1;// 分箱号
	private $idx_ZHZHXH = 2;// 周转箱号
	private $idx_TCLV = 3;// 填充率
	private $idx_SLHJ = 4;// 数量合计
	private $idx_SFZHZN = 5;// 是否周转箱
	private $idx_QFBZ =6;//区分标志
			
		/**
		 * 获取数量集合
		 *
		 * @param String $xshdbh
		 * @return unknown
		 */
	
		public function getzjsl($xshdbh){
			
			$sql = "SELECT SUM(SHULIANG) AS SHULIANG FROM H01DB012202 WHERE QYBH =:QYBH AND XSHDBH =:XSHDBH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XSHDBH'] = $xshdbh;
			$recs = $this->_db->fetchRow($sql,$bind);	
		    return $recs;
		}
		
		/**
		 * 获取零散商品个数 传送口不等于'Z'
		 * retunr $recs 零散商品个数
		 */
		
		public function getlsNum($xshdbh){
			
			$sql = "SELECT COUNT(*) FROM ( SELECT   C.SHPMCH ,A.CKBH ,B.CHSDCHK ,A.KWBH ,C.JLGG, C.DBZHTJ, SUM(A.LSSHL) AS SHULIANG ,
					A.LSSHL,A.PIHAO,D.NEIRONG,A.SHPBH ,(SUM(A.LSSHL) * C.DBZHTJ / C.JLGG) AS TIJI
					FROM H01DB012409 A 
					LEFT JOIN H01DB012408 F ON A.QYBH = F.QYBH AND A.CHKDBH = F.CHKDBH 
					LEFT JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.KWBH = B.KWBH 
					LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH 
					LEFT JOIN H01DB012001 D ON A.QYBH = D.QYBH AND C.BZHDWBH = D.ZIHAOMA AND  D.CHLID = 'DW' 				  
					WHERE A.QYBH =:QYBH AND F.CKDBH =:XSHDBH AND A.LSSHL > '0'  AND B.CHSDCHK != 'Z'
					GROUP BY C.SHPMCH ,A.CKBH ,B.CHSDCHK,A.KWBH ,C.JLGG, C.DBZHTJ, A.LSSHL ,
					A.PIHAO,D.NEIRONG,A.SHPBH ORDER BY TIJI  DESC)" ;
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XSHDBH'] = $xshdbh;
				
			return $this->_db->fetchOne($sql,$bind);
		}
				
		/**
		 * 获取零散商品个数 传送口等于'Z'
		 * retunr $recs 零散商品个数
		 */
		
		public function getlsNumz($xshdbh){
			
			$sql = "SELECT COUNT(*) FROM ( SELECT   C.SHPMCH ,A.CKBH ,B.CHSDCHK ,A.KWBH ,C.JLGG, C.DBZHTJ, SUM(A.LSSHL) AS SHULIANG ,
					A.LSSHL,A.PIHAO,D.NEIRONG,A.SHPBH ,(SUM(A.LSSHL) * C.DBZHTJ / C.JLGG) AS TIJI
					FROM H01DB012409 A 
					LEFT JOIN H01DB012408 F ON A.QYBH = F.QYBH AND A.CHKDBH = F.CHKDBH 
					LEFT JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.KWBH = B.KWBH 
					LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH 
					LEFT JOIN H01DB012001 D ON A.QYBH = D.QYBH AND C.BZHDWBH = D.ZIHAOMA AND  D.CHLID = 'DW' 				  
					WHERE A.QYBH =:QYBH AND F.CKDBH =:XSHDBH AND A.LSSHL > '0'  AND B.CHSDCHK = 'Z'
					GROUP BY C.SHPMCH ,A.CKBH ,B.CHSDCHK,A.KWBH ,C.JLGG, C.DBZHTJ, A.LSSHL ,
					A.PIHAO,D.NEIRONG,A.SHPBH ORDER BY TIJI  DESC)" ;
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XSHDBH'] = $xshdbh;
				
			return $this->_db->fetchOne($sql,$bind);
		}
		
		/**
	 	* 获取分箱总信息
	 	*/	
		public function getfxMainlist($xshdbh){
			
			$sql =" SELECT C.SHPMCH,A.KWBH,A.LSSHL ,D.NEIRONG ,'' AS DBZHTJ,'' AS TCLV,'' AS TIJX,B.CHSDCHK,'' AS QFBZ,'' AS FENXIANGHAO ,C.JLGG, C.DBZHTJ,
					A.PIHAO,A.SHPBH ,(SUM(A.LSSHL) * C.DBZHTJ / C.JLGG) AS TIJI,SUM(A.LSSHL) AS SHULIANG , A.CKBH 
					FROM H01DB012409 A 
					LEFT JOIN H01DB012408 F ON A.QYBH = F.QYBH AND A.CHKDBH = F.CHKDBH 
					LEFT JOIN H01DB012403 B ON A.QYBH = B.QYBH AND A.KWBH = B.KWBH 
					LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH 
					LEFT JOIN H01DB012001 D ON A.QYBH = D.QYBH AND C.BZHDWBH = D.ZIHAOMA AND  D.CHLID = 'DW' 				  
					WHERE A.QYBH =:QYBH AND F.CKDBH =:XSHDBH AND A.LSSHL > '0'
					GROUP BY C.SHPMCH ,A.KWBH,A.CKBH ,B.CHSDCHK ,C.JLGG, C.DBZHTJ, A.LSSHL ,
					A.PIHAO,D.NEIRONG,A.SHPBH ORDER BY TIJI  DESC ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XSHDBH'] = $xshdbh;
			 	
			return $this->_db->fetchAll($sql,$bind);
		}
		
		/**
		 * 获取常量表中的分箱体积长 宽 高
		 */
		public function getCliang(){
			
			$sql =" SELECT  NEIRONG  FROM H01DB012001 WHERE QYBH =:QYBH AND CHLID = 'ZHZHXCH' OR CHLID = 'ZHZHXG' OR CHLID = 'ZHZHXK' OR CHLID = 'ZHDTCHL' ";
			$bind['QYBH'] = $_SESSION['auth']->qybh;
			return $this->_db->fetchAll($sql,$bind);
		
		}
		/**
		 * 更新销售订单状态
		 *
		 * @param string $xshdbh
		 * @return unknown
		 */
		public function changezt($xshdbh){
			
			$sql = "UPDATE H01DB012201 " . " SET XSHDZHT = '3'" . " WHERE QYBH =:QYBH AND XSHDBH =:XSHDBH  ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['XSHDBH'] =$xshdbh;
			$this->_db->query( $sql, $bind );
			return TRUE;
		}
		
		/*
		 * 检索对应商品信息
		 */
		public function getshpxx($xshdbh){
			
			$sql =" SELECT distinct A.SHPBH ,A.PIHAO,B.CKBH,B.KWBH,B.KQBH,A.BZHSHL ,C.JLGG FROM H01DB012202 A ".
				  " LEFT JOIN H01DB012409 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH AND A.PIHAO = B.PIHAO ".
			      " LEFT JOIN H01DB012101 C ON A.QYBH = C.QYBH AND A.SHPBH = C.SHPBH ".
				  " WHERE A.QYBH =:QYBH AND A.XSHDBH=:XSHDBH AND A.BZHSHL > '0' ";
			$bind['QYBH'] = $_SESSION['auth']->qybh;
			$bind ['XSHDBH'] =$xshdbh;
			return $this->_db->fetchAll($sql,$bind);		
		}
		
		/*
		 * 整件拣货添加数据
		 */
		public function insterdytm($zhjfx,$dytm,$xshdbh,$addnum){ // 向整件拣货中插入数据 _当前条数据,对应条码,销售单号,分箱号
			
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DYTM'] = $dytm; //对应条码
		$data ['SHPBH'] = $zhjfx['SHPBH'];//商品编号
		$data ['CKBH'] = $zhjfx['CKBH']; //仓库编号
		$data ['KQBH'] = $zhjfx['KQBH']; //库区编号
		$data ['KWBH'] = $zhjfx['KWBH']; //库位编号
		$data ['SHULIANG'] = $zhjfx['JLGG']; //计量规格
		$data ['PIHAO'] = $zhjfx['PIHAO']; //批号
		$data ['ZHUANGTAI'] = '0'; //状态 - 已分箱
		$data ['DJBH'] = $xshdbh; //销售单号/单据号
		$data ['FENXIANGHAO'] = $addnum; //分箱号
		$data ['ZXSH'] = $zhjfx['BZHSHL']; //总箱数
		$data ['FXRQ'] = new Zend_Db_Expr ( 'sysdate' ); //分箱日期
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//整件拣货明细表
		return $this->_db->insert ( "H01DB012431", $data );
		}
		
		/*
		 * 添加零散周转箱信息
		 */
		
		public function insterlszzx($xshdbh){ // 向零散拣货中插入数据 
			
			foreach ( $_POST ["#grid_fxmingxi"] as $grid ) {
					if ($grid [$this->idx_ZHZHXH] == '') continue;          //周转箱为空的数据
					if ($grid [$this->idx_FENXIANGHAO] == '9999') continue; //不是周转箱数据
		$zxnum = count($_POST ["#grid_fxmingxi"]);             //总箱数
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['ZHZHXH'] = $grid [$this->idx_ZHZHXH]; //周转箱号
		$data ['SHLHJ'] = $grid [$this->idx_SLHJ];//数量合计
		$data ['ZHUANGTAI'] = '0'; //状态 已分箱
		$data ['DJBH'] = $xshdbh; //单据编号 销售单号
		$data ['FENXIANGHAO'] = $grid [$this->idx_FENXIANGHAO]; //分箱号
		$data ['ZXSH'] = $zxnum; //总箱数
		$data ['FXRQ'] = new Zend_Db_Expr ( 'sysdate' ); //分箱日期
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		//零散拣货周转箱表
		 $this->_db->insert ( "H01DB012433", $data );
		
			}
		}
		
		/*
		 * 添加零散周转箱明细信息
		 */
		public function insterlszzxmx($grid,$xshdbh){ // 向零散拣货中插入数据 			
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DJBH'] = $xshdbh; //单据编号 销售单号
		$data ['ZHZHXH'] = $grid ['ZHZHXH']; //周转箱号
		$data ['SHPBH'] = $grid ['SHPBH'];//商品编号
		$data ['CKBH'] = $grid['CKBH']; //仓库编号
		$data ['CHSDCHK'] = $grid['CHSDCHK']; //传送带出口
		$data ['KWBH'] = $grid['KWBH']; //库位编号
		$data ['PIHAO'] = $grid['PIHAO']; //批号
		$data ['SHULIANG'] = $grid['LSSHL']; //数量
		$data ['ZHUANGTAI'] = '0'; //状态 已分箱
		$data ['FHNG'] = '0'; //复检NG

		//零散拣货周转箱明细表
		 $this->_db->insert ( "H01DB012434", $data );
					
		}
		
		//获取暂存区编号
		public function getzcqbh($grid){
			$sql =" SELECT MIN(FJZCQBH) AS FJZCQBH FROM  H01DB012444 WHERE QYBH =:QYBH AND CKBH =:CKBH AND ".
				  " CHSDCHK =:CHSDCHK AND SHYZHT != '1' AND FJZCQBH > '( SELECT DYZCQ FROM  H01DB012437 WHERE  QYBH =:QYBH AND CKBH =:CKBH AND CHSDCHK =:CHSDCHK AND ROWNUM <= 1 ORDER BY ZCHRQ DESC)' ";
			$bind['QYBH'] = $_SESSION['auth']->qybh;
			$bind ['CKBH'] =$grid['CKBH']; // 仓库编号
			$bind ['CHSDCHK'] =$grid['CHSDCHK']; // 仓库编号
			return $this->_db->fetchAll($sql,$bind);
			
		}
		
		//重新获取暂存区编号
		public function getnewzcqbh($grid){
			$sql =" SELECT MIN(FJZCQBH) AS  FJZCQBH FROM  H01DB012444 WHERE QYBH =:QYBH AND CKBH =:CKBH AND ".
				  " CHSDCHK =:CHSDCHK AND SHYZHT != '1' ";
			$bind['QYBH'] = $_SESSION['auth']->qybh;
			$bind ['CKBH'] =$grid['CKBH']; // 仓库编号
			$bind ['CHSDCHK'] =$grid['CHSDCHK']; // 仓库编号
			return $this->_db->fetchAll($sql,$bind);
		}
		
		//零散拣货周转箱 传送带口信息插入数据    暂存区编号
		public function insterlszzxx($grid,$resu_zcq,$xshdbh){
			
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['DJBH'] = $xshdbh; //单据编号 销售单号
		$data ['ZHZHXH'] = $grid ['ZHZHXH']; //周转箱号
		$data ['CKBH'] = $grid['CKBH']; //仓库编号
		$data ['CHSDCHK'] = $grid['CHSDCHK']; //传送带出口
		$data ['ZHUANGTAI'] = '0'; //状态
		$data ['DYZCQ'] = $resu_zcq['0']['FJZCQBH']; //分拣暂存区编号
		$data ['SHLHJ'] = $grid['LSSHL']; //数量
		$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		 $this->_db->insert ( "H01DB012437", $data );
			
		}
		
		//更新对应暂存区状态
		public function updatazcq($grid,$resu_zcq){
			
			$sql = " UPDATE H01DB012444 SET SHYZHT ='1' ".
			   " WHERE QYBH =:QYBH AND CKBH =:CKBH AND CHSDCHK =:CHSDCHK AND FJZCQBH=:FJZCQBH";

		$bind['QYBH'] = $_SESSION ['auth']->qybh; 			
		$bind ['CKBH'] =$grid['CKBH']; // 仓库编号
		$bind ['CHSDCHK'] =$grid['CHSDCHK']; // 仓库编号
		$bind ['FJZCQBH'] = $resu_zcq['0']['FJZCQBH']; //分拣暂存区编号
		return $this->_db->query( $sql, $bind );
		}
}