<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   供货企业调查(ghqy)
 * 作成者：姚磊
 * 作成日：2010/11/19
 * 更新履历：
 *********************************/
class jc_models_ghqy extends Common_Model_Base {
	
	/**
	 * 得到企业供货信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "DJBH", "QYMCH" ); //单据号 企业名称	 
		//检索SQL		
		$sql = "SELECT DJBH,QYMCH,FRDB,SHCHJYXKZHH,ZHZHZHCH,ZHGZSH,ZHLFZR,ZHLRYZHBFB,SHNDXSHE,QYSHCHHXSHPZH,DECODE(YXKZH,'1','是','否') AS YXKZH, 
		XKZHH,TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),GMPGSPTGSHJ,SHNDSHHZHLHGL,ZHLBLXDH,YZHBM,DECODE(WZHDSHG,'1','是','否') AS WZHDSHG,ZJXPZH,XSHRYXM,XSHYWHSZH,DECODE(XSHYYPXRZH,'1','是','否') AS XSHYYPXRZH,
		DECODE(XSHYWWFJL,'1','是','否') AS XSHYWWFJL,DECODE(YSHH,'1','是','否') AS YSHH,DECODE(GHQYSHFGZH,'1','是','否') AS GHQYSHFGZH,TO_CHAR(GZHRQ,'YYYY-MM-DD'),TIANXIEREN,TO_CHAR(TXRQ,'YYYY-MM-DD'),TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZH FROM H01VIEW012108 WHERE QYBH = :QYBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;

		//绑定查询条件 企业名称
		if($filter['searchParams']['SEARCHKEYQYMCH']!=""){
			$sql .= " AND( QYMCH LIKE '%' || :SEARCHKEYQYMCH || '%')";
			$bind ['SEARCHKEYQYMCH'] = strtolower($filter ["searchParams"]['SEARCHKEYQYMCH']);
		}

			//绑定查询条件 填写日期
		if($filter['searchParams']['SEARCHKEYTXRQ']!=""){
			$sql .= " AND( TO_CHAR(TXRQ,'YYYY-MM-DD') LIKE '%' || :SEARCHKEYTXRQ || '%')";
			$bind ['SEARCHKEYTXRQ'] = strtolower($filter ["searchParams"]['SEARCHKEYTXRQ']);
		}
		

		$sql .= Common_Tool::createFilterSql("JC_GHQYWH",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= " ,DJBH";
		
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
	 * 取得供货企业信息
	 *
	 * @param string $djbh   单据号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getGhqy($djbh, $filter, $flg = 'current') {
		//检索SQL
		

		$fields = array ("", "DJBH", "QYMCH" ); //单据号 企业名称
		$filter ["orderby"];
		$sql_list = "SELECT  ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",DJBH) AS NEXTROWID," . 
		"                          LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",DJBH) AS PREVROWID," . 
		"DJBH  FROM H01DB012108 "."WHERE QYBH = :QYBH ";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//绑定查询条件 企业名称
		if($filter['searchParams']['SEARCHKEYQYMCH']!=""){
			$sql_list .= " AND( QYMCH LIKE '%' || :SEARCHKEYQYMCH || '%')";
			$bind ['SEARCHKEYQYMCH'] = strtolower($filter ["searchParams"]['SEARCHKEYQYMCH']);
		}

			//绑定查询条件 填写日期
		if($filter['searchParams']['SEARCHKEYTXRQ']!=""){
			$sql_list .= " AND( TO_CHAR(TXRQ,'YYYY-MM-DD') LIKE '%' || :SEARCHKEYTXRQ || '%')";
			$bind ['SEARCHKEYTXRQ'] = strtolower($filter ["searchParams"]['SEARCHKEYTXRQ']);
		}
		

		$sql_list .= Common_Tool::createFilterSql("JC_GHQYWH",$filter['filterParams'],$bind);
		
		$sql_single = "SELECT DJBH,QYMCH,FRDB,SHCHJYXKZHH,ZHZHZHCH,ZHGZSH,ZHLFZR,ZHLRYZHBFB,SHNDXSHE,QYSHCHHXSHPZH,YXKZH," .
		 "XKZHH,TO_CHAR(XKZHYXQ,'YYYY-MM-DD') AS XKZHYXQ,GMPGSPTGSHJ,SHNDSHHZHLHGL,ZHLBLXDH,YZHBM, WZHDSHG,ZJXPZH,XSHRYXM,XSHYWHSZH,XSHYYPXRZH," . 
		 "XSHYWWFJL,YSHH,GHQYSHFGZH,TO_CHAR(GZHRQ,'YYYY-MM-DD') AS GZHRQ,TIANXIEREN," . 
		 "TO_CHAR(TXRQ,'YYYY-MM-DD') AS TXRQ,TO_CHAR(BGRQ,'YYYY-MM-DD hh24:mi:ss') AS BGRQ,BGZH FROM H01DB012108";
		
		if ($flg == 'current') {
			$sql_single .= " WHERE  QYBH = :QYBH AND DJBH = :DJBH ";
			unset ( $bind ['SEARCHKEYQYMCH'] );
			unset ( $bind ['SEARCHKEYTXRQ'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE QYBH = :QYBH AND ROWID = (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH ))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE QYBH = :QYBH AND ROWID = (SELECT PREVROWID FROM  (SELECT PREVROWID,DJBH FROM ( $sql_list ) WHERE DJBH = :DJBH ))";
		}
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DJBH'] = $djbh;
		
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 更新企业供货信息
	 *
	 * @return bool
	 */
	function updateGhqy() {
	
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012108 WHERE QYBH = :QYBH AND DJBH = :DJBH ";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DJBH' => $_POST ['DJBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		$_POST ['BGRQ'];
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012108 SET " . " QYBH = :QYBH," . " DJBH = :DJBH," . " QYMCH = :QYMCH," . " FRDB = :FRDB," . " SHCHJYXKZHH = :SHCHJYXKZHH," . " ZHZHZHCH = :ZHZHZHCH," . " ZHGZSH = :ZHGZSH," . " ZHLFZR = :ZHLFZR," . " ZHLRYZHBFB = :ZHLRYZHBFB," . " SHNDXSHE = :SHNDXSHE," . " QYSHCHHXSHPZH = :QYSHCHHXSHPZH," . " YXKZH = :YXKZH," . " XKZHH = :XKZHH," . " XKZHYXQ = TO_DATE(:XKZHYXQ,'YYYY-MM-DD')," . " GMPGSPTGSHJ = :GMPGSPTGSHJ," . " SHNDSHHZHLHGL = :SHNDSHHZHLHGL," . " ZHLBLXDH = :ZHLBLXDH," . " YZHBM = :YZHBM," . " WZHDSHG = :WZHDSHG," . " ZJXPZH = :ZJXPZH," . " XSHRYXM = :XSHRYXM," . " XSHYWHSZH = :XSHYWHSZH," . " XSHYYPXRZH = :XSHYYPXRZH," . " XSHYWWFJL = :XSHYWWFJL," . " YSHH = :YSHH," . " GHQYSHFGZH = :GHQYSHFGZH," . " GZHRQ = TO_DATE(:GZHRQ,'YYYY-MM-DD')," . " TIANXIEREN = :TIANXIEREN," . " TXRQ = TO_DATE(:TXRQ,'YYYY-MM-DD')," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DJBH =:DJBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号										
			$bind ['DJBH'] = $_POST ['DJBH']; //单据号
			$bind ['QYMCH'] = $_POST ['QYMCH']; //企业名称
			$bind ['FRDB'] = $_POST ['FRDB']; //法人代表				
			$bind ['SHCHJYXKZHH'] = $_POST ['SHCHJYXKZHH']; //生产、经营许可证号
			$bind ['ZHZHZHCH'] = $_POST ['ZHZHZHCH']; //执照注册号
			$bind ['ZHGZSH'] = $_POST ['ZHGZSH']; //职工总数	
			$bind ['ZHLFZR'] = $_POST ['ZHLFZR']; //质量负责人
			

			$bind ['ZHLRYZHBFB'] = $_POST ['ZHLRYZHBFB']; //质量人员占百分比
			$bind ['SHNDXSHE'] = $_POST ['SHNDXSHE']; //上年度销售额
			$bind ['QYSHCHHXSHPZH'] = $_POST ['QYSHCHHXSHPZH']; //企业生产或销售品种
			$bind ['YXKZH'] = ($_POST ['YXKZH'] == null) ? '0' : '1'; //有许可证
			$bind ['XKZHH'] = $_POST ['XKZHH']; //许可证号
			$bind ['XKZHYXQ'] = $_POST ['XKZHYXQ']; //许可证有效期
			$bind ['GMPGSPTGSHJ'] = $_POST ['GMPGSPTGSHJ']; //GMP、GSP通过时间
			$bind ['SHNDSHHZHLHGL'] = $_POST ['SHNDSHHZHLHGL']; //上年度社会质量合格率
			$bind ['ZHLBLXDH'] = $_POST ['ZHLBLXDH']; //质量部联系电话
			$bind ['YZHBM'] = $_POST ['YZHBM']; //邮编
			$bind ['WZHDSHG'] = ($_POST ['WZHDSHG'] == null) ? '0' : '1'; //无重大事故？
			$bind ['ZJXPZH'] = $_POST ['ZJXPZH']; //总经销品种
			$bind ['XSHRYXM'] = $_POST ['XSHRYXM']; //销售人员姓名
			$bind ['XSHYWHSZH'] = $_POST ['XSHYWHSZH']; //销售员文化素质
			$bind ['XSHYYPXRZH'] = ($_POST ['XSHYYPXRZH'] == null) ? '0' : '1'; //销售员有培训				 
			$bind ['XSHYWWFJL'] = ($_POST ['XSHYWWFJL'] == null) ? '0' : '1'; //销售员无违法记录
			$bind ['YSHH'] = ($_POST ['YSHH'] == null) ? '0' : '1'; //要审核
			$bind ['GHQYSHFGZH'] = ($_POST ['GHQYSHFGZH'] == null) ? '0' : '1'; //供货企业是否盖章
			$bind ['GZHRQ'] = $_POST ['GZHRQ']; //盖章日期7
			$bind ['TIANXIEREN'] = $_POST ['TIANXIEREN']; //填写人
			$bind ['TXRQ'] = $_POST ['TXRQ']; //填写日期					
			$bind ['BGZH'] = $_SESSION ['auth']->userId;
			$this->_db->query ( $sql, $bind );
		
			return true;
		}

	}
	
	/**
	 * 生成企业供货信息
	 *
	 * @return bool
	 */
	function insertGhqy($djbh) {
				
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号				
			$data ['DJBH'] = $djbh; //单据号
			$data ['QYMCH'] = $_POST ['QYMCH']; //企业名称
			$data ['FRDB'] = $_POST ['FRDB']; //法人代表
			$data ['SHCHJYXKZHH'] = $_POST ['SHCHJYXKZHH']; //生产、经营许可证号
			$data ['ZHZHZHCH'] = $_POST ['ZHZHZHCH']; //执照注册号
			$data ['ZHGZSH'] = $_POST ['ZHGZSH']; //职工总数
			$data ['ZHLFZR'] = $_POST ['ZHLFZR']; //质量负责人
			$data ['ZHLRYZHBFB'] = $_POST ['ZHLRYZHBFB']; //质量人员占百分比
			$data ['SHNDXSHE'] = $_POST ['SHNDXSHE']; //上年度销售额
			$data ['QYSHCHHXSHPZH'] = $_POST ['QYSHCHHXSHPZH']; //企业生产或销售品种
			$data ['YXKZH'] = ($_POST ['YXKZH'] == null) ? '0' : '1'; //有许可证
			$data ['XKZHH'] = $_POST ['XKZHH']; //许可证号
			if ($_POST ['XKZHYXQ'] != "") {
				$data ['XKZHYXQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['XKZHYXQ'] . "','YYYY-MM-DD')" ); //许可证有效期
			}
			$data ['GMPGSPTGSHJ'] = $_POST ['GMPGSPTGSHJ']; //GMP、GSP通过时间
			$data ['SHNDSHHZHLHGL'] = $_POST ['SHNDSHHZHLHGL']; //上年度社会质量合格率
			$data ['ZHLBLXDH'] = $_POST ['ZHLBLXDH']; //质量部联系电话
			$data ['YZHBM'] = $_POST ['YZHBM']; //邮编
			$data ['WZHDSHG'] = ($_POST ['WZHDSHG'] == null) ? '0' : '1'; //无重大事故
			$data ['ZJXPZH'] = $_POST ['ZJXPZH']; //总经销品种
			$data ['XSHRYXM'] = $_POST ['XSHRYXM']; //销售人员姓名
			$data ['XSHYWHSZH'] = $_POST ['XSHYWHSZH']; //销售员文化素质
			$data ['XSHYYPXRZH'] = ($_POST ['XSHYYPXRZH'] == null) ? '0' : '1'; //销售员有培训认证					 
			$data ['XSHYWWFJL'] = ($_POST ['XSHYWWFJL'] == null) ? '0' : '1'; //销售员无违法记录
			$data ['YSHH'] = ($_POST ['YSHH'] == null) ? '0' : '1'; //要审核？
			$data ['GHQYSHFGZH'] = ($_POST ['GHQYSHFGZH'] == null) ? '0' : '1'; //供货企业是否盖章？
			if ($_POST ['GZHRQ'] != "") {
				$data ['GZHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['GZHRQ'] . "','YYYY-MM-DD')" ); //盖章日期
			}
			$data ['TIANXIEREN'] = $_POST ['TIANXIEREN']; //填写人
			if ($_POST ['TXRQ'] != "") {
				$data ['TXRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['TXRQ'] . "','YYYY-MM-DD')" ); //填写日期
			}
			$data ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户	
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$this->_db->insert ( "H01DB012108", $data );
				
			return true;			
		
	}
	
	/**
	 * 删除企业供货信息
	 *
	 * @param string $djbh   单据号
	 * @return unknown
	 */
	function deleteGhqy($djbh) {

			//开始一个事务
		if ($djbh == FALSE) { //判断单据号是否为空
			return false;
		} else {
			$sql = "DELETE H01DB012108 WHERE DJBH = :DJBH AND QYBH = :QYBH";
			$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DJBH' => $djbh );
			return $this->_db->query ( $sql, $bind );

		}

	}
}
	