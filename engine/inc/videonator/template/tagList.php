<script type="text/javascript" src="/engine/ajax/menu.js"></script>
<script type="text/javascript" src="/engine/ajax/dle_ajax.js"></script>
<script language="javascript" type="text/javascript">
    <!--
    function MenuBuild(m_id, led_action, t_id) {

        var menu = new Array()

        menu[0] = '<a onClick="document.location=\'?mod=static&action=doedit&id=' + m_id + '\'; return(false)" href="#">Редактировать</a>';
        menu[1] = '<a onClick="javascript:confirmdelete(' + t_id + '); return(false)" href="#">удалить</a>';

        return menu;
    }
    function confirmdelete(id) {
        var agree = confirm("Вы действительно хотите удалить выбранный тэг?");
        if (agree)
            document.location = "?mod=taginator&action=delete&id=" + id;
    }
    //-->


    function ckeck_uncheck_all() {
        var frm = document.tag_list;
        for (var i = 0; i < frm.elements.length; i++) {
            var elmnt = frm.elements[i];
            if (elmnt.type == 'checkbox') {
                if (frm.master_box.checked == true) {
                    elmnt.checked = false;
                }
                else {
                    elmnt.checked = true;
                }
            }
        }
        if (frm.master_box.checked == true) {
            frm.master_box.checked = false;
        }
        else {
            frm.master_box.checked = true;
        }
    }


</script>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>?mod=taginator&action=batch" method="POST" name="tag_list">


    <table width="100%">
        <tbody>
        <tr>
            <td width="4"><img width="4" height="4" border="0" src="engine/skins/images/tl_lo.gif"></td>
            <td background="engine/skins/images/tl_oo.gif"><img width="1" height="4" border="0" src="engine/skins/images/tl_oo.gif"></td>
            <td width="6"><img width="6" height="4" border="0" src="engine/skins/images/tl_ro.gif"></td>
        </tr>
        <tr>
            <td background="engine/skins/images/tl_lb.gif"><img width="4" height="1" border="0" src="engine/skins/images/tl_lb.gif"></td>
            <td bgcolor="#FFFFFF" style="padding:5px;">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td height="29" bgcolor="#EFEFEF" style="padding-left:10px;">
                            <div class="navigation">Список тэгов</div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="unterline"></div>
                <table width="100%" border="0">
                    <tbody>
                    <tr>
                        <td align="left"><input type="checkbox" onclick="javascript:ckeck_uncheck_all()" title="Выбрать все" name="master_box"></td>
                        <td style="padding: 3px;">Изображение:</td>
                        <td style="padding: 3px;">Тэг или поисковой запрос:</td>
                         <td style="padding: 3px;">ЧПУ:</td>


                        <td>
                            <div align="center">Популярность</div>
                        </td>
                        <td>
                            <div align="center">Дата создания</div>
                        </td>
                        <td align="right" style="padding: 3px;">Действие</td>

                    </tr>
                    <tr>
                        <td colspan="17">
                            <div class="hr_line"></div>
                        </td>
                    </tr>
                    <?php foreach ($results as $result): ?>
                    <tr>
                        <td width="30" align="left"><input type="checkbox" name="ids[]" value="<?php echo $result['id']; ?>"/></td>
                        <td width="150" style="padding: 2px;">
                            <?php if ($result['image'] !== null || trim($result['image']) !== '') : ?>
                            <img width="80" src="uploads/taginator/<?php echo $result['image']; ?>" class=""/>
                            <?php endif; ?>
                        </td>
                        <td align="left"><?php echo $result['tag']; ?></td>
                        <td align="left"><?php echo $result['slug']; ?></td>


                        <td width="150" align="center"><?php echo $result['popularity']; ?></td>
                        <td width="210" align="center"><?php echo $result['created_at']; ?></td>
                        <td width="60" align="center"><a href="#"
                                                         onclick="return dropdownmenu(this, event, MenuBuild('<?php echo $result['static_id']; ?>', 'edit', '<?php echo $result['id']; ?>'), '150px')"><img
                                border="0"
                                src="engine/skins/images/browser_action.gif"></a>
                        </td>

                    </tr>
                    <tr>
                        <td height="1" background="engine/skins/images/mline.gif" colspan="17"></td>
                    </tr>
                        <?php endforeach ?>

                    <tr>
                        <td colspan="17">
                            <div class="hr_line"></div>
                        </td>
                    </tr>
                    <tr>

                        <td valign="top" align="left" colspan="3">
                            <div style="margin-bottom:5px; margin-top:5px;">
                                <select name="batch_action">
                                    <option value="">-- Действие --</option>
                                    <option value="delete">Удалить</option>
                                </select>
                                <input type="submit" value="Выполнить" class="edit">
                                Всего найдено: <?php echo $pager->total_rows; ?>
                            </div>
                        </td>
                        <td colspan="4">
                            <?php echo $pager->renderFullNav(); ?>
                        </td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <div class="hr_line"></div>
                <input type="hidden" name="action" value="batch"/>
</form>
<table width="100%">
    <tbody>
    <tr>
        <td colspan="17" align="center">
        </td>
    </tr>
    </tbody>
</table>
</td>
<td background="engine/skins/images/tl_rb.gif"><img width="6" height="1" border="0" src="engine/skins/images/tl_rb.gif"></td>
</tr>
<tr>
    <td><img width="4" height="6" border="0" src="engine/skins/images/tl_lu.gif"></td>
    <td background="engine/skins/images/tl_ub.gif"><img width="1" height="6" border="0" src="engine/skins/images/tl_ub.gif"></td>
    <td><img width="6" height="6" border="0" src="engine/skins/images/tl_ru.gif"></td>
</tr>
</tbody>
</table>