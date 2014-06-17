<?php
/**********************************************************
 * 模块：    销售模块(XS)
 * 机能：    网上销售订单(WSXSDD)
 * 作成者：LiuCong
 * 作成日：2011/10/21
 * 更新履历：
 **********************************************************/

class xs_models_wsxsdd extends Common_Model_Base {
	private $_xsdbh = null;         // 销售单编号
	private $idx_ROWNUM = 0;        // 行号
	private $idx_SHPBH = 1;         // 商品编号
	private $idx_SHPMCH = 2;        // 商品名称
	private $idx_GUIGE = 3;         // 规格
	private $idx_BZHDWM = 4;        // 包装单位
	private $idx_JLGG = 5;          // 计量规格
	private $idx_SHULIANG = 6;      // 数量
	private $idx_DANJIA = 7;        // 单价
	private $idx_HSHJ = 8;          // 含税售价
	private $idx_KOULV = 9;         // 扣率
	private $idx_SHUILV = 10;       // 税率
	private $idx_HSHJE = 11;        // 含税金额
	private $idx_JINE = 12;         // 金额
	private $idx_SHUIE = 13;        // 税额
	private $idx_LSHJ = 14;         // 零售价
	private $idx_SHPTM = 15;        // 商品条码
	private $idx_FLBM = 16;         // 分类编码
	private $idx_PZHWH = 17;        // 批准文号
	private $idx_JIXINGM = 18;      // 剂型
	private $idx_SHCHCHJ = 19;      // 生产厂家
	private $idx_CHANDI = 20;       // 产地
	private $idx_SHFOTC = 21;       // 是否otc
	private $idx_BEIZHU = 22;       // 备注
	private $idx_BZHDWBH = 23;      // 包装单位编号
	
