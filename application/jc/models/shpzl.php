<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品资料(shpzl)
 * 作成者：苏迅
 * 作成日：2010/11/15
 * 更新履历：


 *********************************/
class jc_models_shpzl extends Common_Model_Base {
	
	/*
	 * 列表数据取得（xml格式）

	 */
	function getGridData($filter) {
		//排序用列定义
		$fields = array ("", "SHPZHT", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT DECODE(SHPZHT,'1','正常','禁用'),SHPBH,SHPMCH,GUIGE,ZHJM,JIXINGMCH,YPGNZHZHLXQK,SHPTM,GJBM,FLBM," 
			. "SHCHCHJ,HUAXUEMING,CHYM,SUMING,XWMCH,TYMCH,LEIBIEMCH,YYFLMCH,CHFFLMCH,YFPDMCH," 
			. "DECODE(GZHBZH,'1','贵重','0','普通','') ,YWCHF,SHPLXMCH,YFYYL,KEMUHAO,BLFY,JJZH,SHYZH,CHCZHYSHX,BEIZHU,ZHYSHX," 
			. "CHANDI,BZHGG,DECODE(JXDX,'0','经销','1','代销',''),DECODE(BZHQFSH,'0','没有','1','出厂日期','2','失效日期','')," 
			. "BZHQYSH,YJYSH,HGL_DEC(ZHCHCHCHJ),HGL_DEC(HSHJJ),HGL_DEC(JINJIA)," 
			. "HGL_DEC(HSHSHJ),HGL_DEC(SHOUJIA),HGL_DEC(LSHJ),HGL_DEC(PAIJIA),HGL_DEC(ZHDCHBJ),HGL_DEC(ZDSHJ)," 
			. "HGL_DEC(ZGSHJ),HGL_DEC(MAOLILV),HGL_DEC(SHUILV),HGL_DEC(KOULV),CHBJSMCH,DECODE(SHFYINPIAN,'0','否','1','是',''),BZHDWMCH," 
			. "JLGG,SHFOTCMCH,HGL_DEC(KCSHX),HGL_DEC(KCXX),HGL_DEC(HLKC),HGL_DEC(DBZHTJ),HGL_DEC(DBZHZHL)," 
			. "HGL_DEC(DPZHL),HGL_DEC(DPCH),HGL_DEC(DANPINKUAN),HGL_DEC(DANPINGAO),HGL_DEC(ZXDWTJ),HGL_DEC(ZXDWZHL),HGL_DEC(TJDJ),HGL_DEC(PEISONGJIA),HGL_DEC(GONGHUOJIA)," 
			. "HGL_DEC(PIFAJIA),ZHDYHPZHLX,DHZHQ,XYPZH,DECODE(SHFYP,'0','否','1','是',''),YOUXIAOQI,HGL_DEC(CHKXZHSHL),HGL_DEC(RKXZHSHL)," 
			. "DECODE(YPZHWH,'1','是','否'),PZHWH,TO_CHAR(PZHWHYXQ,'YYYY-MM-DD'),ZHDKQLXMCH,(QSHCKMCH||' '||QSKQMCH||' '||QSKWMCH) AS QSKW,HGL_DEC(ZHBJ),"
			. "TO_CHAR(BGRQ,'YYYY-MM-DD'),BGZHXM FROM H01VIEW012101 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件
		if($filter['searchParams']['SHPBHKEY']!=""){
			$sql .= " AND( SHPBH LIKE '%' || :SHPBHKEY || '%'".
			        "      OR  lower(SHPMCH) LIKE '%' || :SHPBHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = strtolower($filter ["searchParams"]['SHPBHKEY']);
		}
		
		if ($filter ["flbm"] != "") {
			//分类编码
			$sql .= " AND FLBM IN(SELECT SHPFL FROM H01DB012109 START WITH SHPFL = :FLBM OR SHJFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
			$bind ['FLBM'] = $filter ["flbm"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("JC_SHPZL",$filter['filterParams'],$bind);
		
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
	
	//得到剂型列表
	function getJixingList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'JX'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$jixingList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$jixingList ['0'] = '- - 请 选 择 - -';
		ksort ( $jixingList );
		return $jixingList;
	
	}
	
	//得到商品类别列表
	function getLeibieList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'SHPLB'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$leibieList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$leibieList ['0'] = '- - 请 选 择 - -';
		ksort ( $leibieList );
		return $leibieList;
	}
	
	//得到用药分类列表
	function getYyflList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'YYFL'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$yyflList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$yyflList ['0'] = '- - 请 选 择 - -';
		ksort ( $yyflList );
		return $yyflList;
	}
	
	//得到处方分类列表
	function getChfflList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'CFFL'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$chfflList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$chfflList ['0'] = '- - 请 选 择 - -';
		ksort ( $chfflList );
		return $chfflList;
	}
	
