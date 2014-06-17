<?php
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：        库间调拨返库详情(KJDBFKXQ)
 ***** 作  成  者：        姚磊
 ***** 作  成  日：        2011/01/29
 ***** 更新履历：
 ******************************************************************/

class cc_models_kjdbfkxq extends Common_Model_Base {
	/**
	 * 库间调拨入库单信息获取
	 *
	 * @param string $bh
	 * @return array[]
	 */
	function getinfoData($bh){
		//检索SQL
		$sql = "SELECT TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,"           //开票日期
				."A.DJBH,"      		  //单据编号
				."A.DYDBCHKD,"            //对应调拨出库单
				."E.BMMCH,"     		  //部门名称
				."D.YGXM,"      		  //员工名称
				."B.CKMCH AS DCCKMCH,"    //调出仓库
				."C.CKMCH AS DRCKMCH,"    //调入仓库
				."A.DRCKDZH,"             //调入仓库地址
				."A.SHFPS,"               //是否配送
				."A.DHHM,"     		      //电话号码
				."A.BEIZHU,"      		  //备注
				."A.DCHCK,"  		      //调出仓库编号
				."A.DRCK,"                //调入仓库编号
				."TO_CHAR(A.BGRQ,'YYYY-MM-DD HH:mm:ss') AS BGRQ "    //变更日期
			  ."FROM H01DB012423 A "
			  ."LEFT JOIN H01DB012401 B ON A.QYBH = B.QYBH AND A.DCHCK = B.CKBH "
			  ."LEFT JOIN H01DB012401 C ON A.QYBH = C.QYBH AND A.DRCK = C.CKBH "
			  ."LEFT JOIN H01DB012113 D ON A.QYBH = C.QYBH AND A.YWYBH = D.YGBH "
			  ."LEFT JOIN H01DB012112 E ON A.QYBH = C.QYBH AND A.BMBH = E.BMBH "
			  ."WHERE A.QYBH = :QYBH "
			  ."AND A.DJBH = :DJBH ";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $bh;                         //单据编号

		return $this->_db->fetchRow( $sql, $bind );
	}


	/*
	 * 根据调拨出库单号取得调拨出库单明细信息
	 */
	public function getmingxi($filter) {
		//检索SQL
		$sql = "SELECT A.XUHAO,"          //序号
				."A.SHPBH,"      		  //商品编号
				."B.SHPMCH,"     		  //商品名称
				."B.GUIGE,"      		  //规格
				."C.NEIRONG AS BZHDWM,"   //包装单位
				."A.PIHAO,"      		  //批号
				."TO_CHAR(A.SHCHRQ,'yyyy-mm-dd') AS SHCHRQ,"   //生产日期
				."TO_CHAR(A.BZHQZH,'yyyy-mm') AS BZHQZH,"   //保质期至
				."A.BZHSHL,"     		  //包装数量
				."A.LSSHL,"      		  //零散数量
				."A.SHULIANG,"  		  //数量
				."B.CHANDI,"     		  //产地
				."A.BEIZHU,"      	      //备注
				."A.BZHDWBH,"    		  //包装单位编号
				."B.TYMCH,"               //通用名
				."B.JLGG "                //计量规格
			  ."FROM H01DB012424 A "
			  ."LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH "
			  ."LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
			  ."WHERE A.QYBH = :QYBH "
			  ."AND A.DJBH = :DJBH "
			  ."ORDER BY A.XUHAO";

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;    //区域编号
		$bind ['DJBH'] = $filter ['bh'];              //单据编号
		
		return $this->_db->fetchAll( $sql, $bind );
	}

}