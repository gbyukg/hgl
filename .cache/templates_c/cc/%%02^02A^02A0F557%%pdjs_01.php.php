<?php /* Smarty version 2.6.26, created on 2011-05-30 13:58:56
         compiled from pdjs_01.php */ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="expires" CONTENT="-1">
<base target= "_self "> 
<title> <?php echo $this->_tpl_vars['title']; ?>
</title>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../commonHeader.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script type="text/javascript" src="<?php echo $this->_tpl_vars['MODULEURL']; ?>
/js/pdjs_01.js" ></script>

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
	            <input type="hidden" id="DJBH_H" name="DJBH_H" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
"/>
	            <input type="hidden" id="CKBH_H" name="CKBH_H" value="<?php echo $this->_tpl_vars['rec']['CKBH']; ?>
"/>
	            <input type="hidden" id="KQBH_H" name="KQBH_H" value="<?php echo $this->_tpl_vars['rec']['KQBH']; ?>
"/>
	            <input type="hidden" id="KWBH_H" name="KWBH_H" value="<?php echo $this->_tpl_vars['rec']['KWBH']; ?>
"/>
<!--	            <input type="hidden" id="BUMEN_H" name="BUMEN_H"  />-->
	            <input type="hidden" id="YEWUYUAN_H" name="YEWUYUAN_H"  />
	            <input type="hidden" id="BGZH_H" name="BGZH_H"  value="<?php echo $this->_tpl_vars['rec']['BGZH']; ?>
"/>
	            <input type="hidden" id="BGRQ_H" name="BGRQ_H"  value="<?php echo $this->_tpl_vars['rec']['BGRQ']; ?>
"/>
	            
            <table width="100%" cellpadding="0" cellspacing="1" class="form">
              <tr>
			      <td width="120px"></td>
			      <td width="200px"></td>
			      <td width="140px"></td>
			      <td></td>
               </tr>
               <tr>
				<th>
				盘点单据号:
				</th>
				<td >
				<input id="DJBH" name="DJBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 150px" value="<?php echo $this->_tpl_vars['rec']['DJBH']; ?>
" class="editable"/>
				</td>
				</tr>
				<tr>
				<th>
				仓库:
				</th>
				<td >
				<input id="CKBH" name="CKBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 150px" readonly  class="readonly" value="<?php echo $this->_tpl_vars['rec']['CKMCH']; ?>
"/>
				</td>
				</tr>
				<tr>
				<th>
				库区:
				</th>
				<td >
				<input id="KQBH" name="KQBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 150px" readonly class="readonly" value="<?php echo $this->_tpl_vars['rec']['KQMCH']; ?>
"/>
				</td>
				</tr>
				<tr>
				<th>
				库位
				</th>
				<td >
				<input id="KWBH" name="KWBH" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 maxlength= 70 style="width: 300px" class="editable"  value="<?php echo $this->_tpl_vars['rec']['KWMCH']; ?>
"/>
				</td>
				</tr>
				<tr>
				<th>
				账面数量条件
				</th>
				<td style="width: 200px" >

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
				<td >
				<input id="DJBZH" name="DJBZH" size="15" type="checkbox" <?php echo $this->_tpl_vars['check4']; ?>
 <?php echo $this->_tpl_vars['disabled']; ?>
/>
				</td>
				</tr>
				<tr>
				<th>
				开始时间
				</th>
				<td >
				<input id="PDKSHSHJ" name="PDKSHSHJ" size="15" type="text" <?php echo $this->_tpl_vars['disabled']; ?>
 value="<?php echo $this->_tpl_vars['rec']['PDKSHSHJ']; ?>
" maxlength= 100 style="width: 150px" class="editable"/>
				</td>
				</tr>
				<tr>
				<th>
				部门:<span style="color: red">*</span>
				</th>
				<td >
<!--				<input id="JSHBM" name="JSHBM" size="15" type="text" <?php echo $this->_tpl_vars['disabledbm']; ?>
 maxlength="20" style="width: 150px" class="editable" value=""/>-->
				    <span id="JSHBM" name="JSHBM" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
                    <input id="BUMEN_H" name="BUMEN_H" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
				</td>
				</tr>
				<tr>
				<th>
				业务员:<span style="color: red">*</span>
				</th>
				<td >
				<input id="YEWUYUAN" name="YEWUYUAN" size="15"  <?php echo $this->_tpl_vars['disableduser']; ?>
 type="text" maxlength="20" style="width: 150px" class="editable" value=""/>
				</td>
				</tr>
				
				</table>
                </form>
        </div>
</body>
</html>