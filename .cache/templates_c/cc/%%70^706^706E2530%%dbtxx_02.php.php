<?php /* Smarty version 2.6.26, created on 2011-11-07 15:29:01
         compiled from dbtxx_02.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/dbtxx_02.js"></script>


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
 <input type=hidden id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
 <input type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['BGRQ']; ?>
" />
		
		<table width="100%" cellpadding="0" cellspacing="1" class="form">
			<tr>
				<th width="20%">
			        仓库:<span style="color: red">*</span>
			    </th>
			    <td width="80%">
			<?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input  id="CKMCH" name="CKMCH" type="text"  style="width: 160PX" class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
" disabled/>
					<input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
					<?php else: ?>
					<input  id="CKMCH" name="CKMCH" type="text" maxlength="20" style="width: 160PX" readonly class="readonly" value="--双击选择仓库--"//>
					<input  id="CKBH" name="CKBH" type="hidden"/>
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">打包台编号:<span style="color: red">*</span></th>
			    <td width="80%">
			      <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input id="DBTBH" name="DBTBH" type="text" style="width: 160PX" value="<?php echo $this->_tpl_vars['rec']['DBTBH']; ?>
" maxlength="3" class="readonly" disabled/>
					<?php else: ?>
					<input id="DBTBH" name="DBTBH" type="text" style="width: 160PX" maxlength="3"  class="editable"/>
						<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">
			       打包台名称:<span style="color: red">*</span>
			    </th>
			    <td width="80%">
			     <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input id="DBTMCH" name="DBTMCH" type="text" style="width: 160PX" value="<?php echo $this->_tpl_vars['rec']['DBTMCH']; ?>
" maxlength="100" class="editable" />
					<?php else: ?>
					<input id="DBTMCH" name="DBTMCH" type="text" style="width: 160PX"  maxlength="100" class="editable" />
					<?php endif; ?>
			    </td>
			</tr>


		</table>
    </div>
    	 </form>  
    	 </div> 
</body>
</html>