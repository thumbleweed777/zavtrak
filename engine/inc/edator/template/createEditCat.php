<div id="add_form">
    <form id="new_form" action="" method="post">
        <label for="cat_name">Назв.</label> <input id="cat_name" type="text" name="cat_name"/><br/>
        <label for="cat_limit">Лимит.</label> <input id="cat_limit" type="text" name="cat_limit"/><br/>
        <label for="cat_query">Урл</label> <input id="cat_query" type="text" value="<?php echo $cat['query'] ?>" name="cat_query"/><br/>
        <br/>
        <label for="cat_words">Варианты заголовков</label>
        <textarea id="cat_words" rows="5" cols="60" name="cat_words"><?php echo $cat['words'] ?></textarea>
        <br clear="all">
        <label for="cat_words">На главной</label>
        <input type="checkbox" name="allow_main" <?php if ($cat['allow_main'] == 1) echo 'checked=1'; ?> value="yes">
        <br clear="all">
        <label for="cat_words">Парсить картинку</label>
        <input type="checkbox" name="get_image"  <?php  if ($cat['get_image'] == 1) echo 'checked=1'  ?> value="yes">
        <br clear="all"><br clear="all"><br clear="all">
        <a href="#" onclick="$(this).parent('form').submit(); return false;" style="color: white" resid="" class="button1 ">Добавить</a>
        <br/><br/>

    </form>
</div>