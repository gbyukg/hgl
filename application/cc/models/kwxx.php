<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库位信息(kwxx)
 * 作成者：苏迅
 * 作成日：2010/11/10
 * 更新履历：
 *********************************/
class cc_models_kwxx extends Common_Model_Base {
	
	/**
	 * 得到库位信息列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "KWZHT", "CKBH", "KQBH", "KWBH", "NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT DECODE(KWZHT,'1','正常','0','冻结','X','禁用','9','盘点冻结') AS KWZHT,CKMCH,KQMCH,KWBH,"
		     . "KWMCH,DECODE(SHFSHKW,'1','是','否') AS SHFSHKW,JHSHX,HJPH,HJLH,HJSHWZH,HGL_DEC(KRNZHL),HGL_DEC(KWCH),HGL_DEC(KWK),"
		     . "HGL_DEC(KWG),ZHDSHPBH,HGL_DEC(KRNSHPSHL),TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM,CKBH,KQBH"
		     . " FROM H01VIEW012403"
		     . " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件
		if($filter['searchParams']['CKBHKEY']!=""){
			$sql .= " AND( CKBH LIKE '%' || :CKBHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :CKBHKEY || '%')";
			$bind ['CKBHKEY'] = strtolower($filter ["searchParams"]['CKBHKEY']);
		}
		
		if($filter['searchParams']['KQBHKEY']!=""){
			$sql .= " AND( KQBH LIKE '%' || :KQBHKEY || '%'".
			        "      OR  lower(KQMCH) LIKE '%' || :KQBHKEY || '%')";
			$bind ['KQBHKEY'] = strtolower($filter ["searchParams"]['KQBHKEY']);
		}
		
		if($filter['searchParams']['KWBHKEY']!=""){
			$sql .= " AND( KWBH LIKE '%' || :KWBHKEY || '%'".
			        "      OR  lower(KWMCH) LIKE '%' || :KWBHKEY || '%')";
			$bind ['KWBHKEY'] = strtolower($filter ["searchParams"]['KWBHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_KWXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CKBH,KQBH,KWBH";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	//得到仓库列表
	public function getCangkuList() {
		$sql = "SELECT CKBH,CKMCH FROM H01DB012401 WHERE QYBH = :QYBH AND CKZHT = '1' ORDER BY CKBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$cangkuList = $this->_db->fetchPairs ( $sql, $bind );
		$cangkuList ['0'] = '- - 请 选 择 - -';
		ksort ( $cangkuList );
		return $cangkuList;
	}
	
	//得到库区列表
	public function getKuquList($cangku) {
		$sql = "SELECT KQBH,KQMCH FROM H01DB012402";
		$sql .= " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQZHT = '1' ORDER BY KQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $cangku );
		$KuquList = $this->_db->fetchPairs ( $sql, $bind );
		$KuquList ['0'] = '- - 请 选 择 - -';
		ksort ( $KuquList );
		return $KuquList;
	
	}
	
	/**
	 * 取得库位信息
	 *
	 * @param string $ygbh   仓库编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getKwxx($ckbh, $kqbh, $kwbh, $filter=null, $flg = 'current') {
		//排序用字段名
		$fields = array ("", "KWZHT", "CKBH", "KQBH", "KWBH", "NLSSORT(KWMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql_list = "SELECT ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,KQBH,KWBH) AS NEXTROWID," 
			      . "LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",CKBH,KQBH,KWBH) AS PREVROWID, " 
			      . "CKBH,KQBH,KWBH" 
			      . " FROM H01VIEW012403" 
			      . " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件
		if($filter['searchParams']['CKBHKEY']!=""){
			$sql_list .= " AND( CKBH LIKE '%' || :CKBHKEY || '%'".
			        "      OR  lower(CKMCH) LIKE '%' || :CKBHKEY || '%')";
			$bind ['CKBHKEY'] = strtolower($filter ["searchParams"]['CKBHKEY']);
		}
		
		if($filter['searchParams']['KQBHKEY']!=""){
			$sql_list .= " AND( KQBH LIKE '%' || :KQBHKEY || '%'".
			        "      OR  lower(KQMCH) LIKE '%' || :KQBHKEY || '%')";
			$bind ['KQBHKEY'] = strtolower($filter ["searchParams"]['KQBHKEY']);
		}
		
		if($filter['searchParams']['KWBHKEY']!=""){
			$sql_list .= " AND( KWBH LIKE '%' || :KWBHKEY || '%'".
			        "      OR  lower(KWMCH) LIKE '%' || :KWBHKEY || '%')";
			$bind ['KWBHKEY'] = strtolower($filter ["searchParams"]['KWBHKEY']);
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("CC_KWXX",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql_single = "SELECT CKBH,KQBH,CKMCH,KQMCH,KWBH,KWMCH,SHFSHKW,JHSHX,HGL_DEC(KRNZHL) AS KRNZHL,"
					. "HGL_DEC(KWCH) AS KWCH,HGL_DEC(KWK) AS KWK,HGL_DEC(KWG) AS KWG,ZHDSHPBH,HGL_DEC(KRNSHPSHL) AS KRNSHPSHL,"
					. "TO_CHAR(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,BGZH,HJPH,HJLH,HJSHWZH " 
		            . "FROM H01VIEW012403";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH =:QYBH AND CKBH =:CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
			unset ( $bind ['CKBHKEY'] );
			unset ( $bind ['KQBHKEY'] );
			unset ( $bind ['KWBHKEY'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,KWBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,KWBH FROM ( $sql_list ) WHERE CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH))";
		}
		//绑定查询条件
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 生成库位信息
	 *
	 * @return bool
	 */
	function insertKwxx() {
		
		//判断库位是否存在
		if ($this->getKwxx ( $_POST ['CKBH'],$_POST ['KQBH'],$_POST ['KWBH']) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$data ['KQBH'] = $_POST ['KQBH']; //库区编号
			$data ['KWBH'] = ($_POST ['HJPH'].$_POST ['HJLH'].$_POST ['HJSHWZH']); //库位编号
			$data ['HJPH'] = $_POST ['HJPH'];//货架排号
			$data ['HJLH'] = $_POST ['HJLH'];//货架列号
			$data ['HJSHWZH'] = $_POST ['HJSHWZH'];//货架上位置
			$data ['KWMCH'] = $_POST ['KWMCH']; //库位名称
			$data ['JHSHX'] = $_POST ['JHSHX']; //拣货顺序
			$data ['KRNZHL'] = $_POST ['KRNZHL']; //可容纳重量(KG)
			$data ['KWCH'] = $_POST ['KWCH']; //库位长(CM)
			$data ['KWK'] = $_POST ['KWK']; //库位宽(CM)
			$data ['KWG'] = $_POST ['KWG']; //库位高(CM)
			$data ['ZHDSHPBH'] = $_POST ['ZHDSHPBH']; //指定保存商品编号
			$data ['KRNSHPSHL'] = $_POST ['KRNSHPSHL']; //可容纳指定商品数量				
			$data ['KWZHT'] = '1'; //使用状态可用
			$data ['SHFSHKW'] = ($_POST ['SHFSHKW'] == null) ? '0' : '1';//是否散货库位
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			//库位信息表
			$this->_db->insert ( "H01DB012403", $data );
			return true;
		
		}
	}
	
