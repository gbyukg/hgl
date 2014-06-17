<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   分店经营商品维护(fdjyspwh)
 * 作成者：魏峰
 * 作成日：2011/02/10
 * 更新履历：
 *********************************/
class md_models_fdjyspwh extends Common_Model_Base {
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getGridData($filter) {
		//排序用列定义
		$fields = array ("","SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "SHPZHT" );
		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,DECODE(SHPZHT,'1','正常','禁用'),GUIGE,PIFAJIA,LSHJ,HUIYUANJIA,KOULV,ZHJM,CFFL,SHPZHT,CHFFL" 
			. " FROM H01VIEW012503"			
		    . " WHERE QYBH = :QYBH AND MDBH = :MDBH";
				
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		$bind ['MDBH'] = '000001';
		
		//查找条件
		if ($filter ["searchParams"]['SHPBH'] != "") {
			$sql .= " AND (SHPBH LIKE '%' || :SHPBHKEY || '%' OR SHPMCH LIKE '%' || :SHPBHKEY || '%' OR ZHJM LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = $filter ["searchParams"]['SHPBH'];
		}
		if ($filter ["flbm"] != "") {
			//分类编码
			$sql .= " AND MDSHPFL IN(SELECT SHPFL FROM H01VIEW012508 START WITH SHPFL = :FLBM OR SHJFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
			$bind ['FLBM'] = $filter ["flbm"];
		}
		
	    //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_MDJYSHPXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",QYBH,MDBH,SHPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数		
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}	
	
	/**
	 * 更改商品状态
	 *
	 * @return bool
	 */
	function updateStatus($shpbh, $shpzht) {
		
		$sql = "UPDATE H01DB012503 "; 
		if($shpzht==0){
			 $sql .= " SET SHPZHT = '1'"; 
		}else{
			 $sql .= " SET SHPZHT = '0'"; 
		};
	    $sql .= " WHERE QYBH =:QYBH AND MDBH = :MDBH AND SHPBH =:SHPBH";
		//$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => $_SESSION ['mdbh']->mdbh,'SHPBH' => $shpbh );
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => '000001','SHPBH' => $shpbh );
		return $this->_db->query ( $sql, $bind );
	
	}	
	
    /*
	 * 商品选择（门店）列表数据取得（xml格式）
	 */
	function getmdshpListData($filter) {
		//排序用列定义
		$fields = array ("", "A.SHPBH","A.SHPMCH" );
		
		$bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号
	
		//检索SQL
		$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.BZHDWMCH,A.SHCHCHJ,A.ZHJM,A.CHANDI,A.YPGNZHZHLXQK,A.SHPTM,A.GJBM,".
		       "A.JLGG,A.JIXINGMCH,A.HUAXUEMING,A.CHYM,A.SUMING,A.XWMCH,A.TYMCH,A.YWCHF,A.LEIBIEMCH,A.YYFLMCH,A.CHFFLMCH,A.YFPDMCH,DECODE(A.GZHBZH,'0','普通','1','贵重',''),".
		       "A.SHPLXMCH,A.YFYYL,A.KEMUHAO,A.BLFY,A.JJZH,A.SHYZH,A.CHCZHYSHX,A.ZHYSHX,DECODE(A.BZHQFSH,'0','没有','1','有',''),A.BZHQYSH,A.SHUILV,A.ZDSHJ,A.ZGSHJ,A.LSHJ,DECODE(A.SHFYP,'0','否','1','是',''),A.SHFOTCMCH,DECODE(A.SHFYINPIAN,'0','否','1','是','')".
		       "FROM H01VIEW012101 A ".
		       "WHERE A.QYBH = :QYBH ".
                 "AND A.SHPZHT = '1' ".                //状态：可用
                 "AND A.FDSHPBZH = '1' ";              //分店标志：分店可用
	
		//有条件时以条件为准无条件时以分类为准
		if ($filter ["searchParams"]['SEARCHKEY'] != "") {
			$sql .= " AND (A.SHPBH LIKE '%' || :SEARCHKEY || '%' OR A.SHPMCH LIKE '%' || :SEARCHKEY || '%' OR lower(A.ZHJM) LIKE '%' || lower(:SEARCHKEY) || '%' )";
			$bind['SEARCHKEY'] = $filter ["searchParams"]['SEARCHKEY'];
		}elseif ($filter ["flbm"] != ""){
			//分类编码
			$sql .= " AND A.FLBM IN(SELECT SHPFL FROM H01DB012109 WHERE QYBH = :QYBH START WITH SHPFL = :FLBM OR SHJFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
		    $bind['FLBM'] = $filter ["flbm"];
		}
		
			    //自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("MD_SHPXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		$sql .= " ,A.QYBH,A.SHPBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"],$bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"],$bind );
		Common_Logger::logMessage($pagedSql ["sql_page"]);
		
		return Common_Tool::createXml ( $recs,true,$totalCount, $filter ["posStart"] );
	}
	
