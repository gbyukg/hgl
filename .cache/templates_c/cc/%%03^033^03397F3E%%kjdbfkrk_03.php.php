<?php /* Smarty version 2.6.26, created on 2011-05-27 18:04:35
         compiled from kjdbfkrk_03.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kjdbfkrk_03.js"></script>
</head>
<body>
    <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0" >
               <tr><td class="title"><?php echo $this->_tpl_vars['title']; ?>
</td>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>

    </div>
    <div id="vspace"></div>
    <div id="body">
               <table width="100%" cellpadding="0" cellspacing="1" class="form">
                                <tr>
                                    <th width="80px">
                                                                                                    仓库编号
                                    </th>
                                     <td width="120px">
                                     <span id="CKBH"><?php echo $this->_tpl_vars['rec']['CKBH']; ?>
</span>
                                     </td>
                                     <th width="80px">
                                                                                                    仓库名称
                                   </th>
                                     <td>
                                     <span id="CKMCH"><?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
</span>
                                     </td>
                                </tr>
                                                                <tr>
                                    <th width="80px">
                                                                                                    仓库地址
                                    </th>
                                     <td colspan=3 width="200px">
                                     <span id="CKDZ"><?php echo $this->_tpl_vars['rec']['DIZHI']; ?>
</span>
                                     </td>
                                </tr>

                </table>
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:245px;background-color:white;"></div>
            <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div>
        
        
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
    </div>
       

</body>
</html>