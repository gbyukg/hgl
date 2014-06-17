<?php /* Smarty version 2.6.26, created on 2011-06-07 18:10:37
         compiled from fxwh_01.php */ ?>
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
/js/fxwh_01.js"></script>
</head>

<body>
	 <div id="top">
         <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td class="title"><?php echo $this->_tpl_vars['title']; ?>
</td>
              <td><div id="toolbar"></div></td>
            </tr>
         </table>
	 </div>
	 <div id="vspace"></div>
	 <div id="body">
	  <form name="form1" id="form1" style="display:inline;margin:0px;">
		  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
		     <tr>
		     	<td width="100px">查询信息</td>
		     	 <td></td>
		     </tr>
		  </table>
		<table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0">
      <td width="95px"></td>
      <td width="125px"></td>
      <td width="95px"></td>
      <td width="125px"></td>
      <td width="95px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td></td>
    </tr>
		  		<tr>
				    <th >分箱日期从:</th>
				    <td >
				        <input id= "FXRQC" name="FXRQC" type="text" style="width: 115px" readonly class="editable" value="<?php echo $this->_tpl_vars['kprqc']; ?>
"/>
				       
				    </td> 
				    <th >分箱日期到:</th>
				    <td colspan="1">
				        <input id= "FXRQD" name="FXRQD" type="text" style="width: 115px" readonly class="editable" value="<?php echo $this->_tpl_vars['kprqd']; ?>
"/>
				        
				    </td>
				    
				
			   <td >    
					<input id="XSHDZHT" name="XSHDZHT" type="checkbox" />仅显示未出库
				</td>
				    
				    <th style="text-aling:left" colspan="5">
				    	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />&nbsp;
				    	<img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
				    </th>
				    <input type="hidden" id="userid" name="userid" value="<?php echo $this->_tpl_vars['userid']; ?>
"/>
				</tr>
	      </table>
      </form>
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">拣货信息</td>
	     </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
            <tr>
	            <td>
		            <div id="#grid_danju" style="width:100%;height:300px;background-color:white;"></div>	
		             <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>	           
	            </td>
            </tr>
	  </table>
	  
	 </div>

 	
</body>
</html>