	//得到指定库区类型列表
	function getZhdkqlxList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$kqlxList = $this->_db->fetchPairs ( $sql . $where, $bind );
		ksort ( $kqlxList );
		return $kqlxList;
	}
	
	/**
	 * 门店商品编号是否已存在check
	 *
	 * @return int $count {0:不存在;其他:存在}
	 */
	function getMdshpbh($shpbh) {
		$sql = "SELECT COUNT(1) FROM H01DB012503 WHERE QYBH = :QYBH AND MDBH = :MDBH AND SHPBH = :SHPBH";
		//$bind = array ('QYBH' => $_SESSION ['auth']->qybh,'MDBH' => $_SESSION ['mdbh']->mdbh,'SHPBH' => $shpbh );
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => '000001','SHPBH' => $shpbh );
		$count = $this->_db->fetchOne ( $sql, $bind );
		return $count;
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($shpbh,$mdshpfl) {
		if ($shpbh == ""){  
			//商品编号
			return false;
		}
		
		if ($mdshpfl == ""){  
			//商品分类编码
			return false;
		}
		
		return true;
	}
		
	/**
	 * 生成门店商品信息
	 *
	 * @return bool
	 */
	function insertMdshp() {
		
		//判断商品资料是否存在
		if ($this->getMdshpbh ( $_POST ['SHPBH']) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['MDBH'] = '000001'; //门店编号
			$data ['SHPBH'] = $_POST ['SHPBH']; //商品编号			
			$data ['MDSHPFL'] = $_POST ['MDSHPFL']; //门店商品分类编号			
			$data ['YJTSH'] = $_POST ['YJTSH']; //预警天数
			$data ['HUIYUANJIA'] = $_POST ['HUIYUANJIA']; //会员价
			$data ['PIFAJIA'] = $_POST ['PIFAJIA']; //批发价		
			$data ['LSHJ'] = $_POST ['LSHJMD']; //零售价
			$data ['KOULV'] = $_POST ['KOULV']; //扣率
			$data ['KCSHX'] = $_POST ['KCSHX']; //库存上限
			$data ['KCXX'] = $_POST ['KCXX']; //库存下限
			$data ['HLKC'] = $_POST ['HLKC']; //合理库存			
			$data ['KQLX'] = $_POST ['KQLX']; //指定库区类型
			$data ['KWBH'] = $_POST ['KWBH']; //库位编号		
			$data ['SHFCJZHK'] = $_POST ['SHFCJZHK']; //参与折扣活动
			$data ['SHPZHT'] = '1'; //商品状态
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期								
											
			//门店商品信息表
			$this->_db->insert ( "H01DB012503", $data );
			return true;
		}
	}
	
	//得到商品信息
	function getshpData($shpbh) {

		//检索SQL
		$sql = "SELECT A.SHPBH,A.SHPMCH,A.GUIGE,A.SHCHCHJ,A.ZHJM,A.CHANDI,A.YPGNZHZHLXQK,A.SHPTM,A.GJBM,".
		       "A.JLGG,A.JIXINGMCH,A.HUAXUEMING,A.CHYM,A.SUMING,A.XWMCH,A.TYMCH,A.YWCHF,A.LEIBIEMCH,A.YYFLMCH,A.CHFFLMCH,A.YFPDMCH,DECODE(A.GZHBZH,'0','普通','1','贵重','') GZHBZHMCH,".
		       "A.SHPLXMCH,A.YFYYL,A.KEMUHAO,A.BLFY,A.JJZH,A.SHYZH,A.CHCZHYSHX,A.ZHYSHX,DECODE(A.BZHQFSH,'0','没有','1','有','') BZHQFSHMCH,A.BZHQYSH,A.SHUILV,A.ZDSHJ,A.ZGSHJ,A.LSHJ,DECODE(A.SHFYP,'0','否','1','是','') SHFYPMCH,A.SHFOTCMCH,DECODE(A.SHFYINPIAN,'0','否','1','是','') SHFYINPIANMCH ".
		       "FROM H01VIEW012101 A ".
               "WHERE A.QYBH = :QYBH ".
               "AND A.SHPBH = :SHPBH ";
			
	    $bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号	
	    $bind['SHPBH'] = $shpbh; //商品编号
		
		return $this->_db->fetchRow ( $sql, $bind );

	}	
	
	//得到门店商品信息
	function getmdshpData($shpbh,$filter=null,$flg = 'current') {
		
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）		
		//排序用列定义
		$fields = array ("","SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "SHPZHT" );
						
		$sql_list = "SELECT MDSHPROWID,LEAD(MDSHPROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",MDBH,SHPBH) AS NEXTROWID,".
		            "                LAG(MDSHPROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] .",MDBH,SHPBH) AS PREVROWID,QYBH,MDBH,SHPBH".
		            " FROM H01VIEW012503" . 
		            " WHERE QYBH = :QYBH AND MDBH = :MDBH";				
		
	    //绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//$bind ['MDBH'] = $_SESSION ['auth']->mdbh;
		$bind ['MDBH'] = '000001';
		
		//查找条件
		if($filter ["searchParams"]['SEARCHKEY']!=""){
			$sql_list .= " AND (SHPBH LIKE '%' || :SHPBHKEY || '%' OR SHPMCH LIKE '%' || :SHPBHKEY || '%' OR ZHJM LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = strtolower($filter ["searchParams"]['SEARCHKEY']);
		}
		if ($filter ["flbm"] != "") {
			//分类编码
			$sql_list .= " AND MDSHPFL IN(SELECT SHPFL FROM H01VIEW012508 START WITH SHPFL = :FLBM OR SHJFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
			$bind ['FLBM'] = $filter ["flbm"];
		}	
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("MD_MDJYSHPXX",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql = "SELECT SHPBH,MDSHPFL,YJTSH,HUIYUANJIA,PIFAJIA,LSHJ AS LSHJMD,KOULV,KCSHX,KCXX,HLKC,KQLX,KWBH,SHFCJZHK,SHPZHT,BEIZHU,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') BGRQ, KQLXMCM "
		    .  "FROM H01VIEW012503 ";

		if ($flg == 'current') {
			$sql .= 	"WHERE QYBH = :QYBH ".
			              "AND MDBH = :MDBH ".
                          "AND SHPBH = :SHPBH ";
			unset ( $bind ['SHPBHKEY'] );
			unset ( $bind ['FLBM'] );
			
		}else if ($flg == 'next') { //下一条
			$sql .= " WHERE MDSHPROWID = (SELECT NEXTROWID FROM(" . $sql_list . ") WHERE QYBH = :QYBH AND MDBH = :MDBH AND SHPBH =:SHPBH) ";
		} else if ($flg == 'prev') { //前一条
			$sql .= " WHERE MDSHPROWID = (SELECT PREVROWID FROM(" . $sql_list . ") WHERE QYBH = :QYBH AND MDBH = :MDBH AND SHPBH =:SHPBH) ";
		}
		
	    $bind['QYBH'] = $_SESSION['auth']->qybh; //区域编号	
	    $bind['MDBH'] = '000001'; //门店编号
	    $bind['SHPBH'] = $shpbh; //商品编号
		
		return $this->_db->fetchRow ( $sql, $bind );

	}

    /**
	 * 更新门店商品资料信息
	 * @return bool
	 */
	function updateMdshp() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012503 WHERE QYBH = :QYBH AND MDBH = :MDBH AND SHPBH = :SHPBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'MDBH' => '000001','SHPBH' => $_POST ['SHPBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更	
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012503 SET " 
			     . " MDSHPFL = :MDSHPFL," 
			     . " YJTSH = :YJTSH," 
			     . " HUIYUANJIA = :HUIYUANJIA," 
			     . " PIFAJIA = :PIFAJIA," 
			     . " LSHJ = :LSHJ," 
			     . " KOULV = :KOULV," 
			     . " KCSHX = :KCSHX," 
			     . " KCXX = :KCXX," 
			     . " HLKC = :HLKC," 
			     . " KQLX = :KQLX," 
			     . " KWBH = :KWBH," 
			     . " SHFCJZHK = :SHFCJZHK," 
			     . " BEIZHU = :BEIZHU," 
			     . " BGZH = :BGZH," 
			     . " BGRQ = sysdate"
			     . " WHERE QYBH = :QYBH AND MDBH =:MDBH AND SHPBH =:SHPBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			//$bind ['MDBH'] = $_POST ['MDBH']; //门店编号
			$bind ['MDBH'] = '000001'; //门店编号
			$bind ['SHPBH'] = $_POST ['SHPBH']; //商品编号					
			$bind ['MDSHPFL'] = $_POST ['MDSHPFL']; //门店商品分类编号			
			$bind ['YJTSH'] = $_POST ['YJTSH']; //预警天数
			$bind ['HUIYUANJIA'] = $_POST ['HUIYUANJIA']; //会员价
			$bind ['PIFAJIA'] = $_POST ['PIFAJIA']; //批发价
			$bind ['LSHJ'] = $_POST ['LSHJMD']; //零售价
			$bind ['KOULV'] = $_POST ['KOULV']; //扣率
			$bind ['KCSHX'] = $_POST ['KCSHX']; //库存上限
			$bind ['KCXX'] = $_POST ['KCXX']; //库存下限
			$bind ['HLKC'] = $_POST ['HLKC']; //合理库存	
			$bind ['KQLX'] = $_POST ['KQLX']; //库区类型	
			$bind ['KWBH'] = $_POST ['KWBH']; //库位编号	
            $bind ['SHFCJZHK'] = $_POST ['SHFCJZHK'] ==null? '0':'1';	
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}	
		
}