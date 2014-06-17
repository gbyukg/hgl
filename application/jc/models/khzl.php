<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   客户资料(khzl)
 * 作成者：苏迅
 * 作成日：2010/11/01
 * 更新履历：
 *********************************/
class jc_models_khzl extends Common_Model_Base {
	
	/**
	 * 得到客户列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridData($filter) {
		//排序用字段名
		$fields = array ("", "KHZHT", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "", "", "", "", "DYSHYQYBH" ); //单位编号，单位名称，首营企业编号		

		//检索SQL
		$sql = "SELECT DECODE(KHZHT,'1','正常','禁用') AS KHZHT,DWBH,DWMCH,ZHJM,KEMUHAO,DECODE(SHFXSH,'1','是','') AS SHFXSH,DECODE(SHFJH,'1','是','') AS SHFJH," 
		     . "DYSHYQYBH,KHDJ,SHUIHAO,SZSHMCH,SZSHIMCH,DIZHI,DHHM,YHZHH,YZHBM,LXRXM,QYFL,KHLB,DECODE(YXKZH,'1','是','否') AS YXKZH," 
		     . "XKZHH,TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),DECODE(SHFYYYZHZH,'1','是','否') AS SHFYYYZHZH,YYZHZHH,TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD'),HYMCH,JYFW,HGL_DEC(GHXDE),GHXDQ,HGL_DEC(XSHXDE),XSHXDQ,HGL_DEC(YSHSHX),YDHTSH," 
		     . "HGL_DEC(KOULV),HGL_DEC(CSKL),DECODE(FDBSH,'1','是','否') AS FDBSH,FHQMCH,DECODE(SHFZHXZHBJ,'1','是','否') AS SHFZHXZHBJ,DECODE(SHFZHXZP,'1','是','否') AS SHFZHXZP,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM " 
		     . "FROM H01VIEW012106" 
		     . " WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 
		if($filter['searchParams']['DWBHKEY']!=""){
			$sql .= " AND( DWBH LIKE '%' || :DWBHKEY || '%'".
			        "      OR  lower(DWMCH) LIKE '%' || :DWBHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :DWBHKEY || '%')";
			$bind ['DWBHKEY'] = strtolower($filter ["searchParams"]['DWBHKEY']);
		}
		
		//查找条件..如果销售客户和供应商都被选中，则为所有记录
	    if ($filter['searchParams'] ["SHFXSH"] == 'on' AND $filter ['searchParams']["SHFJH"] == 'on'){
	    }
	    //如果只有销售客户被选中，则查询销售客户
		elseif ($filter['searchParams'] ["SHFXSH"] == 'on'){
			$sql .= " AND SHFXSH = :SHFXSH";
			$bind ['SHFXSH'] = '1';
		}
		//如果只有供应商被选中，则查询供应商
		elseif ($filter ['searchParams']["SHFJH"] == 'on'){
			$sql .= " AND SHFJH = :SHFJH";
			$bind ['SHFJH'] = '1';
		}
		
//		//查找条件 ..被选中(1)为销售，未选中(0)为所有(式样书)
//		if ($filter['searchParams'] ["SHFXSH"] == 'on') {
//			$sql .= " AND SHFXSH = :SHFXSH";
//			$bind ['SHFXSH'] = '1';
//		}
//		//查找条件  ..被选中(1)为供应商，未选中(0)为所有(式样书)
//		if ($filter ['searchParams']["SHFJH"] == 'on') {
//			$sql .= " AND SHFJH = :SHFJH";
//			$bind ['SHFJH'] = '1';
//		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("JC_KHZL",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",DWBH";
		
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
	 * 取得客户信息
	 *
	 * @param string $dwbh   单位编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getKhzl($dwbh, $filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		//排序用字段名
		$fields = array ("", "KHZHT", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "", "", "", "", "DYSHYQYBH" ); //单位编号，单位名称，首营企业编号		
		//检索SQL
		$sql_list = "SELECT LROWID,LEAD(LROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",DWBH) AS NEXTROWID,"
		          . "LAG(LROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . " ,DWBH) AS PREVROWID,DWBH"
				  . " FROM H01VIEW012106 WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 
		if($filter['searchParams']['DWBHKEY']!=""){
			$sql_list .= " AND( DWBH LIKE '%' || :DWBHKEY || '%'".
			        "      OR  lower(DWMCH) LIKE '%' || :DWBHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :DWBHKEY || '%')";
			$bind ['DWBHKEY'] = strtolower($filter ["searchParams"]['DWBHKEY']);
		}
		//查找条件 ..被选中(1)为销售，未选中(0)为所有(式样书)
		if ($filter['searchParams'] ["SHFXSH"] == 'on') {
			$sql_list .= " AND SHFXSH = :SHFXSH";
			$bind ['SHFXSH'] = '1';
		}
		//查找条件  ..被选中(1)为供应商，未选中(0)为所有(式样书)
		if ($filter ['searchParams']["SHFJH"] == 'on') {
			$sql_list .= " AND SHFJH = :SHFJH";
			$bind ['SHFJH'] = '1';
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("JC_KHZL",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql_single = "SELECT DWBH,DWMCH,ZHJM,KEMUHAO,SHFXSH,SHFJH,DYSHYQYBH,KHDJ,SHUIHAO,SZSH,SZSHI,DIZHI,DHHM,YHZHH,YZHBM,LXRXM,QYFL,KHLB,YXKZH," 
					. "XKZHH,TO_CHAR(XKZHYXQ,'YYYY-MM-DD') AS XKZHYXQ,SHFYYYZHZH,YYZHZHH,TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD') AS YYZHZHYXQ,HYMCH,JYFW,HGL_DEC(GHXDE) AS GHXDE,GHXDQ,HGL_DEC(XSHXDE) AS XSHXDE,XSHXDQ,HGL_DEC(YSHSHX) AS YSHSHX,YDHTSH,HGL_DEC(KOULV) AS KOULV," 
					. "HGL_DEC(CSKL) AS CSKL,FDBSH,FHQBH,SHFZHXZHBJ,SHFZHXZP,SHFZZHSH,SHFSHCHCHJ,KHHMCH,KHJL,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,BGZH FROM H01VIEW012106";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH =:QYBH AND DWBH =:DWBH";
			unset ( $bind ['DWBHKEY'] );
			unset ( $bind ['SHFXSH'] );
			unset ( $bind ['SHFJH'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE LROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,DWBH FROM ( $sql_list ) WHERE DWBH = :DWBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE LROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,DWBH FROM ( $sql_list ) WHERE DWBH = :DWBH))";
		}
		//绑定查询条件
		$bind ['DWBH'] = $dwbh; //当前员工编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	//得到省列表
	public function getShengList() {
		$sql = 'SELECT SHENGBH,SHENGMCH FROM H01DB012116';
		$shengList = $this->_db->fetchPairs ( $sql );
		$shengList ['0'] = '- - 请 选 择 - -';
		ksort ( $shengList );
		return $shengList;
	}
	
	//得到市列表
	public function getShiList($shengbh = 1) {
		$sql = "SELECT SHIBH,SHIMCH FROM H01DB012115";
		$sql .= " WHERE SZSHENG = " . $shengbh;
		$shiList = $this->_db->fetchPairs ( $sql );
		$shiList ['0'] = '- - 请 选 择 - -';
		ksort ( $shiList );
		return $shiList;
	}
	
	//发货区取得
	public function getfhq() {
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422";
		$where = " WHERE QYBH =:QYBH";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$fhq = $this->_db->fetchPairs ( $sql . $where, $bind );
		$fhq ['0'] = '- - 请 选 择 - -';
		ksort ( $fhq );
		return $fhq;	
	}
	
	/**
	 * 生成客户资料信息
	 *
	 * @return bool
	 */
	function insertKhzl() {
		
		//判断客户资料是否存在
		if ($this->getKhzl ( $_POST ['DWBH'] ) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['DWBH'] = $_POST ['DWBH']; //单位编号
			$data ['DWMCH'] = $_POST ['DWMCH']; //员工姓名
			$data ['ZHJM'] = $_POST ['ZHJM']; //助记码
			$data ['KEMUHAO'] = $_POST ['KEMUHAO']; //科目号			if($_POST ['KHLX_XS']=='1'){
			$data ['SHFXSH'] = '1'; //是否销售
			$data ['SHFJH'] = '0'; //是否进货			}
			if($_POST ['KHLX_JH']=='1'){				
			$data ['SHFXSH'] = '0'; //是否销售
			$data ['SHFJH'] = '1'; //是否进货
			}
			
			$data ['DYSHYQYBH'] = $_POST ['DYSHYQYBH']; //对应首营企业编号
			$data ['KHDJ'] = $_POST ['KHDJ']; //客户等级
			$data ['SHUIHAO'] = $_POST ['SHUIHAO']; //税号
			$data ['SZSH'] = $_POST ['SZSH']; //所在省
			$data ['SZSHI'] = $_POST ['SZSHI']; //所在市
			$data ['DIZHI'] = $_POST ['DIZHI']; //地址
			$data ['DHHM'] = $_POST ['DHHM']; //电话
			$data ['YHZHH'] = $_POST ['YHZHH']; //银行账号
			$data ['YZHBM'] = $_POST ['YZHBM']; //邮编
			$data ['LXRXM'] = $_POST ['LXRXM']; //联系人
			$data ['QYFL'] = $_POST ['QYFL']; //区域分类
			$data ['JYFW'] = $_POST ['JYFW']; //经营范围
			$data ['YXKZH'] = ($_POST ['YXKZH'] == null) ? '0' : '1';//是否有许可证
			$data ['XKZHH'] = $_POST ['XKZHH']; //许可证号
			if ($_POST ['XKZHYXQ'] != "") {		
				$data ['XKZHYXQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['XKZHYXQ'] . "','YYYY-MM-DD')" ); //许可证有效期
			}
			$data ['KHLB'] = $_POST ['KHLB']; //客户类别
			$data ['SHFYYYZHZH'] = ($_POST ['SHFYYYZHZH'] == null) ? '0' : '1'; //是否有营业执照
			$data ['YYZHZHH'] = $_POST ['YYZHZHH']; //营业执照号
			if ($_POST ['YYZHZHYXQ'] != "") {		
				$data ['YYZHZHYXQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['YYZHZHYXQ'] . "','YYYY-MM-DD')" ); //营业执照有效期
			}
			$data ['HYMCH'] = $_POST ['HYMCH']; //行业名称
			$data ['GHXDE'] = $_POST ['GHXDE']; //供货信贷额
			$data ['GHXDQ'] = $_POST ['GHXDQ']; //供货信贷期
			$data ['XSHXDE'] = $_POST ['XSHXDE']; //销售信贷额
			$data ['XSHXDQ'] = $_POST ['XSHXDQ']; //销售信贷期
			$data ['YSHSHX'] = $_POST ['YSHSHX']; //应收上限
			$data ['YDHTSH'] = $_POST ['YDHTSH']; //预到货天数
			$data ['KOULV'] = $_POST ['KOULV']; //扣率
			$data ['CSKL'] = $_POST ['CSKL']; //残损扣率
			$data ['FDBSH'] = ($_POST ['FDBSH'] == null) ? '0' : '1'; //分店标识
			$data ['FHQBH'] = $_POST ['FHQBH']; //发货区
			$data ['SHFZHXZHBJ'] = ($_POST ['SHFZHXZHBJ'] == null) ? '0' : '1'; //是否执行中标价
			$data ['SHFZHXZP'] = ($_POST ['SHFZHXZP'] == null) ? '0' : '1'; //是否执行赠品
			$data ['SHFSHCHCHJ'] = $_POST ['SHFSHCHCHJ']; //是否生产厂家
			$data ['KHHMCH'] = $_POST ['KHHMCH']; //开户行名称
			$data ['SHFZZHSH'] = ($_POST ['SHFZZHSH'] == null) ? '0' : '1'; //是否增值税  	
			
			$data ['KHJL'] = $_POST ['KHJL']; //残损扣率
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //作成日期
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者			
			$data ['KHZHT'] = '1'; //使用状态可用

			//客户资料表
			$this->_db->insert ( "H01DB012106", $data );
			return true;
		
		}
	}
	
