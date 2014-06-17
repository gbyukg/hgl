<?php
/*********************************
 * 模块：   门店模块(MD)
 * 机能：   会员信息(hyxx)
 * 作成者：苏迅
 * 作成日：2011/2/11
 * 更新履历：
 *********************************/
class md_models_hyxx extends Common_Model_Base {
	
	/**
	 * 得到客户列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	
	public function getGridData($filter) {
		//排序用字段名
		
		$fields = array ("", "HYZHT", "HYBH", "NLSSORT(HUIYUANMING,'NLS_SORT=SCHINESE_PINYIN_M')","XINGBIE","LXDH","SHFZHH",
		"CHSHRQ","YZHBM","TXDZH","EMAIL","NLSSORT(BEIZHU,'NLS_SORT=SCHINESE_PINYIN_M')","HYKH","XYJF","NLSSORT(MDMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT DECODE(HYZHT,'1','正常','0','禁用','') AS KHZHT,HYBH,HUIYUANMING,DECODE(XINGBIE,'0','男','1','女','') AS XINGBIE," 
		     . "LXDH,SHFZHH,TO_CHAR(CHSHRQ,'YYYY-MM-DD'),YZHBM,TXDZH,EMAIL,BEIZHU,HYKH,XYJF,JBMDMCH" 
		     . " FROM H01VIEW012501 WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件 会员名
		if ($filter ['searchParams']["HYMKEY"] != "") {
			$sql .= " AND LOWER(HYBH) LIKE LOWER('%' || :HYMKEY || '%') "
			      . "OR LOWER(HUIYUANMING) LIKE LOWER('%' || :HYMKEY || '%')";
			$bind ['HYMKEY'] = $filter ['searchParams']["HYMKEY"];
		}
		//查找条件 会员卡号
		if ($filter ['searchParams']["HYKH"] != "") {
			$sql .= " AND HYKH LIKE '%' || :HYKH || '%'";
			$bind ['HYKH'] = $filter ['searchParams']["HYKH"];
		}
		//查找条件  联系电话
		if ($filter ['searchParams']["LXDH"] != "") {
			$sql .= " AND LXDH LIKE '%' || :LXDH || '%'";
			$bind ['LXDH'] = $filter ['searchParams']["LXDH"];
		}
		
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("MD_HYXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",HYBH";
		
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
	 * 取得会员信息
	 *
	 * @param string $hybh   会员编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getHyxx($hybh, $filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		
		//排序用字段名
		$fields = array ("", "HYZHT", "HYBH", "NLSSORT(HUIYUANMING,'NLS_SORT=SCHINESE_PINYIN_M')","XINGBIE","LXDH","SHFZHH",
		"CHSHRQ","YZHBM","TXDZH","EMAIL","NLSSORT(BEIZHU,'NLS_SORT=SCHINESE_PINYIN_M')","HYKH","XYJF","NLSSORT(JBMDMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql_list = "SELECT  HYBH,ROWID,LEAD(ROWID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",HYBH) AS NEXTROWID,"
		          . "LAG(ROWID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . " ,HYBH) AS PREVROWID"
				  . " FROM H01VIEW012501 WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//查找条件 会员名
		if ($filter ['searchParams']["HYMKEY"] != "") {
			$sql_list .= " AND HYBH LIKE '%' || :HYMKEY || '%' OR HUIYUANMING LIKE '%' || :HYMKEY || '%'";
			$bind ['HYMKEY'] = $filter ['searchParams']["HYMKEY"];
		}
		//查找条件 会员卡号
		if ($filter ['searchParams']["HYKH"] != "") {
			$sql_list .= " AND HYKH LIKE '%' || :HYKH || '%'";
			$bind ['HYKH'] = $filter ['searchParams']["HYKH"];
		}
		//查找条件  联系电话
		if ($filter ['searchParams']["LXDH"] != "") {
			$sql_list .= " AND LXDH LIKE '%' || :LXDH || '%'";
			$bind ['LXDH'] = $filter ['searchParams']["LXDH"];
		}
		//自动生成精确查询用Sql
        $sql_list .= Common_Tool::createFilterSql("MD_HYXX",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql_single = "SELECT HYBH,HUIYUANMING,XINGBIE,LXDH,SHFZHH,TO_CHAR(CHSHRQ,'YYYY-MM-DD') AS CHSHRQ,YZHBM,TXDZH,EMAIL,"
					. "BEIZHU,HYKH,KPLX,DSHBB,HGL_DEC(CSHJF) AS CSHJF,HGL_DEC(XYJF) AS XYJF,HGL_DEC(DHJF) AS DHJF,TO_CHAR(SHXRQ,'YYYY-MM-DD') AS SHXRQ," 
					. "HGL_DEC(LJJF) AS LJJF,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ,JBMDMCH,TO_CHAR(DJRQ,'YYYY-MM-DD') AS DJRQ,JINGBANREN AS JINGBANRENM"
					." FROM H01VIEW012501";
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH =:QYBH AND HYBH =:HYBH";
			unset ( $bind ['HYMKEY'] );
			unset ( $bind ['HYKH'] );
			unset ( $bind ['LXDH'] );
		} else if ($flg == 'next') {
			$sql_single .= " WHERE ROWID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,HYBH FROM ( $sql_list ) WHERE HYBH = :HYBH))";
		} else if ($flg == 'prev') {
			$sql_single .= " WHERE ROWID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,HYBH FROM ( $sql_list ) WHERE HYBH = :HYBH))";
		}
		//绑定查询条件
		$bind ['HYBH'] = $hybh; //当前员工编号
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 生成会员资料信息
	 */
	function insertHyxx($hybh) {
		
		$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
		$data ['HYBH'] = $hybh; //新生成的会员编号
		$data ['JBMD'] = $_SESSION ['auth']->mdbh;; //经办门店编号
		$data ['JINGBANREN'] = $_SESSION ['auth']->userId; //经办人编号	
		$data ['DJRQ'] = new Zend_Db_Expr ("SYSDATE"); //登记日期		
		$data ['HUIYUANMING'] = $_POST ['HUIYUANMING']; //会员名
		$data ['XINGBIE'] = $_POST ['XINGBIE']; //性别
		$data ['SHFZHH'] = $_POST ['SHFZHH']; //身份证号
		if ($_POST ['CHSHRQ'] != "") {		
			$data ['CHSHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['CHSHRQ'] . "','YYYY-MM-DD')" ); //出生日期
		}
		$data ['LXDH'] = $_POST ['LXDH']; //联系电话
		$data ['TXDZH'] = $_POST ['TXDZH']; //通讯地址
		$data ['YZHBM'] = $_POST ['YZHBM']; //邮政编码
		$data ['EMAIL'] = $_POST ['EMAIL']; //EMAIL
		$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
		$data ['KKRQ'] = new Zend_Db_Expr ("SYSDATE"); //开卡日期
		$data ['KPLX'] = $_POST ['KPLX']; //卡片类型
		$data ['DSHBB'] = $_POST ['DSHBB']; //丢失补办
		if ($_POST ['SHXRQ'] != "") {		
			$data ['SHXRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['SHXRQ'] . "','YYYY-MM-DD')" ); //失效日期
		}
		$data ['CSHJF'] = $_POST ['CSHJF']; //初始积分
		$data ['XYJF'] = $_POST ['XYJF']; //现有积分
		$data ['DHJF'] = $_POST ['DHJF']; //兑换积分
		$data ['LJJF'] = $_POST ['LJJF']; //累计积分
		$data ['HYZHT'] = '1'; //会员状态
		$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期
		$data ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
		$data ['HYKH'] = $_POST ['HYKH'];; //会员卡号

		//客户资料表
		$this->_db->insert ( "H01DB012501", $data );
	}
	
