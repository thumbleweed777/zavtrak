<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/engine/inc/<?php echo getModuleName() ?>/template/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/engine/inc/<?php echo getModuleName() ?>/template/js/fancybox/jquery.fancybox-1.3.4.css"/>


<br clear="all"/>
<div class="auto">
    <div style="display: none;">
        <div id="add_form">
            <form id="new_form" action="" method="post">
                <label for="cat_name">Назв.</label> <input id="cat_name" type="text" name="cat_name"/><br/>
                <label for="cat_limit">Лимит.</label> <input id="cat_limit" type="text" value="1000" name="cat_limit"/><br/>
                <label for="cat_query">Урл</label> <input id="cat_query" type="text" value="<?php echo $cat['query'] ?>" name="cat_query"/><br/>
                <br/>
                <label for="p_from">С страницы</label> <input id="p_from" type="text" value='<?php echo $cat['p_from'] ?>' name="p_from"/>
                <span>по </span> <input id="p_to" type="text" value='<?php echo $cat['p_to'] ?>' name="p_to"/>
                <br/>
                <label for="allow_main">На главной</label>
                <input id="allow_main" type="checkbox" name="allow_main" checked=1 value="yes">
                <br clear="all">
                <label for="get_image">Парсить картинку</label>
                <input id="get_image" type="checkbox" name="get_image" checked=1 value="yes">
                <br clear="all"><br clear="all"><br clear="all">
                <a href="#" onclick="$(this).parent('form').submit(); return false;" style="color: white" resid="" class="button1 ">Добавить</a>
                <br/><br/>

            </form>
        </div>
    </div>

    <a style="color: white" href="#" class="button1" onclick="$.fancybox({
            'content'  : $('#add_form').clone()
            });" accesskey="">Добавить категорию</a>

    <a style="color: white; float: right;" target="_blank" href="/engine/inc/<?php echo getModuleName() ?>/parser.php" class="button1 green">Запуск</a>
</div>

<div style="padding-top:5px;padding-bottom:2px;">
    <table id="rounded-corner" width="90%">
        <tr>
            <th>id</th>
            <th>Категория</th>
            <th>Урл</th>
            <th>Напарсено</th>
            <th>Лимит</th>

            <th>Редактир.</th>
            <th>Удал.</th>
            <th>Запуск</th>
        </tr>

        <?php foreach ($results as $res) : ?>

        <tr>
            <td><?php echo $res['id'] ?></td>
            <td> <?php echo $res['title'] ?></td>
            <td> <?php echo $res['query'] ?></td>
            <td><?php echo $res['v_count'] ?> </td>

            <td><?php echo $res['limit'] ?> </td>
            <td>
                <a href="#" onclick="$.fancybox({
                    'href'  : '<?php echo $_SERVER['PHP_SELF'] ?>?mod=<?php echo getModuleName() ?>&action=cat_edit&id=<?php echo $res['id'] ?>'
                    });" accesskey="">Редактировать</a>

            </td>
            <td>

                <a style="color: #ff0000;" href="#" onclick="

                    if (confirm('Не промазал?')) {

                    $.fancybox({
                    'href'            : '<?php echo $_SERVER['PHP_SELF'] ?>?mod=<?php echo getModuleName() ?>&action=cat_full_delete&id=<?php echo $res['id'] ?>'
                    });
                    }
                    " accesskey="">Удалить</a>
            </td>

            <td>
                <a target="_blank" style="color: white" href="/engine/inc/<?php echo getModuleName() ?>/parser.php?cat_id=<?php echo $res['id'] ?>" class="button1">Пуск</a>
            </td>
        </tr>
        <?php endforeach; ?>


    </table>
    <hr >
    <ul>
        <li>http://www.kylinar.com.ua/cooking/25</li>
        <li>http://recipe.repa.kz/category/%D0%B8%D0%BD%D0%B4%D0%B8%D0%B9%D1%81%D0%BA%D0%B0%D1%8F/</li>
        <li>http://epovar.kz/recipes/method/1-cook</li>
        <li>http://www.culinarbook.ru/recipe/cuisine40.html</li>
        <li>http://mactep.com.ua/ru/salati/salati-gribnie.html</li>
        <li>http://ratatui.org/pervye-blyuda/borsch/</li>
    </ul>
    <span><b>Краткий мануал:</b></span><br>

<p>
    В поле <b>УРЛ</b> указывать страницу категории, где непосредсвенно есть ссылки на рецепты и пагинация. 
Настройки страниц работают на всех сайтах, но на <b>http://recipe.repa.kz/</b> - их указывать обязательно.

</p>


</div>



<style type="text/css">
    #cat_name {
        width: 150px;
    }

    #cat_query {
        width: 250px;
    }

    #cont_path {
        width: 250px;
    }

    #cat_limit {
        width: 40px;
    }

    #rounded-corner {
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
        font-size: 12px;
        text-align: left;
        border-collapse: collapse;
        width: 100%;
        margin-top: 15px;
    }

    #rounded-corner thead th.rounded-company {
        background: #b9c9fe url('table-images/left.png') left -1px no-repeat;
    }

    #rounded-corner thead th.rounded-q4 {
        background: #b9c9fe url('table-images/right.png') right -1px no-repeat;
    }

    #rounded-corner th {
        padding: 8px;
        font-weight: normal;
        font-size: 13px;
        color: White;
        background: #00b7ea; /* Old browsers */
        background: -moz-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #00b7ea), color-stop(100%, #009ec3)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#00b7ea', endColorstr = '#009ec3', GradientType = 0); /* IE6-9 */
        background: linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* W3C */
    }

    #rounded-corner td {
        padding: 8px;

        border-top: 1px solid #fff;
        color: #669;
    }

    #rounded-corner tfoot td.rounded-foot-left {
        background: #e8edff url('table-images/botleft.png') left bottom no-repeat;
    }

    #rounded-corner tfoot td.rounded-foot-right {
        background: #e8edff url('table-images/botright.png') right bottom no-repeat;
    }

    #rounded-corner tbody tr:hover td {
        background: #d0dafd;
    }

    .button1 {
        cursor: pointer;
        margin-left: 25px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        background: -moz-linear-gradient(19% 75% 90deg, #FF4D01, #FF8924);
        background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FF8924), to(#FF4D01));
        color: white;
        font-family: arial, helvetica, sans-serif;
        font-size: 15px;
        font-weight: bold;
        padding: 8px 20px;
        background-color: #FF8924;
    }

    .green {
        background-color: #35ff91;
    }

    #new_form label, #edit_form label {
        color: #808080;
        text-align: right;
        font-family: Tahoma;
        font-size: 12px;
        display: block;
        float: left;
        width: 100px;
        line-height: 20px;
        padding-right: 10px;
    }

</style>




