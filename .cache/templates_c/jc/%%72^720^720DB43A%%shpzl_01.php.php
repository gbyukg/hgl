<?php /* Smarty version 2.6.26, created on 2011-05-09 17:00:27
         compiled from shpzl_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/shpzl_01.js"></script>
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
		<tr>
		    <th width="80px">
		             商品

		    </th>
		    <td width="200px">
		        <input id="SHPBHKEY" name="SHPBHKEY" type="text" maxlength="50" style="width:180px" class="editable"/>
		    </td>		    
		    <td width="200px">
		        <img id="BTNSEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />
		        <img id="BTNRESET" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_reset.gif" />
		    </td>
		    <td>
		    </td>		    
		</tr>
    </table>
     </form>   
	       
    <table width="100%" cellpadding="0" cellspacing="1" class="grid">  
	    <tr>   	    
		    <td width="120px">
			   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
			    <td>
					商品分类	    
			    </td>			
			  </table>		    	    		    
		       <div id="treebox" style="width:120px;height:395px;background-color:#f5f5f5;border :1px solid Silver; "></div>
		    </td>
		
		    <td>
			   <table width="100%" cellpadding="0" cellspacing="1" class="subtitle">    	    
			    <td>
					商品基础资料列表    
			    </td>			
			  </table>		    	    
		       <div id="gridbox" style="width:100%;height:395px;background-color:white;" ></div>
               
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