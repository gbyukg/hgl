<?php
/******************************************************************
 ***** 模         块：       采购模块(CG)
 ***** 机         能：       采购退货详情(CGKPXQ)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/03/14
 ***** 更新履历：
 *****
 ******************************************************************/

class cg_models_cgkpxq extends Common_Model_Base {
	
	/**
	 * 根据编号取得采购开票单信息
	 * 
	 * @param array $filter
	 * @return array
	 */
	public function getInfo($bh){
		//检索SQL
		$sql = "SELECT A.CGDBH,TO_CHAR(A.KPRQ,'YYYY-MM-DD') AS KPRQ,A.DWBH,E.DWMCH,B.BMMCH,C.YGXM AS YWY,".
		 " A.DHHM,TO_CHAR(A.YDHRQ,'YYYY-MM-DD')AS YDHRQ,A.SHFZZHSH ,A.DIZHI,".
		 " A.BEIZHU,A.KOULV,D.YGXM AS KPY FROM H01DB012306 A ".
		 " LEFT OUTER JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH ".
		 " LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH ".
		 " LEFT JOIN H01DB012113 D ON A.QYBH = D.QYBH AND A.BGZH = D.YGBH ".
		 " LEFT JOIN H01DB012106 E ON A.QYBH = E.QYBH AND A.DWBH = E.DWBH ".
		 " WHERE A.QYBH = :QYBH AND A.CGDBH =:CGDBH " ;
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGDBH' => $bh );
		$recs = $this->_db->fetchRow ( $sql, $bind );
		return $recs;
	}
	
	
	/**
	 * 根据编号得到采购开票单明细列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getMingxiData($bh){
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG,".
		 " A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.HSHJE,A.JINE,A.SHUIE,B.LSHJ,B.CHANDI,D.BEIZHU,B.TYMCH " . 
		 " FROM H01DB012307 A ".
		 " LEFT OUTER JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " .
		 " LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW'" .
		 " LEFT JOIN H01DB012304 D ON A.QYBH = D.QYBH AND A.CGDBH = D.CGGZHDBH " .
		 " WHERE A.QYBH = :QYBH AND A.CGDBH = :CGDBH";
		 $bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CGDBH' => $bh );
		 $recs = $this->_db->fetchAll($sql,$bind);
		return Common_Tool::createXml ( $recs, true );
	}
	
	/**
	 * 取得上下条采购退货详情
	 *
	 * @param string $bh   编号
	 * @param string $JS   警示标识
	 * @param string $flg  查找方向： current,next,prev
	 * @return array 
	 */
	function getxinxi($bh, $JS, $flg = 'current'){
		
		//检索集合
		$sql_list = "SELECT A.ROWID,LEAD(A.ROWID) OVER(ORDER BY A.CGDBH ASC) AS NEXTROWID,".
		            "LAG(A.ROWID) OVER(ORDER BY A.CGDBH ASC) AS PREVROWID,".
					"A.CGDBH FROM H01DB012306 A "
					."LEFT JOIN H01DB012112 B ON A.QYBH = B.QYBH AND A.BMBH = B.BMBH "
					."LEFT JOIN H01DB012113 C ON A.QYBH = C.QYBH AND A.YWYBH = C.YGBH "
					."LEFT JOIN H01DB012106 D ON A.QYBH = D.QYBH AND A.DWBH = D.DWBH "
					."WHERE A.QYBH = :QYBH "     //区域编号
					."AND A.SHPZHT = '0' "       //审批状态       0：未审核     1：已审核
					."AND A.QXBZH = '1' ";       //取消标准       1：正常          X：删除状态

		//是否报警开票单  0：正常采购开票单            1：报警采购开票单
		if ( $JS == "0" ) {
			$sql_list .= "AND NOT EXISTS (SELECT E.CGDBH FROM H01DB012303 E WHERE E.QYBH = A.QYBH AND E.CGDBH = A.CGDBH)";  
		}else{
			$sql_list .= "AND EXISTS (SELECT E.CGDBH FROM H01DB012303 E WHERE E.QYBH = A.QYBH AND E.CGDBH = A.CGDBH)"; 
		}
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//排序
		$sql_list .= " ORDER BY A.CGDBH";
			  
		//检索SQL
		$sql = "SELECT T1.CGDBH,TO_CHAR(T1.KPRQ,'YYYY-MM-DD') AS KPRQ,T1.DWBH,T4.DWMCH,T2.BMMCH,T3.YGXM AS YWY,".
		" T5.YGXM AS KPY,T1.DHHM, T1.SHFZZHSH,T1.DIZHI,TO_CHAR(T1.YDHRQ,'YYYY-MM-DD') AS YDHRQ,T1.BEIZHU,T1.KOULV ".
		" FROM H01DB012306 T1 ".
		" LEFT OUTER JOIN H01DB012112 T2 ON T1.QYBH = T2.QYBH AND T1.BMBH = T2.BMBH  " .
		" LEFT JOIN H01DB012113 T3 ON T1.QYBH = T3.QYBH AND T1.YWYBH = T3.YGBH " . 
		" LEFT JOIN H01DB012106 T4 ON T1.QYBH = T4.QYBH AND T1.DWBH = T4.DWBH".
		" LEFT JOIN H01DB012113 T5 ON T1.QYBH = T5.QYBH AND T1.BGZH = T5.YGBH " ;
		if ($flg == 'current') {
			$sql .= " WHERE  T1.QYBH =:QYBH AND T1.CGDBH =:CGDBH";
		} else if ($flg == 'next') {
			$sql .= "WHERE T1.ROWID = (SELECT NEXTROWID FROM (SELECT NEXTROWID,CGDBH FROM ( $sql_list ) WHERE CGDBH = :CGDBH))";
		} else if ($flg == 'prev') {
			$sql .= "WHERE T1.ROWID = (SELECT PREVROWID FROM (SELECT PREVROWID,CGDBH FROM ( $sql_list ) WHERE CGDBH = :CGDBH))";
		}

		//绑定查询条件
		$bind['CGDBH'] = $bh;      //编号

		return $this->_db->fetchRow( $sql , $bind );
	}
	
	
}