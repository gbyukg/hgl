<?php /* Smarty version 2.6.26, created on 2011-04-02 14:06:18
         compiled from cgthck_02.php */ ?>
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
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/cgthck_02.js"></script>

</head>
<body>
	 <div id="top">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
               <td class="title">仓储模块-采购退货单查询</td>
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
	   <table width="100%" cellpadding="0" cellspacing="1" class="form">
        <tr>
            <th width="100px">开始日期：</th>
            <td width="200px">
                <input id= "KSRQKEY" name="KSRQKEY" type="text" style="width: 190px" class="editable"/>
            </td>
            <th width="100px">终止日期：</th>
            <td width="200px">
                <input id= "ZZRQKEY" name="ZZRQKEY" type="text" style="width: 190px" class="editable"/>
            </td>
            <td width="100px"></td>
            <td style="text-aling:left" width="200px" >
            	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
            	<img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <th width="100px">单位编号：</th>
            <td width="200px">
                <input id= "DWBHKEY"  name="DWBHKEY" type="text" style="width: 190px" class="editable"/>
            </td>
            <th width="100px">单位名称：</th>
            <td colspan="3">
                <input id= "DWMCHKEY" name="DWMCHKEY" type="text" style="width: 300px"  class="editable"/>
            </td>
        </tr>
      </table>
      </form>
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">单据信息</td>
	     	<td>
	     	</td>
	    </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
         <tr>
           <td>
             <div id="grid_danju" style="width:100%;height:200px;background-color:white;"></div>
             <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
           </td>
         </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	     <tr>
	     	<td width="100px">明细信息</td>
	     </tr>
	  </table>
	  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	       <tr><td>
	            <div id="grid_mingxi" style="width:100%;height:200px;background-color:white;"></div>
	            <div class="paggingarea"><span id="pagingArea1"></span>&nbsp;<span id="infoArea1"></span></div>
	       </td></tr>
	  </table>
	 </div>
 	 <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
            <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
   	 </div>
</body>
</html>