<?php /* Smarty version 2.6.26, created on 2011-05-09 11:06:16
         compiled from cangku_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/cangku_01.js"></script>
</head>
<body>
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
    <form name="form1" id="form1">
     <input type="hidden" id="flg" name="flg" value="<?php echo $this->_tpl_vars['GET']['flg']; ?>
">
               <table width="100%" cellpadding="0" cellspacing="0" class="search">
                                <tr>
                                    <th width="120px">
                                     仓库编号/仓库名称

                                    </th>
                                     <td width="200px">
                                        <input type="text" name="SEARCHKEY" id="SEARCHKEY" class="editable" style="width:180px" value="<?php echo $this->_tpl_vars['searchkey']; ?>
" />
                                    </td>
                                    <td><img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /</td>
                                </tr>
                </table>
    </form>
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:245px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            </td></tr>
            </table>
        </div>
</body>
</html>