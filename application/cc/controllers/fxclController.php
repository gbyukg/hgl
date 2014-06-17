<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：    分箱处理(fxcl)
 * 作成者：dltt-姚磊
 * 作成日：2011/3/24
 * 更新履历：
 *********************************/
class cc_fxclController extends cc_controllers_baseController {
	
	private $idxy_ROWNUM = 0; // 行号
	private $idxy_SHPMCH = 1; // 商品名称
	private $idxy_KWBH = 2; // 库位
	private $idxy_SHULIANG = 3; // 数量
	private $idxy_DANWEI = 4; // 单位
	private $idxy_DWTJ = 5; // 单位体积
	private $idxy_ZTJFX = 6; //总体积/箱
	private $idxx_TIJX = 7;//体积/箱
	private $idxy_CHSDCHK = 8; //传送带出口
	private $idxy_QFBZ = 9; //区分标志
	private $idxy_FENXIANGHAO = 10; //分箱号
	
	private $idx_ROWNUM = 0;// 行号
	private $idx_FENXIANGHAO = 1;// 分箱号
	private $idx_ZHZHXH = 2;// 周转箱号
	private $idx_TCLV = 3;// 填充率
	private $idx_SLHJ = 4;// 数量合计
	private $idx_SFZHZN = 5;// 是否周转箱
	private $idx_QFBZ =6;//区分标志
	/*
	 * 初始化页面
	 */
	
	public function indexAction() {
		$model = new cc_models_fxcl ( );
		$this->_view->assign ( "title", "仓储管理-分箱处理 " ); //标题
		unset ( $_SESSION ['tepp'] ); //重置零散数组
		unset ( $_SESSION ['XLBI'] ); //重置箱列表数组
		unset ( $_SESSION ['TCL'] );  //重置箱列表填充率
		unset ( $_SESSION ['SLHJ'] ); //重置箱列表数量合计
		unset ( $_SESSION['dbtem']); //重置双击数组
		unset ( $_SESSION['TEM_QFBZ']);//区分标志
		$xshdbh = $this->_getParam ( "xshdbh" );
		$this->_view->assign ( "xshdbh", $xshdbh ); //销售单号
		$rec = $model->getzjsl ( $xshdbh );
		$this->_view->assign ( "rec", $rec );
		$this->_view->display ( 'fxcl_01.php' );
	}
	
	/**
	 * 自动处理零散箱 箱列表grid
	 */
	
