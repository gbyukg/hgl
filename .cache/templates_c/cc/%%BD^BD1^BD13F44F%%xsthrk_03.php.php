<?php /* Smarty version 2.6.26, created on 2011-10-10 13:44:08
         compiled from xsthrk_03.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xsthrk_03.js"></script>
</head>
<body>
   <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
               	                  <td  class="title">
	                    <?php echo $this->_tpl_vars['title']; ?>


	                  </td>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>

    </div>
    <div id="vspace"></div>
    <div id="body">

           <input type="hidden" id="thdbh" name="thdbh" value="<?php echo $this->_tpl_vars['GET']['thdbh']; ?>
">
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:240px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div>
       

</body>
</html>