<?php /* Smarty version 2.6.26, created on 2011-04-01 13:45:38
         compiled from cgrkqr_03.php */ ?>
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
<link rel="STYLESHEET" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn_bricks.css">
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
/codebase/ext/dhtmlxgrid_splt.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/cgrkqr_03.js"></script>
</head>
<body>
   <div id="top">

             <table width="100%" cellpadding="0" cellspacing="0" >
               <tr>
                  <td>
                    <div id="toolbar"></div>
                  </td>
                </tr>
             </table>
    </div>
    <div id="vspace"></div>
    <div id="body">

           <input type="hidden" id="cgdbh" name="cgdbh" value="<?php echo $this->_tpl_vars['cgdbh']; ?>
">
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:300px;background-color:white;overflow:auto"></div>
            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div>
        
        
    <div id="loader" style="z-index:999;position:absolute;left:200px;top:200px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
        </div>
       

</body>
</html>