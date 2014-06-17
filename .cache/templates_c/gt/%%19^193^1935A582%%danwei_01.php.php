<?php /* Smarty version 2.6.26, created on 2011-05-24 14:19:40
         compiled from danwei_01.php */ ?>
﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/danwei_01.js"></script>
</head>
<body>
     <input type="hidden" id="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
">
     <input type="hidden" id="status" value="<?php echo $this->_tpl_vars['status']; ?>
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
    <form id="form1">
             <input type="hidden" id="flg" name="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
">
             <input type="hidden" id="status" name="status" value="<?php echo $this->_tpl_vars['status']; ?>
">
              <table width="100%" cellpadding="0" cellspacing="0" class="search">
                                <tr>
                                    <th width="130px">
                                     单位编号/名称/注记码                                  </th>
                                     <td width="190px">
                                        <input type="text" id="SEARCHKEY" name="SEARCHKEY" class="editable" style="width:180px" value="<?php echo $this->_tpl_vars['searchkey']; ?>
" />
                                     </td>
                                      <td>
                                    <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
                                     </td>
                                 </tr>
                </table>
             <!----------- 数据列表 ------------------>
             <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:250px;background-color:white;overflow:auto"></div>
            <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
             </td></tr>
            </table>
            </form>
        </div>
 </body>
</html>