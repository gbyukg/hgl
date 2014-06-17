<?php
/******************************************************************
 ***** 模         块：       配送模块(PS)
 ***** 机         能：       分配线路(FPXL)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/17
 ***** 更新履历：
 ******************************************************************/

class ps_models_fpxl extends Common_Model_Base {
	private $idx_ROWNUM = 0;      // 行号
	private $idx_RIQI = 1;        // 日期
	private $idx_PSXL = 2;        // 配送线路
	private $idx_CHLSJ = 3;       // 司机车辆
	private $idx_BEIZHU = 4;      // 备注
	private $idx_FHQBH = 5;       // 发货区编号
	
	
	/**
	 * 得到退货单列表数据
	 *
	 * @param array $filter
	 * @return string xml
	 */
	public function getGridDanjuData($filter){
		
		//检索SQL
		$sql = "SELECT TO_CHAR(A.SHDRQ,'YYYY-MM-DD'), B.FHQMCH, A.SJCHL, A.BEIZHU, A.FHQBH "
				."FROM H01DB012606 A "
				."LEFT JOIN H01DB012422 B ON A.QYBH = B.QYBH AND A.FHQBH = B.FHQBH "
				."WHERE A.QYBH = :QYBH "
				."AND A.SHDRQ = TO_DATE(:SHDRQ ,'YYYY-MM-DD') ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['SHDRQ'] = $filter["rqkey"];
		
		//当前页数据
		$recs = $this->_db->fetchAll( $sql, $bind );
		
		//调用表格xml生成函数
		return Common_Tool::createXml( $recs );
	}
	
	
	/**
	 * 获取发货区信息
	 */
	function getFHQ()
	{
		$sql = "SELECT FHQBH,FHQMCH ".      //发货区编号，发货区名称
		       "FROM H01DB012422 ".
			   "WHERE QYBH = :QYBH ".
			   "AND CKBH = :CKBH ".
			   "AND FHQZHT = '1' ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
		
		$rec = $this->_db->fetchAll( $sql, $bind );
		
		return $rec;     
	}
	
	
	/*
	 * 画面必须输入项验证
	 */
	public function inputCheck() {
		
		$isHasMingxi = false; //是否存在至少一条明细
		foreach ( $_POST ["#grid_main"] as $grid ) {
			if ($grid [$this->idx_RIQI] != "") {
				$isHasMingxi = true;
				if ($grid [$this->idx_RIQI] == "" || 
					$grid [$this->idx_PSXL] == "" || 
					$grid [$this->idx_CHLSJ] == "") {
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
	 * 数据验证
	 */
	public function logicCheck() {
		
		foreach( $_POST ["#grid_main"] as $row ) {
	    	
		    //获取数据库中是否已有该信息
			$sql = "SELECT COUNT(*) FROM H01DB012606 ".
		            " WHERE QYBH = :QYBH ".
		            " AND SHDRQ = TO_DATE(:SHDRQ ,'YYYY-MM-DD')  ".
					" AND FHQBH = :FHQBH ";
			
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;   		//区域编号
			$bind ['SHDRQ'] = $row [$this->idx_RIQI];           //设定日期
			$bind ['FHQBH'] = $row [$this->idx_FHQBH];          //发货区编号
					 
			$SL = $this->_db->fetchOne( $sql, $bind );   
		    
		    if($SL == '0'){
		    	
				$this->saveMain( $row );		       //分配线路信息保存
				
		    }else{
		    	
		    	$this->updateMain( $row );		       //分配线路信息修改
		    	
		    }
	    }
	}
	
	
	/**
	 * 分配线路信息保存
	 * @return bool
	 */
	public function saveMain( $row ) {
		$insert ['QYBH'] = $_SESSION ['auth']->qybh;                //区域编号
		$insert ['SHDRQ'] = new Zend_Db_Expr ( "TO_DATE('" . $row [$this->idx_RIQI] . "','YYYY-MM-DD')" );   //设定日期
		$insert ['FHQBH'] = $row [$this->idx_FHQBH];                //发货区编号
		$insert ['SJCHL'] = $row [$this->idx_CHLSJ];                //司机车辆
		$insert ['BEIZHU'] = $row [$this->idx_BEIZHU];              //备注
		$insert ['BGRQ'] = new Zend_Db_Expr ( 'sysdate' );          //变更日期
		$insert ['BGZH'] = $_SESSION ['auth']->userId;              //变更者
		$insert ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' );         //作成日期
		$insert ['ZCHZH'] = $_SESSION ['auth']->userId;             //作成者
		
		return $this->_db->insert ( "H01DB012606", $insert );       //插入信息
	}
	
	
	/*
	 * 更新已有信息
	 */
	public function updateMain( $row ) {
		
		//更新在库信息
		$sql_zaiku = "UPDATE H01DB012606 ".
		             "SET SJCHL = :SJCHL, ".
		             " BEIZHU = :BEIZHU, ".
					 " BGZH = :BGZH, ".
					 " BGRQ = SYSDATE ".
		             " WHERE QYBH = :QYBH ".
		             " AND SHDRQ = TO_DATE(:SHDRQ ,'YYYY-MM-DD')  ".
		             " AND FHQBH = :FHQBH ";
		             
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;          //区域编号
		$bind ['SJCHL'] = $row [$this->idx_CHLSJ];          //司机车辆
		$bind ['BEIZHU'] = $row [$this->idx_BEIZHU];        //备注
		$bind ['BGZH'] = $_SESSION ['auth']->userId;        //变更者
		$bind ['SHDRQ'] = $row [$this->idx_RIQI];           //设定日期
		$bind ['FHQBH'] = $row [$this->idx_FHQBH];          //发货区编号
		          
		$this->_db->query ( $sql_zaiku, $bind );
		
	}
	
}