	//得到药方判断列表
	function getYfpdList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'YFPD'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$yfpdList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$yfpdList ['0'] = '- - 请 选 择 - -';
		ksort ( $yfpdList );
		return $yfpdList;
	}
	
	//得到商品类型列表
	function getShplxList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'SHPLX'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$shplxList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$shplxList ['0'] = '- - 请 选 择 - -';
		ksort ( $shplxList );
		return $shplxList;
	}
	
	//得到成本计算列表
	function getChbjsList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'CHBJS'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$chbjsList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$chbjsList ['0'] = '- - 请 选 择 - -';
		ksort ( $chbjsList );
		return $chbjsList;
	}
	
	//得到包装单位列表
	function getBzhdwList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'DW'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bzhdwList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$bzhdwList ['0'] = '- - 请 选 择 - -';
		ksort ( $bzhdwList );
		return $bzhdwList;
	}
	
	//得到是否OTC列表
	function getShfotcList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'SHFOTC'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$shfotcList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$shfotcList ['0'] = '- - 请 选 择 - -';
		ksort ( $shfotcList );
		return $shfotcList;
	}
	
	//得到指定库区类型列表
	function getZhdkqlxList() {
		$sql = 'SELECT ZIHAOMA,NEIRONG FROM H01DB012001';
		$where = " WHERE QYBH =:QYBH AND CHLID = 'KQLX'";
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$kqlxList = $this->_db->fetchPairs ( $sql . $where, $bind );
		$kqlxList ['0'] = '- - 请 选 择 - -';
		ksort ( $kqlxList );
		return $kqlxList;
	}
	
	/**
	 * 取得商品资料信息
	 *
	 * @param string $spbh   商品编号
	 * @param string $direction 查找方向  current,next,prev
	 * @return array 
	 */
	function getShpzl($shpbh, $filter=null, $flg = 'current') {
		//列表查询条件及排序（上一条下一条需按照列表的查询条件和排序为基础）
		

		//排序用列定义
		$fields = array ("", "SHPZHT", "SHPBH", "NLSSORT(SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL(view中不能使用rowid)
		$sql_list = "SELECT SHPBH,SHPID,LEAD(SHPID) OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . ",SHPBH) AS NEXTROWID,"
		           . " LAG(SHPID)  OVER(ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"] . " ,SHPBH) AS PREVROWID"
			       . " FROM H01VIEW012101 WHERE QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件
		if($filter['searchParams']['SHPBHKEY']!=""){
			$sql_list .= " AND( SHPBH LIKE '%' || :SHPBHKEY || '%'".
			        "      OR  lower(SHPMCH) LIKE '%' || :SHPBHKEY || '%'".
			        "      OR  lower(ZHJM) LIKE '%' || :SHPBHKEY || '%')";
			$bind ['SHPBHKEY'] = strtolower($filter ["searchParams"]['SHPBHKEY']);
		}
		if ($filter ["flbm"] != "") {
			//分类编码
			$sql_list .= " AND FLBM IN(SELECT SHPFL FROM H01DB012109 START WITH SHPFL = :FLBM CONNECT BY PRIOR SHPFL = SHJFL)";
			$bind ['FLBM'] = $filter ["flbm"];
		}
		
		//自动生成精确查询用Sql
		$sql_list .= Common_Tool::createFilterSql("JC_SHPZL",$filter['filterParams'],$bind);
		
		//检索SQL
		$sql_single = "SELECT QYBH, SHPBH, SHPMCH, GUIGE, YPGNZHZHLXQK, SHPTM, GJBM, FLBM, SHCHCHJ, ZHJM, HUAXUEMING, CHYM, SUMING, JIXING," 
					. "XWMCH, TYMCH, LEIBIE, YYFL, CHFFL, YFPD, GZHBZH, YWCHF, SHPLX, YFYYL, KEMUHAO, BLFY, JJZH, SHYZH, CHCZHYSHX," 
					. "BEIZHU, ZHYSHX, CHANDI, BZHGG, JXDX, BZHQFSH, BZHQYSH, YJYSH, HGL_DEC(ZHCHCHCHJ) AS ZHCHCHCHJ, HGL_DEC(HSHJJ) AS HSHJJ," 
					. "HGL_DEC(JINJIA) AS JINJIA, HGL_DEC(HSHSHJ) AS HSHSHJ, HGL_DEC(SHOUJIA) AS SHOUJIA, HGL_DEC(LSHJ) AS LSHJ, HGL_DEC(PAIJIA) AS PAIJIA," 
					. "HGL_DEC(ZHDCHBJ) AS ZHDCHBJ, HGL_DEC(ZDSHJ) AS ZDSHJ, HGL_DEC(ZGSHJ) AS ZGSHJ, HGL_DEC(MAOLILV) AS MAOLILV, HGL_DEC(SHUILV) AS SHUILV," 
					. "HGL_DEC(KOULV) AS KOULV, CHBJS, SHFYINPIAN, BZHDWBH, JLGG, ZHDKQLX, QSHCKBH, QSHKQBH, QSHKWBH, HGL_DEC(KCSHX)AS KCSHX," 
					. "HGL_DEC(KCXX) AS KCXX, HGL_DEC(HLKC) AS HLKC, HGL_DEC(DBZHTJ) AS DBZHTJ, HGL_DEC(DBZHZHL) AS DBZHZHL, HGL_DEC(DPZHL) AS DPZHL," 
					. "HGL_DEC(DPCH) AS DPCH, HGL_DEC(DANPINKUAN) AS DANPINKUAN, HGL_DEC(DANPINGAO) AS DANPINGAO, HGL_DEC(ZXDWTJ) AS ZXDWTJ, HGL_DEC(ZXDWZHL) AS ZXDWZHL," 
					. "HGL_DEC(PEISONGJIA) AS PEISONGJIA, HGL_DEC(TJDJ) AS TJDJ, HGL_DEC(GONGHUOJIA) AS GONGHUOJIA, HGL_DEC(PIFAJIA) AS PIFAJIA," 
					. "ZHDYHPZHLX, DHZHQ, XYPZH, SHFYP, YOUXIAOQI, HGL_DEC(CHKXZHSHL) AS CHKXZHSHL, HGL_DEC(RKXZHSHL) AS RKXZHSHL, YPZHWH ,PZHWH ,TO_CHAR(PZHWHYXQ,'YYYY-MM-DD') AS PZHWHYXQ, SHFOTC, FDSHPBZH, SHPZHT, BGZH, to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ, BZHDWMCH, FLMCH, JIXINGMCH," 
					. "LEIBIEMCH, YYFLMCH, CHFFLMCH, SHPLXMCH, SHFOTCMCH, YFPDMCH, CHBJSMCH, ZHDKQLXMCH, (QSHCKMCH||' '||QSKQMCH||' '||QSKWMCH) AS QSKW,HGL_DEC(ZHBJ) AS ZHBJ FROM H01VIEW012101";
		
		if ($flg == 'current') {
			$sql_single .= " WHERE  QYBH =:QYBH AND SHPBH =:SHPBH";
			unset ( $bind ['SHPBHKEY'] );
			unset ( $bind ['FLBM'] );		
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
	 * 生成商品基础资料信息
	 *
	 * @return bool
	 */
	function insertShpzl() {
		
		//判断商品资料是否存在
		if ($this->getShpzl ( $_POST ['SHPBH']) != FALSE) {
			return false;
		} else {
			$data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$data ['SHPBH'] = $_POST ['SHPBH']; //商品编号
			$data ['SHPMCH'] = $_POST ['SHPMCH']; //商品名称
			$data ['GUIGE'] = $_POST ['GUIGE']; //商品规格
			$data ['YPGNZHZHLXQK'] = $_POST ['YPGNZHZHLXQK']; //药品功能主治疗效情况
			$data ['SHPTM'] = $_POST ['SHPTM']; //商品条码
			$data ['GJBM'] = $_POST ['GJBM']; //国家编码
			$data ['FLBM'] = $_POST ['FLBM']; //分类编码
			//$data ['SHCHCHJBM'] = $_POST ['SHCHCHJBM']; //生产厂家编码
			$data ['SHCHCHJ'] = $_POST ['SHCHCHJ']; //生产厂家
			$data ['ZHJM'] = $_POST ['ZHJM']; //助记码	
			$data ['HUAXUEMING'] = $_POST ['HUAXUEMING']; //化学名	
			$data ['CHYM'] = $_POST ['CHYM']; //常用名
			$data ['SUMING'] = $_POST ['SUMING']; //俗名
			$data ['JIXING'] = $_POST ['JIXING']; //剂型
			$data ['XWMCH'] = $_POST ['XWMCH']; //西文名称
			$data ['TYMCH'] = $_POST ['TYMCH']; //通用名
			$data ['LEIBIE'] = $_POST ['LEIBIE']; //类别
			$data ['YYFL'] = $_POST ['YYFL']; //用药分类
			$data ['CHFFL'] = $_POST ['CHFFL']; //处方分类
			$data ['YFPD'] = $_POST ['YFPD']; //药方判断
			$data ['GZHBZH'] = $_POST ['GZHBZH']; //贵重标志
			$data ['YWCHF'] = $_POST ['YWCHF']; //药物成分
			$data ['SHPLX'] = $_POST ['SHPLX']; //商品类型
			$data ['YFYYL'] = $_POST ['YFYYL']; //用法与用量
			$data ['KEMUHAO'] = $_POST ['KEMUHAO']; //科目号
			$data ['BLFY'] = $_POST ['BLFY']; //不良反应
			$data ['JJZH'] = $_POST ['JJZH']; //禁忌症
			$data ['SHYZH'] = $_POST ['SHYZH']; //适应症
			$data ['CHCZHYSHX'] = $_POST ['CHCZHYSHX']; //储存注意事项
			$data ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$data ['ZHYSHX'] = $_POST ['ZHYSHX']; //注意事项
			$data ['CHANDI'] = $_POST ['CHANDI']; //产地
			$data ['BZHGG'] = $_POST ['BZHGG']; //包装规格
			$data ['JXDX'] = $_POST ['JXDX']; //经销代销
			$data ['BZHQFSH'] = $_POST ['BZHQFSH']; //保质期方式
			$data ['BZHQYSH'] = $_POST ['BZHQYSH']; //保质期月数
			$data ['YJYSH'] = $_POST ['YJYSH']; //预警天数
			$data ['ZHCHCHCHJ'] = $_POST ['ZHCHCHCHJ']; //正常出厂价
			$data ['HSHJJ'] = $_POST ['HSHJJ']; //含税进价
			$data ['JINJIA'] = $_POST ['JINJIA']; //进价
			$data ['HSHSHJ'] = $_POST ['HSHSHJ']; //含税售价
			$data ['SHOUJIA'] = $_POST ['SHOUJIA']; //售价
			$data ['LSHJ'] = $_POST ['LSHJ']; //零售价
			$data ['PAIJIA'] = $_POST ['PAIJIA']; //牌价
			$data ['ZHDCHBJ'] = $_POST ['ZHDCHBJ']; //指导成本价
			$data ['ZDSHJ'] = $_POST ['ZDSHJ']; //最低售价
			$data ['ZGSHJ'] = $_POST ['ZGSHJ']; //最高售价
			$data ['MAOLILV'] = $_POST ['MAOLILV']; //毛利率
			$data ['SHUILV'] = $_POST ['SHUILV']; //税率
			$data ['KOULV'] = $_POST ['KOULV']; //扣率
			$data ['CHBJS'] = $_POST ['CHBJS']; //成本计算
			$data ['SHFYINPIAN'] = $_POST ['SHFYINPIAN']; //是否饮片
			$data ['BZHDWBH'] = $_POST ['BZHDWBH']; //包装单位
			$data ['JLGG'] = $_POST ['JLGG']; //计量规格
			$data ['ZHDKQLX'] = $_POST ['ZHDKQLX']; //指定库区类型
			$data ['QSHCKBH'] = $_POST ['QSHCKBH']; //缺省仓库编号
			$data ['QSHKQBH'] = $_POST ['QSHKQBH']; //缺省库区编号
			$data ['QSHKWBH'] = $_POST ['QSHKWBH']; //缺省库位编号
			$data ['KCSHX'] = $_POST ['KCSHX']; //库存上限
			$data ['KCXX'] = $_POST ['KCXX']; //库存下限
			$data ['HLKC'] = $_POST ['HLKC']; //合理库存
			$data ['DBZHTJ'] = $_POST ['DBZHTJ']; //大包装体积
			$data ['DBZHZHL'] = $_POST ['DBZHZHL']; //大包装重量
			$data ['DPZHL'] = $_POST ['DPZHL']; //单品重量
			$data ['DPCH'] = $_POST ['DPCH']; //单品长
			$data ['DANPINKUAN'] = $_POST ['DANPINKUAN']; //单品宽
			$data ['DANPINGAO'] = $_POST ['DANPINGAO']; //单品高
			$data ['ZXDWTJ'] = $_POST ['ZXDWTJ']; //最小单位体积
			$data ['ZXDWZHL'] = $_POST ['ZXDWZHL']; //最小单位重量
			$data ['PEISONGJIA'] = $_POST ['PEISONGJIA']; //配送价
			$data ['TJDJ'] = $_POST ['TJDJ']; //tj单价
			$data ['GONGHUOJIA'] = $_POST ['GONGHUOJIA']; //供货价
			$data ['PIFAJIA'] = $_POST ['PIFAJIA']; //批发价
			$data ['ZHDYHPZHLX'] = $_POST ['ZHDYHPZHLX']; //重点养护品种类型
			$data ['DHZHQ'] = $_POST ['DHZHQ']; //订货周期
			$data ['XYPZH'] = $_POST ['XYPZH']; //协议品种
			$data ['SHFYP'] = $_POST ['SHFYP']; //是否药品
			$data ['YOUXIAOQI'] = $_POST ['YOUXIAOQI']; //有效期
			$data ['CHKXZHSHL'] = $_POST ['CHKXZHSHL']; //出库限制数量
			$data ['RKXZHSHL'] = $_POST ['RKXZHSHL']; //入库限制数量
			$data ['YPZHWH'] = ($_POST ['YPZHWH'] == null) ? '0' : '1'; //是否有批准文号
			$data ['PZHWH'] = $_POST ['PZHWH']; //批准文号
			if ($_POST ['PZHWHYXQ'] != "") {
				$data ['PZHWHYXQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['PZHWHYXQ'] . "','YYYY-MM-DD')" ); //批准文号有效期
			}
			$data ['SHFOTC'] = $_POST ['SHFOTC']; //是否OTC
			$data ['FDSHPBZH'] = ($_POST ['FDSHPBZH'] == null) ? '0' : '1'; //分店商品标志
			$data ['SHPZHT'] = '1'; //商品状态			$data ['XDBZH'] = '0';  //限定标志
			$data ['ZHBJ'] = $_POST ['ZHBJ']; //中标价
			$data ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			$data ['BGRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //变更日期		
			$data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
			$data ['ZCHRQ'] = new Zend_Db_Expr ( "SYSDATE" ); //作成日期	
			
			//客户资料表
			$this->_db->insert ( "H01DB012101", $data );
			return true;
		}
	}
	
	/**
	 * 更新商品资料信息
	 *
	 * @return bool
	 */
	function updateShpzl() {
		//检测时间戳是否发生变动
		$sql = "SELECT to_char(BGRQ,'yyyy-mm-dd hh24:mi:ss') AS BGRQ FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH FOR UPDATE";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $_POST ['SHPBH'] );
		$timestamp = $this->_db->fetchOne ( $sql, $bind );
		
		//时间戳已经变更
		

		if ($timestamp != $_POST ['BGRQ']) {
			return false;
		} else {
			$sql = "UPDATE H01DB012101 SET " . " QYBH = :QYBH," . " SHPMCH = :SHPMCH," . " GUIGE = :GUIGE," . " YPGNZHZHLXQK = :YPGNZHZHLXQK," 
			     . " SHPTM = :SHPTM," . " GJBM = :GJBM," . " FLBM = :FLBM," . " SHCHCHJ = :SHCHCHJ," . " ZHJM = :ZHJM," 
			     . " HUAXUEMING = :HUAXUEMING," . " CHYM = :CHYM," . " SUMING = :SUMING," . " JIXING = :JIXING," . " XWMCH = :XWMCH," 
			     . " TYMCH = :TYMCH," . " LEIBIE = :LEIBIE," . " YYFL = :YYFL," . " CHFFL = :CHFFL," . " YFPD = :YFPD," 
			     . " GZHBZH = :GZHBZH," . " YWCHF = :YWCHF," . " SHPLX = :SHPLX," . " YFYYL = :YFYYL," . " KEMUHAO = :KEMUHAO," 
			     . " BLFY = :BLFY," . " JJZH = :JJZH," . " SHYZH = :SHYZH," . " CHCZHYSHX = :CHCZHYSHX," . " BEIZHU = :BEIZHU," 
			     . " ZHYSHX = :ZHYSHX," . " CHANDI = :CHANDI," . " BZHGG = :BZHGG," . " JXDX = :JXDX," . " BZHQFSH = :BZHQFSH," 
			     . " BZHQYSH = :BZHQYSH," . " YJYSH = :YJYSH," . " ZHCHCHCHJ = :ZHCHCHCHJ," . " HSHJJ = :HSHJJ," . " JINJIA = :JINJIA," 
			     . " HSHSHJ = :HSHSHJ," . " SHOUJIA = :SHOUJIA," . " LSHJ = :LSHJ," . " PAIJIA = :PAIJIA," . " ZHDCHBJ = :ZHDCHBJ," 
			     . " ZDSHJ = :ZDSHJ," . " ZGSHJ = :ZGSHJ," . " MAOLILV = :MAOLILV," . " SHUILV = :SHUILV," . " KOULV = :KOULV," 
			     . " CHBJS = :CHBJS," . " SHFYINPIAN = :SHFYINPIAN," . " BZHDWBH = :BZHDWBH," . " JLGG = :JLGG," . " ZHDKQLX = :ZHDKQLX," 
			     . " QSHCKBH = :QSHCKBH," . " QSHKQBH = :QSHKQBH," . " QSHKWBH = :QSHKWBH," . " KCSHX = :KCSHX," . " KCXX = :KCXX," 
			     . " HLKC = :HLKC," . " DBZHTJ = :DBZHTJ," . " DBZHZHL = :DBZHZHL," . " DPZHL = :DPZHL," . " DPCH = :DPCH," 
			     . " DANPINKUAN = :DANPINKUAN," . " DANPINGAO = :DANPINGAO," . " ZXDWTJ = :ZXDWTJ," . " ZXDWZHL = :ZXDWZHL," 
			     . " PEISONGJIA = :PEISONGJIA," . " TJDJ = :TJDJ," . " GONGHUOJIA = :GONGHUOJIA," . " PIFAJIA = :PIFAJIA," 
			     . " ZHDYHPZHLX = :ZHDYHPZHLX," . " DHZHQ = :DHZHQ," . " XYPZH = :XYPZH," . " SHFYP = :SHFYP," 
			     . " YOUXIAOQI = :YOUXIAOQI," . " CHKXZHSHL = :CHKXZHSHL," . " RKXZHSHL = :RKXZHSHL," . " YPZHWH = :YPZHWH," 
			     . " PZHWH = :PZHWH," . " PZHWHYXQ = TO_DATE(:PZHWHYXQ,'YYYY-MM-DD')," . " SHFOTC = :SHFOTC," . " FDSHPBZH = :FDSHPBZH," 
			     . " ZHBJ = :ZHBJ," . "BGRQ = sysdate," . " BGZH = :BGZH" . " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
			$bind ['SHPBH'] = $_POST ['SHPBH']; //商品编号
			$bind ['SHPMCH'] = $_POST ['SHPMCH']; //商品名称
			$bind ['GUIGE'] = $_POST ['GUIGE']; //商品规格
			$bind ['YPGNZHZHLXQK'] = $_POST ['YPGNZHZHLXQK']; //药品功能主治疗效情况
			$bind ['SHPTM'] = $_POST ['SHPTM']; //商品条码
			$bind ['GJBM'] = $_POST ['GJBM']; //国家编码
			$bind ['FLBM'] = $_POST ['FLBM']; //分类编码
			//$bind ['SHCHCHJBM'] = $_POST ['SHCHCHJBM']; //生产厂家编码
			$bind ['SHCHCHJ'] = $_POST ['SHCHCHJ']; //生产厂家
			$bind ['ZHJM'] = $_POST ['ZHJM']; //助记码	
			$bind ['HUAXUEMING'] = $_POST ['HUAXUEMING']; //化学名	
			$bind ['CHYM'] = $_POST ['CHYM']; //常用名
			$bind ['SUMING'] = $_POST ['SUMING']; //俗名
			$bind ['JIXING'] = $_POST ['JIXING']; //剂型
			$bind ['XWMCH'] = $_POST ['XWMCH']; //西文名称
			$bind ['TYMCH'] = $_POST ['TYMCH']; //通用名
			$bind ['LEIBIE'] = $_POST ['LEIBIE']; //类别
			$bind ['YYFL'] = $_POST ['YYFL']; //用药分类
			$bind ['CHFFL'] = $_POST ['CHFFL']; //处方分类
			$bind ['YFPD'] = $_POST ['YFPD']; //药方判断
			$bind ['GZHBZH'] = $_POST ['GZHBZH']; //贵重标志
			$bind ['YWCHF'] = $_POST ['YWCHF']; //药物成分
			$bind ['SHPLX'] = $_POST ['SHPLX']; //商品类型
			$bind ['YFYYL'] = $_POST ['YFYYL']; //用法与用量
			$bind ['KEMUHAO'] = $_POST ['KEMUHAO']; //科目号
			$bind ['BLFY'] = $_POST ['BLFY']; //不良反应
			$bind ['JJZH'] = $_POST ['JJZH']; //禁忌症
			$bind ['SHYZH'] = $_POST ['SHYZH']; //适应症
			$bind ['CHCZHYSHX'] = $_POST ['CHCZHYSHX']; //储存注意事项
			$bind ['BEIZHU'] = $_POST ['BEIZHU']; //备注
			$bind ['ZHYSHX'] = $_POST ['ZHYSHX']; //注意事项
			$bind ['CHANDI'] = $_POST ['CHANDI']; //产地
			$bind ['BZHGG'] = $_POST ['BZHGG']; //包装规格
			$bind ['JXDX'] = $_POST ['JXDX']; //经销代销
			$bind ['BZHQFSH'] = $_POST ['BZHQFSH']; //保质期方式
			$bind ['BZHQYSH'] = $_POST ['BZHQYSH']; //保质期月数
			$bind ['YJYSH'] = $_POST ['YJYSH']; //预警天数
			$bind ['ZHCHCHCHJ'] = $_POST ['ZHCHCHCHJ']; //正常出厂价
			$bind ['HSHJJ'] = $_POST ['HSHJJ']; //含税进价
			$bind ['JINJIA'] = $_POST ['JINJIA']; //进价
			$bind ['HSHSHJ'] = $_POST ['HSHSHJ']; //含税售价
			$bind ['SHOUJIA'] = $_POST ['SHOUJIA']; //售价
			$bind ['LSHJ'] = $_POST ['LSHJ']; //零售价
			$bind ['PAIJIA'] = $_POST ['PAIJIA']; //牌价
			$bind ['ZHDCHBJ'] = $_POST ['ZHDCHBJ']; //指导成本价
			$bind ['ZDSHJ'] = $_POST ['ZDSHJ']; //最低售价
			$bind ['ZGSHJ'] = $_POST ['ZGSHJ']; //最高售价
			$bind ['MAOLILV'] = $_POST ['MAOLILV']; //毛利率
			$bind ['SHUILV'] = $_POST ['SHUILV']; //税率
			$bind ['KOULV'] = $_POST ['KOULV']; //扣率
			$bind ['CHBJS'] = $_POST ['CHBJS']; //成本计算
			$bind ['SHFYINPIAN'] = $_POST ['SHFYINPIAN']; //是否饮片
			$bind ['BZHDWBH'] = $_POST ['BZHDWBH']; //包装单位
			$bind ['JLGG'] = $_POST ['JLGG']; //计量规格
			$bind ['ZHDKQLX'] = $_POST ['ZHDKQLX']; //指定库区类型
			$bind ['QSHCKBH'] = $_POST ['QSHCKBH']; //缺省仓库编号
			$bind ['QSHKQBH'] = $_POST ['QSHKQBH']; //缺省库区编号
			$bind ['QSHKWBH'] = $_POST ['QSHKWBH']; //缺省库位编号
			$bind ['KCSHX'] = $_POST ['KCSHX']; //库存上限
			$bind ['KCXX'] = $_POST ['KCXX']; //库存下限
			$bind ['HLKC'] = $_POST ['HLKC']; //合理库存
			$bind ['DBZHTJ'] = $_POST ['DBZHTJ']; //大包装体积
			$bind ['DBZHZHL'] = $_POST ['DBZHZHL']; //大包装重量
			$bind ['DPZHL'] = $_POST ['DPZHL']; //单品重量
			$bind ['DPCH'] = $_POST ['DPCH']; //单品长
			$bind ['DANPINKUAN'] = $_POST ['DANPINKUAN']; //单品宽
			$bind ['DANPINGAO'] = $_POST ['DANPINGAO']; //单品高
			$bind ['ZXDWTJ'] = $_POST ['ZXDWTJ']; //最小单位体积
			$bind ['ZXDWZHL'] = $_POST ['ZXDWZHL']; //最小单位重量
			$bind ['PEISONGJIA'] = $_POST ['PEISONGJIA']; //配送价
			$bind ['TJDJ'] = $_POST ['TJDJ']; //tj单价
			$bind ['GONGHUOJIA'] = $_POST ['GONGHUOJIA']; //供货价
			$bind ['PIFAJIA'] = $_POST ['PIFAJIA']; //批发价
			$bind ['ZHDYHPZHLX'] = $_POST ['ZHDYHPZHLX']; //重点养护品种类型
			$bind ['DHZHQ'] = $_POST ['DHZHQ']; //订货周期
			$bind ['XYPZH'] = $_POST ['XYPZH']; //协议品种
			$bind ['SHFYP'] = $_POST ['SHFYP']; //是否药品
			$bind ['YOUXIAOQI'] = $_POST ['YOUXIAOQI']; //有效期
			$bind ['CHKXZHSHL'] = $_POST ['CHKXZHSHL']; //出库限制数量
			$bind ['RKXZHSHL'] = $_POST ['RKXZHSHL']; //入库限制数量
			$bind ['YPZHWH'] = ($_POST ['YPZHWH'] == null) ? '0' : '1'; //有批准文号
			$bind ['PZHWH'] = $_POST ['PZHWH']; //批准文号
			$bind ['PZHWHYXQ'] = $_POST ['PZHWHYXQ']; //批准文号有效期		
			$bind ['SHFOTC'] = $_POST ['SHFOTC']; //是否OTC
			$bind ['FDSHPBZH'] = ($_POST ['FDSHPBZH'] == null) ? '0' : '1'; //分店商品标志
			$bind ['ZHBJ'] = $_POST ['ZHBJ']; //中标价
			$bind ['BGZH'] = $_SESSION ['auth']->userId; //变更者
			
			$this->_db->query ( $sql, $bind );
			
			return true;
		}
	}
	
	/**
	 * 商品条码是否已存在check
	 *
	 * @return int $count {0:不存在;其他:存在}
	 */
	function checkShptm($shptm) {
		$sql = "SELECT COUNT(1) FROM H01DB012101 WHERE QYBH = :QYBH AND SHPTM = :SHPTM";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPTM' => $shptm );
		$count = $this->_db->fetchOne ( $sql, $bind );
		if ($count == 0) {
			$sql1 = "SELECT COUNT(1) FROM H01DB012102 WHERE QYBH = :QYBH AND SHPTM = :SHPTM";
			$count = $this->_db->fetchOne ( $sql1, $bind );
		}
		
		return $count;
	}
	
	/**
	 * 商品拆散信息是否已存在check
	 *
	 * @return bool
	 */
	function checkBzhdw($shpbh) {
		$sql = "SELECT BZHDWBH FROM H01DB012117 WHERE QYBH = :QYBH AND SHPBH = :SHPBH AND SHFWJBDW = '1'";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		return $this->_db->fetchOne ( $sql, $bind );
	
	}
	
	/**
	 * 删除商品拆散信息
	 *
	 * @return bool
	 */
	function delete($shpbh) {
		$sql = "DELETE FROM H01DB012117 WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		$bind = array ('QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		return $this->_db->query ( $sql, $bind );
	
	}
	
	/**
	 * 更改商品使用状态
	 *
	 * @return bool
	 */
	function updateStatus($shpbh, $shpzht) {
		
		$sql = "UPDATE H01DB012101 " . " SET SHPZHT = :SHPZHT" . " WHERE QYBH =:QYBH AND SHPBH =:SHPBH";
		$bind = array ('SHPZHT' => $shpzht, 'QYBH' => $_SESSION ['auth']->qybh, 'SHPBH' => $shpbh );
		return $this->_db->query ( $sql, $bind );
	
	}

}
	