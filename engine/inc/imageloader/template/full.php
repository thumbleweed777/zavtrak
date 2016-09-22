<?php if ($image) : ?>
<div style="text-align: center">
    <img style="max-width: 500px;" title="<?php echo str_replace('"', '\'', $post['title']) ?>" alt="<?php echo str_replace('"', '\'', $post['title']) ?>" src="<?php echo $image ?>" border="0"/>
</div>
<?php endif; ?>

<?php if ($post['ingredients']) : ?>
<div style="font-weight: bold;">Ингредиенты для "<a title="<?php echo str_replace('"', '\'', $post['title']) ?>" href="/"><?php echo $post['title']; ?></a>"</div>
<p><?php echo $post['ingredients']; ?></p>
<br>
<?php endif; ?>
<div style="font-weight: bold;">Способ приготовления "<a title="<?php echo str_replace('"', '\'', $post['title']) ?>" href="/<?php echo  totranslit($cat['title']) ?>/"><?php echo $post['title']; ?></a>"</div>

<p><?php echo $post['method']; ?></p>

<br clear="all">

