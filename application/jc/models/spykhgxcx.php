<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与客户关系查询(SPYKHGXCX)
 * 作成者：姚磊
 * 作成日：2011/1/7
 * 更新履历：
 *********************************/
class jc_models_spykhgxcx extends Common_Model_Base {
	

	/**
	 * 多商品明细grid
	 *
	 * @param 
	 * @return 
	 */
		public function getdanweiGridData($filter){
				$fields = array ("", "A.DWBH", "B.DWMCH" ); 		
			$sql =" SELECT A.DWBH,B.DWMCH ,COUNT(A.SHPBH) ".
				  " FROM H01DB012114 A LEFT JOIN H01DB012106 B ON ".
				  " A.QYBH =B.QYBH AND A.DWBH = B.DWBH ".
				  " WHERE A.QYBH =:QYBH  AND B.SHFXSH ='1' ".
				  " HAVING COUNT (A.SHPBH )> 1 ".
                  " GROUP BY A.DWBH ,B.DWMCH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",A.DWBH ";	
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
			
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
		}
	/**
	 * 单商品明细grid
	 *
	 * @param 
	 * @return 
	 */
		
		public function getdanshpGridData(){
			
			$sql =" SELECT A.DWBH,B.DWMCH ,COUNT(A.SHPBH) ".
				  " FROM H01DB012114 A LEFT JOIN H01DB012106 B ON ".
				  " A.QYBH =B.QYBH AND A.DWBH = B.DWBH ".
				  " WHERE A.QYBH =:QYBH  AND B.SHFXSH ='1' ".
				  " HAVING COUNT (A.SHPBH )= 1 ".
                  " GROUP BY A.DWBH ,B.DWMCH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
			
		}
		
	/**
	 * 无商品明细grid
	 *
	 * @param 
	 * @return 
	 */
		
		public function getwushpGridData(){
			
			$sql ="SELECT A.DWBH,A.DWMCH ,COUNT(B.SHPBH) 
				   FROM H01DB012106 A LEFT JOIN H01DB012114 B ON 
				   A.QYBH =B.QYBH AND A.DWBH = B.DWBH 
				   WHERE A.QYBH =:QYBH  AND A.SHFXSH ='1' 
				   HAVING COUNT (B.SHPBH )= 0 
                   GROUP BY A.DWBH ,A.DWMCH";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );
			
		}
		
	/**
	 * 商品明细
	 */	
		
		public function getshpmxGrid($filter,$dwbh){
			
			$fields = array ("", "A.SHPBH", "B.SHPMCH" ); 
				$sql =" SELECT A.SHPBH,B.SHPMCH  ".
				  " FROM H01DB012114 A LEFT JOIN H01DB012101 B ON ".
				  " A.QYBH =B.QYBH AND A.SHPBH = B.SHPBH ".
				  " WHERE A.QYBH =:QYBH  AND A.DWBH =:DWBH  ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['DWBH'] = $dwbh;
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",A.SHPBH ";	
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
			
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
			
		}
		
	/**
	 * 供应商明细
	 */	
		
		public function getdwbhmxGrid($filter,$shpbh){
			
			$fields = array ("", "A.DWBH", "B.DWMCH" );
			$sql =" SELECT A.DWBH,B.DWMCH  ".
				  " FROM H01DB012114 A LEFT JOIN H01DB012106 B ON ".
				  " A.QYBH =B.QYBH AND A.DWBH = B.DWBH ".
				  " WHERE A.QYBH =:QYBH  AND A.SHPBH =:SHPBH  AND B.SHFXSH ='1'";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind ['SHPBH'] = $shpbh;
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",A.DWBH ";	
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
			
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
			
		}
		
		
		
		/*
		 * 多供应商
		 */
		public function getduoshpGrid($filter){
			$fields = array ("", "A.SHPBH", "A.SHPMCH", "C.NEIRONG","A.CHANDI","A.GUIGE","A.TYMCH" );
			$sql = " SELECT A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH,COUNT(B.DWBH) ".
				   " FROM H01DB012101 A LEFT JOIN H01DB012114 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
				   " LEFT JOIN H01DB012001 C ON A.BZHDWBH = C.ZIHAOMA AND A.QYBH = C.QYBH   AND C.CHLID = 'DW'".
				   " WHERE A.QYBH =:QYBH  " .
				   "  HAVING COUNT(B.DWBH) >1".
				   " GROUP BY A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 项目最后添加主键
		$sql .=",A.SHPBH ";	
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
			
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );	

		}
		
		/*
		 * 单供应商
		 */
		public function getdanshpGrid(){
			
			$sql = " SELECT A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH,COUNT(B.DWBH) ".
				   " FROM H01DB012101 A LEFT JOIN H01DB012114 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
				   " LEFT JOIN H01DB012001 C ON A.BZHDWBH = C.ZIHAOMA AND A.QYBH = C.QYBH   AND C.CHLID = 'DW' ".
				   " WHERE A.QYBH =:QYBH   " .
				   "  HAVING COUNT(B.DWBH) =1".
				   " GROUP BY A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );

		}
		
		/*
		 * 无供应商
		 */
		public function getwushpGrid(){
			
			$sql = " SELECT A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH,COUNT(B.DWBH) ".
				   " FROM H01DB012101 A LEFT JOIN H01DB012114 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH ".
				   " LEFT JOIN H01DB012001 C ON A.BZHDWBH = C.ZIHAOMA AND A.QYBH = C.QYBH  AND C.CHLID = 'DW' ".
				   " WHERE A.QYBH =:QYBH  " .
				   "  HAVING COUNT(B.DWBH) = 0".
				   " GROUP BY A.SHPBH,A.SHPMCH,C.NEIRONG,A.CHANDI,A.GUIGE,A.TYMCH ";
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$recs = $this->_db->fetchAll($sql,$bind);
		    return Common_Tool::createXml ( $recs, true );

		}
}