<?php /* Smarty version 2.6.26, created on 2011-06-02 18:00:04
         compiled from zkspyl_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/zkspyl_01.js"></script>
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
	  <form name="form1" id="form1" style="display:inline;margin:0px;">
		  <table width="100%" cellpadding="0" cellspacing="1" class="form">
				<tr>
				<th width="5%">商品编号</th>
				<td width="16%">
				<input id="SHPBH" name="SHPBH" maxlength="8" type="text" style="90%"/>
				</td>
				
				<th width="5%"> 库位</th>
                <td width="18%">
	            <input id="KUWEI" name="KUWEI" type="text" style="width:200px" value="请双击选择库位" readonly class="readonly"/>
	            </td>	    
	            <td><img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" /> 
	            <img id="RESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif"  /></td>   
	            <td><input type="hidden" id="CKBH" name="CKBH" />
				<input type="hidden" id="KQBH" name="KQBH" />
				<input type="hidden" id="KWBH" name="KWBH" /></td>  
				</tr>
		  </table>	   
	 </form>   	      
		  <table width="100%" cellpadding="0" cellspacing="1" class="grid">
		  <tr>
		      <td>
		       <div id="#grid_zkspyl" style="width:100%;height:300px;background-color:white;overflow:auto"></div>
		       <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
		      </td>
		     </tr>
		 </table>
  </div>
</html>
