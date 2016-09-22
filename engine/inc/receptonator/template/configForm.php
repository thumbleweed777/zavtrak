

<?php if ($saved !== null) : ?>
<link rel="stylesheet" type="text/css" href="/engine/inc/receptonator/template/css/notice/notice.css">
<div style="width: 99%;margin-left: auto; margin-right: auto;">
    <?php if ($saved == true) : ?>
    <div class="msg msg-ok"><p> Настройки успешно сохранены</p></div>
    <?php endif; ?>
    <?php if ($saved == false) : ?>
    <div class="msg msg-error"><p>Возникли проблемы при сохранении настроек</p></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<style type="text/css">
    input[type=text] {
        width: 300px;
    }
    textarea {
       width: 400px;
    }
</style>

<table width="100%">
    <tbody>
    <tr>
        <td width="4"><img width="4" height="4" border="0" src="engine/skins/images/tl_lo.gif"></td>
        <td background="engine/skins/images/tl_oo.gif"><img width="1" height="4" border="0"
                                                            src="engine/skins/images/tl_oo.gif"></td>
        <td width="6"><img width="6" height="4" border="0" src="engine/skins/images/tl_ro.gif"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img width="4" height="1" border="0"
                                                            src="engine/skins/images/tl_lb.gif"></td>
        <td bgcolor="#FFFFFF" style="padding:5px;">


            <table width="100%">
                <tbody>
                <tr>
                    <td height="29" bgcolor="#EFEFEF" style="padding-left:10px;">
                        <div class="navigation">Настройка модуля</div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="unterline"></div>
            <form method="post" action="">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>

                    <?php echo $form; ?>

                    <tr>
                        <td align="center" colspan="3">
                            <input type="submit" value="Сохранить" class="edit">
                    </tr>
                    </tbody>
                </table>
            </form>
        </td>
        <td background="engine/skins/images/tl_rb.gif"><img width="6" height="1" border="0"
                                                            src="engine/skins/images/tl_rb.gif"></td>
    </tr>
    <tr>
        <td><img width="4" height="6" border="0" src="engine/skins/images/tl_lu.gif"></td>
        <td background="engine/skins/images/tl_ub.gif"><img width="1" height="6" border="0"
                                                            src="engine/skins/images/tl_ub.gif"></td>
        <td><img width="6" height="6" border="0" src="engine/skins/images/tl_ru.gif"></td>
    </tr>
    </tbody>
</table>