<?php
/*********************************
 * 模块：   仓储模块(JC)
 * 机能：   预入库采购审核(yrkcgshh)
 * 作成者：ZhangZeliang
 * 作成日：2011/06/03
 * 更新履历：
 *********************************/

class cc_models_yrkdxg extends Common_Model_Base
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
//    private $idx_BHGYY=11;//不合格原因
    private $idx_DANJIA=11;// 单价
    private $idx_HSHJ=12;// 含税售价
    private $idx_KOULV=13;// 扣率
    private $idx_SHUILV=14;// 税率
    private $idx_JINE=15;// 金额
    private $idx_HSHJE=16;// 含税金额
    private $idx_SHUIE=17;// 税额
    private $idx_LSHJ=18;// 零售价
    private $idx_CHANDI=19;// 产地
    private $idx_BEIZHU=20;// 备注
    private $idx_JLGG=21;// 计量规格
    private $idx_BZHDWBH = 22; // 包装单位编号
    private $idx_TYMCH=23;// 通用名称
    private $idx_BZHQYSH=24;//保质期月数
    private $idx_YJYSH=25;//预警月数
    /**
     * 取得列表数据
     *
     * @param array_type $filter
     * @return unknown
     */
    public function getGridData($filter)
    {
        //排序用字段名
        $fields = array ("", 'YRKDBH', 'CKDBH', 'SHQDH', 'KPRQ', 'DWBH', 'DWMCH', 'BMMCH', 'YWYXM', 'ZCHZHXM', 'DYCGYXM' ); //编号，姓名，所属部门,性别

        //检索SQL
        $sql = 'SELECT YRKDBH,CKDBH,SHQDH,TO_CHAR(KPRQ,\'YYYY-MM-DD\') AS KPRQ,DWBH,DWMCH,BMMCH,YWYXM,ZCHZHXM,DYCGYXM FROM H01VIEW012427 '
               . ' WHERE QYBH = :QYBH AND DYCGY = :DYCGY AND ZHUANGTAI = 1';

        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['DYCGY'] = $_SESSION ['auth']->userId;
        
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
        
        //查询条件(送货清单号)
        if ($filter ['searchParams']["SHQDHKEY"] != "" ) {
            $sql .= " AND SHQDH LIKE '%' || :SHQDH || '%'";
            $bind ['SHQDH'] = $filter ['searchParams']["SHQDHKEY"];
        }
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql("CC_YRKCGSHH",$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
        $sql .=",YRKDBH";
        
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
     * 取得列表数据
     *
     * @param string $yrkdbh
     * @return unknown
     */
    public function getmxdata($yrkdbh)
    {
        $sql = 'SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,DYQKWMCH,PIHAO,TO_CHAR(SHCHRQ, \'YYYY-MM-DD\') AS SHCHRQ,TO_CHAR(BZHQZH,\'YYYY-MM-DD\') AS BZHQZH,HGPSHL,BHGPSHL,DANJIA,HSHJ,KOULV,SHUILV,JINE,HSHJE,SHUIE,LSHJ,CHANDI,TYMCH'
               . ' FROM H01UV012409 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
               
        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        
        //调用表格xml生成函数
        return Common_Tool::createXml($this->_db->fetchAll($sql, $bind));
    }
    
    /**
     * 获取指定预入库单信息
     *
     * @param string $yrkdbh
     * @return unknown
     */
    public function getYrkdxx($yrkdbh)
    {
    	$sql = 'SELECT TO_CHAR(KPRQ, \'YYYY-MM-DD\') AS KPRQ,YRKDBH,CKDBH,DWBH,DWMCH,DHHM,DIZHI,KOULV,SHQDH,FPBH,YWYBH,YWYXM,SHFZZHSH,BEIZHU '
    	. 'FROM H01VIEW012427 WHERE YRKDBH = :YRKDBH AND QYBH = :QYBH';
    	//绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        return $this->_db->fetchRow($sql, $bind);
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
        $sql = 'SELECT SHPBH,SHULIANG,DANJIA FROM H01VIEW012307 WHERE QYBH = :QYBH AND CGDBH = :CGDBH';
         //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['CGDBH'] = $cgdbh;
        return $this->_db->fetchAll($sql, $bind);
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
            if($grid[1] != "")
            {
                $isHasMingxi = true;
                if($grid [11] == "0") //单价
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
     * 保存明细信息
     *
     * @param none
     * @return boolean
     */
    public function updateMingxi()
    {
        foreach($_POST['#grid_mingxi'] as $grid)
        {
            $sql = 'UPDATE H01DB012428 SET DANJIA = :DANJIA,HSHJ=:HSHJ,JINE=:JINE,HSHJE=:HSHJE,SHUIE=:SHUIE,BGZH=:BGZH,BGRQ=:BGRQ '
            . 'WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH AND SHPBH = :SHPBH';
            $bind['QYBH'] = $_SESSION ['auth']->qybh;  //区域比啊号
            $bind['YRKDBH'] = $_POST['YRKDBH'];
            $bind['SHPBH'] = $grid[$this->idx_SHPBH];
            $bind['DANJIA'] = $grid[$this->idx_DANJIA];
            $bind['HSHJ'] = $grid[$this->idx_HSHJ];
            $bind['JINE'] = $grid[$this->idx_JINE];
            $bind['HSHJE'] = $grid[$this->idx_HSHJE];
            $bind['SHUIE'] = $grid[$this->idx_SHUIE];
            $bind['BGZH'] = $grid[$this->idx_BGZH];
            $bind['BGRQ'] = $grid[$this->idx_BGRQ];
            
            $this->_db->query($sql, $bind);
        }
    }
        
    /**
     * 保存message信息
     *
     * @param array:$msg
     * @return boolean
     */
    public function saveMessage($msg)
    {
    	$yrkdbh=$_POST['YRKDBH'];
        $autoAdd = 1;
        $sql = 'DELETE FROM H01DB012476 WHERE QYBH = :QYBH AND YRKDBH = :YRKDBH';
        $bind['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['YRKDBH'] = $yrkdbh;
        $this->_db->query($sql, $bind);
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
     * 获取单位信息
     *
     * @param string $dwbh
     * @return unknown
     */
    public function getDanweiInfo($dwbh)
    {
        //检索语句
        $sql = 'SELECT DWMCH FROM H01VIEW012106 WHERE QYBH = :QYBH AND DWBH = :DWBH';
        //绑定查询语句
        $bind['QYBH'] = $_SESSION['auth']->qybh;
        $bind['DWBH'] = $dwbh;
        return $this->_db->fetchRow($sql, $bind);
    }
}

    







