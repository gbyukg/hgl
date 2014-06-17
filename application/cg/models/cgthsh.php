<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货审核(cgthsh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/17
 ***** 更新履历：
 ******************************************************************/

class cg_models_cgthsh extends Common_Model_Base {

	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "", "CGTHDBH", "KPRQ" );

		//检索SQL   单据编号,开票日期,单位编号,单位名称,金额,税额,含税金额,部门名称,业务员,操作员
		$sql = "SELECT CGTHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,"
				."(SELECT SUM(JINE) FROM H01DB012309 T1 WHERE T1.CGTHDBH = A.CGTHDBH) AS JINE,"
				."(SELECT SUM(SHUIE) FROM H01DB012309 T2 WHERE T2.CGTHDBH = A.CGTHDBH) AS SHUIE,"
				."(SELECT SUM(HSHJE) FROM H01DB012309 T3 WHERE T3.CGTHDBH = A.CGTHDBH) AS HSHJE,"
				."BMMCH,YWYXM,BGZHXM,DECODE(THLX,'1','合格品退货','2','不合格品退货') FROM H01VIEW012308 A "
				."WHERE QYBH = :QYBH "     //区域编号
				."AND SHHZHT = '0' "       //审核状态       0：未审核     1：已审核
				."AND QXBZH = '1' ";       //取消标准       1：正常          X：删除状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CG_CGTHSH_DJ",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];

		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGTHDBH";

		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );

		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql ["sql_count"], $bind );

		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql ["sql_page"], $bind );

		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	
	/**
	 * 得到单据明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
				//排序用字段名
		$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		     
		$sql = "SELECT "          
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH,"        //保质期至
//				."A.BZHSHL,"     		  //包装数量
//				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.HSHJE,"     	 	  //含税金额
				."A.JINE,"      	 	  //金额
				."A.SHUIE,"      		  //税额
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."B.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG "                //计量规格
			  ."FROM H01DB012309 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.CGTHDBH = :CGTHDBH ";      //入库单编号 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CGTHDBH,A.XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne( $pagedSql["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll( $pagedSql["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs, true, $totalCount, $filter["posStart"] );
	}
	
	
	/**
	 * 审核通过操作
	 *
	 * @param string $bh    编号
	 */
	public function checkyes($filter){
		$sql = "UPDATE H01DB012308 "
             ." SET SHHZHT = '1', "                           //审核状态    1:审核通过
             ." SHHR = :SHHR, "                               //审核人
             ." SHHYJ = :SHHYJ, "                             //审核意见
             ." SHHRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGTHDBH = :CGTHDBH " ;

        $bind ['SHHR'] = $_SESSION ['auth']->userId;
        $bind ['SHHYJ'] = $filter['shhyj']; 
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter['bh']; 

		return $this->_db->query( $sql,$bind );
	}
	
	
	/**
	 * 审核未通过操作
	 *
	 * @param string $bh   编号
	 */
	public function checkno($filter){
		$sql = "UPDATE H01DB012308 "
             ." SET SHHZHT = '2', "                            //审核状态    2:审核未通过
             ." SHHR = :SHHR, "                                //审核人
             ." SHHYJ = :SHHYJ, "                              //审核意见
             ." SHHRQ = SYSDATE "                              //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGTHDBH = :CGTHDBH " ;

		$bind ['SHHR'] = $_SESSION ['auth']->userId;
        $bind ['SHHYJ'] = $filter['shhyj']; 
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGTHDBH'] = $filter['bh']; 

		return $this->_db->query( $sql,$bind );
	}
}
