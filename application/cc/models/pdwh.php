<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：  盘点维护(pdwh)
 * 作成者：dltt
 * 作成日：2010/11/10
 * 更新履历：

 *********************************/
class cc_models_pdwh extends Common_Model_Base {

	
	/*
	 * 列表数据取得(xml格式)
	 */
	function getListData($filter) {
		$fields = array ("", "DJBH", "PDZHT","CKMCH", "KQMCH","KWMCH","PDKSHSHJ" ,"PDJSHSHJ"); 
		
		//检索SQL
		$sql = "SELECT DJBH,DECODE(PDZHT,1,'开始 ',2,'结束'),CKMCH,KQMCH,KWMCH,TO_CHAR(PDKSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDKSHSHJ ," 
        . "TO_CHAR(PDJSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDJSHSHJ," 
        . "SYJEHJ,DECODE(ZHMSHLTJ,1,'所有商品 ',2,'账面数量>0',3,'账面数量=0'),DECODE(DJBZH,0,'不冻结 ',1,'冻结 ')," 
        . "SHPBMMCH,SHPYWYXM,JSBMMCH,JSHYWYXM,DECODE(JZHZHT,0,'未', 1,'实盘已录入',2,'已记账')," 
        . "TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM "
		. "FROM H01VIEW012417 "
		." WHERE QYBH = :QYBH ";

		//查找条件 单据编号
		if ($filter ['searchParams']["DJBHKEY"] != "") {
			$sql .= " AND LOWER(DJBH) LIKE LOWER('%' || :DJBHKEY || '%') ";
			$bind ['DJBHKEY'] =$filter ['searchParams']["DJBHKEY"];
		}
		
		//查找条件 状态
		if ($filter ['searchParams']["PDZHT"] != "9" && $filter ['searchParams']["PDZHT"] != ""  ) {
			$sql .= " AND PDZHT = :PDZHT ";
			$bind ['PDZHT'] =$filter ['searchParams']["PDZHT"];
		}
		
		//盘点开始时间
		if ($filter ['searchParams']["PDKSHSHJ"] != "") {
			$sql .= " AND TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') = :PDKSHSHJ ";
			$bind ['PDKSHSHJ'] =$filter ['searchParams']['PDKSHSHJ'];
		}
	    //盘点结束时间
		if ($filter ['searchParams']["PDJSHSHJ"] != "") {
			$sql .= " AND TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') = :PDJSHSHJ ";
			$bind ['PDJSHSHJ'] =$filter ['searchParams']['PDJSHSHJ'];
		}
		
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_PDWH",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
	     
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"] ,$bind);
		
		return Common_Tool::createXml($recs,true,$totalCount,$filter["posStart"]);
	
	}
	
	/**
	 * 查找对应盘点信息
	 *
	 * @param unknown_type $djbh 盘点编号
	 * @param unknown_type $filter 关联页面内容
	 * @param unknown_type $flg 判断上一页下一页和第一次打开详细画面flg
	 * @return bool
	 */
	function getPdwhOne($djbh,$filter=null, $flg = 'current'){
			$fields = array ("", "DJBH", "PDZHT", "CKBH", "KQBH","PDKSHSHJ" ,"PDJSHSHJ");
			
			$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",DJBH) AS NEXTROWID,".
            " LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] ." ,DJBH) AS PREVROWID,".
			" DJBH ".
			" FROM H01VIEW012417 " .
            " WHERE QYBH = :QYBH";
			$bind ['QYBH'] =$_SESSION ['auth']->qybh;
			if($filter['searchParams']["DJBHKEY"] != "")
			{
				$sql_list .= " AND DJBH LIKE '%' || :DJBHKEY || '%'";
				$bind['DJBHKEY'] = $filter['searchParams']["DJBHKEY"];
			}
			//查找条件 状态
			if ($filter ['searchParams']["PDZHT"] != "9" && $filter['searchParams']['PDZHT'] != "") {
				$sql_list .= " AND PDZHT = :PDZHT ";
				$bind ['PDZHT'] =$filter ['searchParams']["PDZHT"];
			}
			
