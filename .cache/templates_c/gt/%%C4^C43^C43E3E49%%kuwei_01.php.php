<?php /* Smarty version 2.6.26, created on 2011-05-13 16:01:28
         compiled from kuwei_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kuwei_01.js"></script>
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
    <form name="form1" id="form1" >
         <input type="hidden" id="flg" name="flg" value="<?php echo $this->_tpl_vars['GET']['flg']; ?>
">
         <input type="hidden" id="ckbh" name="ckbh" value="<?php echo $this->_tpl_vars['GET']['ckbh']; ?>
">
         <input type="hidden" id="kqbh" name="kqbh" value="<?php echo $this->_tpl_vars['GET']['kqbh']; ?>
">
                <table width="100%" cellpadding="0" cellspacing="1" class="form">
                                <tr>
                                    <th width="100px">仓库编号</th>
                                     <td width="100px">
                                     <span><?php echo $this->_tpl_vars['GET']['ckbh']; ?>
</span>
                                     </td>
                                     <th width="100px">仓库名称 </th>
                                     <td width="120px">
                                     <span><?php echo $this->_tpl_vars['GET']['ckmch']; ?>
</span>
                                     </td>
                                     <td></td>
                                </tr>
                               <tr>
                                    <th>库区编号 </th>
                                     <td>
                                     <span><?php echo $this->_tpl_vars['GET']['kqbh']; ?>
</span>
                                     </td>
                                     <th>库区名称 </th>
                                     <td>
                                     <span><?php echo $this->_tpl_vars['GET']['kqmch']; ?>
</span>
                                     </td>
                                    <td></td> 
                                </tr>
                                <tr>
                                <th>库位编号/名称</th>
                                <td colspan=2><input type="text" name="SEARCHKEY" id="SEARCHKEY" class="editable" style="width:200px" value="<?php echo $this->_tpl_vars['searchkey']; ?>
" />
                                </td>
                                <td><img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /> </td>
                                <td> </td>
                             
                 
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