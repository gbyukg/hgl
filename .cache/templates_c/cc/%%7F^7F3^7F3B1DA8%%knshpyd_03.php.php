<?php /* Smarty version 2.6.26, created on 2011-06-16 11:45:59
         compiled from knshpyd_03.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/knshpyd_03.js"></script>
</head>
<body>
     <input type="hidden" id="ckbh" value="<?php echo $this->_tpl_vars['GET']['ckbh']; ?>
">
     <input type="hidden" id="kqbh" value="<?php echo $this->_tpl_vars['GET']['kqbh']; ?>
">
     <input type="hidden" id="kwbh" value="<?php echo $this->_tpl_vars['GET']['kwbh']; ?>
">
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
               <table width="100%" cellpadding="0" cellspacing="0" class="search">
                                <tr>
                                    <th width="100px">
                                     &nbsp; 快速查找条件

                                    </th>
                                     <td width="200px">
                                        <input type="text" id="SEARCHKEY" class="editable" style="width:180px" value="<?php echo $this->_tpl_vars['searchkey']; ?>
" />
                                    
                                    </td>
                                    <td width="200px">
                                           <img id="SEARCH" src="<?php echo $this->_tpl_vars['THEMESURL']; ?>
/images/btn_search.gif" />                                
                                    </td>
                                    		    <td>
		    </td>
                                </tr>
                </table>
            <!----------- 数据列表 ------------------>
            <table width="100%" cellpadding="0" cellspacing="1" class="grid">
             <tr><td>
            <div id="gridbox" style="width:100%;height:360px;background-color:white;"></div>
            <div class="paggingarea"><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
            
            </td></tr>
            </table>
        </div>
</body>
</html>