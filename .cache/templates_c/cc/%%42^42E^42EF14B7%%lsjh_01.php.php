<?php /* Smarty version 2.6.26, created on 2011-04-15 11:11:45
         compiled from lsjh_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn_bricks.css">
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.hotkeys-modified.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/jquery.autocomplete.css"></link>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxcommon.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtoolbar.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgrid.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgridcell.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_validation.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_nxml.js "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn.js"></script> 
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_splt.js  "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/lsjh_01.js"></script>
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
				        <input id= "FXRQC" name="FXRQC" type="text" style="width: 115px" readonly class="editable"/>
				       
				    </td> 
				    <th >分箱日期到:</th>
				    <td colspan="1">
				        <input id= "FXRQD" name="FXRQD" type="text" style="width: 115px" readonly class="editable"/>
				        
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
	            </td>
            </tr>
	  </table>
	  
	  
	   <div id="loader" style="z-index:999;position:absolute;left:200px;top:200px;display:none">
            <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
   	 </div>
	  
	   <div id="body">
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">
	           商品信息
	     	 </td>
	    </tr>
	  </table>
	  </div>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	             <tr><td>
	            <div id="#grid_mingxi" style="width:100%;height:100px;background-color:white;"></div>
	      
	            </td></tr>
	  </table>
	 
	 </div>

 	
</body>
</html>