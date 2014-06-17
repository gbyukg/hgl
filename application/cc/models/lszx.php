<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  零散装箱(lszx)
 * 作成者：    姚磊
 * 作成日：    2011/03/29
 * 更新履历：
************************************************************/	
class cc_models_lszx extends Common_Model_Base {

		
		private $idx_ROWNUM=0;     // 行号
		private $idx_SHPBH =1;  // 商品编号
		private $idx_SHPMCH=2;      // 商品名称
		private $idx_KWBH=3;	//库位编号
		private $idx_SHULIANG=4;     // 数量
		private $idx_DWMCH=5;      // 单位名称
		private $idx_PIHAO=6 ;		//批号
		private $idx_ZHUANGTAI =7;		//传送带出口
		private $idx_CKBH=8;      // 仓库编号	
		private $idx_FJBF=9;       // 复检不符
	/**
	 * 生成零散装箱信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "A.SHPBH","B.SHPMCH","A.KWBH","A.SHULIANG","B.DWMCH","A.PIHAO");

		//检索SQL
		$sql = " SELECT A.SHPBH,B.SHPMCH,A.KWBH,A.SHULIANG,C.NEIRONG,A.PIHAO,DECODE(A.ZHUANGTAI,'0','已分箱','1','拣货中','2','已装箱') AS ZHUANGTAI,A.CKBH ,A.FHNG ".
			   " FROM H01DB012434 A LEFT JOIN H01DB012101 B ON A.QYBH =B.QYBH AND A.SHPBH = B.SHPBH ".
			   " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH  AND B.BZHDWBH=C.ZIHAOMA AND C.CHLID = 'DW'".
			   " WHERE A.QYBH=:QYBH AND A.ZHZHXH=:ZHZHXH  AND A.CHSDCHK =:CHSDCHK AND A.CKBH =:CKBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['ZHZHXH'] = $filter['zhzhxh'];
		$bind ['CHSDCHK'] = $filter['chsdchk'];
		$bind ['CKBH'] = $filter['ckbh'];
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键

		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	/**
	 * 判断对应暂存区是否存在
	 * @param string   
	 */
	
	public function getzhzhxh($zhzhxh){
		
		$sql ="SELECT DJBH ,DYZCQ FROM  H01DB012433  WHERE QYBH =:QYBH AND ZHZHXH=:ZHZHXH AND ZHUANGTAI IN('0','1','2')";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['ZHZHXH'] = $zhzhxh;
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	/**
	 *  获取条码对应零散的状态
	 * 
	 */
	
	public function getzhtai($zhzhxh,$djbh){
		
		$sql =" SELECT ZHUANGTAI FROM H01DB012433 WHERE ZHZHXH=:ZHZHXH AND DJBH=:DJBH AND QYBH=:QYBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['ZHZHXH'] = $zhzhxh;
		$bind ['DJBH'] = $djbh; //单据编号
		return $this->_db->fetchRow( $sql, $bind );
	}
	
	/**
	 * 设置对应状态为已装箱
	 */
	public function updatezhuant($zhzhxh,$djbh,$ckbh,$chsdchk){
		
		$sql = " UPDATE  H01DB012437 SET " . " ZHUANGTAI = '2'"  . 
			   " WHERE QYBH = :QYBH AND DJBH=:DJBH AND ZHZHXH =:ZHZHXH ".
			   " AND CKBH =:CKBH AND CHSDCHK =:CHSDCHK";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DJBH'] = $djbh; //单据编号
		$bind ['ZHZHXH'] = $zhzhxh;		   //周转箱号
		$bind ['CKBH'] = $ckbh; //仓库编号
		$bind ['CHSDCHK'] = $chsdchk; //传送带出口
		return $this->_db->query ( $sql, $bind );		
	}
	
	
	/**
	 * 更新状态
	 */
	public function updatezhatai($zhzhxh,$djbh){
		
		$sql = "UPDATE  H01DB012433 SET " . " ZHUANGTAI = '2',"  . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DJBH=:DJBH AND ZHZHXH =:ZHZHXH";
			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DJBH'] = $djbh; //单据编号
		$bind ['ZHZHXH'] = $zhzhxh;		   //周转箱号
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
		return $this->_db->query ( $sql, $bind );		
	
	}
	
	/**
	 * 拣货更新 (拣货中)
	 * 
	 */
	public function upjhval($zhzhxh,$djbh){
		
		
		$sql = "UPDATE  H01DB012433 SET " . " ZHUANGTAI = '1',"  . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DJBH=:DJBH AND ZHZHXH =:ZHZHXH";
			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DJBH'] = $djbh; //单据编号
		$bind ['ZHZHXH'] = $zhzhxh;		   //周转箱号
		$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
		return $this->_db->query ( $sql, $bind );
	}
	
	/**
	 * 复检状态(为1)
	 */
	public function upfjval($zhzhxh,$djbh){
	
		foreach ( $_POST ["#grid_danju"] as $grid ) {
				if ($grid [$this->idx_SHPBH] == '')
				continue;
		$sql = "UPDATE  H01DB012434 SET " . " FHNG = '1' "  .
		      " WHERE QYBH = :QYBH ".
		      " AND DJBH=:DJBH ".
		      " AND ZHZHXH =:ZHZHXH ".
		      " AND PIHAO=:PIHAO".
		      " AND SHPBH=:SHPBH".
		      " AND CKBH=:CKBH".
		      " AND KWBH=:KWBH";
			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DJBH'] = $djbh; //单据编号
		$bind ['ZHZHXH'] = $zhzhxh;		   //周转箱号
		$bind ['PIHAO'] = $grid [$this->idx_PIHAO];
		$bind ['SHPBH'] = $grid [$this->idx_SHPBH];
		$bind ['CKBH'] = $grid [$this->idx_CKBH];

		$bind ['KWBH'] = $grid [$this->idx_KWBH];
		return $this->_db->query ( $sql, $bind );
	}
	}
	
	/*
	 * 获取传送带出口
	 */
	public function getChsd($yhid){
		
		$sql = "SELECT CKBH ,CHSDCHK FROM H01DB012432 WHERE QYBH=:QYBH AND YHID=:YHID";			
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['YHID'] = $yhid;		   //周转箱号
		return $this->_db->fetchRow ( $sql, $bind );
		
	}
	
	/*
	 * 获取已装箱结果数目
	 */
	public function getnum($zhzhxh,$djbh,$ckbh){
		
		$sql ="SELECT COUNT(*) FROM H01DB012437 WHERE QYBH =:QYBH AND DJBH =:DJBH AND ZHZHXH =:ZHZHXH AND CKBH =:CKBH AND ZHUANGTAI != '2'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['DJBH'] = $djbh; //区域编号
		$bind ['ZHZHXH'] = $zhzhxh;		   //周转箱号
		$bind ['CKBH'] = $ckbh; //仓库编号
		return $this->_db->fetchRow ( $sql, $bind );
		
	}
	
}