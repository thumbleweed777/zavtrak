<?php if ($saved !== null) : ?>
<link rel="stylesheet" type="text/css" href="/engine/inc/receptonator/template/css/notice/notice.css">
<div style="width: 99%;margin-left: auto; margin-right: auto;">
    <?php if ($saved == true) : ?>
    <div class="msg msg-ok"><p> Ќастройки успешно сохранены</p></div>
    <?php endif; ?>
    <?php if ($saved == false) : ?>
    <div class="msg msg-error"><p>¬озникли проблемы при сохранении настроек</p></div>
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
                        <div class="navigation">Ќастройка модул€</div>
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
                            <input type="submit" value="—охранить" class="edit">
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



<p> ¬ данном разделе наход€тс€ настройки по умолчанию. “оесть если в шаблоне заинклудить ротатор без параметров(ра) например: </p>
<p><b>{include file="/engine/modules/rotator.php"}</b></p>
<p>то все настройки будут по умолчанию использованы с этой страницы</p>
<p>ѕример инклуда ротатора со <b>всеми</b> параметрами:</p>
<p><b>{include file="/engine/modules/rotator.php?&columns=2&rows=2&show_title=1&title_pos=bottom&img_width=50&img_height=50&cats=181,182"}</b></p>
    <p>ќписание параметров:</p>
        <ul style="list-style: none">
            <li><b>columns</b> - количество колонок  </li>
            <li><b>rows</b> - количество строк  </li>
            <li><b>show_title</b> - показывать тайтл? 1 - да 0 - нет</li>
            <li><b>title_pos</b> - позици€ тайтла, top | bottom </li>
            <li><b>img_width, img_height</b>  - высота ширина картинок  </li>
            <li><b>cats</b> - id категорий с которых брать посты, указывать через зап€тую. ѕример cats=1,2,3,4,5,6,7 </li>
        </ul>


<p>«аинклудить ротатор можно практически в любой шаблон. ƒл€ вывода в определенных категори€х можно использовать DLE-шные тэги <b>[category=X]текст[/ category]</b></p>

<p>777 - на папку <b>/engine/vendor/phpThumb/cache/source</b>,  и при изменении настроек 777 на папку data с файлами</p>

<p>-----------------------------</p>
<p>/engine/inc/rotator/template/block.php - шаблон блока</p>