<div style="text-align: center">
    <!--thisIsSparta-->
</div>

<?php if ($post['ingredients']) : ?>
<div style="font-weight: bold;">Ингредиенты для "<a title="<?php echo str_replace('"', '\'', $post['title']) ?>" href="/"><?php echo $post['title']; ?></a>"</div>
<p><?php echo $post['ingredients']; ?></p>
<br>
<?php endif; ?>
<div style="font-weight: bold;">Способ приготовления "<a title="<?php echo str_replace('"', '\'', $post['title']) ?>" href="/<?php echo  totranslit($cat['title']) ?>/"><?php echo $post['title']; ?></a>"</div>

<p><?php echo $post['method']; ?></p>

<br clear="all">

