<?php /* Smarty version 2.6.26, created on 2011-04-26 17:50:09
         compiled from pdwh_01.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'pdwh_01.php', 53, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title> <?php echo $this->_tpl_vars['title']; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/pdwh_01.js" ></script>

</head>
<body>
  <div id="top">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="title">
                          <?php echo $this->_tpl_vars['title']; ?>

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
    <table width="100%" cellpadding="0" cellspacing="0" class="search">
    
    		<tr height="0">
		      <td width="65px"></td>
		      <td width="125px"></td>
		      <td width="65px"></td>
		      <td width="125px"></td>
		      <td width="65px"></td>
		      <td width="125px"></td>
		      <td width="65px"></td>
		      <td width="125px"></td>
		      <td width="150px"></td>
		      <td></td>
		    </tr>
			<tr>
			<th>
			盘点单据号:
			</th>
			<td>
			<input id="DJBHKEY" name="DJBHKEY" size="15" type="text" maxlength= 14 class="editable"/>
			</td>
			<th>
			盘点状态:
			</th>
			<td>
			<select id="PDZHT" name="PDZHT" style="width: 120px">
					<?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['stats_opts']), $this);?>
                   		
			 </select>    
			</td>
			<th>
			开始日期:
			</th>
			<td>
			<input id="PDKSHSHJ" name="PDKSHSHJ" size="15" type="text" maxlength= 70 class="editable"/>
			</td>
			<th>
			结束日期:
			</th>
			<td>
			<input id="PDJSHSHJ" name="PDJSHSHJ" size="15" type="text" maxlength= 70 class="editable"/>
			</td>
			<td>
		        <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /><img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
		</tr>
    </table>
     </form>
  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
         <div id="mygrid" style="width:100%;height:320px;background-color:white;"></div>
               <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
        </td>
       </tr>
       
  </table>
 </div>
   <div id="loader" style="z-index:999;position:absolute;left:200px;top:100px;display:none">
                  <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/loading.gif" align="absmiddle">
   </div>
  </div>
</html>