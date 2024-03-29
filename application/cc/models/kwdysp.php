<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  库位对商品(kwdysp)
 * 作成者：    姚磊
 * 作成日：    2011/06/28
 * 更新履历：
 **********************************************************/		
class cc_models_kwdysp extends Common_Model_Base {

	private $idx_ROWNUM = 0;// 行号
	private $idx_CKBH = 1;// 仓库编号
	private $idx_KWBH = 3;// 库位编号
	private $idx_ZHDSHPBH =7;//传送带出口
	/**
	 * 得到分箱查询信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "CKBH","CKMCH","KWBH","KWMCH","SHFSHKW","SHFGDJ","ZHDSHPBH","ZHDSHPMCH");

		//检索SQL
		$sql = " SELECT  CKBH,CKMCH,KWBH,KWMCH,DECODE(SHFSHKW,'0','是','1','否'),DECODE(SHFGDJ,'1','是','2','否'),ZHDSHPBH ,".
			   " ZHDSHPMCH ".
			   " FROM H01VIEW012403 WHERE QYBH=:QYBH AND KWZHT != 'X' ";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//是否零散货位
		if($filter ['SHFSHKW'] == NULL){
			$sql .= "AND  SHFSHKW IN ('0','1')";
			
		}else{
			$sql .= "AND  SHFSHKW ='1' ";
		}
		
		//是否固定架
		if($filter ['SHFGDJ'] == NULL){
			$sql .= "AND  SHFGDJ IN ('1','2')";
			
		}else{
			$sql .= "AND  SHFGDJ ='1' ";
		}
		
		//查找条件  编号或名称
		if($filter['searchParams']['CKXX']!=""){
			$sql .= " AND( CKBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :SEARCHKEY || '%')";
			$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['CKXX']);
		}
		
		if($filter['searchParams']['KWXX']!=""){
			$sql .= " AND( KWBH LIKE '%' || :SEARCHKEYKQBH || '%'".
			        "      OR  lower(KWMCH) LIKE '%' || :SEARCHKEYKQBH || '%')";
			$bind ['SEARCHKEYKQBH'] = strtolower($filter ["searchParams"]['KWXX']);
		}
		$sql .= Common_Tool::createFilterSql("CC_KWDYSPXX",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,CKBH ";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true,$totalCount, $filter ["posStart"] );
	}

	/*
	 * 获取传送带出口信息
	 */
	function getcsdxx($ckbh){
		
		$sql ="SELECT  CHSDCHK,CHSDCHK FROM H01DB012443 WHERE CKBH =:CKBH AND QYBH =:QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;		
		$bind ['CKBH'] = $ckbh;
		$recs = $this->_db->fetchPairs ( $sql, $bind );
		return	$recs;
	}
	/*
	 * 获取库位信息
	 */
	function getcsdkwxx($ckbh,$chsdchk){
		
		$sql ="SELECT  CKMCH,KWBH,KWMCH FROM H01VIEW012403 WHERE CKBH =:CKBH AND QYBH =:QYBH AND CHSDCHK =:CHSDCHK";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;		
		$bind ['CKBH'] = $ckbh;
		$bind ['CHSDCHK'] = $chsdchk;
		$recs = $this->_db->fetchRow($sql,$bind);	
		return $recs;	
	}

	/*
	 * 页面grid保存
	 */
	
	function upDatecsdxxgrid(){
		foreach ( $_POST ["#grid_danju"] as $grid ) {
		$sql = " UPDATE  H01DB012403 SET " . " ZHDSHPBH = :ZHDSHPBH " .
			   " WHERE QYBH = :QYBH AND CKBH =:CKBH AND KWBH = :KWBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$bind ['CKBH'] = $grid [$this->idx_CKBH]; //仓库编号
		$bind ['KWBH'] = $grid [$this->idx_KWBH]; //库区编号
		$bind ['ZHDSHPBH'] = $grid [$this->idx_ZHDSHPBH]; //传送带出口
		
		$this->_db->query ( $sql, $bind );
		}
	}
	/*
	 * auto传送带出口
	 */
	function getAutocompleteData($filter){
		
		$sql ="SELECT  CHSDCHK,CHSDCHK FROM H01DB012443 WHERE CKBH =:CKBH AND QYBH =:QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;		
		$bind ['CKBH'] = $filter['CKBH'];
		$recs = $this->_db->fetchAll ( $sql, $bind );
		return	$recs;
	}
}