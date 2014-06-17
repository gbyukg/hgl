<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售退货单审核(XSTHDSH)
 * 作成者：孙宏志
 * 作成日：2011/01/21
 * 更新履历：
 *********************************/
class xs_models_xsthdsh extends Common_Model_Base {

	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "", "THDBH", "KPRQ" );

		//检索SQL   单据编号,开票日期,单位编号,单位名称,金额,税额,含税金额,部门名称,业务员,操作员
		$sql = "SELECT THDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DWBH,DWMCH,JINE,SHUIE,HSHJE,BMMCH,YWYXM,BGZHXM,QYBH,SHHZHT,THDZHT FROM "
				."(SELECT A.THDBH,A.KPRQ,A.DWBH,A.DWMCH,"
				."(SELECT SUM(JINE) FROM H01DB012207 T1 WHERE T1.THDBH = A.THDBH) AS JINE,"
				."(SELECT SUM(SHUIE) FROM H01DB012207 T2 WHERE T2.THDBH = A.THDBH) AS SHUIE,"
				."(SELECT SUM(HSHJE) FROM H01DB012207 T3 WHERE T3.THDBH = A.THDBH) AS HSHJE,"
				."A.BMMCH,A.YWYXM,A.BGZHXM,A.QYBH,A.SHHZHT,A.THDZHT "
				."FROM H01VIEW012206 A )"
				."WHERE QYBH = :QYBH "    //区域编号
				."AND SHHZHT = '0' "      //审核状态       0：未审核     1：已审核
				."AND THDZHT = '0' ";     //退货单状态  0：未入库；1: 已入库
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_XSTHSH_DJ",$filter['filterParams'],$bind);;
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",THDBH";

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );

		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );

		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );

		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	
	/*
	 * 取得退货单详细信息
	 */
	public function getTHDXX($filter) {
		//检索SQL
		$sql = "SELECT ".
				  "A.SHPBH,".
				  "B.SHPMCH,".
				  "B.GUIGE,".
				  "C.NEIRONG AS BZHDW,".
				  "A.BZHSHL,".
				  "A.LSSHL,".
				  "A.SHULIANG,".
				  "A.DANJIA,".
				  "A.HSHJ,".
				  "A.KOULV,".
				  "A.JINE,".		
				  "B.SHUILV,".
				  "A.SHUIE,".
				  "A.HSHJE,".
				  "A.BEIZHU ".
               "FROM H01DB012207 A ".
               "LEFT JOIN H01DB012101 B ON A.QYBH=B.QYBH AND A.SHPBH=B.SHPBH ".
		       "LEFT JOIN H01DB012001 C ON A.QYBH=C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' ".
               "WHERE A.QYBH=:QYBH ".
		       "AND A.THDBH=:THDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['THDBH'] = $filter ['thdh'];    //区域编号
		
		return Common_Tool::createXml($this->_db->fetchAll( $sql, $bind ),true);
	}
	
	
	/**
	 * 审核通过操作
	 *
	 * @param string $bh    编号
	 */
	public function checkyes($filter){
		$sql = "UPDATE H01DB012206 "
             ." SET SHHZHT = '1', "                           //审核状态    1:审核通过
             ." SHHR = :SHHR, "                               //审核人
             ." SHHYJ = :SHHYJ, "                             //审核意见
             ." SHHRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND THDBH = :THDBH " ;

        $bind ['SHHR'] = $_SESSION ['auth']->userId;
        $bind ['SHHYJ'] = $filter['shhyj']; 
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $filter['bh']; 

		return $this->_db->query( $sql,$bind );
	}
	
	
	/**
	 * 审核未通过操作
	 *
	 * @param string $bh   编号
	 */
	public function checkno($filter){
		$sql = "UPDATE H01DB012206 "
             ." SET SHHZHT = '2', "                            //审核状态    2:审核未通过
             ." SHHR = :SHHR, "                                //审核人
             ." SHHYJ = :SHHYJ, "                              //审核意见
             ." SHHRQ = SYSDATE "                              //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND THDBH = :THDBH " ;

		$bind ['SHHR'] = $_SESSION ['auth']->userId;
        $bind ['SHHYJ'] = $filter['shhyj']; 
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $filter['bh']; 

		return $this->_db->query( $sql,$bind );
	}
}	