	/**
	 * 更新客户信息
	 *
	 * @return bool
	 */
	function updateKhzl() {
		//检测时间戳是否发生变动
		$sql_list = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $_POST ['DWBH'] );
		$timestamp = $this->_db->fetchOne ( $sql_list, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012106 SET " . " QYBH = :QYBH," . " DWBH = :DWBH," . " DWMCH = :DWMCH," . " ZHJM = :ZHJM," . " KEMUHAO = :KEMUHAO," 
				 . " SHFXSH = :SHFXSH," . " SHFJH = :SHFJH," . " DYSHYQYBH = :DYSHYQYBH," . " KHDJ = :KHDJ," . " SHUIHAO = :SHUIHAO," . " SZSH = :SZSH," 
				 . " SZSHI = :SZSHI," . " DIZHI = :DIZHI," . " DHHM = :DHHM," . " YHZHH = :YHZHH," . " YZHBM = :YZHBM," . " LXRXM = :LXRXM," . " QYFL = :QYFL," 
				 . " JYFW = :JYFW," . " YXKZH = :YXKZH," . " XKZHH = :XKZHH," . " XKZHYXQ = TO_DATE(:XKZHYXQ,'YYYY-MM-DD')," . " KHLB = :KHLB," . " SHFYYYZHZH = :SHFYYYZHZH," . " YYZHZHH = :YYZHZHH," . " YYZHZHYXQ = TO_DATE(:YYZHZHYXQ,'YYYY-MM-DD')," 
				 . " HYMCH = :HYMCH," . " GHXDE = :GHXDE," . " GHXDQ = :GHXDQ," . " XSHXDE = :XSHXDE," . " XSHXDQ = :XSHXDQ," . " YSHSHX = :YSHSHX," . " YDHTSH = :YDHTSH," 
				 . " KOULV = :KOULV," . " CSKL = :CSKL,"." SHFSHCHCHJ = :SHFSHCHCHJ,"." KHJL = :KHJL,"."KHHMCH= :KHHMCH,"."SHFZZHSH=:SHFZZHSH,". " FDBSH = :FDBSH," . " FHQBH = :FHQBH," . " SHFZHXZHBJ = :SHFZHXZHBJ," . " SHFZHXZP = :SHFZHXZP,"
				 . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND DWBH =:DWBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['DWBH'] = $_POST ['DWBH']; //单位编号
			$bind ['DWMCH'] = $_POST ['DWMCH']; //员工姓名
			$bind ['ZHJM'] = $_POST ['ZHJM']; //助记码
			$bind ['KEMUHAO'] = $_POST ['KEMUHAO']; //科目号
			if($_POST ['AAAA'] == '1'){
			$bind ['SHFXSH'] = '1'; //是否销售
			$bind ['SHFJH'] = '0'; //是否进货		
			}
			if($_POST ['AAAA']== '0'){
			$bind ['SHFXSH'] = '0'; //是否销售
			$bind ['SHFJH'] = '1'; //是否进货	
			}			
			$bind ['DYSHYQYBH'] = $_POST ['DYSHYQYBH']; //对应首营企业编号
			$bind ['KHDJ'] = $_POST ['KHDJ']; //客户等级
			$bind ['SHUIHAO'] = $_POST ['SHUIHAO']; //税号
			$bind ['SZSH'] = $_POST ['SZSH']; //所在省
			$bind ['SZSHI'] = $_POST ['SZSHI']; //所在市
			$bind ['DIZHI'] = $_POST ['DIZHI']; //地址
			$bind ['DHHM'] = $_POST ['DHHM']; //电话
			$bind ['YHZHH'] = $_POST ['YHZHH']; //银行账号
			$bind ['YZHBM'] = $_POST ['YZHBM']; //邮编
			$bind ['LXRXM'] = $_POST ['LXRXM']; //联系人
			$bind ['QYFL'] = $_POST ['QYFL']; //区域分类
			$bind ['JYFW'] = $_POST ['JYFW']; //经营范围
			$bind ['YXKZH'] = ($_POST ['YXKZH'] == null) ? '0' : '1';//是否有许可证
			$bind ['XKZHH'] = $_POST ['XKZHH']; //许可证号
			$bind ['XKZHYXQ'] = $_POST ['XKZHYXQ']; //许可证有效期
			$bind ['KHLB'] = $_POST ['KHLB']; //客户类别
			$bind ['SHFYYYZHZH'] = ($_POST ['SHFYYYZHZH'] == null) ? '0' : '1'; //是否有营业执照
			$bind ['YYZHZHH'] = $_POST ['YYZHZHH']; //营业执照号
			$bind ['YYZHZHYXQ'] = $_POST ['YYZHZHYXQ']; //营业执照有效期
			$bind ['HYMCH'] = $_POST ['HYMCH']; //行业名称
			$bind ['GHXDE'] = $_POST ['GHXDE']; //供货信贷额
			$bind ['GHXDQ'] = $_POST ['GHXDQ']; //供货信贷期
			$bind ['XSHXDE'] = $_POST ['XSHXDE']; //销售信贷额
			$bind ['XSHXDQ'] = $_POST ['XSHXDQ']; //销售信贷期
			$bind ['YSHSHX'] = $_POST ['YSHSHX']; //应收上限
			$bind ['YDHTSH'] = $_POST ['YDHTSH']; //预到货天数
			$bind ['KOULV'] = $_POST ['KOULV']; //扣率
			$bind ['CSKL'] = $_POST ['CSKL']; //残损扣率
						$bind ['SHFSHCHCHJ'] = $_POST ['SHFSHCHCHJ']; //是否生产厂家
			$bind ['KHHMCH'] = $_POST ['KHHMCH']; //开户行名称
			$bind ['KHJL'] = $_POST ['KHJL']; //客户距离
			$bind ['SHFZZHSH'] = ($_POST ['SHFZZHSH'] == null) ? '0' : '1'; //是否增值税  	
			$bind ['FDBSH'] = ($_POST ['FDBSH'] == null) ? '0' : '1'; //分店标识
			$bind ['FHQBH'] = $_POST ['FHQBH']; //发货区
			$bind ['SHFZHXZHBJ'] = ($_POST ['SHFZHXZHBJ'] == null) ? '0' : '1'; //是否执行中标价
			$bind ['SHFZHXZP'] = ($_POST ['SHFZHXZP'] == null) ? '0' : '1'; //是否执行赠品		
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}

	
	function updateStatus($dwbh, $khzht) {
		
		$sql = "UPDATE H01DB012106 " . " SET KHZHT = :KHZHT" . " WHERE QYBH =:QYBH AND DWBH =:DWBH";
		$bind = array ('KHZHT' => $khzht, 'QYBH' => $_SESSION ['auth']->qybh, 'DWBH' => $dwbh );
		return $this->_db->query ( $sql, $bind );
	
	}
}