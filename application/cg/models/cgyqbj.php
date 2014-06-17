<?php
/*********************************
 * 模块：   采购模块(CG)
 * 机能：   采购逾期报警
 * 作成者：侯殊佳
 * 作成日：2011/06/10
 * 更新履历：
 * 	2011/08/31  LiuC  追加退货单明细显示
 *********************************/
class cg_models_cgyqbj extends Common_Model_Base {
	
	/**
	 * 得到采购逾期报警列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		
		//检索SQL
		$sql = "SELECT * FROM ".
			   " (SELECT T1.CGDBH,TO_CHAR(T1.KPRQ,'YYYY-MM-DD')AS KPRQ,TO_CHAR(T1.CGQRRQ,'YYYY-MM-DD') AS CGQRRQ,DECODE(T1.YDHRQ,NULL,TO_CHAR(T1.CGQRRQ + 15,'YYYY-MM-DD'),TO_CHAR(T1.YDHRQ,'YYYY-MM-DD')) AS YDHRQ,".
			   " T1.DWBH,T2.DWMCH FROM H01DB012306 T1,H01DB012106 T2 ".
 			   " WHERE T1.QYBH =T2.QYBH AND T1.QYBH = :QYBH AND T1.CGDZHT = '1' AND T1.QXBZH != 'X' AND T1.DWBH = T2.DWBH )".
 			   " WHERE YDHRQ < TO_CHAR(sysdate,'YYYY-MM-DD')";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
	
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
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
				."DECODE(A.RKZHT,'1','未入库','2','已入库','3','已预入库') AS RKZHT "     //入库状态
			  ."FROM H01DB012307 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "       //区域编号
			  ."AND A.QXBZH != 'X' "         //取消标志
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
	
}