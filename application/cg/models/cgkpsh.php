<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购开票审核(cgkpsh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/03/04
 ***** 更新履历：
 ******************************************************************/

class cg_models_cgkpsh extends Common_Model_Base {

	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "", "CGDBH", "KPRQ" );
		
		//检索SQL   单据编号,开票日期,单位编号,单位名称,金额,税额,含税金额,部门名称,业务员,操作员
		$sql = "SELECT * FROM "
				."(SELECT A.CGDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,A.DWMCH,"
				."to_char((SELECT SUM(JINE) FROM H01DB012307 T1 WHERE T1.CGDBH = A.CGDBH AND T1.QYBH = A.QYBH),'fm99999999990.00') AS JINE,"
				."to_char((SELECT SUM(SHUIE) FROM H01DB012307 T2 WHERE T2.CGDBH = A.CGDBH AND T2.QYBH = A.QYBH),'fm99999999990.00') AS SHUIE,"
				."to_char((SELECT SUM(HSHJE) FROM H01DB012307 T3 WHERE T3.CGDBH = A.CGDBH AND T3.QYBH = A.QYBH),'fm99999999990.00') AS HSHJE,"
				."A.BMMCH,A.YWYXM,A.KPYXM "
				."FROM H01VIEW012306 A "
				."WHERE A.QYBH = :QYBH "     //区域编号
				."AND A.SHPZHT = '0' "       //审批状态       0：未审核     1：已审核
				."AND A.QXBZH = '1' ";       //取消标准       1：正常          X：删除状态
		
		if( $filter ["flg"] == 1 ){
			$sql .="AND NOT EXISTS (SELECT E.CGDBH FROM H01DB012303 E WHERE E.QYBH = A.QYBH AND E.CGDBH = A.CGDBH)";
		}else{
			$sql .="AND EXISTS (SELECT E.CGDBH FROM H01DB012303 E WHERE E.QYBH = A.QYBH AND E.CGDBH = A.CGDBH)";
		}

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		if( $filter ["flg"] == 1 ){
			//自动生成精确查询用Sql
			$sql .= Common_Tool::createFilterSql("CG_CGKPSH",$filter['filterParams'],$bind);
		}else{
			//自动生成精确查询用Sql
			$sql .= Common_Tool::createFilterSql("CG_CGKPSH_TB",$filter['filterParams'],$bind);
		}
		
		//排序
		$sql .= ") ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];

		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CGDBH";

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
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."A.DANJIA,"      	      //单价
				."A.HSHJ,"       		  //含税价
				."A.KOULV,"               //扣率
				."B.SHUILV,"    	 	  //税率
				."A.JINE,"      	 	  //金额
				."A.HSHJE,"     	 	  //含税金额
				."A.SHUIE,"      		  //税额
				."B.CHANDI,"     		  //产地
				."A.BEIZHU "      	      //备注
			  ."FROM H01DB012307 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.CGDBH = :CGDBH ";      //入库单编号 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",A.CGDBH,A.XUHAO";
		
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
	 * 得到警示原因列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getyuanyindata($filter) {

		$sql = "SELECT SHPYY FROM H01DB012303 "
			  ."WHERE QYBH = :QYBH AND CGDBH = :CGDBH "
			  ."ORDER BY CGDBH,XUHAO";     
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CGDBH'] = $filter ["bh"];
				
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
		$sql1 = "UPDATE H01DB012303 "
             ." SET SHPZHT = '1', "                           //审批状态    1:审核通过
             ." SHPR = :SHPR, "                               //审核人
             ." SHPRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGDBH = :CGDBH " ;
		
		$sql2 = "UPDATE H01DB012306 "
             ." SET SHPZHT = '1', "                           //审批状态    1:审核通过
             ." SHHR = :SHHR, "                               //审核人
             ." SHHYJ = :SHHYJ, "                             //审核意见
             ." SHHRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGDBH = :CGDBH " ;

        $bind1 ['SHPR'] = $_SESSION ['auth']->userId;
		$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1 ['CGDBH'] = $filter['bh']; 
		
        $bind2 ['SHHR'] = $_SESSION ['auth']->userId;
        $bind2 ['SHHYJ'] = $filter['shhyj']; 
		$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind2 ['CGDBH'] = $filter['bh']; 

		$this->_db->query( $sql1,$bind1 );
		return $this->_db->query( $sql2,$bind2 );
	}
	
	
	/**
	 * 审核未通过操作
	 *
	 * @param string $bh   编号
	 */
	public function checkno($filter){
		$sql1 = "UPDATE H01DB012303 "
             ." SET SHPZHT = '2', "                           //审批状态     2:审核未通过
             ." SHPR = :SHPR, "                               //审核人
             ." SHPRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGDBH = :CGDBH " ;
             
		$sql2 = "UPDATE H01DB012306 "
             ." SET SHPZHT = '2', "                            //审核状态    2:审核未通过
             ." SHHR = :SHHR, "                                //审核人
             ." SHHYJ = :SHHYJ, "                              //审核意见
             ." SHHRQ = SYSDATE "                              //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND CGDBH = :CGDBH " ;

        $bind1 ['SHPR'] = $_SESSION ['auth']->userId;
		$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1 ['CGDBH'] = $filter['bh']; 
		
		$bind2 ['SHHR'] = $_SESSION ['auth']->userId;
        $bind2 ['SHHYJ'] = $filter['shhyj']; 
		$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind2 ['CGDBH'] = $filter['bh']; 

		$this->_db->query( $sql1,$bind1 );
		return $this->_db->query( $sql2,$bind2 );
	}
}
