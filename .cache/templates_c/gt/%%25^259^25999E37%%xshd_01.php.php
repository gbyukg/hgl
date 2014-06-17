<?php /* Smarty version 2.6.26, created on 2011-04-06 09:54:30
         compiled from xshd_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title>销售单选择</title>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<link rel="STYLESHEET" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn_bricks.css">
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
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
/codebase/ext/dhtmlxgrid_start.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_srnd.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn.js"></script> 
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_splt.js  "></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/calender/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/xshd_01.js"></script>
<body>
    <div id="top">
           <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
                <td class="title" style="width:120px">共通-销售单选择</td>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>
    </div>
    <div id="vspace"></div>
    <div id="body">
    <form name="form1" action="" style="display:inline;margin:0px;">
           <input type="hidden" id="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
">
            <table width="100%" cellpadding="0" cellspacing="1" class="search" border="0">
               <tr>
               <td width="65px"></td>
               <td width="125px"></td>
               <td width="65px"></td>
               <td width="125px"></td>
               <td width="65px"></td>
               <td width="200px"></td>
               <td width="150px"></td>
               <td></td>
               </tr>
                <tr>
                    <th>开始日期 :</th>
                  	<td>
                    	<input id="BEGINDATE" type="text" style="width: 125px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['beginDate']; ?>
"/></td>
                    <th>终止日期:</th>
                    <td>
                        <input id="ENDDATE" type="text" style="width: 125px" readonly class="readonly" value="<?php echo $this->_tpl_vars['endDate']; ?>
"/></td>
					 <th>销往单位: </th>
                  	<td>
                      	<input id="DWMCH" type="text" style="width: 195px" class="editable"/>
                      	<input id="DWBH" type="hidden" /></td>
                    <td >
                        <img id="BTNSEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif">  
                        <img id="BTNRESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif">                     
                  </td>  	
				</tr>
  
            </table>

			<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
             <tr>
				<td width="200px">
					单据信息
                </td>
            </tr>
            </table>
       
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            	<div id="gridbox1" style="width:100%;height:222px;background-color:white;"></div>
            	<div class="paggingarea"><span id="pagingArea1"></span>&nbsp;<span id="infoArea1"></span></div>
            	
             </td></tr>
            </table>
			
			<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
             <tr>
				<td width="200px">
					明细信息
                </td>
            </tr>
            </table>
       
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            	<div id="gridbox2" style="width:100%;height:222px;background-color:white;"></div>
       
             </td></tr>
            </table>
        </div>
         <div id="loader1" style="z-index:999;position:absolute;left:400px;top:190px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle" /></div>
         <div id="loader2" style="z-index:999;position:absolute;left:400px;top:380px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle" /></div>          
    </div>
</form>
</body>
</html>