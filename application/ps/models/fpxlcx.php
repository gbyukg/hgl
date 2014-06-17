<?php
/******************************************************************
 ***** 模         块：       配送模块(PS)
 ***** 机         能：       分配线路查询(FPXLCX)
 ***** 作  成  者：        刘枞
 ***** 作  成  日：        2011/08/19
 ***** 更新履历：
 ******************************************************************/

class ps_models_fpxlcx extends Common_Model_Base {
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
				."WHERE A.QYBH = :QYBH ";
		
		//绑定查询条件
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		
		//查找条件发货区
		if ($filter ["FAHUOQU"] != ""  ) {
			$sql .= " AND A.FHQBH = :FHQBH ";
			$bind ['FHQBH'] = $filter ["FAHUOQU"];
		}
		
		//查询条件( 开始日期 <= 设定日期 <= 终止日期 )
		if ($filter ["QSRQ"] != "" || $filter ["ZZRQ"] != "")
		{
			$sql .= " AND :KSRQ <= TO_CHAR(A.SHDRQ,'YYYY-MM-DD') AND TO_CHAR(A.SHDRQ,'YYYY-MM-DD') <= :ZZRQ";
			$bind ['KSRQ'] = $filter ["QSRQ"] == ""?"1900-01-01":$filter ["QSRQ"];
			$bind ['ZZRQ'] = $filter ["ZZRQ"] == ""?"9999-12-31":$filter ["ZZRQ"];
		}

		//排序
		$sql .= " ORDER BY A.SHDRQ, A.FHQBH ";
		
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
		$sql = "SELECT FHQBH,FHQMCH FROM H01DB012422 WHERE QYBH = :QYBH AND CKBH = :CKBH AND FHQZHT = '1'";
		
		$bind ['QYBH'] = $_SESSION ['auth']->qybh;
		$bind ['CKBH'] = $_SESSION ['auth']->ckbh;
		
		$result = $this->_db->fetchPairs ( $sql, $bind );
		
		$result [''] = '--选择发货区--';
		ksort ( $result );
		
		return $result; 
	}
	
	
}