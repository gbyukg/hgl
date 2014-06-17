<?php /* Smarty version 2.6.26, created on 2011-06-08 15:38:52
         compiled from kqxx_02.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'kqxx_02.php', 85, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $this->_tpl_vars['title']; ?>
</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kqxx_02.js"></script>


</head>

<body>
    <div id="top">
             <table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                  <td class="title">仓储管理 -<?php echo $this->_tpl_vars['title']; ?>
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
" readonly/>
					<input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
					<?php else: ?>
					<input  id="CKMCH" name="CKMCH" type="text" maxlength="20" style="width: 160PX" class="editable"/>
					<input  id="CKBH" name="CKBH" type="hidden"/>
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">库区编号:<span style="color: red">*</span></th>
			    <td width="80%">
			      <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input id="KQBH" name="KQBH" type="text" style="width: 160PX" value="<?php echo $this->_tpl_vars['rec']['KQBH']; ?>
" maxlength="6" class="readonly" readonly/>
					<?php else: ?>
					<input id="KQBH" name="KQBH" type="text" style="width: 160PX" maxlength="6"  class="editable"/>
						<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">
			        库区名称:<span style="color: red">*</span>
			    </th>
			    <td width="80%">
			     <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input id="KQMCH" name="KQMCH" type="text" style="width: 160PX" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
" maxlength="100" class="editable" />
					<?php else: ?>
					<input id="KQMCH" name="KQMCH" type="text" style="width: 160PX"  maxlength="100" class="editable" />
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">
			        拣货顺序:
			    </th>
			    <td width="80%">
			     <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input id="JHSHX" name="JHSHX"  type="text" style="width: 160PX"  value="<?php echo $this->_tpl_vars['rec']['JHSHX']; ?>
" maxlength="6" class="editable"/>
					<?php else: ?>
					<input id="JHSHX" name="JHSHX"  type="text" style="width: 160PX"  maxlength="6" class="editable"/>
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th width="20%">
			        库区类型:<span style="color: red">*</span>
			    </th>
			    <td width="80%" >
			   		<?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<select style="width: 160PX" id="KQLX" name="KQLX">
					 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kqlx'],'selected' => $this->_tpl_vars['rec']['KQLX']), $this);?>
					
					</select>
					<?php else: ?>
					<select style="width: 160PX" id="KQLX" name="KQLX">
					 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['kqlx']), $this);?>
					
					</select>
					<?php endif; ?>
			    </td>
			</tr>

		</table>
    </div>
    	 </form>  
    	 </div> 
</body>
</html>