	private $ddbh;                // 订单编号
	private $xuhao=0;             // 明细序号
	
	
	/*
	 * 根据单位编号编号取得单位信息
	 */
	public function getDanweiInfo(){
		//检索单位编号
		$sql = "SELECT DWBH " .
		      " FROM H01DB012107 " .
		      " WHERE QYBH = :QYBH " .  //区域编号
		      " AND YHID = :YHID ".     //用户ID
		      " AND YHLX = '2' " .      //用户类型      1:员工      2:单位
		      " AND YHZHT ='1' ".       //用户状态
			  " AND YXQKSH <= SYSDATE ".
			  " AND SYSDATE <= YXQJSH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['YHID'] = $_SESSION ['auth']->userId;
		$dwbh = $this->_db->fetchOne( $sql, $bind );
		
        //检索单位信息
		$dwsql = "SELECT DWBH,DWMCH,(SZSHMCH || SZSHIMCH || DIZHI) AS DIZHI,".
				 "DHHM,KOULV,FHQBH,FHQMCH,DECODE(XSHXDQ,NULL,0,XSHXDQ) AS XSHXDQ,SHFZZHSH" .
		      	 " FROM H01VIEW012106 " . 
		         " WHERE QYBH = :QYBH " .   //区域编号
		         " AND DWBH = :DWBH " .     //单位编号
		         " AND KHZHT = '1' " ;
				
		//绑定查询条件
		$dwbind ['QYBH'] = $_SESSION ['auth']->qybh;
		$dwbind ['DWBH'] = $dwbh;
		return $this->_db->fetchRow( $dwsql, $dwbind );
	}
	
	
	/*
	 * 客户资质验证（证照，资信，数量）
	 * $data:画面提交数据
	 * $xshdbh:销售单编号
	 */
	public function checkQualification($xshdbh,$data){
		$zige["status"] = "0";//验证返回值
		$xuhao = 0;
		
		//客户证照资质验证
		$zhzhCheck = $this->checkZhZh($data["DWBH"]);
		if ($zhzhCheck["status"]!="0"){
			//许可证过期
			if($zhzhCheck["data"]["XKZHYXQOK"]=="0"){
				$zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "许可证已过期。有效期：" .$zhzhCheck["data"]["XKZHYXQ"];
			}
		    //营业执照过期
		    if($zhzhCheck["data"]["YYZHZHYXQOK"]=="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]= "营业执照已过期。有效期：" .$zhzhCheck["YYZHZHYXQ"];
		    }
		}
		
	    //客户帐期（信贷期）验证
		if($data["FKFSH"]!="0"){
			$xdqCheck =$this->checkXdq($data);
		    if($xdqCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷期已超期。最长信贷期:".$xdqCheck["xdq"]."，超期天数：".$xdqCheck["xdqover"];
		    }
		}
		
		//客户信贷额验证(包括本次订单销售额)
		if($data["FKFSH"]=="1"){
			$xdeCheck =$this->checkXde($xshdbh,$data);
		    if($xdeCheck["status"]!="0"){
			    $zige["status"] = "1"; //需要审批
			    $zige["data"][$xuhao++]="销售信贷额已超过限额。最大信贷额:".$xdeCheck["xde"]."，累计已用信贷额：".$xdeCheck["xde_used"];
		    }
		}
		
		//判断商品出货量是否有异常
		$shuliangCheck = $this->checkShuliang($xshdbh);
		if($shuliangCheck["status"]!="0"){
			$zige["status"] = "1"; //需要审批
			foreach ($shuliangCheck["data"] as $errdata){
				$zige["data"][$xuhao++] = "超过出库限制数量。商品:".$errdata["SHPBH"].$errdata["SHPMCH"].",出库限制数量:".$errdata["CHKXZHSHL"].",开单数量：".$errdata["SHULIANG"];
			}
		}
		
		//有需要审批的项目
		if($zige["status"]== "1"){
			foreach ($zige["data"] as $xuhao=>$value){
				$shp["QYBH"]= $_SESSION ['auth']->qybh;
			    $shp["XSHDBH"]= $xshdbh;
			    $shp["XUHAO"]= $xuhao;
			    $shp["SHPYY"]= $value;
			    $shp["SHPZHT"]="0"; //审批状态 =未审批
			    $this->_db->insert("H01DB012203",$shp);
			}
			
			//修改销售订单中的审核状态为待审核
			$sql = "UPDATE H01DB012201 SET SHHZHT = '3' WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
			$this->_db->query($sql,array("QYBH"=>$_SESSION ['auth']->qybh,"XSHDBH"=>$xshdbh));
		}
				
		return $zige;
    }

	
	/*
	 * 销售订单保存
	 * $xshdbh:销售单编号 $xshddata:销售单数据 
	 */
	public function createXshd($xshdbh,$xshddata) {
		$result["status"] = "0";
		
		$xshd ['QYBH'] = $_SESSION ['auth']->qybh;             //区域编号
		$xshd ['DWBH'] = $_POST ['DWBH'];                      //单位编号
		$xshd ['WSHXSHDH'] = $xshdbh;                          //网上销售单编号
		$xshd ['ZHUANGTAI'] = '1';                             //状态
		$xshd ['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $xshddata ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$xshd ['DIZHI'] = $xshddata ['DIZHI'];                 //地址
		$xshd ['DHHM'] = $xshddata ['DHHM'];                   //电话
		$xshd ['FKFSH'] = $xshddata ['FKFSH'];                 //付款方式
		$xshd ['SHFZZHSH'] = isset($xshddata ['SHFZZHSH'])? $xshddata ['SHFZZHSH'] : '0';   //是否增值税
		$xshd ['SHFPS'] = isset($xshddata ['SHFPS'])? $xshddata ['SHFPS'] : '0';            //是否配送
		$xshd ['SHFYQTPH'] = isset($xshddata ['SHFTPH'])? $xshddata ['SHFTPH'] : '0';       //是否要求同批号
		$xshd ['FHQBH'] = $xshddata ['FHQBH'];                 //发货区编号
		$xshd ['BEIZHU'] = $xshddata ['BEIZHU'];               //备注
		$xshd ['QXBZH'] = "1";                                 //取消标志   1:正常      2:删除 
		$xshd ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );      //作成日期
		$xshd ['ZCHZH'] = $_SESSION ['auth']->userId;          //作成者
		$xshd ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );       //变更日期
		$xshd ['BGZH'] = $_SESSION ['auth']->userId;           //变更者
		//网上销售订单信息表
		$this->_db->insert ( "H01DB012215", $xshd );
		
