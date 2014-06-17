<?php /* Smarty version 2.6.26, created on 2011-05-13 10:13:42
         compiled from shangpin_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/shangpin_01.js"></script>
<body>
  <div id="top">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
       <td>
          <div id="toolbar"></div>
        </td>
      </tr>
    </table>
  </div> 
  <div id="vspace"></div>
  <div id="body">

    <form id="form1" name="form1">
     <input type="hidden" id="flg" name="flg" value="<?php echo $this->_tpl_vars['flg']; ?>
">
     <input type="hidden" id="status" name="status" value="<?php echo $this->_tpl_vars['status']; ?>
">
     <input type="hidden" id="dwbh" name="dwbh" value="<?php echo $this->_tpl_vars['dwbh']; ?>
">
    <table width="100%" cellpadding="0" cellspacing="0" class="search">
		<tr>
		    <th width="250px">
		            商品编号/商品名称/助记码/化学名/常用名
		    </th>
		    <td width="210px">
		        <input type="text" name="SEARCHKEY" id="SEARCHKEY" style="width:200px" class="editable" value="<?php echo $this->_tpl_vars['searchkey']; ?>
"/>
		    </td>		    
		    <td class="button">
		        <img src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" id="BTNSEARCH"/>
		    </td>
		    <td >
		    </td>		    
		</tr>
    </table>
    </form>
  <table width="100%" cellpadding="0" cellspacing="1" class="grid">  
	    <tr>   	    
		    <td width="220px">
			   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
			    <td >
					商品分类	    
			    </td>			
			  </table>		    	    		    
		       <div id="treebox" style="width:218px;height:300px;background-color:#f5f5f5;border :1px solid Silver; "></div>
		    </td>
		    <td>
			   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
			    <td >
					商品列表    
			    </td>			
			  </table>		    	    
		       <div id="gridbox" style="width:100%;height:300px;background-color:white;overflow:auto" ></div>
		    </td>
	    </tr>
        <tr>
        <td>
        &nbsp;
        </td>
        <td >
         <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
        </td>
        </tr>
    </table>
  </div>
</body>
</html>