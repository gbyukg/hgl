<?php /* Smarty version 2.6.26, created on 2011-05-09 11:06:14
         compiled from kwxx_02.php */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/kwxx_02.js"></script>
	
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
            <input type=hidden id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
			<input type=hidden id="BGRQ" name="BGRQ" value="<?php echo $this->_tpl_vars['rec']['BGRQ']; ?>
" />         
              <table width="100%" cellpadding="0" cellspacing="1" class="form">
            <tr>
				<th width="130px">
			        仓库<font color="red">*</font>
			    </th>
			    <td>
			    	<?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input  id="CKMCH" name="CKMCH" type="text" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
" disabled/>
					<input  id="CKBH" name="CKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
					<?php else: ?>
					<input  id="CKMCH" name="CKMCH" type="text" style="width: 160px" class="readonly" value="请双击选择仓库" readonly />
					<input  id="CKBH" name="CKBH" type="hidden"/>
					<?php endif; ?>
			    </td>
			</tr>
        
			<tr>
				<th>
			        库位编号
			    </th>
			    <td>
			    <?php if ($this->_tpl_vars['action'] == 'update'): ?>
			    	<input  id="KWBH" name="KWBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['KWBH']; ?>
"/>
					<input  id="KWBH" name="KWBH" type="text" maxlength="6" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['KWBH']; ?>
" disabled/>
					<?php else: ?>
					<input  id="KWBH" name="KWBH" type="text" maxlength="6" style="width: 160px" class="readonly" value="--保存时自动生成--" readonly/>
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
			<th>货架排号<font color="red">*</font></th>
			<td>
			 <?php if ($this->_tpl_vars['action'] == 'update'): ?>
			 		<input  id="HJPH" name="HJPH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['HJPH']; ?>
"/>
					<input  id="HJPH" name="HJPH" type="text" maxlength="2" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['HJPH']; ?>
" disabled/>
					<?php else: ?>
					<input  id="HJPH" name="HJPH" type="text" maxlength="2" style="width: 160px" class="editable" />
					<?php endif; ?>
			</td>
			</tr>
			
				<tr>
			<th>货架列号<font color="red">*</font></th>
			<td>
			 <?php if ($this->_tpl_vars['action'] == 'update'): ?>
			 		<input  id="HJLH" name="HJLH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['HJLH']; ?>
"/>
					<input  id="HJLH" name="HJLH" type="text" maxlength="2" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['HJLH']; ?>
" disabled/>
					<?php else: ?>
					<input  id="HJLH" name="HJLH" type="text" maxlength="2" style="width: 160px" class="editable" />
					<?php endif; ?>
			</td>
			</tr>
			
				<tr>
			<th>货架上位置<font color="red">*</font></th>
			<td>
			 <?php if ($this->_tpl_vars['action'] == 'update'): ?>
			 		<input  id="HJSHWZH" name="HJSHWZH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['HJSHWZH']; ?>
"/>
					<input  id="HJSHWZH" name="HJSHWZH" type="text" maxlength="2" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['HJSHWZH']; ?>
" disabled/>
					<?php else: ?>
					<input  id="HJSHWZH" name="HJSHWZH" type="text" maxlength="2" style="width: 160px" class="editable" />
					<?php endif; ?>
			</td>
			</tr>
			
			
			<tr>
				<th>
			        库位名称<font color="red">*</font>
			    </th>
			    <td>
					<input id="KWMCH" name="KWMCH" type="text" maxlength="100" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['KWMCH']; ?>
"/>
			    </td>
			</tr>
			
			     	<tr>
				<th>
			        库区<font color="red">*</font>
			    </th>
			    <td>
			    	<?php if ($this->_tpl_vars['action'] == 'update'): ?>
					<input  id="KQMCH" name="KQMCH" type="text" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
" />
					<input  id="KQBH" name="KQBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['KQBH']; ?>
"/>
					<?php else: ?>
					<input  id="KQMCH" name="KQMCH" type="text" style="width: 160px" class="readonly" value="请双击选择库区" readonly/>
					<input  id="KQBH" name="KQBH" type="hidden"/>
					<?php endif; ?>
			    </td>
			</tr>
			<tr>
				<th>
			        拣货顺序
			    </th>
			    <td>
					<input id="JHSHX" name="JHSHX" maxlength="6" type="text" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['JHSHX']; ?>
"/>
			    </td>
			</tr>	
			<tr>
				<th>
			        可容纳重量(KG)
			    </th>
			    <td>
					<input id="KRNZHL" name="KRNZHL" type="text" maxlength="12" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['KRNZHL']; ?>
"/>
			    </td>
			</tr>		
			<tr>
				<th>
			        库位长(CM)
			    </th>
			    <td>
					<input id="KWCH" name="KWCH" type="text" maxlength="12" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['KWCH']; ?>
"/>
			    </td>
			</tr>		
			<tr>
				<th>
			        库位宽(CM)
			    </th>
			    <td>
					<input id="KWK" name="KWK" type="text" maxlength="12" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['KWK']; ?>
"/>
			    </td>
			</tr>
			<tr>
				<th>
			        库位高(CM)
			    </th>
			    <td>
					<input id="KWG" name="KWG" type="text" maxlength="12" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['KWG']; ?>
"/>
			    </td>
			</tr>
			<tr>
				<th>
			        指定保存商品编号
			    </th>
			    <td>
					<input id="ZHDSHPBH" name="ZHDSHPBH" type="text" maxlength="8" style="width: 160px" class="editable" value="<?php echo $this->_tpl_vars['rec']['ZHDSHPBH']; ?>
"/>
			    </td>
			</tr>
			<tr>
				<th>
			        可容纳指定商品数量

			    </th>
			    <td>
					<input id="KRNSHPSHL" name="KRNSHPSHL" type="text" maxlength="12" style="width: 160px" class="editable_num" value="<?php echo $this->_tpl_vars['rec']['KRNSHPSHL']; ?>
"/>
			    </td>
			</tr>
			<tr>
				<th>
			        是否散货库位
			    </th>
			   	<td>
			   	<?php if ($this->_tpl_vars['rec']['SHFSHKW'] == '1'): ?>
                        <input id="SHFSHKW" name="SHFSHKW" type="checkbox" checked="checked"></input>   
                        <?php else: ?>
                        <input type="checkbox" id="SHFSHKW" name="SHFSHKW"></input> 
                        <?php endif; ?>                
                </td>
			</tr>
                </table>
                </form>
        </div>

</body>
</html>

