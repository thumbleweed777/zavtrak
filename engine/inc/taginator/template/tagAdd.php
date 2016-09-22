<style type="text/css">
    input[type=text] {
        width: 350px;
    }

    ul.error_list {
        margin-top: 5px;
        list-style: none;
        padding: 4px;
        border: 1px solid #cc3300;
        background-color: #ffcccc;
    }

    ul.error_list li {
        font-weight: bold;
        color: maroon;

    }
    .button1 {
        background: -moz-linear-gradient(19% 75% 90deg, #FF4D01, #FF8924) repeat scroll 0 0 #FF8924;
        border-radius: 5px 5px 5px 5px;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        color: white;
        cursor: pointer;
        font-family: arial,helvetica,sans-serif;
        font-size: 15px;
        font-weight: bold;
        margin-left: 25px;
        padding: 8px 20px;
        background: rgb(254,187,187); /* Old browsers */
        background: -moz-linear-gradient(top,  rgba(254,187,187,1) 0%, rgba(254,144,144,1) 45%, rgba(255,92,92,1) 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(254,187,187,1)), color-stop(45%,rgba(254,144,144,1)), color-stop(100%,rgba(255,92,92,1))); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top,  rgba(254,187,187,1) 0%,rgba(254,144,144,1) 45%,rgba(255,92,92,1) 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top,  rgba(254,187,187,1) 0%,rgba(254,144,144,1) 45%,rgba(255,92,92,1) 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top,  rgba(254,187,187,1) 0%,rgba(254,144,144,1) 45%,rgba(255,92,92,1) 100%); /* IE10+ */
        background: linear-gradient(to bottom,  rgba(254,187,187,1) 0%,rgba(254,144,144,1) 45%,rgba(255,92,92,1) 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#febbbb', endColorstr='#ff5c5c',GradientType=0 ); /* IE6-9 */

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
                        <div class="navigation">
                            Настройка модуля
                            <a class="button1" style="float: right; color: white; " target="_blank" href="/engine/modules/taginator/cron.php">Запуск</a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="unterline"></div>
            <form method="post" action="" enctype="multipart/form-data">
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
			
			
			
					<br />			
					<br />
					<br />
					<b><font color="red">Правила игры ))</font></b><br /><br />
					1) Вставляем кучу ключевиков (до 5000. Если их больше, работаем напрямую с текстовичком <i>/engine/data/taginator.db.txt</i>) и жмем кнопку <b>СОХРАНИТЬ</b><br /><br />
					2) В крон на <u>2 минуты</u> добавляем команду: <b>php /usr/home/*USER*/data/www/*SITE*/engine/modules/taginator/cron.php</b><br /><br />
					3) Жмакаем <b>ЗАПУСК!</b><br /><br />
					4) Тагинатор берет по пять ключей из списка, проверяет, создает и удаляет из списка использованные. Каждые 2 минутги <br /><br />
					<br />
					
					
					
					
					
					
					
			
			
			
			
			
			
			
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