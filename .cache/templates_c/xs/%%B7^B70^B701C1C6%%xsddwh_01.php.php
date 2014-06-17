<?php /* Smarty version 2.6.26, created on 2011-05-13 15:34:25
         compiled from xsddwh_01.php */ ?>
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
/js/xsddwh_01.js"></script>
</head>
<body >
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
	   <table width="100%" cellpadding="0" cellspacing="1" class="search">
   		<tr height="0">
		      <td width="85px"></td>
		      <td width="125px"></td>
		      <td width="85px"></td>
		      <td width="125px"></td>
		      <td width="120px"></td>
		      <td width="60px"></td>
		      <td width="60px"></td>
		      <td></td>
	    </tr>
        <tr>
            <th>开始日期：</th>
            <td>
                <input id="KSRQ" name="KSRQ" type="text" style="width: 115px"class="editable"/>
            </td>
            <th>终止日期：</th>
            <td>
                <input type="text" id="ZZRQ" name="ZZRQ" style="width: 115px" class="editable"/>
            </td>
			<th>
                <input id="SHSJ" name="SHSJ" type="checkbox" ><label for="SHSJ">包括已审核数据</label>
            </th>
            <td></td>
            <td class="button" style="width: 60px">
		        <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
		    </td>
		    <td class="button" style="width: 60px">
		        <img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
        </tr>
        <tr>
            <th>单位编号：</th>
            <td>
                <input id="DWBH" name="DWBH" type="text" style="width: 115px" class="editable"/>
            </td>
            <th>单位名称：</th>
            <td colspan="2">
                <input id="DWMCH" name="DWMCH" type="text" style="width: 230px" readonly class="readonly"/>
            </td>
            <td></td>
        </tr>
       </table>
    </form>
	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	    <tr>
	     	<td width="100px">单据信息</td>
	    </tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="grid">
	   <tr>
	       <td>
		       <div id="#grid_main" style="width:100%;height:170px;background-color:white;"></div>
	       </td>
       </tr>
 	</table>
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
          <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
    </div>
	<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	    <tr>
	     	<td width="100px">明细信息</td>
	    </tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="1" class="grid">
	    <tr><td>
	         <div id="#grid_mingxi" style="width:100%;height:170px;background-color:white;"></div>
	    </td></tr>
	</table>
</div>	 

</body>
</html>