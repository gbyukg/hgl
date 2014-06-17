<?php
/**********************************************************
 * 模     块：    仓储模块(CC)
 * 机     能：  零散复检打包(lsfjdb)
 * 作成者：    李洪波
 * 作成日：    2011/05/19
 * 更新履历：
 **********************************************************/
class cc_lsfjdbController extends cc_controllers_baseController{
        /*
        * 零散复检打包初始页面
        */
        public function indexAction(){
                $this->_view->assign ( "title", "仓储管理-零散复检打包" ); //标题
                $this->_view->display ( "lsfjdb_01.php" );
        }

        /*
        * 查询整件拣货信息 返回xml格式
        */
        public function getlistdataAction(){
				$filter ['ddbh'] = $_POST["DDBH"];   //订单编号
                $model = new cc_models_lsfjdb();
                header ( "Content-type:text/xml" ); //返回数据格式xml
                echo json_encode($model->getGridData( $filter ));
        }
    /**
    * 通过商品编号取得商品相关信息*
    */
        public function getshangpininfoAction()
        {
                $filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
                $model = new cc_models_lsfjdb();
                echo json_encode($model->getShangpinInfo($filter));
        }

    /**
    * 通过数量返回相关信息*
    */
        public function getshlinfoAction()
        {
                $filter ['shpbh'] = $this->_getParam('shpbh');   //商品编号
                $filter ['pihao'] = $this->_getParam('pihao');   //批号
                $filter ['shuliang'] = $this->_getParam('shuliang');   //数量
                $model = new cc_models_lsfjdb();
                echo json_encode($model->getShuliangInfo($filter));
        }
        /*
        * 判断订单编号是否存在
        */
        public function getppddbhAction(){
                $model = new cc_models_lsfjdb();
                
                //判断订单编号是否存在
                $rec = $model->getDingDanBH( $this->_getParam('zhzhxh'));
                if ( $rec == FALSE) {
                        echo json_encode('-1'); //不存在订单号
                }else{
                
                //判断匹配状态是否是1
                $flag = $model->getShPxx( $this->_getParam('zhzhxh'),$rec['DJBH']);
                if ( $flag == FALSE) {
                        echo json_encode('0'); //不匹配
                } else {
                        echo json_encode($rec); //匹配
                }
                }
        }
        /*
        * 保存信息
        */
        public function saveAction() {

                try {
                        $model = new cc_models_lsfjdb();
                        //开始一个事务
                        $model->beginTransaction ();

                        //信息保存
                        $rec=$model->savelsfjdb();

                        //保存成功
                        if($rec ==FALSE){
                              echo json_encode(0);
                        }else{
                              $model->commit ();
                              echo json_encode(1);
                        }
                }
                catch ( Exception $e )
                {
                        //回滚
                        $model->rollBack ();
                        throw $e;
                }

        }
        /*
        * 挂起信息
        */
        public function guaqiAction() {

                try {
                        $model = new cc_models_lsfjdb();
                        //开始一个事务
                        $model->beginTransaction ();

                        //信息保存
                        $model->guaqilsfjdb();

                        $model->commit ();
                       
                }
                catch ( Exception $e )
                {
                        //回滚
                        $model->rollBack ();
                        throw $e;
                }
        }
}