		$idx = 1;         //明细序号自增
        //循环所有明细行，保存销售订单明细
		foreach ( $xshddata ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")continue;         //忽略空白行

			$xshdmx ['QYBH'] = $_SESSION ['auth']->qybh;         //区域编号
			$xshdmx ['DWBH'] = $_POST ['DWBH'];                  //单位编号
			$xshdmx ['WSHXSHDH'] = $xshdbh;                      //网上销售单编号
			$xshdmx ['XUHAO'] = $idx ++;                         //序号
			$xshdmx ['SHPBH'] = $grid [$this->idx_SHPBH];        //商品编号
			$xshdmx ['SHULIANG'] = $grid [$this->idx_SHULIANG];  //数量
			$xshdmx ['DANJIA'] = $grid [$this->idx_DANJIA];      //单价
			$xshdmx ['HSHJ'] = $grid [$this->idx_HSHJ];          //含税价
			$xshdmx ['KOULV'] = $grid [$this->idx_KOULV];        //扣率
			$xshdmx ['JINE'] = $grid [$this->idx_JINE];          //金额
			$xshdmx ['HSHJE'] = $grid [$this->idx_HSHJE];        //含税金额
			$xshdmx ['SHUIE'] = $grid [$this->idx_SHUIE];        //税额
			$xshdmx ['BEIZHU'] = $grid [$this->idx_BEIZHU];      //备注
			$xshdmx ['BGRQ'] = new Zend_Db_Expr ( 'SYSDATE' );   //变更日期
			$xshdmx ['BGZH'] = $_SESSION ['auth']->userId;       //变更者
			$xshdmx ['ZCHRQ'] = new Zend_Db_Expr ( 'SYSDATE' );  //作成日期
			$xshdmx ['ZCHZH'] = $_SESSION ['auth']->userId;      //作成者
			//网上销售订单明细表
			$this->_db->insert ( "H01DB012216", $xshdmx );	
		}
		
