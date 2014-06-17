<?php /* Smarty version 2.6.26, created on 2011-04-25 10:17:47
         compiled from spygysgxcx_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css"href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/style.css">
<link rel="stylesheet" type="text/css"href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/skins/dhtmlxtoolbar_dhx_skyblue.css"></link>
<link rel="stylesheet" type="text/css"href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtabbar.css">
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.hotkeys-modified.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/css/jquery.autocomplete.css"></link>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxcommon.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtoolbar.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgrid.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxgridcell.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_nxml.js "></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_pgn.js"></script>
<script type="text/javascript"src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/ext/dhtmlxgrid_start.js"></script>
<script src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/dhtmlxtabbar.js"></script>
<script src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/codebase/excells/dhtmlxgrid_excell_cntr.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/spygysgxcx_01.js"></script>
</head>
<body>

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

<div id="a_tabbar" style="width: 100%; height: 350px; margin-top: 5px;">
<div id="a1" style="width: 100%">
<table width="100%" cellpadding="0" cellspacing="1" >
<tr>
<td width="60%">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
	
		<td width="150px">供应商明细</td>
		<td><img id="DUOSHP" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_product_m.gif"  /> <img
            id="DANSHP" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_product_s.gif"  />
            <img id="WUSHP" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_product_n.gif"  />
            </td>
                        

	
</table>
<div id="#grid_main"style="width: 100%; height: 300px; background-color: white; overflow: auto"></div>
</td>

<td>
	  <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
		    <td >
                         商品明细 
		    </td>			
		  </table>		    	    
	       <div id="#grid_shp" style="width:100%;height:300px;background-color:white;overflow:auto" ></div>
 </td> 

 </tr>
 </table>
</div>

<div id="a2" style="width: 100%">
<table width="100%" cellpadding="0" cellspacing="1" >
<tr>
<td width="60%">
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">

		<td width="150px">商品明细</td>
		<td><img id="DUOGYS" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_supplier_m.gif"  /> <img
           id="DANGYS" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_supplier_s.gif"  />
           <img id="WUGYS" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_supplier_n.gif"  />
           </td>
           

</table>
<div id="#grid_mingxi"style="width: 100%; height: 300px; background-color: white; overflow: auto"></div>
</td>
             <td>
					   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
					    <td >
			                         供应商明细 
					    </td>			
					  </table>		    	    
				       <div id="#grid_dw" style="width:100%;height:300px;background-color:white;overflow:auto" ></div>
			 </td>
</tr>
</table>
</div>
</div>
</div>
</div>

</body>
</html>


