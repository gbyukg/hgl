<?php /* Smarty version 2.6.26, created on 2011-05-30 10:35:49
         compiled from kjdbfkwh_01.php */ ?>
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
/js/kjdbfkwh_01.js"></script>
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
	  <form name="form1" id="form1" >
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
      <td width="65px"></td>
      <td width="125px"></td>
      <td width="95px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td></td>
    </tr>
		  		<tr>
				    <th >调拨返库单据号:</th>
				    <td >
				        <input id= "DBRKD"  name="DBRKD" type="text" style="width: 90%" class="editable"/>
				    </td>
				    <th >调出仓库:</th>
				    <td >
				        <input id= "DCCK" name="DCCK" type="text" style="width: 90%" readonly class="editable"/>

				    </td> 
				    <th >调入仓库:</th>
				    <td >
				        <input id= "DRCK" name="DRCK" type="text" style="width: 90%" readonly class="editable"/>

				    </td>
				    <th style="text-aling:left">
				    	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
				    	<img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
				    </th>
				    <td colspan="1"></td>
				</tr>
				<tr>
				    <th >开票日期从:</th>
				    <td >
				        <input id= "KSRQKEY" name="KSRQKEY" type="text" style="width: 90%"class="editable"/>
				    </td>
				    <th >开票日期到:</th>
				    <td >
				        <input id= "ZZRQKEY" name="ZZRQKEY" type="text" style="width: 90%" class="editable"/>
				    </td>
				    <th >对应调拨出库单:</th>
				    <td >
				        <input id= "DBCKD" name="DBCKD" type="text" style="width: 90%" readonly class="editable"/>
				    </td>
				    <td colspan="2"></td>
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
		            <div id="grid_danju" style="width:100%;height:300px;background-color:white;"></div>
		            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
	            </td>
            </tr>
	  </table>
	 </div>
 	 <div id="loader" style="z-index:999;position:absolute;left:200px;top:200px;display:none">
            <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
   	 </div>
</body>
</html>