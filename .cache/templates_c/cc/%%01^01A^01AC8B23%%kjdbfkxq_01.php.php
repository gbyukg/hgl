<?php /* Smarty version 2.6.26, created on 2011-05-30 10:55:32
         compiled from kjdbfkxq_01.php */ ?>
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
/js/kjdbfkxq_01.js"></script>
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
<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="100px">基本信息</td>
    </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="1" class="form">
    <tr height="0">
      <td width="95px"></td>
      <td width="125px"></td>
      <td width="85px"></td>
      <td width="125px"></td>
      <td width="75px"></td>
      <td width="125px"></td>
      <td width="65px"></td>
      <td></td>
    </tr>
    <tr>
        <th >开票日期:</th>
        <td >
        	<input id="KPRQ" name="KPRQ" type="text" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KPRQ']; ?>
" />
        </td>
        <th >单据编号:</th>
        <td >
        	<input id="DJBH" name="DJBH" type="text" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" />
        </td>
        <th >部门:</th>
        <td>
        	<input id="BMMCH" name="BMMCH" type="text" style="width: 115px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['BMMCH']; ?>
" />
        </td>
    	<th >业务员:</th>
        <td>
        	<input id="YWYMCH" name="YWYMCH" type="text" style="width: 115px"  readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['YGXM']; ?>
" />
        </td>
    </tr>
    <tr>
    	
    	<th >调拨出库单:</th>
        <td >
        	<input id="DYDBCHKD" name="DYDBCHKD" type="text" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DYDBCHKD']; ?>
" />
        </td>
     
         <th >调出仓库:</th>
        <td >
        	<input id="DCCK" name="DCCK" type="text" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DCCKMCH']; ?>
"/>
        	<input id="DCCKBH" name="DCCKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['DCHCK']; ?>
"/>
        </td>
        <th >调入仓库:</th>
        <td >
        	<input id="DRCK" name="DRCK" type="text" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DRCKMCH']; ?>
"/>
        	<input id="DRCKBH" name="DCCKBH" type="hidden" value="<?php echo $this->_tpl_vars['rec']['DRCK']; ?>
"/>
        </td>
           <th >是否配送:</th>
        <td>
        	<?php if ($this->_tpl_vars['rec']['SHFPS'] == '1'): ?>
            	<input id="SHFPS" name="SHFPS" type="checkbox" checked="checked" disabled></input>   
            <?php else: ?>
            	<input id="SHFPS" name="SHFPS" type="checkbox" disabled></input> 
            <?php endif; ?>
        </td>
    </tr>
    <tr>
       
        <th >电话:</th>
        <td>
        	<input id="DHHM" name="DHHM" type="text" maxlength="25" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DHHM']; ?>
"/>
        </td>
         <th >调入仓库地址:</th>
        <td >
        	<input id="DIZHI" name="DIZHI" type="text" maxlength="100" style="width: 115px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['DRCKDZH']; ?>
"/>
        </td>
             <th >备注:</th> 
        <td  colspan="3">
        	<input id="BEIZHU" name="BEIZHU" type="text" maxlength="500" style="width: 311px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['BEIZHU']; ?>
"/>
        </td>
       
    </tr>

 
</table>

<table width="100%" cellpadding="0" cellspacing="0" class="subtitle" border=0>
    <tr>
        <td width="200px">明细信息</td>  
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="grid">
    <tr>
        <td>
        	<div id="#grid_mingxi" style="width: 100%; height:160px; background-color: white; "></div>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">当前商品详细信息</td>
        <td></td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="100px">通用名:</th>
        <td width="200px"><span id="TONGYONGMING" class="span_num" style="width:185px" ></span></td>
        <th width="100px">产地:</th>
        <td width="200px"><span id="CHANDI" class="span_num" style="width:185px" ></span></td>
        <th width="100px">规格:</th>
        <td><span id="SHPGG" class="span_num" style="width:185px"></span></td>
    </tr>
    <tr>
        <th width="100px">单位:</th>
        <td width="200px"><span id="BZHDW" class="span_num" style="width:185px" ></span></td>
        <th width="100px">数量:</th>
        <td width="200px"><span id="SHULIANG" class="span_num" style="width:185px"></span></td>
        <th width="100px">批号:</th>
        <td><span id="PIHAO" class="span_num" style="width:185px"></span></td>
    </tr>

</table>

<table width="100%" cellpadding="0" cellspacing="1" class="subtitle">
    <tr>
        <td width="150px">合计信息:</td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="1" class="form">
    <tr>
        <th width="100px">数量合计:</th>
        <td><span id="SHULIANGHJ"></span></td>
    </tr>
</table>

</form>
</div>

</body>
</html>