<?php
/****
**业务配置数据
**
**/

//备份数据库时标准备份的数据表名(不需要带表前缀)
$dbback_tlist = array('admin_logs','menu','role_menu','sys_config','sys_role','sys_role_user','sys_user');

$_SESSION["dbback_tlist"] = $dbback_tlist;

//图片上传的模认文件夹
@define('IMAGES_UPLOAD_DIR', "cache/upload/images");

//新闻模块上传的文件夹
@define('IMAGES_MODNEWS_DIR', IMAGES_UPLOAD_DIR."/modnews");

//flash图片上传的文件夹
@define('IMAGES_FLASH_DIR', "web/themes/default/images/swf");

//商品图片上传的文件夹
@define('IMAGES_PRODUCT_DIR', "cache/upload/product");
