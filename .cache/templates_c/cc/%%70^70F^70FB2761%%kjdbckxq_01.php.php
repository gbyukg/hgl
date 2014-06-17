<?php /* Smarty version 2.6.26, created on 2011-04-29 09:12:27
         compiled from kjdbckxq_01.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kjdbckxq_01.js" ></script>

</head>
<body>
   <div id="top">
           <table width="100%" cellpadding="1" cellspacing="0">
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
           <table width="100%" cellpadding="0" cellspacing="0" class="subtitle">
             <tr>
             <td width="200px">
				库间调拨出库相关信息
             </td>
            </tr>
            </table>
            
  <form name="form1" id="form1" style="display:inline;margin:0px;">
    <input type="hidden" id="djbh_h" name="djbh_h" value = "<?php echo $this->_tpl_vars['djbh_h']; ?>
" />
    <table width="100%" cellpadding="0" cellspacing="1" class="form">
       
		<tr>
		    <th width="120px">
		             开票日期
		    </th>
		    <td width="150px">
		        <input id="KPRQ" name="KPRQ" type="text" maxlength="18" style="width: 130px" disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['KPRQ']; ?>
"/>
		    </td>
		    <th width="120px">
		             单据编号
		    </th>
		    <td width="150px">
		    <input type="hidden" id="DJBH" name="DJBH" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" />
		        <input type="text" maxlength="14" style="width: 130px" disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
"/>
		    </td>
        <tr>
		    <th width="120px">
		             部门
		    </th>
		    <td width="150px">
		        <input id="BMBH" name="BMBH" type="text" maxlength="6" style="width: 130px" disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['BMBH']; ?>
"/>
		    </td>
		    <th width="120px">
		             业务员
		    </th>
		    <td width="150px">
		        <input id="YWYBH" name="YWYBH" type="text" maxlength="8" style="width: 130px" disabled  class="editable" value="<?php echo $this->_tpl_vars['rec']['YWYBH']; ?>
"/>
		    </td>
        <tr>
		    <th width="120px">
		             调出仓库
		    </th>
		    <td width="150px">
		        <input id="DCHCK" name="DCHCK" type="text" maxlength="6" style="width: 130px"  disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['DCHCK']; ?>
"/>
		    </td>
		    <th width="120px">
		             调入仓库
		    </th>
		    <td width="150px">
		        <input id="DRCK" name="DRCK" type="text" maxlength="6" style="width: 130px"  disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['DRCK']; ?>
"/>
		    </td>
		    <th width="120px">
		             是否配送
		    </th>
		    <td width="150px">
		    	<?php if ($this->_tpl_vars['rec']['SHFPS'] == '1'): ?>
		        	<input id="SHFPS" name="SHFPS" checked disabled type="checkbox" />
		        <?php else: ?>
		        	<input id="SHFPS" name="SHFPS" disabled type="checkbox" />
		        <?php endif; ?>
		    </td>
        <tr>
		    <th width="120px">
		             调入仓库地址
		    </th>
		    <td width="150px" colspan="3">
		        <input id="DRCKDZH" name="DRCKDZH" type="text" maxlength="200" style="width:412px" disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['DRCKDZH']; ?>
"/>
		    </td>
		    <th width="120px">
		             电话
		    </th>
		    <td width="150px">
		        <input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 130px" disabled class="editable"  value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
"/>
		    </td>
        <tr>
		    <th width="120px">
		             备注
		    </th>
		    <td width="150px">
		        <input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width:412px" disabled class="editable" value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
"/>
		    </td>
        </tr>

    </table>
     </form>   
	       
       <table width="100%" cellpadding="0" cellspacing="1" class="grid">
	   <tr>
	       <td>
		       <div id="#grid_mingxi" style="width:100%;height:240px;background-color:white;overflow:auto"></div>
               <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span></div>
	       </td>
       </tr>
 	   </table>
 	   <table width="100%" cellpadding="0" cellspacing="1" class="form">
   
        <tr>
        <th width="120px">数量合计</th>
        <td><label id="SHLHJ" ></label>
        </td>
        </tr>
		</table>
        </div>
</body>
</html>