	public function getlistdataAction() {
		$sjfx = array();
		$xshdbh = $this->_getParam ( "xsdh" ); //获取销售单号
		$model = new cc_models_fxcl ( ); //
		$res = $model->getfxMainlist ( $xshdbh ); //获取分箱总信息
		$sjfx = $res; //设置散件分箱二维数组 sjfx
		$zytj = 0;
		$slhj = 0; //传送带口 = Z的数量合计
		$cliang = $model->getCliang ( $xshdbh ); //获取常量表中长 宽 高
		$kzsl = 0;
		$addzytj = 0;
		$addtclv = 0;
		$qfbz='';
		$zhzhxtj = ( float ) ($cliang ['0'] ['NEIRONG'] * $cliang ['1'] ['NEIRONG'] * $cliang ['2'] ['NEIRONG']); //周转箱体积 = 长* 宽 * 高
		$fenxianghao = '0001'; //分箱号
		for($z = 0; $z < count ( $sjfx ); $z ++) {
			$sjfx [$z] ['FENXIANGHAO'] = '';//设置分箱号
			$sjfx [$z] ['ZHZHXH']='';//周转箱号
			$sjfx [$z] ['QFBZ'] =$z;//区分零散商品 区分标志
		}
		
		//$lsnum = $model->getlsNum ( $xshdbh ); //获取不等于'Z'的零散商品个数												  
		for($i = 0; $i < count($sjfx); $i ++) {
			foreach ( $sjfx as $list => $things ) {
				$list;
				if ($things ['CHSDCHK'] != 'Z') {
					if ($things ['FENXIANGHAO'] != '') {
						continue;
					} else {
						$dwtj =  ( float )($things ['DBZHTJ'] / $things ['JLGG']); //当前商品单位体积 = 最大包装体积 / 计量规格
						$zytj += ( float )($things ['LSSHL'] * $dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
						$tclv =  ( float )($zytj / $zhzhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积(总体积/箱)
						$tijx = (float)($dwtj/$zhzhxtj);         //体积/箱 = 当前商品单位体积/周转箱体积

						if ($tclv > $cliang ['3'] ['NEIRONG']) { //如果大于最大填充率 直接分箱
							$temp = ( float ) ($zytj - $things ['LSSHL'] * $dwtj); //当前箱剩余体积
							if ($temp == 0) { /*如果当前箱剩余体积为0,则添加分箱号*/
								//添加分箱号至数组									
								$kzsl = ( int ) ($zhzhxtj * $cliang ['3'] ['NEIRONG'] / $dwtj); //当前箱可装数量
								$addzytj = ( float )($kzsl * $dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
								$addtclv =  ( float )($addzytj / $zhzhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积(总体积/箱)
								$sjfx [$list] ['LSSHL'] = ( int ) ($things ['LSSHL'] - $kzsl); //修改当前条数据的数量
								//追加一条新记录
								array_push ( $sjfx, array ("SHPMCH"=>$sjfx[$list]['SHPMCH'],
														   "KWBH"=>$sjfx[$list]['KWBH'],
														   "LSSHL" =>$kzsl,
														   "NEIRONG"=>$sjfx[$list]['NEIRONG'],
														   "DBZHTJ"=>$dwtj, 
														   "TCLV"=>$addtclv,
								                           "TIJX"=>$tijx,
														   "CHSDCHK"=>$sjfx[$list]['CHSDCHK'],	
								                           "QFBZ"=>count($sjfx),													   
														   "FENXIANGHAO"=>$fenxianghao,														   														   														   														   														   
														   "JLGG"=>$sjfx[$list]['JLGG'],														   														    
														   "SHPBH"=>$sjfx[$list]['SHPBH'],
														   "PIHAO" =>$sjfx[$list]['PIHAO'],
														   "ZHZHXH"=>'',//周转箱号
														   "SFZHZN" => '是',
														   "CKBH"=>$sjfx[$list]['CKBH']
								) ); //追加一条新数据到数组
											continue;
							} else {
								$zytj = ( float ) ($zytj - $things ['LSSHL'] * $dwtj); //占用体积
								continue;
							}
						} else { //分箱
							$sjfx [$list] ['DBZHTJ'] = $dwtj ;  //添加单位体积
							$sjfx [$list] ['TCLV'] = $tclv;     //添加总体积/箱
							$sjfx [$list] ['TIJX'] = $tijx;     //添加体积/箱
							$sjfx [$list] ['FENXIANGHAO'] = $fenxianghao; //添加分箱号
						}
					}
				}
			}
			
			$zytj = 0; //占用体积重置为 0
			$dwtj =0;
			$fenxianghao = strval ( (intval ( $fenxianghao ) + 1) ); //分箱号 + 1
			if (strlen ( $fenxianghao ) == 1) { //当仅有个位时，补0 至4位整数
				$fenxianghao = '000' . $fenxianghao; ///*处理分箱号+1，自动补0
			} elseif (strlen ( $fenxianghao ) == 2) {
				$fenxianghao = '00' . $fenxianghao;			
			} elseif (strlen ( $fenxianghao ) == 3) {
				$fenxianghao = '0' . $fenxianghao;
			} else {
				$fenxianghao;
			}
		} //*/
		$templist = 0;
		foreach ( $sjfx as $key => $value ) {
			if (( int ) ($sjfx [$key]['FENXIANGHAO']) > $templist) {
				$templist = $sjfx [$key] ['FENXIANGHAO'];
			}		
		}
		$templist = strval ( (intval ( $templist ) + 1) );
		if (strlen ( $templist ) == 1) { //当仅有个位时，补0 至4位整数			
			$fenxianghao = '000' . $templist; ///*处理分箱号+1，自动补0		
		} elseif (strlen ( $fenxianghao ) == 2) {
			$fenxianghao = '00' . $templist;		
		} elseif (strlen ( $fenxianghao ) == 3) {
			$fenxianghao = '0' . $templist;
		} else {
			$fenxianghao = $templist;
		}
		
		/*
 * 等于'Z'传送带出口
 */
		foreach ( $sjfx as $list => $things ) {
			if ($sjfx[$list]['CHSDCHK'] == 'Z') {
				
				$dwtj = ( float ) ($things ['DBZHTJ'] / $things ['JLGG']); //当前商品单位体积 = 最大包装体积 / 计量规格
				$zytj += ( float ) ($things ['LSSHL'] * $dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
				$tclv = ( float ) ($zytj / $zhzhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积
				$slhj += ( float ) ($things ['LSSHL']);
				$_SESSION ['TCL'] = $tclv;
				$_SESSION ['SLHJ'] = $slhj;
				$sjfx[$list]['FENXIANGHAO']='9999';
				continue;
			}
		}
		
		$templist = 0;
		foreach ( $sjfx as $key => $value ) {
			if (( int ) ($sjfx [$key]['FENXIANGHAO']) > $templist) {
				$templist = ( int ) ($sjfx [$key] ['FENXIANGHAO']);
			}	
		}
		$tempfxh = '0001'; //临时分箱号 0001
		$temptcl = 0; //填充率
		$tempsum = 0;
		//清0
		$dwtj = 0;
		$zytj = 0;
		$zhzhxh = '';
		$resul = array (); //数量合计
		for($i = 0; $i < $templist; $i ++) {
			foreach ( $sjfx as $list => $things ) {
				
				if ($things ['FENXIANGHAO'] == $tempfxh) {
					$dwtj = ( float ) ($things ['DBZHTJ'] ); //当前商品单位体积 = 最大包装体积 / 计量规格	
					$zytj += ( float ) ($things ['LSSHL'] * $dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
					$temptcl = ( float ) ($zytj / $zhzhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积
					$tempsum += $things ['LSSHL'];
					$text = '是';
					$qfbz.= '*'.$things['QFBZ']	;
				}			
			}
			if ($temptcl != 0) {				
				$resul += array ($i => array ("FENXIANGHAO" => $tempfxh,
											 "ZHZHXH" => $zhzhxh, 
											 "TCLV" => $temptcl, 
											 "SLHJ" => $tempsum, 
											 "SFZHZN" => $text, 
											 "QFBZ"=>$qfbz) );
				$tempfxh = strval ( (intval ( $tempfxh ) + 1) ); //分箱号 + 1		
				if (strlen ( $tempfxh ) == 1) { //当仅有个位时，补0 至4位整数
					

					$tempfxh = '000' . $tempfxh; ///*处理分箱号+1，自动补0
				} elseif (strlen ( $tempfxh ) == 2) {
					$tempfxh = '00' . $tempfxh;
				} elseif (strlen ( $tempfxh ) == 3) {
					$tempfxh = '0' . $tempfxh;
				} else {
					$tempfxh = $tempfxh;
				}
				$dwtj = 0;
				$zytj = 0;
				$temptcl = 0; //清0													 //填充率
				$tempsum = 0;
				$qfbz='';
			} else {
				$fenxianghao = strval ( (intval ( $fenxianghao ) + 1) ); //分箱号 + 1
				$temptcl = 0; //填充率
				$tempsum = 0; //数量合计
			}		
		}
		$tem_tj = $_SESSION ['TCL'];
		$tem_sl = $_SESSION ['SLHJ'];
		if($tem_sl != 0){
		array_push ( $resul, array ("FENXIANGHAO" => '9999', "ZHZHXH" =>'9999', "TCLV" =>$tem_tj, "SLHJ" => $tem_sl, "SFZHZN" =>'否' ) ); //追加一条新数据到数组
		}

		$_SESSION ['arr'] = $sjfx; //零散商品变量数组
		$_SESSION ['ZHZUTJ'] = $zhzhxtj; //周转箱体积
		$_SESSION ['RESUL'] = $resul; //箱列表数组
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo Common_Tool::createXml ( $resul );	
	}
	
	/**
	 * 删除功能 并显示到零散GRID中
	 * 
	 */
	public function deletedataAction() {
		
		$list_qfbz =array();
		$temparr = $_SESSION ['arr'];						//获取原始数据session
		$qxbzval = $this->_getParam ( "qxbz" );				//获取取消标志		
		$list_qfbz = split('[*]',$qxbzval);					//以 * 分割区分标志字符串至一个新数组
		
		//生成零散商品数组
		foreach ( $temparr as $key => $value ) {
			if ($value['CHSDCHK'] =='Z') continue;     //选取传送带借口不为Z的数据
			for($k =0;$k<= count($list_qfbz);$k++){
				if($k == 0) continue;
				if($list_qfbz[$k] == '') continue;
			if ($value ['QFBZ'] == $list_qfbz[$k]) {   //选取区分标志相同的数据进行计算
				$temparr[$key]['FENXIANGHAO']='';	   //清空分箱号
			}
		}	
		}
		 $_SESSION ['arr'] = $temparr ;               //覆盖原始数据
		 //显示分箱号为空的数据
		foreach ($temparr as $i=>$v){
			if($v['FENXIANGHAO']!=''){
				unset($temparr[$i]);
				continue;
			}			
		}

		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo Common_Tool::createXml ( $temparr );

	}
	
	/**
	 * 删除箱别表后重新排序分箱号
	 */
	
	public function conutdataAction() {
		$list_temparr = $_SESSION ['arr']; //原始数据数组
		$temparr = $_SESSION ['RESUL'];    //获取箱列表数组
		$s = array (); //获取返回结果数组集
		$fxhao = $this->_getParam ( "fxhaom" );
		if (isset ( $_SESSION ['XLBI'] )) {
			$s = $_SESSION ['XLBI'];
			$temparr = $s;
			foreach ( $temparr as $key => $value ) {
				if ($value ['FENXIANGHAO'] == $fxhao) { //选取选中的分箱号的商品			
					unset ( $temparr [$key] ); //删除选中的箱列表信息					
					$s = $temparr;
					$_SESSION ['XLBI'] = $s; //存储箱列表更改后信息
				} else if($value ['FENXIANGHAO'] == '9999'){
					continue;
				}else if ($value ['FENXIANGHAO'] > $fxhao) {
					$value ['FENXIANGHAO'] = strval ( (intval ( $value ['FENXIANGHAO'] ) - 1) ); //分箱号 - 1
					if (strlen ( $value ['FENXIANGHAO'] ) == 1) { //当仅有个位时，补0 至4位整数							
						$temparr [$key] ['FENXIANGHAO'] = '000' . $value ['FENXIANGHAO']; ///*处理分箱号+1，自动补0
					} elseif (strlen ( $value ['FENXIANGHAO'] ) == 2) {
						$temparr [$key] ['FENXIANGHAO'] = '00' . $value ['FENXIANGHAO'];
					} elseif (strlen ( $value ['FENXIANGHAO'] ) == 3) {
						$temparr [$key] ['FENXIANGHAO'] = '0' . $value ['FENXIANGHAO'];
					} else {
						$temparr [$key] ['FENXIANGHAO'] = $value ['FENXIANGHAO'];
					}
				}
			}
		} else {
			foreach ( $temparr as $key => $value ) {
				if ($value ['FENXIANGHAO'] == $fxhao) { //选取选中的分箱号的商品			
					unset ( $temparr [$key] );
					$s = $temparr;
					$_SESSION ['XLBI'] = $s; //存储箱列表更改后信息
				} else if($value['FENXIANGHAO'] =='9999'){
					continue;
				}else if ($value ['FENXIANGHAO'] > $fxhao) {
					$value ['FENXIANGHAO'] = strval ( (intval ( $value ['FENXIANGHAO'] ) - 1) ); //分箱号 - 1
					if (strlen ( $value ['FENXIANGHAO'] ) == 1) { //当仅有个位时，补0 至4位整数							
						$temparr [$key] ['FENXIANGHAO'] = '000' . $value ['FENXIANGHAO']; ///*处理分箱号+1，自动补0
					} elseif (strlen ( $value ['FENXIANGHAO'] ) == 2) {
						$temparr [$key] ['FENXIANGHAO'] = '00' . $value ['FENXIANGHAO'];
					} elseif (strlen ( $value ['FENXIANGHAO'] ) == 3) {
						$temparr [$key] ['FENXIANGHAO'] = '0' . $value ['FENXIANGHAO'];
					} else {
						$temparr [$key] ['FENXIANGHAO'] = $value ['FENXIANGHAO'];
					}
				}
			}
		}
		//原始数据数组分箱号 -1 
		
			foreach ( $list_temparr as $k => $v ) {
				if ($v ['FENXIANGHAO'] == '')continue; //如果分箱号为空,即为已删除数据,跳出;								
				if ($v ['FENXIANGHAO'] == '9999')continue; //如果分箱号为不是周转箱号的数据,跳出;
				if ($v ['FENXIANGHAO'] > $fxhao) {			  //重新分配分箱号,原始数据分箱号大于选中数据分箱号减1
					$v ['FENXIANGHAO'] = strval ( (intval ( $v ['FENXIANGHAO'] ) - 1) ); //分箱号 - 1
					if (strlen ( $v ['FENXIANGHAO'] ) == 1) { //当仅有个位时，补0 至4位整数							
						$list_temparr [$k] ['FENXIANGHAO'] = '000' . $v ['FENXIANGHAO']; ///*处理分箱号+1，自动补0
					} elseif (strlen ( $v ['FENXIANGHAO'] ) == 2) {
						$list_temparr [$k] ['FENXIANGHAO'] = '00' . $v ['FENXIANGHAO'];
					} elseif (strlen ( $v ['FENXIANGHAO'] ) == 3) {
						$list_temparr [$k] ['FENXIANGHAO'] = '0' . $v ['FENXIANGHAO'];
					} else {
						$list_temparr [$k] ['FENXIANGHAO'] = $v ['FENXIANGHAO'];
					}
				}
			}
			$_SESSION ['arr'] = $list_temparr ;               //覆盖原始数据				
		$s = $temparr;										  //保存输出数据
		$_SESSION ['XLBI'] = $temparr;
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo Common_Tool::createXml ( $temparr );
	}
	
	/*
	 * 分箱按钮事件
	 */
	public function fxclAction() {
		//初始化信息
		$temp_arrlist = array ();      
		$temp_arrlist = $_SESSION ['XLBI'];  //箱列表显示数组
		$temparr = $_SESSION ['arr'];        //原始数据数组
		$tem_dwtj = 0;
		$tem_zytj = 0;
		$tem_tempsum = 0;
		$tem_temptcl = 0;
		$qfbz='';
		$tempfxh = '0001'; //临时分箱号 0001
		/*   处理压缩分箱       */
		$flg = $this->_getParam ( "fx" ); //当前页面的箱列表数据个数	
		$tem_zhxtj = $_SESSION ['ZHZUTJ'];//周转箱体积
		
		if($flg == 0){                    //如果当前箱列表没有数据,匹配分箱号为00001
				$tempfxh =1;	
			}else{			
			$tempfxh = strval ( (intval ( $flg ) ) ); 	
			}
							 //如果仅剩1条不是周转箱数据,分箱号为0001
			if (strlen ( $tempfxh ) == 1) { //当仅有个位时，补0 至4位整数							
				$tempfxh = '000' . $tempfxh; //*处理分箱号+1，自动补0
			} elseif (strlen ( $tempfxh ) == 2) {
				$tempfxh = '00' . $tempfxh;
			} elseif (strlen ( $tempfxh ) == 3) {
				$tempfxh = '0' . $tempfxh;
			} else {
				$tempfxh = $tempfxh;
			}
		
			foreach ( $_POST ["#grid_shp"] as $row ) {							
				$tem_dwtj = ( float ) ($row [$this->idxy_DWTJ]); //单位体积 = grid 获取的此条体积	
				$tem_tempsum += $row [$this->idxy_SHULIANG];	
				$qfbz.= '*'.$row [$this->idxy_QFBZ]	;
				$row [$this->idxy_FENXIANGHAO]  = $tempfxh;   //将获取的grid里添加当前分箱号				
				foreach ($temparr as $key=>$value){           //判断区分标志是否相等,相等则添加分箱号
					if($temparr[$key]['QFBZ']== $row [$this->idxy_QFBZ]){
						$temparr[$key]['FENXIANGHAO'] = $tempfxh;
					}else{
						continue;
					}
				}
			}
			$_SESSION ['arr'] = $temparr ;               //覆盖原始数据
			$tem_zytj = ( float ) ($tem_tempsum * $tem_dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
			$tem_temptcl = ( float ) ($tem_zytj / $tem_zhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积								
		
		//追加分箱后的数据到原始箱列表数组中
		array_push ( $temp_arrlist, array ("FENXIANGHAO" =>$tempfxh, "ZHZHXH" =>'',  "TCLV" =>$tem_temptcl, "SLHJ" =>$tem_tempsum, "SFZHZN"=>'是',"QFBZ"=>$qfbz ) ); //追加一条新数据到数组
		$_SESSION ['XLBI'] = $temp_arrlist;
		$_SESSION ['RESUL']= $temp_arrlist;
		echo Common_Tool::json_encode ( $temp_arrlist );
	
	}
	
	/**
	 * 双击返回数据
	 */
	public function dbsetdataAction(){
		$s = array();
		$list_result =array();
		$list_dbarr =array();
		$zhzhxtj = $_SESSION ['ZHZUTJ'];					//获取原始周转箱体积数据数组
		$list_dbarr = $_SESSION ['arr'];					//获取原始数据数组
		$fxhao = $this->_getParam ( "fxhao" );				//获取分箱号为箱列表分配用
		$qxbzval = $this->_getParam ( "qxbz" );				//获取取消标志	
		$list_qfbz = split('[*]',$qxbzval);	

		foreach ( $list_dbarr as $key => $value ) {
			if ($value['CHSDCHK'] =='Z') continue;     //选取传送带借口不为Z的数据
			for($k =1;$k<= count($list_qfbz);$k++){
				if($list_qfbz[$k] == null) continue;
			if ($value ['QFBZ'] == $list_qfbz[$k]) {   //选取区分标志相同的数据进行计算
				
				$dwtj = ( float ) ($value ['DBZHTJ'] / $value ['JLGG']); //当前商品单位体积 = 最大包装体积 / 计量规格
				$zytj = ( float ) ($value ['LSSHL'] * $dwtj); //当前箱占用体积 += 数量 * 当前商品单位体积
				$tclv = ( float ) ($zytj / $zhzhxtj); //当前箱填充率     = 当前箱占用体积  / 周转箱体积
				$dtj = ( float ) ($dwtj / $zhzhxtj); //当前体积/箱
				
				$list_result += array ($key => array ("SHPMC" => $value ['SHPMCH'],
											 "KWBH" => $value ['KWBH'],
											 "SHULIANG" => $value ['LSSHL'],
											 "DANWEI" => $value ['NEIRONG'],
											 "DANWEI" => $value ['NEIRONG'],
											 "DWTJ" => $dwtj, 
											 "ZTJFX" => $tclv, 
											 "TIJXIANG" => $dtj, 
											 "CHSDCHK" => $value ['CHSDCHK'],
											 "QFBZ"=>  $value ['QFBZ']) );
				$dwtj = 0; //清空体积
				$zytj = 0;

			} else if($value ['FENXIANGHAO'] == '9999'){
				continue;
			}else if ($value ['FENXIANGHAO'] > $fxhao ) {
				$value ['FENXIANGHAO'] = strval ( (intval ( $value ['FENXIANGHAO'] ) - 1) ); //分箱号 - 1
				if (strlen ( $value ['FENXIANGHAO'] ) == 1) { //当仅有个位时，补0 至4位整数							
					$temparr [$key] ['FENXIANGHAO'] = '000' . $value ['FENXIANGHAO']; ///*处理分箱号+1，自动补0
				} elseif (strlen ( $value ['FENXIANGHAO'] ) == 2) {
					$temparr [$key] ['FENXIANGHAO'] = '00' . $value ['FENXIANGHAO'];
				} elseif (strlen ( $value ['FENXIANGHAO'] ) == 3) {
					$temparr [$key] ['FENXIANGHAO'] = '0' . $value ['FENXIANGHAO'];
				} else {
					$temparr [$key] ['FENXIANGHAO'] = $value ['FENXIANGHAO'];
				}
			} else {
				continue;
			}	
		}	
		}
		$_SESSION['dbtem'] = $list_result;
		$s += $list_result;
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo Common_Tool::createXml ( $s );
	}
	
	/*
	 * 双击后 如当前列表有删除的数据,则将数据退回到零散列表中
	 */
	function thuidataAction(){
		
		$temparr = $_SESSION ['arr'];						//获取原始数据session
			foreach ($temparr as $key=>$value){
			if($value['FENXIANGHAO']!=''){
				unset($temparr[$key]);
				continue;
			}			
		}
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo Common_Tool::createXml ( $temparr );
		
	}
		
	/**
	 * 保存 
	 */
	function changeztAction(){
		$zhjfx = array();          //整件分箱二维数组
		$tem_list = array();       //临时原始数组
		$addnum ='0001';		   //对应条码需要添加的尾数
		$zjzxs = 0;				   //整件总箱数
		$xshdbh = $_POST['XSDH']; //获取销售单号
		$tem_list = $_SESSION ['arr'];        //原始数据数组
		$model = new cc_models_fxcl ( );
		$result['status'] = '0';   //错误警告
		try{
		$model->beginTransaction (); //开启事物
		$model->changezt( $xshdbh ); //更新销售订单状态
		$zhjfx = $model->getshpxx( $xshdbh ); //检索对应商品信息	
		/******************* 整件拣货操作 开始 *************************/	
		foreach ($zhjfx as $key=>$value){
			for($i=0;$i<$zhjfx[$key]['BZHSHL'];$i++){	
				$zjzxs = $zhjfx[$key]['BZHSHL'];//获取当前条的整件总箱数	
					//整件总箱数 不足4位补0
					$zjzxs = strval  (intval ( $zjzxs ));
			if (strlen ( $zjzxs ) == 1) { //当仅有个位时，补0 至4位整数
				$zhjfx[$key]['BZHSHL'] = '000' . $zjzxs; ///*处理分箱号+1，自动补0
			} elseif (strlen ( $zjzxs ) == 2) {
				$zhjfx[$key]['BZHSHL'] = '00' . $zjzxs;			
			} elseif (strlen ( $zjzxs ) == 3) {
				$zhjfx[$key]['BZHSHL'] = '0' . $zjzxs;
			} 		
				$dytm = $xshdbh.strval($zhjfx[$key]['BZHSHL']).$addnum;  //销售订单号+整件分箱数目+分箱号						
			$zj_resu = $model->insterdytm($zhjfx[$key],$dytm,$xshdbh,$addnum); // 向整件拣货中插入数据 _当前条数据,对应条码,销售单号,分箱号

			if(!$zj_resu){
				$result['status'] = '1';                //整件拣货操作数据错误
			}
			
			$addnum = strval ( (intval ( $addnum )+1) );//对应条码需要添加的尾数+1
				if (strlen ( $addnum ) == 1) { //当仅有个位时，补0 至4位整数
				$addnum = '000' . $addnum; ///*处理分箱号+1，自动补0
			} elseif (strlen ( $addnum ) == 2) {
				$addnum = '00' . $addnum;			
			} elseif (strlen ( $addnum ) == 3) {
				$addnum = '0' . $addnum;
			} else {
				$addnum = $addnum;
			}
			$zjzxs =0;				
			}
		}
		/******************* 整件拣货操作 结束 *************************/	
		
		/******************* 零散拣货操作 开始 *************************/		
			 $model->insterlszzx($xshdbh);      //保存零散拣货 周转箱信息
	
			foreach ( $_POST ["#grid_fxmingxi"] as $row ) {							
				foreach ($tem_list as $num => $nuvalue){
				if($num['FENXIANGHAO']=='9999')continue;
				if($tem_list[$num]['CHSDCHK']=='Z')continue;
					if($tem_list[$num]['FENXIANGHAO']== $row [$this->idx_FENXIANGHAO]){   //如果当前数组的分箱号等于箱列表grid的分箱号
						$tem_list[$num]['ZHZHXH'] = $row [$this->idx_ZHZHXH];			  //当前输入的周转箱号
					$sals_resu = $model->insterlszzxmx($tem_list[$num],$xshdbh);      //保存零散拣货 周转箱明细信息
					if(!$sals_resu){
						$result['status'] = '3';         //保存零散拣货错误
					}
					}else{
						continue;
					}
								
				//获取暂存区			
				$resu_zcq = $model->getzcqbh($tem_list[$num]);       //暂存区编号				
				if($resu_zcq['0']['FJZCQBH'] == null){
				$resu_zcq = $model->getnewzcqbh($tem_list[$num]);      		 //如果暂存区编号为空,重新取暂存区编号			
				}
				$salszzxx_resu = $model->insterlszzxx($tem_list[$num],$resu_zcq,$xshdbh);     //零散拣货周转箱 传送带口信息插入数据    暂存区编号	
				if(!$salszzxx_resu){
				$result['status'] = '4';				 //保存零散拣货周转箱
				}
				$updata_resu = $model->updatazcq($tem_list[$num],$resu_zcq);				 //更新对应暂存区状态
				if(!$updata_resu){
						$result['status'] = '5';         //更新暂存区状态错误
					}
				}			
			}
			$model->commit ();
		/******************* 零散拣货操作 结束 *************************/		
		}catch( Exception $e){
		//回滚
			$model->rollBack ();
     		throw $e;
		}
		echo json_encode($result);    //返回结果集
			
		
	}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}