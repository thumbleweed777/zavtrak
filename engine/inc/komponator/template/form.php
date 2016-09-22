<link href="/engine/inc/komponator/template/css/bootstrap.css" rel="stylesheet">
<form action="/engine/inc/komponator/dump.php" method="post" target="_blank">

    <table class="table table-hover">
        <tr>
            <th>Категория</th>
            <th>Количество</th>
            <th>Сколько брать</th>
        </tr>
        <?php foreach ($cats as $cat): ?>
        <tr>
            <td><?php echo $cat['parentid'] > 0 ? ' -- ' : '' ?><?php echo $cat['name'] ?></td>
            <td><?php echo $cat['post_count'] ?></td>
            <td>
                <input class="span2" type="text" value="0" name="cats[<?php echo $cat['id'] ?>]">
            </td>
        </tr>
        <?php endforeach ?>
    </table>
    <div class="form-actions">
        <button class="btn" type="submit"><i class="icon-arrow-down"></i> Скомпоновать</button>
    </div>
</form>