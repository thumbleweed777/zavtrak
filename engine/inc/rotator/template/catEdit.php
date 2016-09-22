<h3>Редактирование "<?php echo $cat['title'] ?>"</h3>
<div style="width:500px; height: 300px;">
    <form id="edit_form" action="/admin.php?mod=receptonator&action=cat_save&id=<?php echo $cat['id'] ?>" method="post">

        <label for="cat_name">Назв.</label> <input id="cat_name" type="text" value="<?php echo $cat['title'] ?>" name="cat_name"/>
        <br />
        <label for="cat_limit">Лимит.</label> <input id="cat_limit" type="text" value='<?php echo $cat['limit'] ?>' name="cat_limit"/>
        <br />
        <label for="cat_query">Запрос</label> <input id="cat_query" type="text" value='<?php echo $cat['query'] ?>' name="cat_query"/>
        <br />

        <label for="cat_words">Кл. слова</label>
        <textarea id="cat_words" rows="5" cols="60" name="cat_words"><?php echo $cat['words'] ?></textarea>
        <br ><br >
        <a href="#" onclick="$('#edit_form').submit(); return false;" style="color: white" resid="" class="button1">Сохранить</a>

        <a class="button1" style="color: white" onclick="return confirm('Are you sure you want to delete?')" href="admin.php?mod=receptonator&action=cat_delete&id=<?php echo $cat['id'] ?>">Удалить</a>
    </form>

</div>