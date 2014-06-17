<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    采购预入库(CGYRK)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/24
 * 更新履历：
 *********************************/
class cc_models_cgyrk extends Common_Model_Base 
{
	private $idx_ROWNUM=0;// 行号
	private $idx_SHPBH=1;// 商品编号
	private $idx_SHPMCH=2;// 商品名称
	private $idx_GUIGE=3;// 规格
	private $idx_BZHDWM=4;// 包装单位
	private $idx_DYQWZH=8;// 待验区位置
	private $idx_PIHAO=5;// 批号
	private $idx_SHCHRQ=6;// 生产日期
	private $idx_BZHQZH=7;// 保质期至
	private $idx_HGPSHL=9;//合格品数量
	private $idx_BHGPSHL=10;//不合格品数量
	private $idx_BHGYY=11;//不合格原因
	private $idx_DANJIA=12;// 单价
	private $idx_HSHJ=13;// 含税售价
	private $idx_KOULV=14;// 扣率
	private $idx_SHUILV=15;// 税率
	private $idx_JINE=16;// 金额
	private $idx_HSHJE=17;// 含税金额
	private $idx_SHUIE=18;// 税额
	private $idx_LSHJ=19;// 零售价
	private $idx_CHANDI=20;// 产地
	private $idx_BEIZHU=21;// 备注
	private $idx_JLGG=22;// 计量规格
	private $idx_BZHDWBH = 23; // 包装单位编号
	private $idx_TYMCH=24;// 通用名称
	private $idx_BZHQYSH=25;//保质期月数
	private $idx_YJYSH=26;//预警月数
	