	/**
	 * 更新库位信息
	 *
	 * @return bool
	 */
	function updateKwxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012403 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $_POST ['CKBH'], 'KQBH' => $_POST ['KQBH'], 'KWBH' => $_POST ['KWBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012403 SET " . " QYBH = :QYBH," . " CKBH = :CKBH," . " KQBH = :KQBH," . " KWBH = :KWBH," . " KWMCH = :KWMCH," . " JHSHX = :JHSHX," . " KRNZHL = :KRNZHL," . " KWCH = :KWCH," . " KWK = :KWK," . " KWG = :KWG," . " ZHDSHPBH = :ZHDSHPBH," . " KRNSHPSHL = :KRNSHPSHL," . " SHFSHKW = :SHFSHKW," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND CKBH =:CKBH AND KQBH = :KQBH AND KWBH = :KWBH";		
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['CKBH'] = $_POST ['CKBH']; //仓库编号
			$bind ['KQBH'] = $_POST ['KQBH']; //库区编号
			$bind ['KWBH'] = $_POST ['KWBH']; //库位编号
			$bind ['KWMCH'] = $_POST ['KWMCH']; //库位名称
			$bind ['JHSHX'] = $_POST ['JHSHX']; //拣货顺序
			$bind ['KRNZHL'] = $_POST ['KRNZHL']; //可容纳重量(KG)
			$bind ['KWCH'] = $_POST ['KWCH']; //库位长(CM)
			$bind ['KWK'] = $_POST ['KWK']; //库位宽(CM)
			$bind ['KWG'] = $_POST ['KWG']; //库位高(CM)
			$bind ['ZHDSHPBH'] = $_POST ['ZHDSHPBH']; //指定保存商品编号
			$bind ['KRNSHPSHL'] = $_POST ['KRNSHPSHL']; //可容纳指定商品数量				
			$bind ['SHFSHKW'] = ($_POST ['SHFSHKW'] == null) ? '0' : '1';//是否散货库位
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
	
	//更改库位状态
	function updateStatus($ckbh, $kqbh, $kwbh, $kwzht) {
		
		$sql = "UPDATE H01DB012403 " . " SET KWZHT = :KWZHT" . " WHERE QYBH =:QYBH AND CKBH =:CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
		$bind = array ('KWZHT' => $kwzht, 'QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'KQBH' => $kqbh, 'KWBH' => $kwbh );
		return $this->_db->query ( $sql, $bind );
	
	}
	
	//检查库位所在库区状态是否可用
	function getKqzht($ckbh, $kqbh) {
		
		$sql = "SELECT KQZHT FROM H01DB012402 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'CKBH' => $ckbh, 'KQBH' => $kqbh );
		$kqzht = $this->_db->fetchOne ( $sql, $bind );
		return $kqzht;
	}

}
