<?php
/************************下面为系统运行时用变量(不可修改)*********************************/

/**
 * 网站根目录
 */
@define('ROOT_PATH', dirname(dirname(__FILE__) . '/'));

/**
 * config 目录
 */
@define('CONF_PATH', ROOT_PATH . DIRECTORY_SEPARATOR.'config');
@define('CONFIGURATION_FILE',CONF_PATH . DIRECTORY_SEPARATOR . 'config.ini');

/**
 * library 目录
 */
@define('LIB_PATH', ROOT_PATH . '/library');

/**
 * APP网站程序主目录
 */
@define('APP_PATH', ROOT_PATH . '/application');


/**
 * 模板存放目录
 */
@define('TEMPLATE_PATH',  ROOT_PATH.'/web');

/**
 * 模板URL
 */
@define('TEMPLATEURL',  'web');

/**
 * 网站缓存数据目录
 */
@define('CACHE_PATH', ROOT_PATH . '/.cache');


/**
 * 模板缓存存放目录
 */
@define('TEMPLATE_C_PATH', CACHE_PATH . '/templates_c');
if (! file_exists(TEMPLATE_C_PATH))
{
    mkdir(TEMPLATE_C_PATH, 0777);
    @chmod(TEMPLATE_C_PATH, 0777);
}

/**
 * 图片上传目录
 */
@define('UPLOAD_PATH', CACHE_PATH . '/upload');

if (! file_exists(UPLOAD_PATH))
{
    mkdir(UPLOAD_PATH, 0777);
    @chmod(UPLOAD_PATH, 0777);
}

/**
 * 日志文件存放目录
 */
@define('LOG_PATH', ROOT_PATH . '/.log');
if (! file_exists(LOG_PATH))
{
    mkdir(LOG_PATH, 0777);
    @chmod(LOG_PATH, 0777);
}




/**
 * 加载引用路径
 *
 */

set_include_path(LIB_PATH.PATH_SEPARATOR.
                 APP_PATH.PATH_SEPARATOR
                 );