	/**
	 * 更新会员信息
	 *
	 * @return bool
	 */
	function updateHyxx() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012501 WHERE QYBH = :QYBH AND HYBH = :HYBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'HYBH' => $_POST ['HYBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012501 SET " . " QYBH = :QYBH," . " HUIYUANMING = :HUIYUANMING," . " XINGBIE = :XINGBIE,"
				 . " SHFZHH = :SHFZHH," . " CHSHRQ = TO_DATE(:CHSHRQ,'YYYY-MM-DD')," . " LXDH = :LXDH," . " TXDZH = :TXDZH,"
				 . " YZHBM = :YZHBM," . " EMAIL = :EMAIL," . " BEIZHU = :BEIZHU," . " KPLX = :KPLX," . " DSHBB = :DSHBB," 
				 . " SHXRQ = TO_DATE(:SHXRQ,'YYYY-MM-DD')," . " CSHJF = :CSHJF," . " XYJF = :XYJF," . " DHJF = :DHJF," 
				 . " LJJF = :LJJF," . " HYKH = :HYKH," . " BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND HYBH =:HYBH";
				 
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['HYBH'] = $_POST['HYBH']; //会员编号	
			$bind ['HUIYUANMING'] = $_POST ['HUIYUANMING']; //会员名
			$bind ['XINGBIE'] = ($_POST ['XINGBIE'] == null)? '0' : '1'; //性别
			$bind ['SHFZHH'] = $_POST ['SHFZHH']; //身份证号
			$bind ['CHSHRQ'] = $_POST ['CHSHRQ']; //出生日期
			$bind ['LXDH'] = $_POST ['LXDH']; //联系电话
			$bind ['TXDZH'] = $_POST ['TXDZH']; //通讯地址
			$bind ['YZHBM'] = $_POST ['YZHBM']; //邮政编码
			$bind ['EMAIL'] = $_POST ['EMAIL']; //EMAIL
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['KPLX'] = $_POST ['KPLX']; //卡片类型
			$bind ['DSHBB'] = $_POST ['DSHBB']; //丢失补办
			$bind ['SHXRQ'] = $_POST ['SHXRQ']; //失效日期
			$bind ['CSHJF'] = $_POST ['CSHJF']; //初始积分
			$bind ['XYJF'] = $_POST ['XYJF']; //现有积分
			$bind ['DHJF'] = $_POST ['DHJF']; //兑换积分
			$bind ['LJJF'] = $_POST ['LJJF']; //累计积分
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //操作用户
			$bind ['HYKH'] = $_POST ['HYKH'];; //会员卡号
			
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
	
	function updateStatus($hybh, $hyzht) {
		
		$sql = "UPDATE H01DB012501 " . " SET HYZHT = :HYZHT" . " WHERE QYBH =:QYBH AND HYBH =:HYBH";
		$bind = array ('HYZHT' => $hyzht, 'QYBH' => $_SESSION ['auth']->qybh, 'HYBH' => $hybh );
		return $this->_db->query ( $sql, $bind );
	
	}
}