<?php /* Smarty version 2.6.26, created on 2011-11-07 15:29:17
         compiled from fhzcq_02.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'fhzcq_02.php', 46, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/fhzcq_02.js"></script>
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
<table width="100%" cellpadding="0" cellspacing="1" class="form" border="0">
     <tr>
       <th width="120px">所属仓库:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">*</span></th>
       <td> <?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input  id="CKMCH" name="CKMCH" type="text"  style="width: 200px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
" disabled  />
					<input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
					<input  id="CKZHT" name="CKZHT" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKZHT']; ?>
"/>
					<?php else: ?>
					<input  id="CKMCH" name="CKMCH" type="text" maxlength="100" style="width: 200px" class="editable"/>
					<input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
					<input  id="CKZHT" name="CKZHT" type="hidden"/>
					<?php endif; ?> 
       </td> 
    </tr>
   <tr>
       <th>发货区:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">*</span></th>
       <td width="80%" >
			   		<?php if ($this->_tpl_vars['action'] == 'update'): ?>
					  <input id="FHQ" name="FHQ" type="text" maxlength="100" style="width: 200px" class="readonly" readonly  value="<?php echo $this->_tpl_vars['rec']['FHQMCH']; ?>
"/>
					   <input id="FHQBH" name="FHQBH" type=hidden  value="<?php echo $this->_tpl_vars['rec']['FHQBH']; ?>
"/>
					<?php else: ?>
					<select style="width: 207PX" id="FHQ" name="FHQ" class="editable">
					 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fhq']), $this);?>
					
					</select>
					<?php endif; ?>
	  </td>
    </tr>
    <tr>
       <th>出货口:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: red">*</span></th>
       <td>
            <?php if ($this->_tpl_vars['action'] == 'update'): ?>
            <input type=hidden id="CHHKBH" name="CHHKBH" value="<?php echo $this->_tpl_vars['rec']['CHHKBH']; ?>
" />  
            <input id="CHHKMCH" name="CHHKMCH" type="text"  style="width: 200px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['CHHKMCH']; ?>
"/>
	            <?php else: ?>
	            <input id="CHHKMCH" name="CHHKMCH" type="text"  style="width: 200px" class="readonly" readonly value="<?php echo $this->_tpl_vars['rec']['CHHKMCH']; ?>
"/>
	            <input id="CHHKBH" name="CHHKBH" type="hidden" />
	            <?php endif; ?>
      </td>
    </tr>
     <tr>
        <th>发货暂存区编号:<span style="color: red">*</span></th>
        <td>
            <?php if ($this->_tpl_vars['action'] == 'update'): ?>
            <input id="FHZCQBH" name="FHZCQBH" type="text" maxlength="5" style="width: 200px"
            class="readonly" readonly  value="<?php echo $this->_tpl_vars['rec']['FHZCQBH']; ?>
" />
            <?php else: ?>
            <input id="FHZCQBH" name="FHZCQBH" type="text" maxlength="5" style="width: 200px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['FHZCQBH']; ?>
" />
             <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>发货暂存区名称:<span style="color: red">*</span></th>
        <td><input id="FHZCQMCH" name="FHZCQMCH" type="text"  style="width: 200px"
            class="editable"  value="<?php echo $this->_tpl_vars['rec']['FHZCQMCH']; ?>
" />
        </td>
    </tr>
    <tr>
        <th>发货暂存区类别:<span style="color: red">*</span></th>
        <td>
            <?php if ($this->_tpl_vars['action'] == 'update'): ?>
		    <select style="width:207px" id="FHZCQLB" name="FHZCQLB">
		    <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fhzcqlb_ops'],'selected' => $this->_tpl_vars['rec']['FHZCQLB']), $this);?>
					
			</select>
			<?php else: ?>
            <select id="FHZCQLB" name="FHZCQLB" style="width: 207px">
       		 <?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['fhzcqlb_ops']), $this);?>
	 		
			</select>
			        <?php endif; ?>
        </td>    
    </tr>
    

</table>
</form>
</div>
</body>
</html>