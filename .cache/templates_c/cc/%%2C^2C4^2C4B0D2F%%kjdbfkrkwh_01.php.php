<?php /* Smarty version 2.6.26, created on 2011-06-07 09:27:24
         compiled from kjdbfkrkwh_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kjdbfkrkwh_01.js"></script>

</head>
<body>
    <div id="top">
             <table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                  <td class="title" style="width:220px;">
                     仓储管理-库间调拨返库入库维护
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
			    <th width="90px">
			         调拨返库入库单
			    </th>
				    <td width="120px">
				        <input id="DBFKRKD" name="DBFKRKD" maxlength="14" style="width:110px" type="text" class="editable"/>
				    </td>
			    <th width="90px">
			          调出仓库
			    </th>
			    <td width="120px">
			        <input id="DCCKMCH" name="DCCKMCH" type="text" style="width:110px" class="editable"/>
			        <input id="DCCKKEY" name="DCCKKEY" type="hidden" />
			    </td>
			    
			    <th width="90px">
			             对应调拨返库单
			    </th>
			    <td width="120px">
			        <input id="DYDBFKD" name="DYDBFKD" type="text" style="width:110px" readonly="readonly" class="readonly"/>
			    </td>
			    
			    <td width="200px">
            	<img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
            	<img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"/>

	     	 	</td> <td></td>  
			</tr>
			<tr>
			    <th>
			             开票日期从
			    </th>
			    <td>
			        <input id="KSRQKEY" name="KSRQKEY" type="text" style="width:110px" class="editable"/>
			    </td>
			    <th>
			             开票日期到
			    </th>
			    <td>
			        <input id="ZZRQKEY" name="ZZRQKEY" type="text" style="width:110px" class="editable"/>
			    </td>	    
			    <th>
			            对应调拨出库单
			    </th>
			    <td>
			        <input id="DYDBCHKD" name="DYDBCHKD" type="text" style="width:110px" readonly="readonly" class="readonly"/>
			    </td>	
			</tr>
		</table>
		</form>
		
		<table width="100%" cellpadding="0" cellspacing="1" class="grid">
			<tr>
				<td>
					<div id="grid_danju" style="width:100%;height:400px;background-color:white;"></div>
					<div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
				</td>
			</tr>
		</table>
    </div>
    	 	      <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                   <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
    		</div>
</body>