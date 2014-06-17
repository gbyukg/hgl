<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库位选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_models_kuwei extends Common_Model_Base{
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		//排序用字段名
		$fields=array("","KWBH","NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')","JHSHX","","SHFSHKW","","KWZHT");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh']; //仓库
		$bind['KQBH'] = $filter['kqbh']; //库区
		
		//检索SQL
		$sql = "SELECT KWBH,KWMCH,JHSHX,SHFSHKW,DECODE(SHFSHKW,'1','零散','0','整件','未知') AS SHFSHKWM,KWZHT,DECODE(KWZHT,'0','冻结','1','可用','X','删除','未知') AS KWZHTM ".
		       " FROM H01VIEW012403 " .
		       " WHERE QYBH = :QYBH ".   //区域编号
		       " AND CKBH = :CKBH ".  //仓库编号
		       " AND KQBH = :KQBH ";
		       
     	if($filter['flg']=='0'){//可用及冻结
			$sql .= " AND KWZHT IN ('0','1') ";
		}elseif($filter['flg']=='1'){//全部
			$sql .= " AND KWZHT IN ('0','1','X')";
		}
		
		if($filter['searchParams']['SEARCHKEY']!=""){
			$sql .= " AND( KWBH LIKE '%' || :SEARCHKEY || '%'".
			        "      OR  lower(KWMCH) LIKE '%' || :SEARCHKEY || '%')";
		$bind ['SEARCHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
			
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("GT_KUWEI1",$filter['filterParams'],$bind);
	
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"].",KWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml ($recs,true,$totalCount,$filter["posStart"]);
	}
	
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListAllData($filter) {
		//排序用字段名
		$fields=array("","CKBH","NLSSORT(CKMCH,'NLS_SORT=SCHINESE_PINYIN_M')","KQBH","NLSSORT(KQMCH,'NLS_SORT=SCHINESE_PINYIN_M')","","KQLX","KWBH","NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')","","SHFSHKW");
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		
		$view_sql = "SELECT A.QYBH,A.CKBH,A.CKMCH,B.KQBH,B.KQMCH,B.KQLX,D.NEIRONG AS KQLXMCH,C.KWBH,C.KWMCH,C.SHFSHKW".
		       " FROM H01DB012401 A ".
		       " JOIN H01DB012402 B ON B.QYBH = A.QYBH AND B.CKBH = A.CKBH ".
		       " JOIN H01DB012403 C ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH = B.KQBH ".
		       " LEFT JOIN H01DB012001 D ON D.QYBH = B.QYBH AND D.CHLID = 'KQLX' AND D.ZIHAOMA = B.KQLX ".
		       " WHERE A.CKZHT = '1' AND B.KQZHT = '1' AND C.KWZHT = '1'";

		$sql = "SELECT CKBH,CKMCH,KQBH,KQMCH,KQLX,KQLXMCH,KWBH,KWMCH,SHFSHKW,DECODE(SHFSHKW,'1','零散','0','整件','-') AS KWLXMCH ".
		       " FROM  ($view_sql) ".		       
		       " WHERE QYBH = :QYBH ";
		
		if($filter['ckbh']!=''){
			$sql .= " AND CKBH = :CKBH ";
			$bind['CKBH'] = $filter['ckbh']; //仓库
		}
		
	    if($filter['kqbh']!=''){
			$sql .= " AND KQBH = :KQBH ";
			$bind['KQBH'] = $filter['kqbh']; //库区
		}
		
	    if($filter['kqlx']!=''){
			$sql .= " AND KQLX = :KQLX ";
			$bind['KQLX'] = $filter['kqlx']; //库区类型
		}
		
		if($filter['kwlx']!=''){
			$sql .= " AND SHFSHKW = :KWLX ";
			$bind['KWLX'] = $filter['kwlx']; //库位类型
		}
		
		//排序
		$sql .= " ORDER BY ".$fields[$filter["orderby"]]." ".$filter["direction"];
		$sql .= " ,CKBH,KQLX,KQBH,KWBH,SHFSHKW";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"] ,$bind);
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	}

	/*
	 * 树形数据取得(xml格式)
	 * 对仓库，库区，库位三表进行合并，然后利用oracle抽出树形数据结构
	 */
	function getTreeData($filter) {
		//检索SQL
		$sql = "SELECT SYS_CONNECT_BY_PATH(ID,'/') AS PATH,ID,TEXT,PARENTID FROM ( ".
		       //仓库
               " SELECT CKBH AS ID,CKMCH AS TEXT,'0000000' AS PARENTID FROM H01DB012401 WHERE QYBH = :QYBH";
	    if($filter['flg']=='0'){
     		$sql .= " AND CKZHT IN('1')";      //可用
		}else if($filter['flg']=='1'){
			$sql .= " AND CKZHT IN('0','1')";  //可用和冻结
		}else if($filter['flg']=='2'){
			$sql .= " AND CKZHT IN('0','1','X')";//可用 冻结和删除
		}
		
		//库区
        $sql .=" UNION ALL ".
               " SELECT CKBH || KQBH AS ID,KQMCH AS TEXT,CKBH AS PARENTID FROM H01DB012402 WHERE QYBH = :QYBH";
	    if($filter['flg']=='0'){
     		$sql .= " AND KQZHT IN('1')";      //可用
		}else if($filter['flg']=='1'){
			$sql .= " AND KQZHT IN('0','1')";  //可用和冻结
		}else if($filter['flg']=='2'){
			$sql .= " AND KQZHT IN('0','1','X')";//可用 冻结和删除
		}
  		
		//库位
		$sql .=" UNION ALL ". 
               " SELECT CKBH || KQBH || KWBH AS ID,KWMCH AS TEXT,CKBH || KQBH AS PARENTID FROM H01DB012403 WHERE QYBH = :QYBH";
	    if($filter['flg']=='0'){
     		$sql .= " AND KWZHT IN('1')";      //可用
		}else if($filter['flg']=='1'){
			$sql .= " AND KWZHT IN('0','1')";  //可用和冻结
		}else if($filter['flg']=='2'){
			$sql .= " AND KWZHT IN('0','1','X')";//可用 冻结和删除
		}
		
		//是否散货库位
		if($filter['shfshkw']=='0'){
			$sql .=" AND SHFSHKW = '0'";
		}else if($filter['shfshkw']=='1'){
			$sql .=" AND SHFSHKW = '1'";
		}elseif ($filter['shfshkw']=='2') {
			$sql .=" AND SHFSHKW IN('0','1')";
		}	

        $sql .=" ) START WITH PARENTID = '0000000' CONNECT BY PRIOR ID = PARENTID ".
		       " ORDER SIBLINGS BY ID ";
   
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		//取得数据
		$recs = $this->_db->fetchAll ( $sql,$bind );
		return Common_Tool::createTreeXml($recs,'ID','TEXT');
	}
	
	/*
	 * 取得仓库下拉列表数据
	 */
	public function getCk(){
		$sql = "SELECT CKBH,CKMCH FROM H01DB012401 WHERE QYBH = :QYBH AND CKZHT = '1' ORDER BY CKBH";
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		//取得数据
		$recs = $this->_db->fetchPairs( $sql,$bind );
		return $recs;
		
	}
	/*
	 * 取得库区下拉列表数据
	 */
     public function getKqlx(){
		$sql = "SELECT ZIHAOMA,NEIRONG FROM H01DB012001 WHERE QYBH = :QYBH AND CHLID = 'KQLX' ORDER BY ZIHAOMA";
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		//取得数据
		$recs = $this->_db->fetchPairs( $sql,$bind );
		return $recs;
		
	}
	/*
	 * 取得库区类型下拉列表数据
	 */
     public function getKq($filter){
		$sql = "SELECT KQBH,KQMCH FROM H01DB012402 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQZHT = '1' ";
		
		//库区类型
		if($filter['kqlx']!=''){
			$sql .= " AND KQLX = :KQLX";
			$bind['KQLX'] = $filter['kqlx'];
		}
		
		$sql .= " ORDER BY KQBH";
		
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $filter['ckbh'];
		//取得数据
		$recs = $this->_db->fetchPairs( $sql,$bind );
		return $recs;
		
	}
	
}
