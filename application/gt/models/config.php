<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    配置
 * 作成者：周义
 * 作成日：2011/04/01
 * 更新履历：
 *********************************/
class gt_models_config extends Common_Model_Base {
	
    public function getGridLayout($gridid){
		$sql = "SELECT GRIDID, POS,HEADER,TYPE,WIDTH,ALIGN,HIDDEN,DECODE(SORT,'1','str','na') AS SORT FROM GRIDLAYOUT ".
		       " WHERE QYBH =:QYBH AND USERID = :USERID AND GRIDID =:GRIDID ".
		        "ORDER BY POS";
		$bind['QYBH'] =  $_SESSION ['auth']->qybh;
		$bind['USERID'] =  $_SESSION ['auth']->userId;
		$bind['GRIDID'] = $gridid;
		$recs = $this->_db->fetchAll($sql,$bind);
		return $recs;
		
	}
	
	public function getGridFilter($gridid){
		//项目信息
		$sql = "SELECT * FROM GRIDFILTER WHERE QYBH = :QYBH AND GRIDID=:GRIDID ORDER BY POS";
		$bind['QYBH'] =  $_SESSION ['auth']->qybh;
		$bind['GRIDID'] = $gridid;
		$recs = $this->_db->fetchAll($sql,$bind);
		
		$sqlselect = "SELECT ZIHAOMA AS KEY,NEIRONG AS VALUE FROM H01DB012001 WHERE QYBH = :QYBH AND CHLID = :CHLID ORDER BY ZIHAOMA";
		unset($bind['GRIDID']);
	
		foreach ($recs as $key=>$item){
			//radiobutton checkbox select
			if($item['TYPE']=='3' || $item['TYPE']=='4' || $item['TYPE']=='5'){
				if($item['DATATYPE'] =='0'){
					//固定内容
					$recs[$key]['DATA'] = $this->splitData($item['DATA']);
				}elseif($item['DATATYPE'] =='1'){
					//依从于常量表的下拉列表
					$bind['CHLID'] = $item['DATA'];
				     $recs[$key]['DATA'] = $this->_db->fetchAll($sqlselect,$bind);
				}
			}

		}
		return $recs;
	}
	
	public function SaveGridHiddenCols($gridcols){
		
		//查询是否存在该用户的自定义数据
		$sql = "SELECT * FROM GRIDLAYOUT WHERE QYBH = :QYBH AND USERID = :USERID AND GRIDID = :GRIDID";
		$bind["QYBH"] = $_SESSION ['auth']->qybh;
		$bind["USERID"] = $_SESSION ['auth']->userId;
		$bind["GRIDID"] = $gridcols["gridid"];
			
		$rec = $this->_db->fetchAll($sql,$bind);
		//不存在自定义数据
		if(count($rec)==0){
			$sql = "INSERT INTO GRIDLAYOUT ".
			       "SELECT QYBH,:USERID,GRIDID,POS,HEADER,TYPE,WIDTH,ALIGN,HIDDEN,SORT FROM GRIDLAYOUT ".
			       "WHERE QYBH = :QYBH AND GRIDID = :GRIDID";
			$this->_db->query($sql,$bind);
		}
		
		$displaypos =  implode(",",$gridcols["displaycols"]);  //显示的项目
		$hiddenpos =  implode(",",$gridcols["hiddencols"]);    //隐藏的项目
		
		$sql = "UPDATE GRIDLAYOUT SET HIDDEN = CASE ".
               (($displaypos==NULL)? "":"WHEN POS IN (".$displaypos.") THEN 'd' ").
		       (($hiddenpos==NULL)? "":" WHEN POS IN (".$hiddenpos.") THEN 'h' ").
		       " ELSE HIDDEN END".  //固定列项目或者永久隐藏项目
		       " WHERE QYBH = :QYBH AND USERID = :USERID AND GRIDID = :GRIDID ";

		
		$this->_db->query($sql,$bind);
		
	}
	
    function splitData($data){
		$options = array();
		
		$arrs = split(';',$data);
		
		for($i=0;$i<count($arrs);$i++){
			
			$arr = split(':',$arrs[$i]);
			$option['KEY']= $arr[0];
			$option['VALUE']= $arr[1];
			$options[$i] = $option;
		}
		
			
		return $options;
		
	}


}
