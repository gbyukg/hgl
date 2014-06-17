<?php
/******************************************************************
 ***** 模         块：       销售模块(XS)
 ***** 机         能：       销售审核(xssh)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/01/27
 ***** 更新履历：
 ******************************************************************/

class xs_models_xssh extends Common_Model_Base {
	/**
	 * 得到单据列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		//排序用字段名
		$fields = array ( "","XSHDBH","KPRQ","DWBH","DWMCH","BMBH","BMMCH","YWYBH","YGXM" );

		//检索SQL
		$sql = "SELECT XSHDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,"
				."DWBH,DWMCH,BMBH,BMMCH,YWYBH,YWYXM "
				."FROM H01VIEW012201 "
				."WHERE QYBH = :QYBH "       //区域编号
				."AND SHHZHT = '3' "         //审核状态      0: 不需审核； 1: 审核通过；2：审核不通过；3：待审核
				."AND QXBZH = '1' ";         //取消标准       1：正常                X：删除状态

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("XS_XSKPSH_DJ",$filter['filterParams'],$bind);;

		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];

		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",XSHDBH";

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
	 * 检索销售订单审批原因
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getSHPGridData($filter) {	     
		$sql = "SELECT SHPYY "                //审批原因
			  ."FROM H01DB012203 "
			  ."WHERE QYBH = :QYBH "          //区域编号
			  ."AND XSHDBH = :XSHDBH "        //销售单编号
			  ."ORDER BY XUHAO";
			  
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ["bh"];
		
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
	 * 得到单据明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {	     
		$sql = "SELECT "          
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"     //生产日期
				."TO_CHAR(A.BZHQZH,'YYYY-MM') AS BZHQZH,"        //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
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
			  ."FROM H01DB012202 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "  
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "         //区域编号
			  ."AND A.XSHDBH = :XSHDBH ";      //入库单编号 
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $filter ["bh"];
				
		//排序
		$sql .= " ORDER BY A.XSHDBH,A.XUHAO";
		
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
	 * @param array $filter  参数
	 */
	public function checkyes($filter){
		$sql1 = "UPDATE H01DB012201 "
             ." SET SHHZHT = '1', "                           //审核状态    1:审核通过
             ." SHHR = :SHHR, "                               //审核人
             ." SHHYJ = :SHHYJ, "                             //审核意见
             ." SHHRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND XSHDBH = :XSHDBH " ;

        $bind1 ['SHHR'] = $_SESSION ['auth']->userId;
        $bind1 ['SHHYJ'] = $filter['shyj']; 
		$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1 ['XSHDBH'] = $filter['bh']; 

		$this->_db->query( $sql1,$bind1 );
		
		
		$sql2 = "UPDATE H01DB012203 "
             ." SET SHPZHT = '1', "                           //审核状态    1:审核通过
             ." SHPR = :SHPR, "                               //审核人
             ." SHPRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND XSHDBH = :XSHDBH " ;

        $bind2 ['SHPR'] = $_SESSION ['auth']->userId;
		$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind2 ['XSHDBH'] = $filter['bh']; 

		$this->_db->query( $sql2,$bind2 );
		
		return true;
	}
	
	
	/**
	 * 审核未通过操作
	 *
	 * @param array $filter  参数
	 */
	public function checkno($filter){
		$sql1 = "UPDATE H01DB012201 "
             ." SET SHHZHT = '2', "                            //审核状态    2:审核未通过
             ." SHHR = :SHHR, "                                //审核人
             ." SHHYJ = :SHHYJ, "                              //审核意见
             ." SHHRQ = SYSDATE "                              //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND XSHDBH = :XSHDBH " ;

		$bind1 ['SHHR'] = $_SESSION ['auth']->userId;
        $bind1 ['SHHYJ'] = $filter['shyj']; 
		$bind1 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind1 ['XSHDBH'] = $filter['bh'];

		$this->_db->query( $sql1,$bind1 );
		
		
		$sql2 = "UPDATE H01DB012203 "
             ." SET SHPZHT = '2', "                           //审核状态    2:审核未通过
             ." SHPR = :SHPR, "                               //审核人
             ." SHPRQ = SYSDATE "                             //审核时间
             ." WHERE QYBH = :QYBH "
             ." AND XSHDBH = :XSHDBH " ;

        $bind2 ['SHPR'] = $_SESSION ['auth']->userId;
		$bind2 ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind2 ['XSHDBH'] = $filter['bh']; 

		$this->_db->query( $sql2,$bind2 );
		
		return true;
	}
}
