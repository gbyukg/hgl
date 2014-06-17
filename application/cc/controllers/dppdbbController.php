<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   动碰盘点报表(dppdbb)
 * 作成者：李洪波
 * 作成日：2011/01/13
 * 更新履历
 *********************************/
class cc_dppdbbController extends cc_controllers_baseController {
        /*
        * 客户特价初始页面
        */
        public function indexAction()
        {
               $this->_view->assign ( 'action', 'new' ); 
               $this->_view->assign ( "title", "仓储管理-动碰盘点报表" ); 
               $this->_view->display ( "dppdbb_01.php" );
        }

        /*
        * 获取表格信息
        */
        public function getlistdataAction() {
                //取得列表参数
                $filter ['kshrq'] = $this->_getParam ( "kshrq", '' ); //检索条件_开始日期
                $filter ['jshrq'] = $this->_getParam ( "jshrq", '' ); //检索条件_结束日期
                $filter ['ckbh'] = $this->_getParam ( "ckbh", '' ); //检索条件_仓库编号
                $filter ['kqbh'] = $this->_getParam ( "kqbh", '' ); //检索条件_库区编号
                $filter ['shuliang'] = $this->_getParam ( "shuliang", '' ); //检索条件_账面数量条件
                $filter ['orderby'] = $this->_getParam ( "orderby", '' ); //检索条件_排序字段
                $filter ['direction'] = $this->_getParam ( "direction", '' ); //检索条件_升序降序
                $model = new cc_models_dppdbb();
                header ( "Content-type:text/xml" ); //返回数据格式xml
                echo $model->getGridData ( $filter );
        }
}
