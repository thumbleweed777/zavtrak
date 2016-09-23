<table cellpadding="10" cellspacing="10">

    <tr>
        <?php  $i = 1; ?>
        <?php foreach ($posts as $post): ?>


        <td>
            <a href="/<?php echo $post['id'] ?>-<?php echo $post['alt_name'] ?>.html">
            
            
                <?php if ($config['show_title'] && $config['title_pos'] == 'top') : ?>
                <div style="text-align: center" class="rotator_title"><?php echo $post['title'] ?></div>
                <?php endif; ?>
                
                <?php if ($config['show_title'] && $config['title_pos'] == 'right') : ?>
                <table>
                <tr>
               
                <td>
                   <div style="" class="rotator_img">
                    <img height="<?php echo $config['img_height'] ?>" width="<?php echo $config['img_width'] ?>" src="/engine/vendor/phpThumb/phpThumb.php?src=<?php echo urlencode($post['img']) ?>&w=<?php echo $config['img_width'] ?>&h=<?php echo $config['img_height'] ?>&q=100&zc=1" alt="<?php echo $post['title'] ?>">
                    <?php if ($config['show_title'] && $config['title_pos'] == 'bottom') : ?>
                    <div style="text-align: center" class="rotator_title"><?php echo $post['title'] ?></div>
                    <?php endif; ?>
                    </div>
                </td>
                 <td width="150" style="vertical-align: middle; padding-left: 10px;"><?php echo $post['title'] ?></td>
                </tr>
                </table>
 
                <?php else: ?>
                     <div style="text-align: center" class="rotator_img">
                    <img height="<?php echo $config['img_height'] ?>" width="<?php echo $config['img_width'] ?>" src="/engine/vendor/phpThumb/phpThumb.php?src=<?php echo urlencode($post['img']) ?>&w=<?php echo $config['img_width'] ?>&h=<?php echo $config['img_height'] ?>&q=100&zc=1" alt="<?php echo $post['title'] ?>">
                    <?php if ($config['show_title'] && $config['title_pos'] == 'bottom') : ?>
                    <div style="text-align: center" class="rotator_title"><?php echo $post['title'] ?></div>
                    <?php endif; ?>
                </div>           
                
                <?php endif; ?>


            </a>
        </td>

        <?php if ($i % $config['columns'] == 0 && $i < count($posts)) : ?>
            <?php echo '</tr><tr>' ?>
            <?php endif ?>

        <?php $i++; ?>
        <?php endforeach ?>
    </tr>

</table>