			//盘点开始时间
			if ($filter ['searchParams']["PDKSHSHJ"] != "") {
				
				$sql_list .= " AND TO_CHAR(PDKSHSHJ,'YYYY-MM-DD') = :PDKSHSHJ ";
				$bind ['PDKSHSHJ'] =$filter ['searchParams']['PDKSHSHJ'];
			}
		    //盘点结束时间
			if ($filter ['searchParams']["PDJSHSHJ"] != "") {
				$sql_list .= " AND TO_CHAR(PDJSHSHJ,'YYYY-MM-DD') = :PDJSHSHJ ";
				$bind ['PDJSHSHJ'] =$filter ['searchParams']['PDJSHSHJ'];
			}
			
			//自动生成精确查询用Sql
            $sql_list .= Common_Tool::createFilterSql("CC_PDWH",$filter['filterParams'],$bind);
			
			$sql_single = "SELECT SHPBMMCH AS KSBMBH,JSBMMCH AS JSBMCMH ,SHPYWYXM AS KSYGBH,JSHYWYXM AS JSYEWUYUAN,CKMCH,KQMCH,KWMCH,QYBH,DJBH,PDLX,TO_CHAR(PDKSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDKSHSHJ,TO_CHAR(PDJSHSHJ,'YYYY-MM-DD HH24:MI:SS') AS PDJSHSHJ,PDJHDH,CKBH,KQBH,KWBH,ZHMSHLTJ,DJBZH,SHPYWY,SHPBM,PDZHT,JZHZHT,HGL_DEC(SYJEHJ),	BEIZHU,BGRQ,BGZH ,TO_CHAR(BGRQ,'YYYY-MM-DD HH24:MI:SS'),BGZH,TO_CHAR(PDJSHSHJ,'YYYY-MM-DD HH24:MI:SS'),HGL_DEC(ZHMJEHJ) as ZHMJEHJ,HGL_DEC(SHPJEHJ) as SHPJEHJ " 
			      ." FROM H01VIEW012417 " ;
//			      ." LEFT JOIN H01DB012401 B ON A.CKBH = B.CKBH  AND A.QYBH =B.QYBH "
//				  ." LEFT JOIN H01DB012402 C ON A.CKBH = C.CKBH AND A.KQBH = C.KQBH  AND A.QYBH =C.QYBH "
//				  ." LEFT JOIN H01DB012403 D ON A.CKBH = D.CKBH AND A.KQBH = D.KQBH AND A.KWBH = D.KWBH  AND D.QYBH =D.QYBH "
//				  ." LEFT JOIN H01DB012112 E ON A.BMBH = E.BMBH  AND A.QYBH =E.QYBH "
//				  ." LEFT JOIN H01DB012112 F ON A.JSHBM = F.BMBH AND A.QYBH =F.QYBH"
//				  ." LEFT JOIN H01DB012113 G ON A.YWYBH = G.YGBH AND A.QYBH =G.QYBH"
//				  ." LEFT JOIN H01DB012113 I ON A.JSHYWY = I.YGBH AND A.QYBH =I.QYBH "
//			      . " ";
			      
			//当前
			if ($flg == 'current') {
				$sql_single .= " WHERE QYBH = :QYBH AND DJBH = :DJBH";
				unset($bind['DJBHKEY']);
				unset($bind['PDZHT']);
				unset($bind['PDKSHSHJ']);
				unset($bind['PDJSHSHJ']);
				
			} else if ($flg == 'next') {//下一条
	
				$sql_single .= "WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";		
			} else if ($flg == 'prev') {//前一条
	
				$sql_single .= "WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH))";		
			}
			$bind ['DJBH'] =$djbh;
			$recs = $this->_db->fetchRow($sql_single,$bind);
			if($recs == false){
				return false;
			}else{
				return $recs;
			}
	}
	
	
}
