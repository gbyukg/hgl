<?php
//引入系统路径文件
include_once ('path.php');
require_once('Zend/Loader/Autoloader.php');

Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
Zend_Session::start();

//系统配置文件载入
Common_Model_Application::loadConfiguration();

//常用常量设置
Common_Model_Application::loadConst();

//日志文件
Common_Logger::setLoggerWriter();

//初始化Controller
Common_Model_Application::loadController();

//载入并设置数据库连接
Common_Model_Application::loadDB();

//Smarty模板载入
Common_Model_Application::loadSmarty();