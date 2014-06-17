<?php /* Smarty version 2.6.26, created on 2011-05-25 17:34:01
         compiled from cgddsh_01.php */ ?>
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
/js/cgddsh_01.js"></script>

</head>
	<body >
<div id="top">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="title"><?php echo $this->_tpl_vars['title']; ?>
</td>
        <td>
        <div id="toolbar"></div>
        </td>
    </tr>
</table>
</div>
<div id="vspace"></div>
	  <div id="body">
	

     
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">
	           单据信息
	     	 </td>
	    </tr>
	  </table>
	  
	      <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	   <tr>
	       <td>
		       <div id="#grid_main" style="width:100%;height:250px;background-color:white;"></div>
		     
	       </td>
       </tr>
 	</table>
 	  <form name="form1" id="form1" style="display:inline;margin:0px;">
 	  
 	   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
         	<th width="100px">审核意见:</th>
         	<td width="450px">
         		<input id= "SHYJ" name="SHYJ" type="text" maxlength="500" style="width: 90%" class="editable"/>
         	</td>
         	<td style="text-aling:left" width="200px">
            	<img id="shyjtg" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_check_yes.gif" />
            	<img id="shyjbtg" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_check_no.gif" />
            </td>
            <td></td>
         </tr>
	  </table>
 	
 	</form>
</div>


	   <div id="body">
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">
	           明细信息
	     	 </td>
	    </tr>
	  </table>
	  </div>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	             <tr><td>
	            <div id="#grid_mingxi" style="width:100%;height:100px;background-color:white;"></div>
	      
	            </td></tr>
	  </table>
	 
	 
	</body>
	</html>