<?php /* Smarty version 2.6.26, created on 2011-04-26 11:17:13
         compiled from pdwh_02.php */ ?>
<?php if ($this->_tpl_vars['full_page']): ?>
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
/js/PDWH_02.js" ></script>

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
				盘点维护相关信息
             </td>
            </tr>
            </table>
<?php endif; ?>
            <form name="form1" id="form1" style="display:inline;margin:0px;">
            <input id="PDKSHSHJ" name="PDKSHSHJ" type="hidden" value="<?php echo $this->_tpl_vars['pdkshshj']; ?>
"/>
            <input id="PDJSHSHJ" name="PDJSHSHJ"  type="hidden" value="<?php echo $this->_tpl_vars['pdjshshj']; ?>
"/>
            <input id="ZHTAI" name="ZHTAI"  type="hidden" value="<?php echo $this->_tpl_vars['zhtai']; ?>
"/>
            <input id="DJBHWH" name="DJBHWH"  type="hidden" value="<?php echo $this->_tpl_vars['djbhwh']; ?>
"/>
            <input type="hidden" id="orderby" value="<?php echo $this->_tpl_vars['orderby']; ?>
">
			<input type="hidden" id="direction" value="<?php echo $this->_tpl_vars['direction']; ?>
">
			
               <table width="100%" cellpadding="0" cellspacing="1" class="form">
               <tr>
					<th width="120px">
					盘点单据号
					</th>
					<td>
					<input id="DJBH" name="DJBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 150px" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" class="editable"/>
					</td>
					</tr>
					<tr>
					<th>
					仓库
					</th>
					<td>
					<input id="CKBH" name="CKBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 150px" readonly  class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					库区
					</th>
					<td>
					<input id="KQBH" name="KQBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70  style="width: 150px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					库位
					</th>
					<td>
					<input id="KWBH" name="KWBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70  style="width: 300px"  class="editable"  value="<?php echo $this->_tpl_vars['rec']['KWMCH']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					账面数量条件
					</th>
					<td>
					
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ1" value="1" <?php echo $this->_tpl_vars['check1']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
/>所有商品
					
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ2" value="2" <?php echo $this->_tpl_vars['check2']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
/>账面数量>0
					
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ3" value="3" <?php echo $this->_tpl_vars['check3']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
/>账面数量=0
					
					</td>
					</tr>
					<th>
					冻结
					</th>
					<td>
					<input id="DJBZH" name="DJBZH" size="15" type="checkbox" <?php echo $this->_tpl_vars['check4']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 />
					</td>
					</tr>
					<tr>
					<th>
					开始时间
					</th>
					<td>
					<input id="PDKSHSHJ" name="PDKSHSHJ" size="15" type="text"  style="width: 150px"  <?php echo $this->_tpl_vars['disabled']; ?>
 value="<?php echo $this->_tpl_vars['rec']['PDKSHSHJ']; ?>
" maxlength= 100 class="editable"/>
					</td>
					</tr>
					<tr>
					<th>
					开始部门
					</th>
					<td>
					<input id="KSBMBH" name="KSBMBH" size="15" type="text" <?php echo $this->_tpl_vars['disabledbm']; ?>
 maxlength= 70  style="width: 150px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KSBMBH']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					开始业务员
					</th>
					<td>
					<input id="KSYGBH" name="KSYGBH" size="15"  <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength= 70  style="width: 150px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KSYGBH']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					结束时间
					</th>
					<td>
					<input id="PDJSHSHJ" name="PDJSHSHJ" size="15" type="text"  style="width: 150px"  <?php echo $this->_tpl_vars['disabled']; ?>
 value="<?php echo $this->_tpl_vars['rec']['PDJSHSHJ']; ?>
" maxlength= 100 class="editable"/>
					</td>
					</tr>
					<tr>
					<th>
					结束部门
					</th>
					<td>
					<input id="JSHBM" name="JSHBM" size="15" type="text" <?php echo $this->_tpl_vars['disabledbm']; ?>
 maxlength= 70  style="width: 150px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['JSBMCMH']; ?>
"/>
					
					</td>
					</tr>
					<tr>
					<th>
					结束业务员
					</th>
					<td>
					<input id="JSYEWUYUAN" name="JSYEWUYUAN" size="15"  <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength= 70  style="width: 150px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['JSYEWUYUAN']; ?>
"/>
					</td>
					</tr>
					
					<tr>
					<th>
					账面金额
					</th>
					<td>
					<input id="ZHMJEHJ" name="ZHMJEHJ" size="15"  <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength= 70  style="width: 150px"  readonly class="editable_num" value="<?php echo $this->_tpl_vars['rec']['ZHMJEHJ']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					实盘金额
					</th>
					<td>
					<input id="SHPJEHJ" name="SHPJEHJ" size="15"  <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength= 70  style="width: 150px"  readonly class="editable_num" value="<?php echo $this->_tpl_vars['rec']['SHPJEHJ']; ?>
"/>
					</td>
					</tr>
					</table>
                </form>
<?php if ($this->_tpl_vars['full_page']): ?>
        </div>
</body>
</html>
<?php endif; ?>