
<div style="text-align: center">
    <!--thisIsSparta-->
</div>
<div style="font-weight: bold;">Ингредиенты для <a title="<?php echo $post['n_title'];?>" href="/">рецепта <?php echo $post['n_title']; ?></a></div>

<p><?php echo $post['ingredients']; ?></p>

<div style="font-weight: bold;">Как приготовить <a title="<?php echo $post['n_title'];?>" href="/<?php echo  totranslit($cat['title']) ?>/">рецепт <?php echo $post['n_title']; ?></a>?</div>

<p><?php echo $post['method']; ?></p>
<br clear="all">


