<?php /* Smarty version 2.6.26, created on 2011-05-13 15:47:32
         compiled from spsjlr_03.php */ ?>
<?php if ($this->_tpl_vars['full_page']): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/spsjlr_03.js" ></script>

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
             <td width="100px">
				盘点维护相关信息
             </td>
            </tr>
            </table>

<form name="form1" id="form1" style="display:inline;margin:0px;">
<?php endif; ?>
<DIV ID="info">
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
<tr height="0">
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
       <td width="125px"></td>
      <td width="75px"></td>
      <td></td>
    </tr>
<tr>
	<th>
	盘点单号:
	</th>
	<td width="200px">
	<input type=hidden id="DJBH" name="DJBH" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" />
	<input type="text" maxlength="70" style="width: 115px" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" class="readonly"/>
	</td>
	<th>
	仓&nbsp;&nbsp;&nbsp;&nbsp;库:
	</th>
	<td>
	<input id="CKBH" name="CKBH" size="15" type="text" maxlength="70" style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
	</td>
	<th>
	库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;区:
	</th>
	<td>
	<input id="KQBH" name="KQBH" size="15" type="text" maxlength="70"  style="width: 115px"  class="readonly" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
"/>
	</td>
	</tr>
	<tr>		
	<th>
	开始时间:
	</th>
	<td>
	<input id="PDKSHSHJ" name="PDKSHSHJ" size="15" type="text"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['PDKSHSHJ']; ?>
" maxlength= 100 class="editable"/>
	</td>
	<th>
	开始部门:
	</th>
	<td>
	<input id="KSBMBH" name="KSBMBH" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['KSBMBH']; ?>
"/>
	</td>
	<th>
	开始业务员:
	</th>
	<td>
	<input id="KSYGBH" name="KSYGBH" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['KSYGBH']; ?>
"/>
	</td>
	</tr>
	<tr>
	<th>
	结束时间:
	</th>
	<td>
	<input id="PDJSHSHJ" name="PDJSHSHJ" size="15" type="text"  style="width: 115px" value="<?php echo $this->_tpl_vars['rec']['PDJSHSHJ']; ?>
" maxlength="100" class="readonly"/>
	</td>
	<th>
	结束部门:
	</th>
	<td>
	<input id="JSHBM" name="JSHBM" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['JSBMCMH']; ?>
"/>
	
	</td>
	<th>
	结束业务员:
	</th>
	<td>
	<input id="JSYEWUYUAN" name="JSYEWUYUAN" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['JSYEWUYUAN']; ?>
"/>
	</td>
	</tr>
	<tr>
	<th>
	实盘部门:
	</th>
	<td>
	<input id="SHPBMCH" name="SHPBMCH" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['SHPBMCH']; ?>
"/>
	</td>
	<th>
	实盘业务员:
	</th>
	<td>
	<input id="SHPYWYMCH" name="SHPYWYMCH" size="15" type="text" maxlength="70"  style="width: 115px" class="readonly" value="<?php echo $this->_tpl_vars['rec']['SHPYWYMCH']; ?>
"/>
	</td>
	</tr>
	<tr>
	<th>
	冻&nbsp;&nbsp;&nbsp;&nbsp;结:
	</th>
	<td>
	<input id="DJBZH" name="DJBZH" size="15" type="checkbox" <?php echo $this->_tpl_vars['check4']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 />
	</td>
	<th>
	账面数量条件:
	</th>
	<td>
	   <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ1" value="1" <?php echo $this->_tpl_vars['check1']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 style="float:none" /><label for="ZHMSHLTJ1">所有商品</label>
       <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ2" value="2" <?php echo $this->_tpl_vars['check2']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 style="float:none" /><label for="ZHMSHLTJ2">账面数量>0</label>
       <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ3" value="3" <?php echo $this->_tpl_vars['check3']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
 style="float:none" /><label for="ZHMSHLTJ3">账面数量=0</label>
	</td>
</table>
</DIV>
<?php if ($this->_tpl_vars['full_page']): ?>
<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="100px">商品明细</td>
        <td></td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        <div id="#grid_spsjlr" style="width: 100%; height:300px; background-color: white;"></div>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
	<tr height="0">
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
       <td width="125px"></td>
      <td width="75px"></td>
      <td></td>
    </tr>
	<tr>
      <th>账面金额合计:</th>
      <td><span id="ZHJEHJ" class="span_num" style="width:115px"></span></td>
      <th>实盘金额合计:</th>
      <td><span id="SPJEHJ" class="span_num" style="width:115px"></span></td>
      <th>损溢金额合计:</th>
      <td><span id="SYJEHJ" class="span_num" style="width:115px"></span></td>
    </tr> 
</table>
</form>
</div>
</body>
</html>
<?php endif; ?>