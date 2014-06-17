<?php /* Smarty version 2.6.26, created on 2011-04-27 13:04:51
         compiled from kjdbfk_02.php */ ?>
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
/js/kjdbfk_02.js"></script>

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
	   <table width="100%" cellpadding="0" cellspacing="1" class="search">
        <tr>
            <th width="10%">
                开始日期：
            </th>
            <td width="20%">
                <input id="SERCHKSRQ" name="SERCHKSRQ" type="text" style="width: 90%"class="editable"/>
            </td>
            <th width="10%">
                终止日期：

            </th>
           <td width="20%">
                <input type="text" id="SERCHJSRQ" name="SERCHJSRQ" style="width: 90%" class="editable"/>
            </td>

            <td class="button">
		        <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /><img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
            <td>
            </td>
        </tr>
        <tr>
            <th width="10%">
                调出仓库：
            </th>
            <td width="20%">
                <input id="DCCK" name="DCCK" type="text"  style="width: 90%" class="editable"/>
                <input id="DCCKBH" name="DCCKBH" type="hidden"  style="width: 90%" class="editable"/>
                
            </td>
            <th width="10%">
               调入仓库：
            </th>
           <td width="20%" colspan="2">
                <input type="text"  id="DRCK" name="DRCK" style="width:253px"  class="editable"  />
                <input id="DRCKBH" name="DRCKBH" type="hidden"   class="editable"/>
            </td>
            
            
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
		       <div id="#grid_main" style="width:100%;height:150px;background-color:white;overflow:auto"></div>
		     
	       </td>
       </tr>
 	</table>
</div>
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
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
	            <div id="#grid_mingxi" style="width:100%;height:100px;background-color:white;overflow:auto"></div>
	      
	            </td></tr>
	  </table>
	 
	 
	</body>
	</html>