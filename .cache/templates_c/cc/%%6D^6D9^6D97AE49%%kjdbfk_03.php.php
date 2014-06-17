<?php /* Smarty version 2.6.26, created on 2011-05-04 10:33:55
         compiled from kjdbfk_03.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kjdbfk_03.js"></script>
</head>
<body>
<form name="form1" id="form1" style="display:inline;margin:0px;">
   <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>

    </div>
    <div id="body">

           <input type="hidden" id="djbh" name="djbh" value="<?php echo $this->_tpl_vars['djbh']; ?>
">
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="#gridbox" style="width:100%;height:240px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div> 
</form>
</body>
</html>