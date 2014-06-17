<?php /* Smarty version 2.6.26, created on 2011-05-30 12:54:54
         compiled from pdksjpdbsc_01.php */ ?>
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
/js/pdksjpdbsc_01.js" ></script>

</head>
<body>
  <div id="top">
           <table width="100%" cellpadding="0" cellspacing="0">
               <tr>
                  <td class="title" style="width: 220px">
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
            <input type="hidden" id="PDJHDH_H" name="PDJHDH_H" />
            <input type="hidden" id="CKBH_H" name="CKBH_H" />
            <input type="hidden" id="KQBH_H" name="KQBH_H" />
            <input type="hidden" id="KWBH_H" name="KWBH_H" />
<!--            <input type="hidden" id="BUMEN_H" name="BUMEN_H"  />-->
            <input type="hidden" id="YEWUYUAN_H" name="YEWUYUAN_H"  />
            
              <table width="100%" cellpadding="0" cellspacing="1" class="form">
               <tr>
               	  <td width="120px"></td>
			      <td width="240px"></td>
			      <td width="140px"></td>
			      <td></td>	
               </tr>
               <tr>
					<th>
					盘点单据号:
					</th>
					<td>
					<input id="DJBH" name="DJBH" size="15" type="text"  style="width: 150px"  disabled class="readonly" value="--保存时自动生成--"/>
					</td>
					</tr>
					<tr>
					<th>
					盘点计划单据号:
					</th>
					<td>
					<input id="PDJHDH" name="PDJHDH" size="15" type="text" maxlength= "14" style="width: 150px" readonly class="readonly" value="--双击选择盘点计划单据--"/>
					</td>
					</tr>
					<tr>
					<th>
					仓库:<span style="color: red">*</span>
					</th>
					<td>
					<input id="CKBH" name="CKBH" size="15" type="text" maxlength= "70" style="width: 150px" readonly class="readonly" value="--双击选择仓库--"/>
					</td>
					</tr>
					<tr>
					<th>
					库区:<span style="color: red">*</span>
					</th>
					<td>
					<input id="KQBH" name="KQBH" size="15" type="text" maxlength= "70" style="width: 150px" readonly class="readonly" value="--双击选择库区--"/>
					</td>
					</tr>
					<tr>
					<th>
					库位:
					</th>
					<td>
					<input id="KWBH" name="KWBH" size="15" type="text" maxlength= 70 style="width: 300px" class="editable" value="--双击选择库位--"/>
					</td>
					
					</tr>
					<tr>
					<th>
					账面数量条件<span style="color: red">*</span>
					</th>
					<td>
						
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ1" value="1" checked/>所有商品
					
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ2" value="2"/>账面数量>0
					
					         <input type="radio" name="ZHMSHLTJ" id="ZHMSHLTJ3" value="3"/>账面数量=0
						
					</td>
					</tr>
					<th>
					冻结
					</th>
					<td>
					<input id="DJBZH" name="DJBZH" size="15" disabled type="checkbox" checked/>
					</td>
					</tr>
					
					<tr>
					<th>
					部门:<span style="color: red">*</span>
					</th>
					<td>
<!--					<input id="JSHBM" name="JSHBM" size="15" type="text" maxlength= 70 style="width: 150px" class="editable"/>-->
                        <span id="BMMCH" name="BMMCH" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
                        <input id="BUMEN_H" name="BUMEN_H" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
					</td>
					</tr>
					<tr>
					<th>
					业务员:<span style="color: red">*</span>
					</th>
					<td>
					<input id="YEWUYUAN" name="YEWUYUAN" size="15" type="text" maxlength= 70 style="width: 150px" class="editable"/>
					</td>
					</tr>
					
					</table>
                </form>
        </div>
</body>
</html>