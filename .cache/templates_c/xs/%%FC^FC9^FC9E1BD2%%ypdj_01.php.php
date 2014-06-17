<?php /* Smarty version 2.6.26, created on 2011-04-15 10:33:51
         compiled from ypdj_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title>一品多价选择</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxcommon.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtoolbar.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgrid.js"></script>     
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgridcell.js"></script> 
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_start.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/ypdj_01.js"></script>
</head>
<body>
     <input type="hidden" id="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
">
    <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>

    </div>
    <div id="vspace"></div>
    <div id="body">
    <input type="hidden" id="DWBH" value="<?php echo $this->_tpl_vars['dwbh']; ?>
" />
    <input type="hidden" id="SHPBH" value="<?php echo $this->_tpl_vars['shpbh']; ?>
" />

               <table width="100%" cellpadding="0" cellspacing="1" class="form">
                                <tr>
                                    <th width="80px">
                                                                                                    商品编号
                                    </th>
                                     <td width="120px">
                                     <span id="SHPBH"><?php echo $this->_tpl_vars['shpbh']; ?>
</span>
                                     </td>
                                     <th width="80px">
                                                                                                    商品名称
                                   </th>
                                     <td>
                                     <span id="SHPMCH"><?php echo $this->_tpl_vars['shpmch']; ?>
</span>
                                     </td>
                                </tr>

                </table>
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:245px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div>
        
        
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
    </div>
       

</body>
</html>