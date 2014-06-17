<?php /* Smarty version 2.6.26, created on 2011-05-30 11:12:23
         compiled from pdjhsc_01.php */ ?>
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
/js/pdjhsc_01.js" ></script>

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
            <input type="hidden" id="action" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
            <input type="hidden" id="CKBH_H" name="CKBH_H" />
            <input type="hidden" id="KQBH_H" name="KQBH_H" />
            <input type="hidden" id="KWBH_H" name="KWBH_H" />
            <input type="hidden" id="BMBH_H" name="BMBH_H" />
            <input type="hidden" id="YEWUYUAN_H" name="YEWUYUAN_H" />
            
               <table width="100%" cellpadding="1" cellspacing="1" class="form">
               <tr>
			      <td width="120px"></td>
			      <td width="200px"></td>
			      <td width="160px"></td>
			      <td></td>
               </tr>
               <tr>
					<th>
					单据号:
					</th>
					<td>
					<input id="PDJHDH" name="PDJHDH" size="15" type="text" maxlength=14 style="width: 150px" disabled class="readonly" value="--保存时自动生成--"/>
					</td>
					<td>
					</td>
				    </tr>

					<tr>
					<th>
					仓库:<span style="color: red">*</span>
					</th>
					<td colspan="3">
					<input id="CKBH" name="CKBH" size="30" type="text" maxlength= 70 style="width: 400px" readonly class="readonly" value="--双击选择仓库--"  />
					</td>
					</tr>
					 <tr>
					<th>
					库区:<span style="color: red">*</span>
					</th>
					<td colspan="3">
					<input id="KQBH" name="KQBH" size="50" type="text" maxlength= 70 style="width: 500px"  readonly class="readonly" value="--双击选择库区--" />
					</td>
					</tr>
					 <tr>
					<th>
					库位:
					</th>
					<td colspan="3">
					<input id="KWBH" name="KWBH" size="15" type="text" maxlength= 70 style="width: 150px"  readonly class="readonly" value="--双击选择库位--"/>
					</td>
					</tr>
					 <tr>
						<th>
						预计开始日期:<span style="color: red">*</span>
						</th>
						<td>
						<input id="YJKSHRQ" name="YJKSHRQ" size="15" type="text" maxlength= 10 style="width: 150px" class="editable" />
						</td>
						
						<th>
						预计开始时刻:<span style="color: red">*</span>(hh:mi:ss)
						</th>
						<td>
						<input id="YJKSHRQT" name="YJKSHRQT" size="15" type="text" maxlength= 8 style="width: 150px" class="editable"/>
						</td>
					</tr>
					 <tr>
					<th>
					预计结束日期:<span style="color: red">*</span>
					</th>
					<td>
					<input id="YJJSHRQ" name="YJJSHRQ" size="15" type="text" maxlength= 10 style="width: 150px"  class="editable" />
					</td>
					
					<th>
					预计结束时刻:<span style="color: red">*</span>(hh:mi:ss)
					</th>
					<td>
					<input id="YJJSHRQT" name="YJJSHRQT" size="15" type="text" maxlength= 8 style="width: 150px"  class="editable" />
					</td>
					</tr>
					 <tr>
					<th>
					提前通知日数:
					</th>
					<td>
					<input id="TQTZHRSH" name="TQTZHRSH" size="15" type="text" maxlength= 4  style="width: 150px" class="editable_num"/>
					</td>
					</tr>
					 <tr>
					<th>
					部门:<span style="color: red">*</span>
					</th>
					<td>
<!--					<input id="BUMEN" name="BUMEN" size="15" type="text" maxlength= 70 style="width: 150px"  class="editable"  />-->
                        <span id="BMMCH" name="BMMCH" class="span_normal" ><?php echo $this->_tpl_vars['bmmch']; ?>
</span>
                        <input id="BMBH" name="BMBH" type="hidden" value="<?php echo $this->_tpl_vars['bmbh']; ?>
"/>
					</td>
					</tr>
					 <tr>
					<th>
					业务员:<span style="color: red">*</span>
					</th>
					
					<td>
					<input id="YEWUYUAN" name="YEWUYUAN" size="15" type="text" maxlength= 70 style="width: 150px"  class="editable"  />
					</td>
		
					</tr>
					
					</table>
                </form>
        </div>
</body>
</html>