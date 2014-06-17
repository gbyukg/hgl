<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：    零散复检打包(lsfjdb)
 * 作成者：    李洪波
 * 作成日：    2011/05/19
 * 更新履历：
 **********************************************************/
class cc_models_lsfjdb extends Common_Model_Base {
                private $idx_ROWNUM=0;             // 行号
                private $idx_SHPBH =1;          // 商品编号
                private $idx_SHPMCH=2;      // 商品名称
                private $idx_SHULIANG=3;    // 数量
                private $idx_PIHAO=4;              // 批号
                private $idx_ZHZHXBH=5;            // 周转箱编号
                private $idx_GUIGE =6;            // 商品规格
                private $idx_DWMCH=7;       // 单位名称
                private $idx_DWBH=8;            // 单位编号
                private $idx_CKBH=9;        // 仓库编号
                private $idx_CWZHL=10;      // 错误种类
        /**
         * 生成零散复检打包信息
         * @param array $filter
         * @return string xml
         */
        public function getGridData($filter){
                //在【封箱后箱子列表】GRID中加入1条记录：
                //检索SQL
                $sql = "SELECT ZHXBH".
                           " FROM H01DB012475 ".
                           " WHERE QYBH = :QYBH AND DJBH =:DJBH";
 
                $bind ['DJBH'] = $filter ['ddbh'];           //订单编号
                $bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                $recs=$this->_db->fetchRow( $sql, $bind );
                if($recs==false){
                        $data ['ZHXBH']=1;
                        $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $data ['DJBH'] = $filter ['ddbh'];           //订单编号
 
                        $this->_db->insert ("H01DB012475", $data);
                }else{
                        $updateSql="UPDATE H01DB012475 SET ZHXBH=:ZHXBH".
                                                                        " WHERE QYBH=:QYBH AND DJBH=:DJBH ";
                        $data['ZHXBH']=$recs['ZHXBH']+1;
                        $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $data ['DJBH'] = $filter ['ddbh'];           //订单编号
                        $this->_db->query($updateSql,$data);
 
                }
                
                for ($i=0;$i < count($_POST ["#grid_shangpin"])-1;$i++){
 
                        $ZHIXIANG[$i]['SHPBH'] =$_POST ["#grid_shangpin"][$i][$this->idx_SHPBH];
                        $ZHIXIANG[$i]['PIHAO'] = $_POST ["#grid_shangpin"][$i][$this->idx_PIHAO];
                        $ZHIXIANG[$i]['SHULIANG'] = $_POST ["#grid_shangpin"][$i][$this->idx_SHULIANG];
                        $ZHIXIANG[$i]['ZHZHXH'] = $_POST ["#grid_shangpin"][$i][$this->idx_ZHZHXH]; 
                        $ZHIXIANG[$i]['GUIGE'] =$_POST ["#grid_shangpin"][$i][$this->idx_GUIGE];
                        $ZHIXIANG[$i]['BZHDWBH'] =$_POST ["#grid_shangpin"][$i][$this->idx_DWBH];
                        $ZHIXIANG[$i]['CKBH'] = $_POST ["#grid_shangpin"][$i][$this->idx_CKBH]; //仓库编号 
                        $ZHIXIANG[$i]['ZHXBH'] =count($_POST ["#grid_xiang"]);        
                        $ZHIXIANG[$i]['XUHAO'] =$_POST ["#grid_shangpin"][$i][$this->idx_ROWNUM]; 
 
                }
                $_SESSION['ZHIXIANG']=$ZHIXIANG;
                if (strlen ($recs['ZHXBH']) == 1) { //当仅有个位时，补0 至4位整数 
                                                $recs['ZHXBH'] = '000' . $recs['ZHXBH']; ///*处理分箱号+1，自动补0
                                        } elseif (strlen ( $recs['ZHXBH']) == 2) {
                                                $recs['ZHXBH'] = '00' .$recs['ZHXBH'];
                                        } elseif (strlen ( $recs['ZHXBH'] ) == 3) {
                                                $recs['ZHXBH'] = '0' . $recs['ZHXBH'];
                                        }
                $rec['DYTM']=$filter ['ddbh']."0000".$recs['ZHXBH'];
                return $rec;
        }
 
        /*
         * 根据商品编号取得商品信息
         */
        public function getShangpinInfo($filter) {
 
                //检索SQL
                $sql = "SELECT " .
                       "SHPBH," . //商品编号
                       "SHPMCH," . //商品名称
                       "BZHDWMCH,BZHDWBH,GUIGE".
                           " FROM H01VIEW012101 ".
                           " WHERE QYBH = :QYBH AND SHPBH =:SHPBH";
 
                $bind ['SHPBH'] = $filter ['shpbh']; //商品编号
                $bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                return $this->_db->fetchRow ( $sql, $bind );
        } 
 
