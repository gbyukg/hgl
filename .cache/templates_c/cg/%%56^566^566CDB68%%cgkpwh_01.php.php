<?php /* Smarty version 2.6.26, created on 2011-05-30 15:41:00
         compiled from cgkpwh_01.php */ ?>
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
/js/cgkpwh_01.js"></script>

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
	  <form name="form1" id="form1" style="display:inline;margin:0px;">
	  <table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
    <tr height="0">
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="125px"></td>
    
    </tr>
        <tr>
            <th >
                开始日期：
            </th>
            <td >
                <input id="SERCHKSRQ" name="SERCHKSRQ" type="text" style="width: 115px"class="editable"/>
            </td>
            <th >
                终止日期：

            </th>
           <td >
                <input type="text" id="SERCHJSRQ" name="SERCHJSRQ" style="width: 115px" class="editable"/>
            </td>

            <td class="button">
		        <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /><img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
             
        </tr>
        <tr>
            <th >
                单位编号：
            </th>
            <td >
                <input id="SERCHDWBH" name="SERCHDWBH" type="text"  style="width: 115px" class="editable"/>
            </td>
            <th >
                单位名称：
            </th>
           <td>
              <span id="SERCHDWMCH" name="SERCHDWMCH"  class="span_normal" style="width: 115px">
            </td>
            <td colspan="2"></td>
            
        </tr>
      </table>
      </form>
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