		return $result;
	}

	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck($data) {
		if ($data ["KPRQ"] == "" || //开票日期
            $data ["DWBH"] == "" || //单位编号
            $data ["FKFSH"] == "0" || //付款方式
            $data ["#grid_mingxi"] == "") { //明细表格
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $data ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_SHULIANG] == "" || //数量
                    $grid [$this->idx_SHULIANG] == "0" ){
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
	
	
	/*
	 * 数据合法性逻辑性验证
	 */
	public function logicCheck($data) {
		//单位合法性
		$dwModel = new gt_models_danwei();
		$dwfilter ['dwbh'] = $data ['DWBH'];
		$dwfilter ['flg'] = "0";
		if ($dwModel->getDanweiInfo ( $dwfilter ) == FALSE) {
			return false;
		}
		
		//商品合法性
		$shpModel = new gt_models_shangpin();
		foreach ( $data ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] == "")	continue;
			$shpfilter ['dwbh'] = $data ['DWBH'];
			$shpfilter ['shpbh'] = $grid [$this->idx_SHPBH];
			if ($shpModel->getShangpinInfo ( $shpfilter ) == FALSE) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/*
	 * 判断证照是否过期
	 */
	public function checkZhZh($dwbh){
		$zige["status"] = "0";
		//许可证有效期，营业执照有效期
		$sql = " SELECT TO_CHAR(XKZHYXQ,'YYYY-MM-DD'),(CASE WHEN XKZHYXQ < SYSDATE THEN 0 WHEN XKZHYXQ IS NULL THEN 0  ELSE 1 END) AS XKZHYXQOK,".
               " TO_CHAR(YYZHZHYXQ,'YYYY-MM-DD'),(CASE WHEN YYZHZHYXQ < SYSDATE THEN 0 WHEN YYZHZHYXQ IS NULL THEN 0  ELSE 1 END) AS YYZHZHYXQOK".
               " FROM H01DB012106  WHERE QYBH =:QYBH AND DWBH = :DWBH";
		//绑定查询变量
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $dwbh;
		$rec = $this->_db->fetchRow($sql,$bind);
		
		//许可证过期 营业执照过期
		if($rec["XKZHYXQOK"]=="0" || $rec["YYZHZHYXQOK"]=="0"){
			$zige["status"] = "1"; //需要审批
			$zige["data"]=$rec;
		}
		
		return $zige;
	}

	
	/*
	 * 判断信贷期是否超期
	 */
	public function checkXdq($data) {
		$xdqCheck["status"] = "0";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $data["DWBH"];
		
		//销售信贷期取得
		$sql = "SELECT DECODE(XSHXDQ,NULL,0,XSHXDQ) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		$xshxdq = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账销售单的最长天数
		$sql = " SELECT DECODE(FLOOR(SYSDATE - min(A.KPRQ)),NULL,0,FLOOR(SYSDATE - min(A.KPRQ)))FROM H01DB012201 A ".
		       " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH " .
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
   	    $days = $this->_db->fetchOne ( $sql, $bind );
   				
		//帐期已经超期
		if ($days > $xshxdq) {
			$xdqCheck["status"] = "1";
			$xdqCheck["xdq"] = $days; //信贷期天数
			$xdqCheck["xdqover"] = $days - $xshxdq; //超期天数
		}
		
		return $xdqCheck;
	}
	
	
    /*
	 * 判断信贷额是否超过额度
	 */
	public function checkXde($xshdbh,$data) {
		$xdeCheck["status"] = "0";
			
		//销售信贷额取得
		$sql = "SELECT DECODE(XSHXDE,NULL,0,XSHXDE) FROM H01DB012106 WHERE QYBH = :QYBH AND DWBH = :DWBH";
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $data["DWBH"];
		
		$xde = $this->_db->fetchOne ( $sql, $bind );

		//账期销售单中尚未结账的合计金额
		$sql = " SELECT DECODE(SUM(B.YSHJE),NULL,0,SUM(B.YSHJE)) FROM H01DB012201 A ".
               " JOIN H01DB012208 B ON A.QYBH = B.QYBH AND A.XSHDBH = B.XSHDBH ".
		       " WHERE A.QYBH = :QYBH AND A.DWBH = :DWBH" . 
		       " AND A.QXBZH ='1' AND A.FKFSH = '1' AND B.JSZHT <> '1' ";
		//本次付款为非帐期，则排除本次金额
		if($data["FKFSH"]!="1"){
		    $sql .= " AND A.XSHDBH <> :XSHDBH";
		    $bind ['XSHDBH'] = $xshdbh;
		
		}
   	    $yshje = $this->_db->fetchOne ( $sql, $bind ); //应收金额
    	   
   	    //判断合计金额是否超过信贷额
   	    if($yshje > (float)$xde){
   	    	$xdeCheck["status"] = "1"; //超过信贷额
   	    	$xdeCheck["xde"] = (float)$xde; //最大信贷额
   	    	$xdeCheck["xde_used"] = $yshje;//已用信贷额
   	    }
   	    
		return $xdeCheck;
	}
	
	
	/*
	 * 检验商品出货量是否有异常
	 */
	public function checkShuliang($xshdbh){
		$result["status"] = "0";
		//取出本单所有超过出库限制数量的商品
		$sql = "SELECT A.SHPBH,B.SHPMCH,SUM(A.SHULIANG) AS SHULIANG,B.CHKXZHSHL FROM H01DB012202 A
                JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH
                WHERE A.QYBH = :QYBH AND A.XSHDBH = :XSHDBH
                GROUP BY A.SHPBH,B.SHPMCH,B.CHKXZHSHL
                HAVING SUM(A.SHULIANG) > B.CHKXZHSHL";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['XSHDBH'] = $xshdbh;
		
		$recs = $this->_db->fetchAll($sql,$bind);
		if(count($recs)>0){
			$result["status"] = "1";
			$result["data"] = $recs;
		}
		
		return $result;
	}
	
	
	/*
	 * 商品价格信息
	 */
	public function getJiageInfo($filter) {
		$result ['type'] = 'none'; //无特价无一品多价
	
		$sql = "SELECT HSHSHJTJ,XSHTJ" . 
		       " FROM H01DB012105 " .
		       " WHERE QYBH = :QYBH " . 
		       " AND SHPBH = :SHPBH " . 
		       " AND DWBH = :DWBH" . 
		       " AND QSHZHXRQ <= SYSDATE" . //起始执行日期
               " AND ZHZHZHXRQ >=SYSDATE";  //终止执行日期

		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		$bind ['SHPBH'] = $filter ['shpbh'];
		
		$recs = $this->_db->fetchRow ( $sql, $bind ); //特价信息

		//特价不存在的时候，看一品多价是否存在
		if ($recs == FALSE) {
			//查看一品多价是否存在
			$sql = "SELECT COUNT(*) FROM H01DB012104 A" .
			       " INNER JOIN H01DB012106 B ON A.QYBH = B.QYBH AND A.KHDJ = B.KHDJ" .
			       " WHERE A.QYBH = :QYBH " .
			       " AND A.SHPBH = :SHPBH " .
			       " AND B.DWBH = :DWBH";
			
			//存在一品多价
			if ($this->_db->fetchOne ( $sql, $bind ) != "0") {
				$result ['type'] = '1'; //一品多价
			}
		} else {
			$result ['type'] = '0';   //特价存在
			$result ['data'] = $recs; //特价信息
		}
		
		return $result;
	}

}