        /*
        * 通过数量返回相关信息*
        */
        public function getShuliangInfo($filter) {
 
                $ZHZHXSHP=$_SESSION ['ZHZHXSHP'];
                $flagShpbh=true;
                $flagPihao=true;
                $flagShuL=true; 
                for ($i=0;$i<count($ZHZHXSHP);$i++){
                        if($ZHZHXSHP[$i]['SHPBH']==$filter['shpbh']){
                                $flagShpbh=false;
                                break;
                        }
                    if($ZHZHXSHP[$i]['PIHAO']==$filter['pihao']){
                                $flagPihao=false;
                                break;
                        }
                    if($ZHZHXSHP[$i]['SHULIANG']==$filter['shuliang']){
                                $flagShuL=false;
                                break;
                        }
                }
                if($flagShpbh){
                       return 1;
                }
                if($flagPihao){
                       return 2;
                }
                if($flagShuL){
                       return 3;
                }
                if($flagShpbh==false and $flagPihao==false and $flagShuL==false){
                        for ($i=0;$i<count($ZHZHXSHP);$i++){
                             $ZHZHXSHP[$i]['ZHUANGTAI']=1;
                    }
                    $_SESSION ['ZHZHXSHP']=$ZHZHXSHP;
                }
        } 
 
        /**
         * 判断订单编号是否存在
         */ 
        public function getDingDanBH($zhzhxh){
 
                $sql ="SELECT DJBH FROM (SELECT A.DJBH FROM H01DB012433 A WHERE A.QYBH =:QYBH AND 
                           A.ZHZHXH =:ZHZHXH AND A.ZHUANGTAI ='2' ORDER BY A.FXRQ DESC) WHERE ROWNUM <= '1' ORDER BY ROWNUM DESC";
                $bind ['QYBH'] = $_SESSION ['auth']->qybh;
                $bind ['ZHZHXH'] = $zhzhxh;
                return $this->_db->fetchRow( $sql, $bind );
        }
        
        /**
         *  判断匹配状态是否是1
         */ 
        public function getShPxx($zhzhxh,$djbh){
        
                $flag=true;
                //检索SQL
                $sql = " SELECT A.SHPBH,A.SHULIANG,A.PIHAO,A.DJBH,A.ZHZHXH,A.ZHUANGTAI,B.BZHDWBH,B.GUIGE,A.KWBH,A.CKBH".
                           " FROM H01VIEW012434 A".
                           " LEFT JOIN H01DB012101 B ON A.QYBH = B.QYBH AND A.SHPBH = B.SHPBH".
                           " WHERE A.QYBH=:QYBH AND A.ZHZHXH=:ZHZHXH AND A.DJBH =:DJBH";
 
                //绑定查询条件
                $bind ['QYBH'] = $_SESSION ['auth']->qybh;
                $bind ['ZHZHXH'] = $zhzhxh;
                $bind ['DJBH'] = $djbh;
                //排序
                $sql .= " ORDER BY A.SHPBH";
                $ZHZHXSHP=$this->_db->fetchAll( $sql, $bind );
                $_SESSION ['ZHZHXSHP']=$ZHZHXSHP;
                if($ZHZHXSHP==false){
                        return false;
                } 
                for ($i=0;$i<count($ZHZHXSHP);$i++){
                        if($ZHZHXSHP[$i]['ZHUANGTAI']==0 or $ZHZHXSHP[$i]['ZHUANGTAI']!=1){
                                $flag=false;
                                break;
                        }
                }
 
                return $flag;
        }
 
        /**
         * 挂起信息
         */
        public function guaqilsfjdb(){
 
                //（1）将【待封箱商品】GRID中的数据存放入挂起处理中纸箱商品信息（H01DB012448）中。
                foreach ( $_POST ["#grid_shangpin"] as $grid ) {
 
                        $bind ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bind ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                        $bind ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                        $bind ['DJBH'] = $_POST['DDBH'];           //订单编号
                        $bind ['XUHAO'] = $grid [$this->idx_ROWNUM];
                        $bind ['SHPBH'] = $grid [$this->idx_SHPBH];
                        $bind ['PIHAO'] = $grid [$this->idx_PIHAO];
                        $bind ['SHULIANG'] = $grid [$this->idx_SHULIANG];
                        $bind ['ZHZHXH'] = $grid [$this->idx_ZHZHXBH]; 
                        $bind ['KWBH'] = null;
                        $bind ['GUIGE'] = $grid [$this->idx_GUIGE];
                        $bind ['BZHDWBH'] = $grid [$this->idx_DWBH];
                        $bind ['CWZHL'] = $grid [$this->idx_CWZHL]; 
 
                        $this->_db->insert ("H01DB012448", $bind);
 
                }
                //（2）新规复核打包台挂起信息（H01DB012452）信息。
                $bindNew ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                $bindNew ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                $bindNew ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                $bindNew ['DJBH'] = $_POST['DDBH'];           //订单编号
                $bindNew ['SHYXSH'] =100;// $_POST['BDSHYXSH'];
                $bindNew ['ZCHZH'] = $_SESSION ['auth']->userId;         //作成者
                $bindNew ['ZCHRQ'] = new Zend_Db_Expr ( 'sysdate' ); //作成日期
                $this->_db->insert ("H01DB012452", $bindNew);
 
        //（3）将当前【已处理周转箱】GRID中的信息存放入挂起信息处理完成周转箱一览（H01DB012453）中。
                foreach ( $_POST ["#grid_zhzhx"] as $grid ) {
                        $bindchl ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindchl ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                        $bindchl ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                        $bindchl ['DJBH'] = $_POST['DDBH'];           //订单编号
                        $bindchl ['WCHZHZHXH'] = $grid [$this->idx_SHPBH]; 
                        
                        $this->_db->insert ("H01DB012453", $bindchl);
                }
 
        //（4）将当前【封箱后箱子列表】GRID中的信息存放入挂起处理完成纸箱信息（H01DB012454）中。 
                foreach ( $_POST ["#grid_xiang"] as $grid ) {
                        $bindfx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindfx ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                        $bindfx ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                        $bindfx ['DJBH'] = $_POST['DDBH'];           //订单编号
                        $bindfx ['ZHXBH'] = $grid [$this->idx_ROWNUM];//纸箱编号
                        $bindfx ['DYTM'] = $grid [$this->idx_SHPBH]; //对应条码
                        $bindfx ['ZHUANGTAI'] = null;                //状态
 
                        $this->_db->insert ("H01DB012454", $bindfx);
                } 
        //（5）将当前二维数组ZHIXIANG中的信息存放入挂起处理完成纸箱商品信息（H01DB012455）中。
                $ZHIXIANG=$_SESSION['ZHIXIANG']; 
                for ($i=0;$i<count($ZHIXIANG);$i++){
                
                        $bindzhx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindzhx ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                        $bindzhx ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                        $bindzhx ['DJBH'] = $_POST['DDBH'];           //订单编号ZHXBH
                        $bindzhx ['ZHXBH'] = $ZHIXIANG[$i]["ZHXBH"];  //纸箱编号
                        $bindzhx ['XUHAO'] = $ZHIXIANG[$i]["XUHAO"];
                        $bindzhx ['SHPBH'] = $ZHIXIANG[$i]["SHPBH"];
                        $bindzhx ['PIHAO'] = $ZHIXIANG[$i]["PIHAO"];
                        $bindzhx ['SHULIANG'] = $ZHIXIANG[$i]["SHULIANG"];
                        $bindzhx ['ZHZHXH'] = $ZHIXIANG[$i]["ZHZHXH"];        
                        $bindzhx ['KWBH'] = null;
                        $bindzhx ['GUIGE'] = $ZHIXIANG[$i]["GUIGE"];
                        $bindzhx ['BZHDWBH'] = $ZHIXIANG[$i]["BZHDWBH"];
                        $bindzhx ['CWZHL'] =null; 
 
                        $this->_db->insert ("H01DB012455", $bindzhx);
 
                } 
                //（6）将当前二维数组ZHZHXSHP中的信息存放入挂起处理中周转箱商品信息（H01DB012456）中。
                $ZHZHXSHP =$_SESSION['ZHZHXSHP']; 
                for ($j=0;$j<count($ZHZHXSHP);$j++){
 
                        $bindzhzhxshp ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindzhzhxshp ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号 
                        $bindzhzhxshp ['DQZHZHXH'] = $_POST['DQZZX'];      //当前周转箱号
                        $bindzhzhxshp ['DJBH'] = $_POST['DDBH'];           //订单编号ZHXBH
                        $bindzhzhxshp ['ZHXBH'] = $ZHZHXSHP[$j]["ZHXBH"];  //纸箱编号ZHUANGTAI
                        $bindzhzhxshp ['XUHAO'] = $ZHZHXSHP[$j]["XUHAO"];
                        $bindzhzhxshp ['SHPBH'] = $ZHZHXSHP[$j]["SHPBH"];
                        $bindzhzhxshp ['PIHAO'] = $ZHZHXSHP[$j]["PIHAO"];
                        $bindzhzhxshp ['SHULIANG'] = $ZHZHXSHP[$j]["SHULIANG"];
                        $bindzhzhxshp ['ZHZHXH'] = $ZHZHXSHP[$j]["ZHZHXH"]; 
                        $bindzhzhxshp ['KWBH'] = null;
                        $bindzhzhxshp ['GUIGE'] = $ZHZHXSHP[$j]["GUIGE"];
                        $bindzhzhxshp ['BZHDWBH'] = $ZHZHXSHP[$j]["BZHDWBH"];
                        $bindzhzhxshp ['ZHUANGTAI'] = $ZHZHXSHP[$j]["ZHUANGTAI"];
                        $bindzhzhxshp ['CWZHL'] =null; 
 
                        $this->_db->insert ("H01DB012456", $bindzhx);
 
                }
                return 1;
        }
        /**
         * 保存信息
         */
        public function savelsfjdb(){
 
                //（1）检索二维数组ZHZHXSHP中所有商品的匹配状态是否都是1:已匹配。 
                //如果不都是1，则提示当前周转箱还有其他商品未封箱，不能保存。 
                //如果都是1，将当前周转箱号加入到【已处理周转箱】GRID中，并执行事务。
                $ZHZHXSHP =$_SESSION['ZHZHXSHP']; 
                for ($i=0;$i<count($ZHZHXSHP);$i++){
                        if($ZHZHXSHP[$i]['ZHUANGTAI']!=1){
                                return false;
                        }
                }
              
                //事务开始
                //（2）将【封箱后箱子列表】GRID中的信息存入处理完成纸箱信息（H01DB012457）表中。
                foreach ( $_POST ["#grid_xiang"] as $grid ) {
                        $bindfx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindfx ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号
                        $bindfx ['DJBH'] = $_POST['DDBH'];           //订单编号
                        $bindfx ['ZHXBH'] = $grid [$this->idx_ROWNUM];//纸箱编号
                        $bindfx ['DYTM'] = $grid [$this->idx_SHPBH]; //对应条码
                        $bindfx ['ZHUANGTAI'] = null;                //状态

                        $this->_db->insert ("H01DB012457", $bindfx);
                }
            //（3）将二维数组ZHIXIANG中的信息存入处理完成纸箱中商品信息（H01DB012458）表中。
            $ZHIXIANG=$_SESSION['ZHIXIANG'];        
            for ($i=0;$i<count($ZHIXIANG);$i++){

                        $bindzhx ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                        $bindzhx ['CKBH'] = $_SESSION ['auth']->ckbh; //仓库编号
                        $bindzhx ['DJBH'] = $_POST['DDBH'];           //订单编号ZHXBH
                        $bindzhx ['ZHXBH'] = $ZHIXIANG[$i]["ZHXBH"];  //纸箱编号
                        $bindzhx ['XUHAO'] = $ZHIXIANG[$i]["XUHAO"];
                        $bindzhx ['SHPBH'] = $ZHIXIANG[$i]["SHPBH"];
                        $bindzhx ['PIHAO'] = $ZHIXIANG[$i]["PIHAO"];
                        $bindzhx ['SHULIANG'] = $ZHIXIANG[$i]["SHULIANG"];
                        $bindzhx ['ZHZHXH'] = $ZHIXIANG[$i]["ZHZHXH"];        
                        $bindzhx ['KWBH'] = null;
                        $bindzhx ['GUIGE'] = $ZHIXIANG[$i]["GUIGE"];
                        $bindzhx ['BZHDWBH'] = $ZHIXIANG[$i]["BZHDWBH"];

                        $this->_db->insert ("H01DB012458", $bindzhx);

                }
            //（4）将零散拣货分周转箱信息（H01DB012433）中的对应信息状态改为3:已打包。
            //事务结束
                $sql="UPDATE H01DB012433 SET ZHUANGTAI=3".
                                                                        " WHERE QYBH=:QYBH AND DJBH=:DJBH AND ZHZHXH=:ZHZHXH ";

                $data ['QYBH'] = $_SESSION ['auth']->qybh; //区域编号
                $data ['DJBH'] = $_POST['DDBH'];           //订单编号
                $data ['ZHZHXH'] = $_POST['DQZZX'];        //周转箱编号
                $this->_db->query($sql,$data);

                return true;

        }
}