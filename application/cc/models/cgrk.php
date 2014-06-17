<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购入库(CGRK)
 * 作成者：ZhangZeliang
 * 作成日：2011/03/30
 * 更新履历：
 *********************************/

class cc_models_cgrk extends Common_Model_Base
{
	private $idx_ROWNUM=0;// 行号
    private $idx_SHPBH=1;// 商品编号
    private $idx_SHPMCH=2;// 商品名称
    private $idx_GUIGE=3;// 规格
    private $idx_PIHAO=4;// 批号
    private $idx_HWMCH=5;// 货位
    private $idx_BZHDWM=6;// 包装单位
    private $idx_SHCHRQ=7;// 生产日期
    private $idx_BZHQZH=8;// 保质期至
    private $idx_JLGG=9;// 计量规格
    private $idx_BZHSHL=10;// 包装数量
    private $idx_LSSHL=11;// 零散数量
    private $idx_SHULIANG=12;// 数量
    private $idx_DANJIA=13;// 单价
    private $idx_HSHJ=14;// 含税售价
    private $idx_KOULV=15;// 扣率
    private $idx_SHUILV=16;// 税率
    private $idx_JINE=17;// 金额
    private $idx_HSHJE=18;// 含税金额
    private $idx_SHUIE=19;// 税额
    private $idx_LSHJ=20;// 零售价
    private $idx_CHANDI=21;// 产地
    private $idx_BEIZHU=22;// 备注
    private $idx_BZHDWBH = 23; // 包装单位编号
    private $idx_ZHDKQLX=24;// 指定库区类型
    private $idx_KQLXMCH=25;// 指定库区类型名称
    private $idx_TYMCH=26;// 通用名称
    private $idx_CKBH=27;// 仓库编号
    private $idx_KQBH=28;// 库区编号
    private $idx_KWBH=29;// 库位编号
    private $idx_SHFSHKW=30;// 是否散货区
	 /**
	 * 得到采购单列表数据(cgrk_02.php页面)--采购单
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getCgGridData($filter) {
		//排序用字段名
		$fields = array ("", "YRKDBH", "CKDBH", "KPRQ", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(ZCHZH,'NLS_SORT=SCHINESE_PINYIN_M')" );
		
		//检索SQL
		$sql = "SELECT YRKDBH,CKDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DWBH,DWMCH,BMMCH,YWYXM,YWYXM AS CZY " 
		. "FROM H01VIEW012429 WHERE QYBH = :QYBH AND CGYFHZHT = '1' AND RKZHT = '0' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		
		if ($filter['searchParams']["KSRQKEY"] != "" || $filter['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter['searchParams']["KSRQKEY"];
			$bind ['ZZRQ'] = $filter['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(单位编号输入)
		if ($filter['searchParams']["DWBHKEY"] != "") {
			$sql .= " AND DWBH = :DWBH";
			$bind['DWBH'] = $filter['searchParams']["DWBHKEY"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter['searchParams']["DWBHKEY"] == "" && $filter['searchParams']["DWMCHKEY"] != "") {
			$sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter['searchParams']["DWMCHKEY"];
		}
		//自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_RKZJXX",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter["orderby"]] . " " . $filter["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",CKDBH";
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
	 * 得到采购单明细列表数据(cgrk_02.php页面)--明细
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getCgdXzMx($filter) {
				
		//检索SQL
		$sql = "SELECT A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,A.PIHAO,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD'),TO_CHAR(A.BZHQZH,'YYYY-MM-DD'),B.JLGG,A.BZHSHL,A.LSSHL,A.SHULIANG," . 
		"A.KRKSHL,A.DANJIA,A.HSHJ,A.KOULV,B.SHUILV,A.JINE,A.HSHJE,A.SHUIE,B.LSHJ,B.CHANDI,A.BEIZHU " . 
		" FROM H01DB012430 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH=B.SHPBH LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.SHPBH = B.SHPBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " . 
		" WHERE A.QYBH=:QYBH AND YRKDBH = :YRKDBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YRKDBH'] = $filter ["yrkdbh"];
		//排序
		//$sql .= " ORDER BY A.CGDBH,A.XUHAO";
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	/**
	 * 根据预入库单编号获取单据信息(cgrk_01.php页面)
	 *
	 * @param string $yrkdbh
	 * @return array json
	 */
	public function getdjinfo($yrkdbh)
	{
		//查询语句
		$sql="SELECT A.CKDBH,A.SHFZZHSH,A.DWBH,B.DWMCH,A.DHHM,A.DIZHI,A.KOULV FROM H01DB012429 A LEFT JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.DWBH = B.DWBH WHERE A.YRKDBH = :YRKDBH";
		//绑定查询条件
		$bind["YRKDBH"] = $yrkdbh;
		return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
	 * 根据预入库单编号获取单据 详细信息(cgrk_01.php页面)
	 *
	 * @param string $yrkdbh
	 * @return array json
	 */
	public function yrkdspmxInfo($yrkdbh)
	{
		$sql = "SELECT A.KRKSHL,A.SHPBH,B.SHPMCH,B.GUIGE,C.NEIRONG,A.PIHAO,TO_CHAR(A.SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,".
		"TO_CHAR(A.BZHQZH,'YYYY-MM-DD') AS BZHQZH,HGL_DEC(B.JLGG) AS JLGG,HGL_DEC(A.BZHSHL) AS BZHSHL,HGL_DEC(A.LSSHL) AS LSSHL,HGL_DEC(A.SHULIANG) AS SHULIANG," . 
		"HGL_DEC(A.DANJIA) AS DANJIA,HGL_DEC(A.HSHJ) AS HSHJ,HGL_DEC(A.KOULV) AS KOULV,HGL_DEC(B.SHUILV) AS SHUILV,".
		"HGL_DEC(A.JINE) AS JINE,HGL_DEC(A.HSHJE) AS HSHJE,HGL_DEC(A.SHUIE) AS SHUIE,HGL_DEC(B.LSHJ) AS LSHJ,B.CHANDI,".
		"A.BEIZHU,D.NEIRONG AS KQLXMCH,B.BZHDWBH,B.ZHDKQLX,B.TYMCH " . 
		" FROM H01DB012430 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH=B.SHPBH LEFT JOIN H01DB012001 C ON ".
		"A.QYBH = C.QYBH AND A.SHPBH = B.SHPBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' " .
		" LEFT JOIN H01DB012001 D ON B.ZHDKQLX = D.ZIHAOMA AND D.CHLID = 'KQLX'".
		" WHERE A.QYBH=:QYBH AND A.YRKDBH=:YRKDBH ORDER BY A.XUHAO ASC";
		//绑定数据
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["YRKDBH"] = $yrkdbh;
		
		return $this->_db->fetchAll($sql, $bind);
	}
	
	/**
	 * 判断数据库中是否存制定的库位中是否存在与给定批号相同的同一商品(cgrk_01.php商品明细列表选择库位)
	 *
	 * @param array $filter
	 * @return array json
	 */
	public function pdPhHw($filter)
	{
		$sql="SELECT COUNT(*) AS CON FROM " 
		. "(SELECT DISTINCT PIHAO FROM H01DB012404 " 
		. "WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH AND SHULIANG <> 0) " 
		. "WHERE PIHAO <> :PIHAO";
		//绑定查询条件
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["CKBH"] = $filter["ckbh"];
		$bind["KQBH"] = $filter["kqbh"];
		$bind["KWBH"] = $filter["kwbh"];
		$bind["SHPBH"] = $filter["shpbh"];
		$bind["PIHAO"] = $filter["pihao"];
		return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
	 * 获取质检信息明细选择表详细信息(cgrk_03.php)
	 *
	 * @param string $yrkdbh
	 * @return array json
	 */
	public function zjmxData($filter)
	{
		$sql="SELECT A.SHPBH,B.SHPMCH,B.GUIGE,A.PIHAO,C.NEIRONG AS BZHDW,TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH," 
		. "B.JLGG，A.BZHSHL,A.LSSHL,A.SHULIANG,A.KRKSHL,A.DANJIA,A.HSHJ,A.KOULV," 
		. "B.SHUILV,A.JINE,A.HSHJE,A.SHUIE,B.LSHJ,B.CHANDI,A.BEIZHU,B.TYMCH,B.BZHDWBH,B.ZHDKQLX ,D.NEIRONG AS KQLXMCH "
		. "FROM H01DB012430 A LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH " 
		. "LEFT JOIN H01DB012001 C ON A.QYBH = C.QYBH AND A.SHPBH = B.SHPBH AND B.BZHDWBH = C.ZIHAOMA AND C.CHLID = 'DW' "
		. "LEFT JOIN H01DB012001 D ON B.ZHDKQLX = D.ZIHAOMA AND D.CHLID = 'KQLX' "
		. "WHERE A.QYBH = :QYBH AND A.YRKDBH = :YRKDBH ORDER BY A.XUHAO";
		
		//绑定查询条件
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["YRKDBH"] = $filter["yrkdbh"];
		
		//翻页表格用SQL生成(总行数与单页记录)
        $pagedSql = Common_Tool::getPageSql ( $sql, $filter );
        
        //总行数
        $totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
        
        //当前页数据
        $recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
        
        //调用表格xml生成函数
        return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );
	}
	
	/**
     * 获取明细信息表中每条数据的库位状态(cgrk_01.php)
     *
     * @param array $filter
     * @return array json
     */
	public function kuweiZt($filter)
	{
		$sql="SELECT KWZHT FROM H01DB012403 WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH";
		
		//绑定查询条件
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["CKBH"] = $filter["ckbh"];
		$bind["KQBH"] = $filter["kqbh"];
		$bind["KWBH"] = $filter["kwbh"];
		
		return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
     * 获取明细信息表中每条数据的库区类型(cgrk_01.php)
     *
     * @param array $filter
     * @return array json
     */
	public function getKqlx($filter)
	{
		$sql="SELECT A.KQLX,B.NEIRONG AS KQLXMCH FROM H01DB012402 A LEFT JOIN H01DB012001 B ON " 
		. "A.QYBH = B.QYBH AND A.KQLX = B.ZIHAOMA AND B.CHLID = 'KQLX' WHERE A.CKBH = :CKBH AND A.KQBH = :KQBH AND A.QYBH = :QYBH";
		
		//绑定查询条件
		$bind["QYBH"] = $_SESSION["auth"]->qybh;
		$bind["CKBH"] = $filter["ckbh"];
        $bind["KQBH"] = $filter["kqbh"];
        
        return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
     * 画面输入项验证(cgrk_01.php)
     *
     * @param none
     * @return array boolean
     */
	public function inputCheck()
	{
		if($_POST["KPRQ"] == "" || $_POST["BMBH"] == "" || $_POST["YWYBH"] == "" || $_POST["#grid_mingxi"] == "")
		{
			return false;
		}
		$isHasMingxi = false; //是否存在至少一条明细
		foreach($_POST["#grid_mingxi"] as $grid)
		{
			if($grid[$this->idx_SHPBH] != "")
			{
				$isHasMingxi = true;
				if($grid [$this->idx_PIHAO] == "" || //批号
                    $grid [$this->idx_SHULIANG] == "" || //数量
                    $grid [$this->idx_SHULIANG] == "0" || //数量
                    $grid [$this->idx_CKBH] == "" || //仓库编号
                    $grid [$this->idx_KQBH] == "" || //库区编号 
                    $grid [$this->idx_KWBH] == "")
                    {
                    	return false;
                    }
			}
		}
		
		//一条明细也没有输入
        if (! $isHasMingxi) {
            return false;
        }   
        return true;
	}
	
	/**
     * 单位合法性
     *
     * @param none
     * @return array boolean
     */
    public function logicCheck() {
        
        //单位合法性


        $filter ['dwbh'] = $_POST ['DWBH'];
        if ($this->getDanweiInfo ( $filter ) == FALSE) {
            return false;
        }
        
        return true;
    }
	
    /*
     * 根据单位编号编号取得单位信息
     * 
     * @param array $filter
     * @return string array
     */
    public function getDanweiInfo($filter) {
        //检索SQL
        $sql =  "SELECT A.DWBH,A.DWMCH,A.DIZHI,A.DHHM,HGL_DEC(A.KOULV),A.FHQBH," . "DECODE(A.XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //销售信贷期 
                " FROM H01DB012106 A" . " WHERE A.QYBH = :QYBH " . //区域编号
                " AND A.DWBH = :DWBH" . //单位编号
                " AND A.FDBSH ='0'" . //分店标识
                " AND A.SHFXSH = '1'" . //是否销售

                " AND A.KHZHT = '1'"; //客户状态


        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind ['DWBH'] = $filter ['dwbh'];
        
        return $this->_db->fetchRow ( $sql, $bind );
    }
    
    
	/**
     * 入库单保存(cgrk_01.php)
     *
     * @param $rkdbh 入库单编号
     * @return array boolean
     */
	public function saveRukudan($rkdbh)
	{
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;  
        $rukudan['RKDBH'] = $rkdbh;
        $rukudan['CKDBH'] = $_POST["CGDBH"];
        $rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期

        $rukudan['BMBH'] = $_POST["BMBH"];
        $rukudan['YWYBH'] = $_POST["YWYBH"];
        $rukudan['DWBH'] = $_POST["DWBH"];
        $rukudan['DIZHI'] = $_POST["DIZHI"];
        $rukudan['DHHM'] = $_POST["DHHM"];
        $rukudan['SHFZZHSH'] = $_POST["SHFZZHSH"];
        $rukudan['KOULV'] = $_POST["KOULV"];
        $rukudan['BEIZHU'] = $_POST["BEIZHU"];
        $rukudan['RKLX'] = '1';                             //采购入库
        $rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
        $rukudan ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
        $rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
        $rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者

        $this->_db->insert ( "H01DB012406", $rukudan );
	}
	
	/**
     * 入库单明细信息保存(cgrk_01.php)
     *
     * @param $rkdbh 入库单编号
     * @return array array
     */
	public function executeMingxi($rkdbh)
	{
		$idx_rukumingxi = 1; //入库单明细信息序号  
        $idx_lvli = 1; //在库移动履历
        foreach ( $_POST ["#grid_mingxi"] as $row ) {
            //生成入库单明细信息
            $this->InsertRukumingxi($row,$rkdbh,$idx_rukumingxi); 
                      
            //入库单明细信息序号自增   
            $idx_rukumingxi ++;
            
            //在库商品信息新生成
            $this->insertZaiku($row,$rkdbh);
                                                    
            //商品移动履历的新生成
            $this->insertLvli($row,$rkdbh,$idx_lvli);
            
            $idx_lvli++;
        }   
	}
	
    /*
     * 生成入库单明细信息
     * 
     * @param   array   $row:明细
     *          string  $rkdbh:新生成的采购入库单编号
     *          int     $idx_rukumingxi:入库单明细信息序号   
     * @return array 
     */
    public function InsertRukumingxi($row,$rkdbh,$idx_rukumingxi) {
        
        $data['QYBH'] = $_SESSION ['auth']->qybh;
        $data['RKDBH'] = $rkdbh;
        $data['XUHAO'] = $idx_rukumingxi;
        $data['SHPBH'] = $row [$this->idx_SHPBH];
        $data['BZHSHL'] = $row [$this->idx_BZHSHL];
        $data['LSSHL'] = $row [$this->idx_LSSHL];
        $data['SHULIANG'] = $row [$this->idx_SHULIANG];
        $data['DANJIA'] = $row [$this->idx_DANJIA];
        $data['HSHJ'] = $row [$this->idx_HSHJ];
        $data['KOULV'] = $row [$this->idx_KOULV];
        $data['JINE'] = $row [$this->idx_JINE];
        $data['HSHJE'] = $row [$this->idx_HSHJE];
        $data['SHUIE'] = $row [$this->idx_SHUIE];
        $data['BEIZHU'] = $row [$this->idx_BEIZHU];
        $data['PIHAO'] = $row [$this->idx_PIHAO];
        if ($row [$this->idx_SHCHRQ] != ""){
            $data['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
        }
        if ($row [$this->idx_BZHQZH] != ""){
            $data['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
        }
        $data['CKBH'] = $row [$this->idx_CKBH];
        $data['KQBH'] = $row [$this->idx_KQBH];
        $data['KWBH'] = $row [$this->idx_KWBH];
        $data['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
        $data['BGZH'] = $_SESSION ['auth']->userId; //变更者   
        $data ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
        $data ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
        $this->_db->insert ( "H01DB012407", $data );             
    }
    
     /*
     * 新做成在库商品信息
     * 
     * @param   array   $row:明细
     *          string  $rkdbh:新生成的入库单编号
     * 
     * @return  bool    
     */
    public function insertZaiku($row,$rkdbh)
    {
    	$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
        $zaiku['CKBH'] = $row [$this->idx_CKBH];
        $zaiku['KQBH'] = $row [$this->idx_KQBH];
        $zaiku['KWBH'] = $row [$this->idx_KWBH];
        $zaiku['SHPBH'] = $row [$this->idx_SHPBH];
        $zaiku['PIHAO'] = $row [$this->idx_PIHAO];
        $zaiku['RKDBH'] = $rkdbh;
        $zaiku['ZKZHT'] = '0';
        $zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
        $zaiku['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");
        $zaiku['SHULIANG'] = $row [$this->idx_SHULIANG];
        if ($row [$this->idx_SHCHRQ] != ""){
            $zaiku['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
        }
        if ($row [$this->idx_BZHQZH] != ""){
            $zaiku['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
        }
        
        $this->_db->insert ( "H01DB012404", $zaiku );
    }
    
    /*
     * 移动履历做成
     * 
     * @param   array   $row:明细
     *          string  $rkdbh:新生成的入库单编号
     *          int     $idx_lvli:移动履历序号
     * 
     * @return  bool    
     */
    public function insertLvli($row,$rkdbh,$idx_lvli)
    {
    	$lvli['QYBH'] = $_SESSION ['auth']->qybh;
        $lvli['CKBH'] = $row [$this->idx_CKBH];
        $lvli['KQBH'] = $row [$this->idx_KQBH];
        $lvli['KWBH'] = $row [$this->idx_KWBH];
        $lvli['SHPBH'] = $row [$this->idx_SHPBH];
        $lvli['PIHAO'] = $row [$this->idx_PIHAO];
        $lvli['RKDBH'] = $rkdbh;
        $lvli['YDDH'] = $rkdbh;
        $lvli['XUHAO'] = $idx_lvli;
        if ($row [$this->idx_SHCHRQ] != ""){
            $lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
        }
        if ($row [$this->idx_BZHQZH] != ""){
            $lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
        }
        $lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
        $lvli['SHULIANG'] = $row [$this->idx_SHULIANG];
        $lvli['BZHDWBH'] = $row [$this->idx_BZHDWBH];
        $lvli['ZHYZHL'] = '11';
        $lvli['ZKZHT'] = '0';
        $lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
        $lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者       
        $lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
        $lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
        $this->_db->insert ( 'H01DB012405', $lvli );
    }
    
     /*
     * 更新采购单状态(H01DB012306)
     * 
     * @param   none
     * 
     * @return  bool
     */
     public function updateCgd06()
     {
     	$sql="UPDATE H01DB012306 SET CGDZHT = '1',BGZH=:BGZH,BGRQ=sysdate WHERE QYBH=:QYBH AND CGDBH=:CGDBH";
     	//绑定查询条件
     	$bind["QYBH"] = $_SESSION['auth']->qybh;
     	$bind["BGZH"] = $_SESSION['auth']->userId;    //变更者
     	$bind["CGDBH"] = $_POST['CKDBH'];             //采购单编号
     	
     	$this->_db->query($sql, $bind);
     }
     
     /*
     * 更新采购单状态(H01DB012429)
     *
     * @param   none
     *
     * @return  bool
     */
    public function updateCgd29()
    {
    	$sql="UPDATE H01DB012429 SET RKZHT = '1',BGZH=:BGZH,BGRQ=sysdate WHERE QYBH=:QYBH AND YRKDBH=:YRKDBH";
        //绑定查询条件
        $bind["QYBH"] = $_SESSION['auth']->qybh;
        $bind["BGZH"] = $_SESSION['auth']->userId;    //变更者
        $bind["YRKDBH"] = $_POST['CGDBH'];             //采购单编号
        
        $this->_db->query($sql, $bind);
    }
	
	public function beginTransaction()
	{
		$this->_db->beginTransaction();
	}
	
	public function commit(){
        $this->_db->commit();
    }
    
    public function rollBack(){
        $this->_db->rollBack();
    }
    
    /*
     * 获取合适仓库信息(cgrk_04.php)
     *
     * @param   $filter
     *
     * @return  XML
     */
    public function loadCangKu($filter)
    {
    	$sql="SELECT A.CKBH,A.CKMCH,B.KQBH,B.KQMCH,B.KQLX,D.NEIRONG AS KQLXMCH,C.KWBH,C.KWMCH,DECODE(C.SHFSHKW,'1','零散库位','0','整件库位','-') AS KWLXMCH,C.SHFSHKW " 
		. "FROM H01DB012401 A "
		. "JOIN H01DB012402 B ON B.QYBH = A.QYBH AND B.CKBH = A.CKBH "
		. "JOIN H01DB012403 C ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH = B.KQBH "
		. "LEFT JOIN H01DB012001 D ON D.QYBH = B.QYBH AND D.CHLID = 'KQLX' AND D.ZIHAOMA = B.KQLX "
		. "WHERE A.QYBH = :QYBH AND C.KWZHT = '1' AND B.KQZHT = '1' AND A.CKZHT = '1' "
		. "AND B.KQLX = :KQLX AND C.KWBH NOT IN ("
		. "SELECT KWBH FROM H01DB012404 WHERE SHPBH = :SHPBH AND PIHAO <> :PIHAO)";
		
		$bind["QYBH"] = $_SESSION['auth']->qybh;
		$bind["SHPBH"] = $filter["shpbh"];
		$bind["PIHAO"] = $filter['pihao'];
		$bind["KQLX"] = $filter['zhdkqlx'];
		
		 //当前页数据
        $recs = $this->_db->fetchAll ( $sql, $bind );
        
        //调用表格xml生成函数
        return Common_Tool::createXml ( $recs);
    }
}
?>