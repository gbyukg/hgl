<?php 
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   库存报警线设定信息(kcbjsd)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/19
 * 更新履历：
 *********************************/

class cc_models_kcbjsd extends Common_Model_Base
{
	/**
     * 取得列表数据
     *
     * @param unknown_type $filter
     * @return unknown
     */
	public function getGridData($filter)
	{
		//排序用字段名
		//编号，名称，规格,包装单位，生产厂家，剂型，库存下线，库存上线
        $fields = array ("", "SHPBH","SHPMCH","GUIGE","BZHDWMCH","SHCHCHJ","JIXING","KCXX","KCSHX" ); 

        //检索SQL
        $sql="SELECT SHPBH,SHPMCH,GUIGE,BZHDWMCH,SHCHCHJ,JIXINGMCH,KCXX,KCSHX,"
        ."'设定^javascript:alarmSetting(' || '\"' || SHPBH || '\"' || ')^_self'"
        //. "'<a target=\"_blank \" onclick=\"alarmSetting(' ||   '''' || SHPBH || '''' || ');\">设定</a>' "
        . "FROM H01VIEW012101 WHERE QYBH = :QYBH";

        //绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        //商品
        if($filter['searchParams']['SHANGPIN']!="")
        {
            $sql .= " AND( SHPBH LIKE '%' || :SHANGPIN || '%'".
                    "      OR  lower(SHPMCH) LIKE '%' || :SHANGPIN || '%'".
                    "      OR  lower(ZHJM) LIKE '%' || :SHANGPIN || '%')";
            $bind ['SHANGPIN'] = strtolower($filter ["searchParams"]['SHANGPIN']);
        }
        //厂家
        if($filter['searchParams']['SHCHCHJ'] != '')
        {
        	$sql .= " AND( SHCHCHJ LIKE '%' || :SHCHCHJ || '%')";
            $bind ['SHCHCHJ'] = strtolower($filter ["searchParams"]['SHCHCHJ']);
        }
        
        //库存下线
        $filter['searchParams']['KCXX'] == 'on' ? ($sql .=' AND KCXX IS NULL') : '';
        
        //库存上线
        $filter['searchParams']['KCSHX'] == 'on' ? $sql .=' AND KCSHX IS NULL' : '';
        
        //自动生成精确查询用Sql
        $sql .= Common_Tool::createFilterSql('CC_KWBJXSD',$filter['filterParams'],$bind);
        
        //排序
        $sql .= " ORDER BY ". $fields [$filter ["orderby"]] . " " . $filter ["direction"];
        //防止重复数据引发翻页排序异常，orderby 项目最后添加主键
        $sql .=",SHPBH";
        
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
     * 获取商品信息
     *
     * @param $shpbh
     * @return 
     */
	public function getshpxx($shpbh)
	{
		$sql='SELECT SHPMCH,KCSHX,KCXX FROM H01DB012101 WHERE QYBH = :QYBH AND SHPBH = :SHPBH';
		//绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['shpbh'] = $shpbh;  //商品编号
        return $this->_db->fetchRow($sql, $bind);
	}
	
	/**
     * 更新数据
     *
     * @param $filter
     * @return 
     */
	public function updateData($filter)
	{
		$sql="UPDATE H01DB012101 SET KCSHX = :KCSHX,KCXX = :KCXX WHERE QYBH = :QYBH AND SHPBH = :SHPBH";
		//绑定查询条件
        $bind ['QYBH'] = $_SESSION ['auth']->qybh;
        $bind['KCSHX'] = $filter['kcshx'];  //库存上线
        $bind['KCXX'] = $filter['kcxx'];  //库存下线
        $bind['shpbh'] = $filter['shpbh'];  //商品编号
        $this->_db->query ( $sql, $bind );
        return true;
	}
	
	/**
     * 更新数据(直接在检索页面保存)
     *
     * @param $filter
     * @return 
     */
	public function updateSave()
	{
		foreach ($_POST['params'] as $value)
		{
			$sql = 'UPDATE H01DB012101 SET KCSHX = :KCSHX,KCXX = :KCXX WHERE QYBH = :QYBH AND SHPBH = :SHPBH';
			$bind ['QYBH'] = $_SESSION ['auth']->qybh;
			$bind['KCSHX'] = $value[0]['kcshx'];  //库存上线
			$bind['KCXX'] = $value[0]['kcxx'];  //库存下线
			$bind['SHPBH'] = $value[0]['shpbh'];  //商品编号
			$this->_db->query ( $sql, $bind );
		}
		return true;
	}
}







?>