	/**
     * 得到采购单列表数据(采购单选择页面)--采购单
     *
     * @param array $filter
     * @return unknown
     */
	public function getCgdList($filter)
	{
		//排序用字段名
        $fields = array ("", "CGDBH", "KPRQ", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(ZCHZHXM,'NLS_SORT=SCHINESE_PINYIN_M')" );
        
        //检索SQL
        $sql = "SELECT CGDBH,TO_CHAR(KPRQ,'YYYY-MM-DD') AS KPRQ,DWBH,DWMCH,BMMCH,YWYXM,ZCHZHXM AS CZYXM FROM H01VIEW012306 WHERE QYBH = :QYBH AND QXBZH ='1' AND SHPZHT = '1' AND CGDZHT = '1'";
        
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        
        //查询条件(开始日期<=开票日期<=终止日期)
        if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "") {
            $sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
            $bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == "" ? "1900-01-01" : $filter ['searchParams']["KSRQKEY"];
            $bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == "" ? "9999-12-31" : $filter ['searchParams']["ZZRQKEY"];
        }
        
        //查询条件(单位编号输入)
        if ($filter ['searchParams']["DWBHKEY"] != "") {
            $sql .= " AND DWBH = :DWBH";
            $bind ['DWBH'] = $filter ['searchParams']["DWBHKEY"];
        }
        
        //查询条件(单位编号没输入,只输入单位名称)
        if ($filter ['searchParams']["DWBHKEY"] == "" && $filter ['searchParams']["DWMCHKEY"] != "") {
            $sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
            $bind ['DWMCH'] = $filter ['searchParams']["DWMCHKEY"];
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_CGDXZ",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 添加主键
        $sql .= ",CGDBH";
        
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
     * 得到采购单列表数据明细信息(采购单选择页面)--采购单
     *
     * @param array $filter
     * @return unknown
     */
    public function getCgMingxiData($filter) {
        //检索SQL
        $sql="SELECT SHPBH,SHPMCH,GUIGE,BZHDW,JLGG,BZHSHL,LSSHL,SHULIANG,DANJIA,HSHJ,KOULV,SHUILV,JINE,HSHJE,SHUIE,LSHJ,CHANDI,BEIZHU FROM H01VIEW012307"
        . " WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND RKZHT='1' ";
        
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind ['CGDBH'] = $filter ["cgdbh"];
        
        //排序
        $sql .= " ORDER BY XUHAO";
        
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
     * 得到采购单列表数据明细信息(采购单选择页面)--采购单
     *
     * @param string $cgdbh
     * @return array
     */
    public function getSpecificCgd($cgdbh)
    {
    	//查询语句
    	$sql = 'SELECT CGDBH,DWBH,DWMCH,DHHM,DIZHI,KOULV,FKFSH,SHFZZHSH FROM H01VIEW012306"
    	. " WHERE QYBH = :QYBH AND CGDBH = :CGDBH';
    	
    	 //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind ['CGDBH'] = $cgdbh;
        
        return $this->_db->fetchRow($sql, $bind);
    }
    
    /**
     * 得到商品明细信息
     *
     * @param string $shpbh
     * @return array
     */
    public function getSpecificShpxx($shpbh)
    {
        //查询语句
        $sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,SHUILV,LSHJ,CHANDI,JLGG,BZHDWBH,TYMCH,BZHQYSH,KOULV,YJYSH'
        . ' FROM H01VIEW012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH';
        
         //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind ['SHPBH'] = $shpbh;
        return $this->_db->fetchRow($sql, $bind);
    }
    
    /**
     * 获取待验区信息
     *
     * @param none
     * @return array
     */
    public function getDyqxx()
    {
    	//查询语句
        $sql = 'SELECT CKBH,CKMCH,DYQBH,DYQMCH,DYQKWBH,DYQKWMCH FROM H01VIEW012439 WHERE QYBH = :QYBH AND ZHUANGTAI = \'1\'';
         //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        return $this->_db->fetchAll($sql, $bind);
    }
    
    /**
     * 获取采购单明细信息(cgyrk_01页面使用)
     *
     * @param $cgdbh
     * @return array
     */
    public function getmingxi($cgdbh)
    {
    	//查询语句
        $sql = 'SELECT SHPBH,SHULIANG,DANJIA FROM H01VIEW012307 WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND RKZHT = \'1\' ';
         //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['CGDBH'] = $cgdbh;
        return $this->_db->fetchAll($sql, $bind);
    }
    
    /**
     * 必填项验证
     *
     * @param null
     * @return boolean
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
                    $grid [$this->idx_SHCHRQ] == "" || //生产日期
                    $grid [$this->idx_BZHQZH] == "" || //保质期至
                    $grid [$this->idx_DYQWZH] == "" || //待验区位置
                    $grid [$this->idx_DANJIA] == "0") //单价
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
     * 保存预入库单信息
     *
     * @param string $yrkdbh
     * @return array
     */
    public function saveYrkd($yrkdbh)
    {
    	$yrukudan['QYBH'] = $_SESSION ['auth']->qybh;  
        $yrukudan['YRKDBH'] = $yrkdbh;  //预入库单编号
        $yrukudan['CKDBH'] = $_POST["CGDBH"];   //采购单编号
        $yrukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
        $yrukudan['BMBH'] = $_POST["BMBH"]; //部门编号
        $yrukudan['YWYBH'] = $_POST["YWYBH"];   //业务员编号
        $yrukudan['DWBH'] = $_POST["DWBH"]; //单位编号
        $yrukudan['DIZHI'] = $_POST["DIZHI"];   //地址
        $yrukudan['DHHM'] = $_POST["DHHM"];     //电话
        $yrukudan['SHFZZHSH'] = ($_POST["SHFZZHSH"] == 'on') ? '1' : '0'; //是否增值税
        $yrukudan['KOULV'] = $_POST["KOULV"];   //扣率
        $yrukudan['BEIZHU'] = $_POST["BEIZHU"]; //备注
        $yrukudan['FKFSH'] = $_POST["FKFSH"]; //付款方式
        $yrukudan['RKLX'] = '1';                //入库类型 采购入库
        $yrukudan['ZHUANGTAI'] = $_POST['rkzt'];          //状态
        $yrukudan['SHQDH'] = $_POST["SHQD"]; //送货清单号
        $yrukudan['FPBH'] = $_POST["FPBH"]; //发票编号
        $yrukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
        $yrukudan ['BGZH'] = $_SESSION ['auth']->userId;     //变更者
        $yrukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
        $yrukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者

        $this->_db->insert ( "H01DB012427", $yrukudan );
    }
    
    /**
     * 转换message为数组
     *
     * @param string:$msg
     * @return Array
     */
    public function turnMessage($msg)
    {
    	$msg1 = array();
    	$msg2 = array();
    	$message = array();
    	$msg1 = explode('||', $msg);
    	for($i=0; $i<count($msg1); $i++)
    	{
    		$msg2 = explode(':', $msg1[$i]);
    		$message[$i]['shp'] = $msg2['0'];
    		$message[$i]['msg'] = $msg2['1'];
    	}
    	return $message;
    }
    
    /**
     * 保存message信息
     *
     * @param array:$msg; string:$yrkdbh
     * @return boolean
     */
    public function saveMessage($msg, $yrkdbh)
    {
    	$autoAdd = 1;
    	foreach ($msg as $value)
    	{
    		$message['QYBH'] = $_SESSION ['auth']->qybh;  //区域比啊号
    		$message['YRKDBH'] = $yrkdbh;     //预入库单编号
    		$message['XUHAO'] = $autoAdd;     //序号
    		$message['SHPBH'] = $value['shp'];    //商品编号
    		$message['YRKJGXX'] = $value['msg'];  //报警信息
    		$message ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
            $message ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
            //保存
            $this->_db->insert ("H01DB012476", $message);
            $autoAdd++;
    	}
    }
    
    /**
     * 保存明细信息
     *
     * @param string:$yrkdbh
     * @return boolean
     */
    public function saveMingxi($yrkdbh)
    {
        $autoAdd = 1;
    	foreach($_POST['#grid_mingxi'] as $grid)
    	{
    		$mingxi['QYBH'] = $_SESSION ['auth']->qybh;     //区域编号
    		$mingxi['YRKDBH'] = $yrkdbh;                  //预入库单编号
    		$mingxi['XUHAO'] = $autoAdd;                  //序号
    		$mingxi['SHPBH'] = $grid[$this->idx_SHPBH];            //商品编号
    		$mingxi['HGPSHL'] = $grid[$this->idx_HGPSHL];                      //合格品数量
    		$mingxi['BHGPSHL'] = $grid[$this->idx_BHGPSHL];                      //不合格品数量
    		$mingxi['DANJIA'] = $grid[$this->idx_DANJIA];                      //单价
    		$mingxi['HSHJ'] = $grid[$this->idx_HSHJ];                      //含税价
    		$mingxi['KOULV'] = $grid[$this->idx_KOULV];                      //扣率
    		$mingxi['JINE'] = $grid[$this->idx_JINE];                      //金额
    		$mingxi['HSHJE'] = $grid[$this->idx_HSHJE];                      //含税金额
    		$mingxi['SHUIE'] = $grid[$this->idx_SHUIE];                      //税额
    		$mingxi['BEIZHU'] = $grid[$this->idx_BEIZHU];                      //备注
    		$mingxi['PIHAO'] = $grid[$this->idx_PIHAO];                      //批号
	    	if($grid [$this->idx_SHCHRQ] != "")
	    	{
	            $mingxi['SHCHRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_SHCHRQ] . "','YYYY-MM-DD')" );
	        }
	        if($grid [$this->idx_BZHQZH] != "")
	        {
	            $mingxi['BZHQZH'] = new Zend_Db_Expr ( "TO_DATE('" . $grid [$this->idx_BZHQZH] . "','YYYY-MM-DD')" );
	        }
    		$mingxi['DYQKWBH'] = $grid[$this->idx_DYQWZH];                      //待验区库位编号
    		$mingxi['BHGYY'] = $grid[$this->idx_BHGYY];                      //不合格原因
    		$mingxi['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
	        $mingxi['BGZH'] = $_SESSION ['auth']->userId; //变更者   
	        $mingxi ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
	        $mingxi ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
	        
	        //保存
            $this->_db->insert ("H01DB012428", $mingxi);
    		$autoAdd++;
    	}
    }
    
    /**
     * 更新商品状态
     *
     * @param string $yrkdbh
     */
    public function updateShpZht($yrkdbh)
    {
    	$sql = 'UPDATE H01DB012307 SET RKZHT = \'3\' WHERE QYBH = :QYBH AND CGDBH = :CGDBH AND SHPBH IN '
    	. '(SELECT DISTINCT SHPBH FROM H01DB012428 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH)';
    	
    	$bind['QYBH'] = $_SESSION['auth']->qybh;
    	$bind['CGDBH'] = $_POST["CGDBH"];
    	$bind['YRKDBH'] = $yrkdbh;
    	
    	$this->_db->query($sql, $bind);
    }
}