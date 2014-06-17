<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   首营商品资料(shyshp)
 * 作成者：苏迅
 * 作成日：2010/11/30
 * 更新履历：
 *********************************/
class jc_models_shyshp extends Common_Model_Base {
	
	/*
	 * 列表数据取得（xml格式）
	 */
	function getGridData($filter) {
		//排序用列定义
		$fields = array ("", "SHPTG", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT DECODE(SHPTG,'1','通过','0','未通过','-'),SHPBH,SHPMCH,GUIGE,YPGNZHZHLXQK,DECODE(YXKZHSHY,'0','否','1','是',''),XKZHHSHY,XKZHYXQSHY,DECODE(YYYZHZHSHY,'0','否','1','是',''),YYZHZHHSHY,YYZHZHYXQSHY,DECODE(YPZHWHSHY,'0','否','1','是',''),PZHWHSHY,PZHWHYXQSHY,DECODE(FHZHLBZH,'0','不符合','1','符合',''),ZHLBZH," 
			 . "DECODE(YXBZH,'0','否','1','是',''),DECODE(YZHCSHB,'0','否','1','是',''),ZHCSHB,DECODE(YOUBIAOQIAN,'0','否','1','是',''),DECODE(YSHMSH,'0','否','1','是',''),DECODE(YOUYANGPIN,'0','否','1','是',''),DECODE(GMPDB,'0','未达标','1','达标',''),GSHDW,CCHTJ,GCHFZQ,SHQBMMCH,TO_CHAR(KSHRQ,'YYYY-MM-DD'),SHQYY,CGYYJ,YWBMZHGYJ,ZHLBMYJ," 
			 . "WJBMYJ,JLSHPYJ,CHLQK,SHPJG,TO_CHAR(SHPRQ,'YYYY-MM-DD'),SHPZLDAH,ZHZHDAH,ZHAIYAO,TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM" 
			 . " FROM H01VIEW012101 WHERE QYBH = :QYBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件
		if ($filter ['searchParams']["SHPBH"] != "") {
			$sql .= " AND (SHPBH LIKE '%' || :SHPBHKEY || '%' OR SHPMCH LIKE '%' || :SHPBHKEY || '%' OR ZHJM LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = $filter ['searchParams']["SHPBH"];
		}
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("JC_SHYSHP",$filter['filterParams'],$bind);
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",SHPBH";
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		return Common_Tool::createXml ( $recs, true,$totalCount, $filter ["posStart"] );
	}
	
	/**
	 * 取得首营商品资料信息
	 *
	 * @param string $spbh   商品编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getShyshp($shpbh, $filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）        
		//排序用列定义
		$fields = array ("", "SHPTG", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		//检索SQL(view中不能使用rowid)
		$sql_list = "SELECT SHPBH,SHPID,LEAD(SHPID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",SHPBH) AS NEXTROWID," 
				  . "LAG(SHPID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . " ,SHPBH) AS PREVROWID" 
				  . " FROM H01VIEW012101 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		//快速查找条件
		if ($filter ['searchParams']["SHPBH"] != "") {
			$sql_list .= " AND (SHPBH LIKE '%' || :SHPBHKEY || '%' OR SHPMCH LIKE '%' || :SHPBHKEY || '%' OR ZHJM LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = $filter ['searchParams']["SHPBH"];
		}
		
		//自动生成精确查询用Sql
        $sql_list .= Common_Tool::createFilterSql("JC_SHYSHP",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql_single = "SELECT SHPTG,SHPBH,SHPMCH,GUIGE,YPGNZHZHLXQK,YXKZHSHY,XKZHHSHY,TO_CHAR(XKZHYXQSHY,'YYYY-MM-DD') AS XKZHYXQSHY,YYYZHZHSHY,YYZHZHHSHY,TO_CHAR(YYZHZHYXQSHY,'YYYY-MM-DD') AS YYZHZHYXQSHY," 
					. "YPZHWHSHY,PZHWHSHY,TO_CHAR(PZHWHYXQSHY,'YYYY-MM-DD') AS PZHWHYXQSHY,FHZHLBZH,ZHLBZH,YXBZH,YZHCSHB,ZHCSHB,YOUBIAOQIAN,YSHMSH,YOUYANGPIN,GMPDB,GSHDW,CCHTJ,GCHFZQ,SHQBM,SHQBMMCH,TO_CHAR(KSHRQ,'YYYY-MM-DD') AS KSHRQ," 
					. "SHQYY,CGYYJ,YWBMZHGYJ,ZHLBMYJ,WJBMYJ,JLSHPYJ,CHLQK,SHPJG,TO_CHAR(SHPRQ,'YYYY-MM-DD') AS SHPRQ,SHPZLDAH,ZHZHDAH,ZHAIYAO,to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ" 
					. " FROM H01VIEW012101";
		
		if ($flg == 'current') {
			$sql_single .= " WHERE QYBH =:QYBH AND SHPBH =:SHPBH";
			unset ( $bind ['SHPBHKEY'] );		
		} else if ($flg == 'next') { //下一条
			$sql_single .= " WHERE SHPID =  (SELECT NEXTROWID FROM  (SELECT NEXTROWID,SHPBH FROM ( $sql_list ) WHERE SHPBH = :SHPBH))";
		} else if ($flg == 'prev') { //前一条
			$sql_single .= " WHERE SHPID =  (SELECT PREVROWID FROM  (SELECT PREVROWID,SHPBH FROM ( $sql_list ) WHERE SHPBH = :SHPBH))";
		}
		//绑定查询条件
		$bind ['SHPBH'] = $shpbh;
		return $this->_db->fetchRow ( $sql_single, $bind );
	}
	
	/**
	 * 更新商品资料信息(首营商品信息)
	 *
	 * @return bool
	 */
	function updateShyshp() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $_POST ['SHPBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更		
		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012101 SET " . " QYBH = :QYBH," . " SHPTG = :SHPTG," 
			     . " YXKZHSHY = :YXKZHSHY," . " XKZHHSHY = :XKZHHSHY," . " XKZHYXQSHY = TO_DATE(:XKZHYXQSHY,'YYYY-MM-DD')," . " YYYZHZHSHY = :YYYZHZHSHY," 
			     . " YYZHZHHSHY = :YYZHZHHSHY," . " YYZHZHYXQSHY = TO_DATE(:YYZHZHYXQSHY,'YYYY-MM-DD')," . " YPZHWHSHY = :YPZHWHSHY," 
			     . " PZHWHSHY = :PZHWHSHY," . " PZHWHYXQSHY = TO_DATE(:PZHWHYXQSHY,'YYYY-MM-DD')," 
			     . " FHZHLBZH = :FHZHLBZH," . " ZHLBZH = :ZHLBZH," . " YXBZH = :YXBZH," . " YZHCSHB = :YZHCSHB," 
			     . " ZHCSHB = :ZHCSHB," . " YOUBIAOQIAN = :YOUBIAOQIAN," . " YSHMSH = :YSHMSH," . " YOUYANGPIN = :YOUYANGPIN," . " GMPDB = :GMPDB," 
			     . " GSHDW = :GSHDW," . " CCHTJ = :CCHTJ," . " GCHFZQ = :GCHFZQ," . " SHQBM = :SHQBM," . " KSHRQ = TO_DATE(:KSHRQ,'YYYY-MM-DD')," . " SHQYY = :SHQYY," 
			     . " CGYYJ = :CGYYJ," . " YWBMZHGYJ = :YWBMZHGYJ," . " ZHLBMYJ = :ZHLBMYJ," . " WJBMYJ = :WJBMYJ," . " JLSHPYJ = :JLSHPYJ," . " CHLQK = :CHLQK," . " SHPJG = :SHPJG," 
			     . " SHPRQ = TO_DATE(:SHPRQ,'YYYY-MM-DD')," . " SHPZLDAH = :SHPZLDAH," . " ZHZHDAH = :ZHZHDAH," . " ZHAIYAO = :ZHAIYAO," . " BGRQ = SYSDATE," . " BGZH = :BGZH," 
			     . " YPZHWH = :YPZHWHSHY," . " PZHWH = :PZHWHSHY," . " PZHWHYXQ = TO_DATE(:PZHWHYXQSHY,'YYYY-MM-DD')" . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['SHPBH'] = $_POST ['SHPBH']; //商品编号
			$bind ['SHPTG'] = ($_POST ['SHPTG'] == null)? '0' : '1'; //审批通过
			$bind ['YXKZHSHY'] = ($_POST ['YXKZHSHY'] == null)? '0' : '1'; //有许可证(首营)
			$bind ['XKZHHSHY'] = $_POST ['XKZHHSHY']; //许可证号(首营)
			$bind ['XKZHYXQSHY'] = $_POST ['XKZHYXQSHY']; //许可证有效期(首营)
			$bind ['YYYZHZHSHY'] = ($_POST ['YYYZHZHSHY'] == null)? '0' : '1'; //有营业执照(首营)
			$bind ['YYZHZHHSHY'] = $_POST ['YYZHZHHSHY']; //营业执照号(首营)
			$bind ['YYZHZHYXQSHY'] = $_POST ['YYZHZHYXQSHY']; //营业执照有效期(首营)
			$bind ['YPZHWHSHY'] = ($_POST ['YPZHWHSHY'] == null)? '0' : '1'; //有批准文号(首营)
			$bind ['PZHWHSHY'] = $_POST ['PZHWHSHY']; //批准文号(首营)
			$bind ['PZHWHYXQSHY'] = $_POST ['PZHWHYXQSHY']; //批准文号有效期(首营)
			$bind ['FHZHLBZH'] = ($_POST ['FHZHLBZH'] == null)? '0' : '1'; //符合质量标准
			$bind ['ZHLBZH'] = $_POST ['ZHLBZH']; //质量标准
			$bind ['YXBZH'] = ($_POST ['YXBZH'] == null)? '0' : '1'; //有小包装
			$bind ['YZHCSHB'] = ($_POST ['YZHCSHB'] == null)? '0' : '1'; //有注册商标
			$bind ['ZHCSHB'] = $_POST ['ZHCSHB']; //注册商标
			$bind ['YOUBIAOQIAN'] = ($_POST ['YOUBIAOQIAN'] == null)? '0' : '1'; //有标签
			$bind ['YSHMSH'] = ($_POST ['YSHMSH'] == null)? '0' : '1'; //有说明书
			$bind ['YOUYANGPIN'] = ($_POST ['YOUYANGPIN'] == null)? '0' : '1'; //有样品
			$bind ['GMPDB'] = ($_POST ['GMPDB'] == null)? '0' : '1'; //GMP达标
			$bind ['GSHDW'] = $_POST ['GSHDW']; //供商单位
			$bind ['CCHTJ'] = $_POST ['CCHTJ']; //存储条件
			$bind ['GCHFZQ'] = $_POST ['GCHFZQ']; //工厂负责期
			$bind ['SHQBM'] = $_POST ['SHQBM']; //申请部门
			$bind ['KSHRQ'] = $_POST ['KSHRQ']; //开始日期
			$bind ['SHQYY'] = $_POST ['SHQYY']; //申请原因
			$bind ['CGYYJ'] = $_POST ['CGYYJ']; //采购员意见
			$bind ['YWBMZHGYJ'] = $_POST ['YWBMZHGYJ']; //业务部门主管意见
			$bind ['ZHLBMYJ'] = $_POST ['ZHLBMYJ']; //质量部门意见
			$bind ['WJBMYJ'] = $_POST ['WJBMYJ']; //物价部门意见
			$bind ['JLSHPYJ'] = $_POST ['JLSHPYJ']; //经理审批意见
			$bind ['CHLQK'] = $_POST ['CHLQK']; //处理情况
			$bind ['SHPJG'] = $_POST ['SHPJG']; //审批结果
			$bind ['SHPRQ'] = $_POST ['SHPRQ']; //审批日期
			$bind ['SHPZLDAH'] = $_POST ['SHPZLDAH']; //审批资料档案号
			$bind ['ZHZHDAH'] = $_POST ['ZHZHDAH']; //证照档案号
			$bind ['ZHAIYAO'] = $_POST ['ZHAIYAO']; //摘要
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}

}
	