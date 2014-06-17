<?php /* Smarty version 2.6.26, created on 2011-04-22 14:45:50
         compiled from sysp_01.php */ ?>
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
/js/sysp_01.js"></script>
</head>
<body>
  <div id="top">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="title" style="width:230px;">
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
  <form name="form1" id="form1" style="display:inline;margin:0px;">
    <table width="100%" cellpadding="0" cellspacing="0" class="search">
		<tr>
		    <th width="160px">
		             商品编号/商品名称/助记码

		    </th>
		    <td width="200px">
		        <input id="SHPBH" name="SHPBH" type="text" maxlength="50" style="width:180px" class="editable"/>
		    </td>		    
		    <td width="200px">
		        <img id="BTNSEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
		        <img id="BTNRESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
		    <td>
		    </td>		    
		</tr>
    </table>
     </form>   
 <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	   <tr>
	       <td>
		       <div id="gridbox" style="width:100%;height:400px;background-color:white;overflow:auto"></div>
               <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
	       </td>
       </tr>
 	</table>
  </div>

</body>
</html>