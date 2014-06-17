<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  整件拣货(zjjh)
 * 作成者：    姚磊
 * 作成日：    2011/03/22
 * 更新履历：
 **********************************************************/

class cc_models_zjjh extends Common_Model_Base {

	/**
	 * 得到整件拣货信息
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter){
		//排序用字段名
		$fields = array ("", "SHPBH","SHPMCH" ,"NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "SHULIANG", "PIHAO", "ZHUANGTAI");

		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH, substr(CKBH,0) ||  substr(KQMCH,0) ||  substr(KWMCH,0) AS KUWEI,SHULIANG, PIHAO,
				DECODE(ZHUANGTAI,'0','已分箱','1','已打印','2','出库中') AS ZHUANGTAI, DJBH,FENXIANGHAO,ZXSH,
				TO_CHAR(FXRQ,'YYYY-MM-DD') AS FXRQ,DYTM,BGZH  FROM H01VIEW012431   
			    WHERE QYBH = :QYBH AND  DLZH =:DLZH AND DLZHT ='1'";

		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DLZH'] = $_SESSION ['auth']->userId;
		//查询条件(分箱日期从<=分箱日期<=分箱日期到)

		
			if ($filter['searchParams']["FXRQC"] != "" || $filter['searchParams']["SERCHJSRQ"] != "")
		{
			$sql .= " AND :SERCHKSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD')AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :SERCHJSRQ ";
			$bind ['SERCHKSRQ'] = $filter['searchParams']["FXRQC"] == ""?"1900-01-01":$filter['searchParams']["FXRQC"];
			$bind ['SERCHJSRQ'] = $filter['searchParams']["FXRQD"] == ""?"9999-12-31":$filter['searchParams']["FXRQD"];
		}
		//排序
		$sql .= Common_Tool::createFilterSql("CC_ZJJH",$filter['filterParams'],$bind);
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",SHPBH";
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
	 * 修改所选记录状态
	 *
	 * @param array 
	 * @return string xml
	 */
	public function update($filter){
		$sql = "UPDATE  H01DB012431 SET " . " ZHUANGTAI = :ZHUANGTAI,"  . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DYTM=:DYTM";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['ZHUANGTAI'] = $filter ['ZHUANGTAI']; //状态
			$bind ['DYTM'] = $filter ['dytm']; //对应条码
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			return $this->_db->query ( $sql, $bind );
			
			 
		
	}

}