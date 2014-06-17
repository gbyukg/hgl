<?php
/*********************************
 * 模块：    仓储模块(CC)
 * 机能：    销售退货入库(XSTHRK)
 * 作成者：DLTT-苏迅
 * 作成日：2010/12/14
 * 更新履历：
**********************************/
class cc_models_xsthrk extends Common_Model_Base {
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
	 * 得到退货单列表数据(退货单选择页面)--退货单
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter) {
		//排序用字段名
		$fields = array ("", "THDBH", "KPRQ", "DWBH", "NLSSORT(DWMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "XSHDBH", "XSHRQ", "NLSSORT(BMMCH,'NLS_SORT=SCHINESE_PINYIN_M')", "NLSSORT(YWYXM,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT THDBH,TO_CHAR(KPRQ,'YYYY-MM-DD'),DWBH,DWMCH,XSHDBH,TO_CHAR(XSHRQ,'YYYY-MM-DD'),BMMCH,YWYXM" 
		     . " FROM H01VIEW012206 WHERE QYBH = :QYBH AND THDZHT = '0' AND THLX = '1'";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查询条件(开始日期<=开票日期<=终止日期)
		if ($filter ['searchParams']["KSRQKEY"] != "" || $filter ['searchParams']["ZZRQKEY"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(KPRQ,'YYYY-MM-DD') AND TO_CHAR(KPRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ['searchParams']["KSRQKEY"] == ""?"1900-01-01":$filter ['searchParams']["KSRQKEY"];
			$bind ['ZZRQ'] = $filter ['searchParams']["ZZRQKEY"] == ""?"9999-12-31":$filter ['searchParams']["ZZRQKEY"];
		}
		
		//查询条件(单位编号输入)
		if ($filter ['searchParams']["DWBHKEY"] != "") {
			$sql .= " AND DWBH = :DWBH";
			$bind ['DWBH'] = $filter ['searchParams']["DWBHKEY"];
		}
		
		//查询条件(单位编号没输入,只输入单位名称)
		if($filter ['searchParams']["DWBHKEY"] == "" && $filter ['searchParams']["DWMCHKEY"] != "") {
			$sql .= " AND DWMCH LIKE '%' || :DWMCH || '%'";
			$bind ['DWMCH'] = $filter ['searchParams']["DWMCHKEY"];
		}
		
		//自动生成精确查询用Sql
		$sql .= Common_Tool::createFilterSql("CC_XSTHRK_02",$filter['filterParams'],$bind);
		
		//排序
		$sql .= " ORDER BY " . $fields [$filter ["orderby"]] . " " . $filter ["direction"];
		//防止重复数据引发翻页排序异常，orderby 添加主键
		$sql .= ",THDBH";
		
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
	 * 得到退货单明细列表数据(退货单选择页面)--明细
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridMingxiData($filter) {
		//排序用字段名
		//$fields = array ("", "A.SHPBH", "NLSSORT(B.SHPMCH,'NLS_SORT=SCHINESE_PINYIN_M')");
		
		//检索SQL
		$sql = "SELECT SHPBH,SHPMCH,GUIGE,PIHAO,BZHDWMCH,TO_CHAR(SHCHRQ,'YYYY-MM-DD'),TO_CHAR(BZHQZH,'YYYY-MM-DD'),"
			 . "JLGG,HGL_DEC(BZHSHL),HGL_DEC(LSSHL),HGL_DEC(SHULIANG),HGL_DEC(DANJIA),HGL_DEC(HSHJ),HGL_DEC(KOULV),"
			 . "HGL_DEC(SHUILV),HGL_DEC(JINE),HGL_DEC(HSHJE),HGL_DEC(SHUIE),HGL_DEC(LSHJ),CHANDI,BEIZHU,BZHDWBH,ZHDKQLX,"
			 . "ZHDKQLXMCH,TYMCH"
		     . " FROM H01UV012406" 
		     . " WHERE QYBH = :QYBH AND THDBH = :THDBH";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $filter ["thdbh"];
				
		//排序
		$sql .= " ORDER BY THDBH,XUHAO";
		
		//翻页表格用SQL生成(总行数与单页记录)
		$pagedSql = Common_Tool::getPageSql ( $sql, $filter );
		
		//总行数
		$totalCount = $this->_db->fetchOne ( $pagedSql ["sql_count"], $bind );
		
		//当前页数据
		$recs = $this->_db->fetchAll ( $pagedSql ["sql_page"], $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml ( $recs, true, $totalCount, $filter ["posStart"] );		
	}
	
	/*
	 * 根据退货单编号取得退货单信息
	 * 
	 * @param string $thdbh:退货单编号
	 * @return array
	 */
	public function getSingleThdInfo($thdbh) {
		
		$sql = "SELECT THDBH,SHFZZHSH,DWBH,DWMCH,DIZHI,DHHM,HGL_DEC(KOULV) AS KOULV,BEIZHU,XSHDBH" 
		     . " FROM H01VIEW012206 WHERE QYBH = :QYBH AND THDBH = :THDBH";
		     
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $thdbh;
		
		return $this->_db->fetchRow ( $sql, $bind );
		     
	}	
	
	/**
	 * 得到退货单明细列表数据
	 *
	 * @param array $filter
	 * @return array 
	 */
	public function getxsthdmingxi($thdbh) {	
		//检索SQL
		$sql = "SELECT SHPBH,"
					."SHPMCH,"
					."GUIGE,"
					."PIHAO,"
					."BZHDWMCH,"
					."TO_CHAR(SHCHRQ,'YYYY-MM-DD') AS SHCHRQ,"
					."TO_CHAR(BZHQZH,'YYYY-MM-DD') AS BZHQZH,"
					."HGL_DEC(BZHSHL) AS BZHSHL,"
					."HGL_DEC(LSSHL) AS LSSHL,"
					."HGL_DEC(SHULIANG) AS SHULIANG,"
					."HGL_DEC(DANJIA) AS DANJIA,"
					."HGL_DEC(HSHJ) AS HSHJ,"
					."HGL_DEC(KOULV) AS KOULV,"
					."HGL_DEC(SHUILV) AS SHUILV,"
					."HGL_DEC(JINE) AS JINE,"
					."HGL_DEC(HSHJE) AS HSHJE,"
					."HGL_DEC(SHUIE) AS SHUIE,"
					."HGL_DEC(LSHJ) AS LSHJ,"
					."CHANDI,"
					."BEIZHU,"
					."BZHDWBH,"
					."JLGG,"
					."ZHDKQLX,"
					."TYMCH,"
					."ZHDKQLXMCH" 
		     . " FROM H01UV012406" 
		     . " WHERE QYBH = :QYBH AND THDBH = :THDBH ORDER BY XUHAO";
		     
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $thdbh;
		
		return $this->_db->fetchAll( $sql, $bind );
		
	}
	
	/**
	 * 取得仓库/库区/库位状态信息
	 * @param 	string 	$ckbh	仓库编号
	 * 			string 	$ckbh	库区编号
	 * 			string	$kwbh	库位编号
	 * 
	 * @return 	array 
	 */
	public function getkuweizht($ckbh,$kqbh,$kwbh) {
		$sql = "SELECT C.KWBH,"
			."C.SHFSHKW,"
			."C.KWZHT,"
			."A.CKZHT,"
			."B.KQZHT,"
			."B.KQLX"
			." FROM H01DB012403 C LEFT JOIN H01DB012401 A ON C.QYBH = A.QYBH AND C.CKBH = A.CKBH LEFT JOIN H01DB012402 B ON C.QYBH = B.QYBH AND C.CKBH = B.CKBH AND C.KQBH =B.KQBH"
			." WHERE C.QYBH = :QYBH AND C.CKBH = :CKBH AND C.KQBH = :KQBH AND C.KWBH = :KWBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $ckbh;
		$bind ['KQBH'] = $kqbh;
		$bind ['KWBH'] = $kwbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/**
	 * 取得退货状态
	 * @param 	string 	$thdbh	退货单编号
	 * 
	 * @return 	array 
	 */
	public function getthzht($thdbh) {
		$sql = "SELECT THDZHT FROM H01DB012206 WHERE QYBH = :QYBH AND THDBH = :THDBH";
			
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['THDBH'] = $thdbh;
		
		return $this->_db->fetchRow( $sql, $bind );	
	}
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		if ($_POST ["KPRQ"] == "" || //开票日期
            $_POST ["BMBH"] == "" || //部门编号
            $_POST ["YWYBH"] == "" || //业务员编号   
            $_POST ["#grid_mingxi"] == "") { //明细表格
            	
			return false;
		}
		
		$isHasMingxi = false; //是否存在至少一条明细

		foreach ( $_POST ["#grid_mingxi"] as $grid ) {
			if ($grid [$this->idx_SHPBH] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_PIHAO] == "" || //批号
					$grid [$this->idx_SHULIANG] == "" || //数量
					$grid [$this->idx_SHULIANG] == "0") { 
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
	 * 取得商品大包装长宽高信息
	 * @return 	array 
	 */
	public function getshpchkg() {
		$shpchkgxx['status'] = '0';
		//自动分配货位时
		if($_POST ["AUTOHUOWEI"] == "1"){
			foreach ( $_POST ["#grid_mingxi"] as $grid ) {
				if((int)$grid [$this->idx_BZHSHL] > 0){
					$sql = "SELECT SHPBH,NVL(DBZHCH,0) AS DBZHCH,NVL(DBZHK,0) AS DBZHK,NVL(DBZHG,0) AS DBZHG"
						  ." FROM H01DB012101"
						  ." WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
					
					//绑定查询条件
					$bind ['QYBH'] = $_SESSION ['auth']->qybh;
					$bind ['SHPBH'] = $grid [$this->idx_SHPBH];
					
					$singlechkg = $this->_db->fetchRow( $sql, $bind );
					
					if($singlechkg == false){
						$shpchkgxx['status'] = '4';//商品无效
					}
					
					if($singlechkg["DBZHCH"] == "0" || $singlechkg["DBZHK"] == "0" || $singlechkg["DBZHG"] == "0" ){
						$shpchkgxx['status'] = '3';	//包装信息错误	
						$singlechkg['rIdx'] = ( int ) $grid [$this->idx_ROWNUM];
						$shpchkgxx['bzhdata'][] = $singlechkg;		
					}
				}
	
			}	
		}
	
		return $shpchkgxx;	
	}
	
	/**
	 * 对于指定库位的商品,需要检查该指定库位是否含有其他批号该商品
	 * @return 	array 
	 */
	public function checkpihao(){
		$otherpihao['status'] = '0';
		//手动分配货位时check
		if($_POST ["AUTOHUOWEI"] == "0"){
			foreach ( $_POST ["#grid_mingxi"] as $grid ) {
				
				$sql="SELECT PIHAO FROM " 
				. "(SELECT DISTINCT PIHAO FROM H01DB012404 " 
				. "WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH AND SHPBH = :SHPBH AND SHULIANG <> 0) " 
				. "WHERE PIHAO <> :PIHAO";
				//绑定查询条件
				$bind["QYBH"] = $_SESSION["auth"]->qybh;
				$bind["CKBH"] = $grid [$this->idx_CKBH];
				$bind["KQBH"] = $grid [$this->idx_KQBH];
				$bind["KWBH"] = $grid [$this->idx_KWBH];
				$bind["SHPBH"] = $grid [$this->idx_SHPBH];
				$bind["PIHAO"] = $grid [$this->idx_PIHAO];
				
				$otherpihaorow = $this->_db->fetchAll($sql, $bind);
				
				$pihao = "";
				if($otherpihaorow == false){
					continue;
				}else{
					$otherpihao['status'] = '6';
					foreach ($otherpihaorow as $singlerow){
						 $pihao .= $singlerow['PIHAO'].",";
					}
					//$pihao=substr($pihao,0,strlen($pihao)-1);
					$pihao = substr($pihao,0,-1);
					$singlerow['rIdx'] = ( int ) $grid [$this->idx_ROWNUM];
					$singlerow['PIHAO'] = $pihao;
					$singlerow['SHPBH'] = $grid [$this->idx_SHPBH];
					$otherpihao['phdata'][] = $singlerow;
				}
			}

		}
		
		return $otherpihao;
		
	}
	
	/**
	 * 入库单信息保存

	 * @param 	string 	$rkdbh	新生成的退货入库单编号
	 * @return 	bool
	 */
	public function saveRukudan($rkdbh) {
		
		$rukudan['QYBH'] = $_SESSION ['auth']->qybh;	
		$rukudan['RKDBH'] = $rkdbh;
		$rukudan['CKDBH'] = $_POST["THDBH"];
		$rukudan['KPRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $_POST ['KPRQ'] . "','YYYY-MM-DD')" ); //开票日期
		$rukudan['BMBH'] = $_POST["BMBH"];
		$rukudan['YWYBH'] = $_POST["YWYBH"];
		$rukudan['DWBH'] = $_POST["DWBH"];
		//$rukudan['DWMCH'] = $_POST["DWMCH"];
		$rukudan['DIZHI'] = $_POST["DIZHI"];
		$rukudan['DHHM'] = $_POST["DHHM"];
		$rukudan['SHFZZHSH'] = ($_POST ['SHFZZHSH'] == null) ? '0' : '1';//是否增值税
		$rukudan['KOULV'] = $_POST["KOULV"];
		$rukudan['BEIZHU'] = $_POST["BEIZHU"];
		$rukudan['RKLX'] = '2';
		$rukudan ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' ); //变更日期
		$rukudan ['BGZH'] = $_SESSION ['auth']->userId; //变更者
		$rukudan ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$rukudan ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		
		$this->_db->insert ( "H01DB012406", $rukudan );
		
	}
	
	/*
	 * 循环读取明细信息,退货入库更新操作
	 * 
	 * 1.自动分配货位时，画面grid明细数组需要重新处理，加入货位信息(可能一条grid明细对应多个货位记录),生成新的入库明细数组$afterassign
	 * 2.查找明细商品对应的原始入库单--从移动履历中找此次销售该商品对应的多个入库单号
	 * 3.针对每个入库单,先减掉对应的其他退货数量,再更新在库
	 * 
	 * @param 	string 	$rkdbh	新生成的退货入库单编号
	 * @return 	array	$result
	 */
	public function executeMingxi($rkdbh) {
		$result ['status'] = '0';		
		$idx_rukumingxi = 1; //入库单明细信息序号	
		$idx_lvli = 1; //在库移动履历
		
		$afterassign = array();
		$i=0;
		//若自动分配库位,调用自动分配货位共通方法,返回自动分配库位信息,重新生成入库明细数组$afterassign
		if($_POST ["AUTOHUOWEI"] == "1"){
			foreach ( $_POST ["#grid_mingxi"] as $beforeassign ){
				
				$shpbh = $beforeassign[$this->idx_SHPBH];	//商品编号
				$pihao = $beforeassign[$this->idx_PIHAO];	//批号
				$bzhshl = $beforeassign[$this->idx_BZHSHL];	//包装数量	
				$lsshl = $beforeassign[$this->idx_LSSHL];	//零散数量
				//自动分配货位共通函数--商品编号，批号，包装数量，零散数量
				$auto = new gt_models_tool();
				$kuwei = $auto->autoAssignKuwei($shpbh,$pihao,$bzhshl,$lsshl);
				//返回数组--仓库编号，库区编号，库位编号，数量，是否散货库位
				foreach ($kuwei as $singlekuwei){
					$afterassign[] = $beforeassign;
					$afterassign[$i][$this->idx_CKBH] = $singlekuwei['CKBH'];
					$afterassign[$i][$this->idx_KQBH] = $singlekuwei['KQBH'];
					$afterassign[$i][$this->idx_KWBH] = $singlekuwei['KWBH'];
					if($singlekuwei['SHFSHKW'] == "1"){	//散货货位时
						$afterassign[$i][$this->idx_SHULIANG] = $singlekuwei['SHULIANG'];	//数量 = 共通库位数量
						$afterassign[$i][$this->idx_LSSHL] = $singlekuwei['SHULIANG'];		//零散数量 = 共通库位数量
						$afterassign[$i][$this->idx_BZHSHL] = "0";							//包装数量
								
					}else{ //包装货位时
						$afterassign[$i][$this->idx_SHULIANG] = ( int )$beforeassign[$this->idx_JLGG] * ( int )$singlekuwei['SHULIANG'];	//数量=库位数量*计量规格
						$afterassign[$i][$this->idx_LSSHL] = "0";							//零散数量
						$afterassign[$i][$this->idx_BZHSHL] = $singlekuwei['SHULIANG'];		//包装数量 = 共通库位数量
					}
					//金额,含税金额,税额重新计算				
					$afterassign[$i][$this->idx_JINE] = ( float )$afterassign[$i][$this->idx_SHULIANG] * ( float )$beforeassign[$this->idx_DANJIA] * ( float )$beforeassign[$this->idx_KOULV] / 100;
					$afterassign[$i][$this->idx_HSHJE] = ( float )$afterassign[$i][$this->idx_SHULIANG] * ( float )$beforeassign[$this->idx_HSHJ] * ( float )$beforeassign[$this->idx_KOULV] / 100;
					$afterassign[$i][$this->idx_SHUIE] = $afterassign[$i][$this->idx_HSHJE] - $afterassign[$i][$this->idx_JINE];
										
					$i++;
				}
			}
		//若用户手动指定库位,画面明细直接作为入库明细数组
		}else{
			$afterassign = $_POST ["#grid_mingxi"];
		}
		
		//循环所有明细行
		foreach ( $afterassign as $row ) {
			
			//生成入库单明细信息
			$this->InsertRukumingxi($row,$rkdbh,$idx_rukumingxi);
			
			//入库单明细信息序号自增	
			$idx_rukumingxi ++;
			
			//从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量
			$recs = $this->getXiaoshouxinxi($row);
			
			//变量:剩余退货数量 = 画面项目：数量
			$shuliang_shengyu = ( int ) $row [$this->idx_SHULIANG];
			
			//循环上面取到的销售信息
			foreach ( $recs as $rec ) {
				$rkdbh_xiaoshou = $rec['RKDBH'];		//变量：入库单号

				$shuliang_xiaoshou = $rec['SHULIANG'];	//变量：销售数量

				$xshdbh = $rec['XSHDBH'];				//变量：销售单编号
					
				//取处理中入库单的该商品的其它退货单对应的退货数量
				$shuliang_qitatuihuo = $this->getQitatuihuo($row,$xshdbh,$rkdbh_xiaoshou);
				
				//没有其他退货
				if($shuliang_qitatuihuo == false){
					$shuliang_qitatuihuo = 0;
				}
				
				//在库更新数量
				$shuliang_update = 0; 
				
				//变量：销售数量  = 变量：其它退货数量 时,循环下一条rec
				if($shuliang_xiaoshou == $shuliang_qitatuihuo) continue;
				
				//判断 变量：剩余退货数量 <= 变量：销售数量 - 变量：其它退货数量 时
				if($shuliang_shengyu <= $shuliang_xiaoshou - $shuliang_qitatuihuo) {
					$shuliang_update = $shuliang_shengyu;
					$shuliang_shengyu = 0;
				}
				//判断 变量：剩余退货数量 > 变量：销售数量 - 变量：其它退货数量 时
				else{
					$shuliang_update = $shuliang_xiaoshou - $shuliang_qitatuihuo;
					$shuliang_shengyu = $shuliang_shengyu - $shuliang_xiaoshou + $shuliang_qitatuihuo;
				}
				
				//检索在库商品信息是否存在
				$kucuncunzai = $this->checkCunzai($row,$rkdbh_xiaoshou);
				
				//存在,更新该在库商品信息
				if($kucuncunzai != false){
					$this->updateZaiku($row,$shuliang_update,$rkdbh_xiaoshou);
				}
				//不存在时，新做成在库商品信息
				else{
					$this->insertZaiku($row,$shuliang_update,$rkdbh_xiaoshou);
				}								
				//商品移动履历的新生成				
				$this->insertLvli($row,$rkdbh,$shuliang_update,$rkdbh_xiaoshou,$idx_lvli);
				$idx_lvli++;
			
				if($shuliang_shengyu == 0) break;
								
			}
			//if数量还有剩余->退货数量超过了客户采购数量,退出整个明细的循环,返回页面
			if($shuliang_shengyu > 0){
				$result ['status'] = '3'; //退货数量超过了客户采购数量
				$result ['data']['rIdx'] = ( int ) $row [$this->idx_ROWNUM]; //定位明细行index
				//$result ['data']['shpbh'] = $row [$this->idx_SHPBH];
				//$result ['data']['pihao'] = $row [$this->idx_PIHAO];
				break;
			}
		}
		
		return $result;
		
	}
	
	/*
	 * 生成入库单明细信息
	 * 
	 * @param 	array 	$row:明细
	 * 			string 	$rkdbh:新生成的退货入库单编号
	 * 			int 	$idx_rukumingxi:入库单明细信息序号	
	 * @return array 
	 */
	public function InsertRukumingxi($row,$rkdbh,$idx_rukumingxi) {
		
		$data['QYBH'] = $_SESSION ['auth']->qybh;
		$data['RKDBH'] = $rkdbh;
		$data['XUHAO'] = $idx_rukumingxi;
		$data['SHPBH'] = $row [$this->idx_SHPBH];
		//$data['GUIGE'] = $row [$this->idx_GUIGE];
		//$data['BZHDWBH'] = $row [$this->idx_BZHDWBH];
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
	 * 从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量
	 * --(由于销售商品时可能销售了多个入库单号的该商品，所以入库单号可能为多个，库位可能不同所以数量得求和)
	 * @param 	array 	$row:明细	
	 * @return 	array ：	1)入库单号
	 * 					2)销售单号
	 * 					3)sum(数量)
	 */
	public function getXiaoshouxinxi ($row) {
		
		//从履历中找此次销售该商品对应的多个入库单号，及每个入库单号对应的总数量	
		$sql_xiaoshou = "SELECT C.RKDBH,"					//入库单号(多条)
						."A.XSHDBH,"						//销售单号(1条)
						."ABS(SUM(C.SHULIANG)) AS SHULIANG"	//SUM出库数量(负值取绝对值)
						." FROM H01DB012405 C,H01DB012206 A,H01DB012207 B,H01DB012101 D"
						." WHERE A.QYBH = :QYBH AND B.QYBH = :QYBH AND C.QYBH = :QYBH"
						." AND A.THDBH = :THDBH AND B.THDBH = A.THDBH AND B.SHPBH = :SHPBH"
						." AND B.PIHAO = :PIHAO AND C.YDDH = A.XSHDBH AND C.BZHDWBH = D.BZHDWBH"
						." AND C.BZHDWBH = :BZHDWBH AND C.SHPBH = B.SHPBH AND C.PIHAO = B.PIHAO AND C.QYBH = D.QYBH AND C.SHPBH = D.SHPBH AND B.SHCHRQ = C.SHCHRQ AND TO_CHAR(B.SHCHRQ,'YYYY-MM-DD') = :SHCHRQ"
						." GROUP BY A.XSHDBH,C.RKDBH ORDER BY C.RKDBH DESC";
						
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['THDBH'] = $_POST["THDBH"];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
	
		//移动履历中该销售退货单对应销售单的入库信息(入库单号及出库总数量)
		return $this->_db->fetchAll( $sql_xiaoshou, $bind );
	}
	
	/*
	 * 取处理中入库单的该商品的其它退货单对应的退货数量
	 * 
	 * @param 	array 	$row:明细
	 * 			string  $xshdbh:销售单编号
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	int		其他退货数量
	 *      or  bool	false(没有其他退货)
	 */
	public function getQitatuihuo ($row,$xshdbh,$rkdbh_xiaoshou) {
		
		//抽取处理中入库单的该商品的其它退货单，保存其数量
		$sql_qitatuihuo = "SELECT SUM(C.SHULIANG)"
						. " FROM H01DB012405 C,H01DB012206 A,H01DB012207 B,H01DB012406 D,H01DB012101 E"
						. " WHERE A.QYBH = :QYBH AND B.QYBH = :QYBH AND C.QYBH = :QYBH AND D.QYBH = :QYBH"
						. " AND A.XSHDBH = :XSHDBH AND B.THDBH = A.THDBH AND B.SHPBH = :SHPBH"
						. " AND B.PIHAO = :PIHAO AND C.YDDH = D.RKDBH AND D.CKDBH = A.THDBH"
						. " AND C.RKDBH = :RKDBH AND C.BZHDWBH = E.BZHDWBH AND C.BZHDWBH = :BZHDWBH"
						. " AND C.SHPBH = B.SHPBH AND C.PIHAO = B.PIHAO AND C.QYBH = E.QYBH AND C.SHPBH = E.SHPBH AND B.SHCHRQ = C.SHCHRQ AND TO_CHAR(B.SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
						
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['XSHDBH'] = $xshdbh;
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['RKDBH'] = $rkdbh_xiaoshou;
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
		
		return $this->_db->fetchOne ( $sql_qitatuihuo, $bind );
		
	}
	
	/*
	 * 检索在库商品信息是否存在

	 * 
	 * @param 	array 	$row:明细
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	int		
	 */
	public function checkCunzai($row,$rkdbh_xiaoshou) {
		
		$sql_kucun = "SELECT RKDBH"
					." FROM H01DB012404"
					." WHERE QYBH = :QYBH"
					." AND CKBH = :CKBH"
					." AND KQBH = :KQBH"
					." AND KWBH = :KWBH"
					." AND SHPBH = :SHPBH"
					." AND PIHAO = :PIHAO"
					." AND BZHDWBH = :BZHDWBH"
					." AND ZKZHT = '0'"
					." AND RKDBH = :RKDBH"
					." AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ FOR UPDATE WAIT 10";

		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['CKBH'] = $row [$this->idx_CKBH];
		$bind['KQBH'] = $row [$this->idx_KQBH];
		$bind['KWBH'] = $row [$this->idx_KWBH];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['RKDBH'] = $rkdbh_xiaoshou;
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
				
		return $this->_db->fetchOne ( $sql_kucun, $bind );
		
	}
		
	/*
	 * 更新在库商品信息
	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_update:更新数量
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	bool	
	 */
	public function updateZaiku($row,$shuliang_update,$rkdbh_xiaoshou) {
		
		$sql_update = "UPDATE H01DB012404"
					. " SET SHULIANG = SHULIANG + :SHULIANG_UPDATE,"
					. "ZZHCHKRQ = TO_DATE(:ZZHCHKRQ,'YYYY-MM-DD hh24:mi:ss')"
					. " WHERE QYBH = :QYBH AND CKBH = :CKBH AND KQBH = :KQBH AND KWBH = :KWBH"
					. " AND SHPBH = :SHPBH AND PIHAO = :PIHAO AND BZHDWBH = :BZHDWBH AND ZKZHT = '0'"
					. " AND RKDBH = :RKDBH AND TO_CHAR(SHCHRQ,'YYYY-MM-DD') = :SHCHRQ";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['SHULIANG_UPDATE'] = $shuliang_update;
		$bind['ZZHCHKRQ'] = '9999/12/31 23:59:59';
		$bind['CKBH'] = $row [$this->idx_CKBH];
		$bind['KQBH'] = $row [$this->idx_KQBH];
		$bind['KWBH'] = $row [$this->idx_KWBH];
		$bind['SHPBH'] = $row [$this->idx_SHPBH];
		$bind['PIHAO'] = $row [$this->idx_PIHAO];
		$bind['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$bind['RKDBH'] = $rkdbh_xiaoshou;
		$bind['SHCHRQ'] = $row [$this->idx_SHCHRQ];
			
		$this->_db->query ( $sql_update,$bind );
	}
	
	/*
	 * 新做成在库商品信息

	 * 
	 * @param 	array 	$row:明细
	 * 			int		$shuliang_update:更新数量
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 
	 * @return 	bool	
	 */
	public function insertZaiku($row,$shuliang_update,$rkdbh_xiaoshou) {
		
		$zaiku['QYBH'] = $_SESSION ['auth']->qybh;
		$zaiku['CKBH'] = $row [$this->idx_CKBH];
		$zaiku['KQBH'] = $row [$this->idx_KQBH];
		$zaiku['KWBH'] = $row [$this->idx_KWBH];
		$zaiku['SHPBH'] = $row [$this->idx_SHPBH];
		$zaiku['PIHAO'] = $row [$this->idx_PIHAO];
		$zaiku['RKDBH'] = $rkdbh_xiaoshou;
		$zaiku['ZKZHT'] = '0';
		$zaiku['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$zaiku['ZZHCHKRQ'] = new Zend_Db_Expr("TO_DATE('9999/12/31 23:59:59','YYYY-MM-DD hh24:mi:ss')");
		$zaiku['SHULIANG'] = $shuliang_update;
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
	 * @param 	array 	$row:明细
	 * 			string	$rkdbh:新生成的退货入库单编号
	 * 			int		$shuliang_update:更新数量
	 * 			string  $rkdbh_xiaoshou:入库单号
	 * 			int		$idx_lvli:移动履历序号
	 * 
	 * @return 	bool	
	 */
	public function insertLvli($row,$rkdbh,$shuliang_update,$rkdbh_xiaoshou,$idx_lvli) {
		
		$lvli['QYBH'] = $_SESSION ['auth']->qybh;
		$lvli['CKBH'] = $row [$this->idx_CKBH];
		$lvli['KQBH'] = $row [$this->idx_KQBH];
		$lvli['KWBH'] = $row [$this->idx_KWBH];
		$lvli['SHPBH'] = $row [$this->idx_SHPBH];
		$lvli['PIHAO'] = $row [$this->idx_PIHAO];
		$lvli['RKDBH'] = $rkdbh_xiaoshou;
		$lvli['YDDH'] = $rkdbh;
		$lvli['XUHAO'] = $idx_lvli;
		if ($row [$this->idx_SHCHRQ] != ""){
			$lvli['SHCHRQ'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_SHCHRQ]."','YYYY-MM-DD')"); //生产日期;
		}
		if ($row [$this->idx_BZHQZH] != ""){
			$lvli['BZHQZH'] = new Zend_Db_Expr("TO_DATE('".$row [$this->idx_BZHQZH]."','YYYY-MM-DD')"); //保质期至
		}
		$lvli['CHLSHJ'] = new Zend_Db_Expr('SYSDATE');
		$lvli['SHULIANG'] = $shuliang_update;
		$lvli['BZHDWBH'] = $row [$this->idx_BZHDWBH];
		$lvli['ZHYZHL'] = '22';
		$lvli['ZKZHT'] = '0';
		$lvli['BGRQ'] = new Zend_Db_Expr('SYSDATE');//变更日期
		$lvli['BGZH'] = $_SESSION ['auth']->userId; //变更者		
		$lvli ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
		$lvli ['ZCHZH'] = $_SESSION ['auth']->userId; //作成者
		$this->_db->insert ( 'H01DB012405', $lvli );
	}
	
	/*
	 * 销售退货单状态更新
	 * 
	 * @param
	 * @return 	bool	
	 */
	public function updatezht() {
		$sql = "UPDATE H01DB012206"
			 . " SET THDZHT = '1'"
			 . " WHERE QYBH = :QYBH"
			 . " AND THDBH = :THDBH";
			 
		//绑定查询条件
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['THDBH'] = $_POST["THDBH"];
			 
		$this->_db->query ( $sql,$bind );
	}
	
	/*
	 * 单位信息
	 * 
	 * @param array $filter
	 * @return string array
	 */
	public function getDanweiInfo($filter) {
		//
		$sql =  "SELECT DWBH,DWMCH,DIZHI,DHHM,HGL_DEC(KOULV),FHQBH," . "DECODE(XSHXDQ,NULL,0,XSHXDQ) XSHXDQ" . //
			    " FROM H01DB012106" . " WHERE QYBH = :QYBH " . 
				" AND DWBH = :DWBH" . //
				" AND FDBSH ='0'" . //
				" AND SHFXSH = '1'" . //
				" AND KHZHT = '1'"; //

		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['DWBH'] = $filter ['dwbh'];
		
		return $this->_db->fetchRow ( $sql, $bind );
	}
	
	/*
	 * 更改原销售单应付应收
	 */
	public function uptJsxx() {
		
		$sql_update = "UPDATE H01DB012208"
					. " SET JINE = JINE - :JINE,"
					. "HSHJE = HSHJE - :HSHJE,"
					. "YSHJE = YSHJE - :HSHJE"
					. " WHERE QYBH = :QYBH AND XSHDBH = :XSHDBH";
		
		$bind['QYBH'] = $_SESSION ['auth']->qybh;
		$bind['JINE'] = $_POST['JINE_HEJI'];
		$bind['HSHJE'] = $_POST['HANSHUIJINE_HEJI'];
		$bind['XSHDBH'] = $_POST ['XSHDBH'];
			
		$this->_db->query ( $sql_update,$bind );
	}
	
}
	
	
	