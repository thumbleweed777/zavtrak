<table width="100%" cellspacing="0">
    <tr>
        <?php if ($image) : ?>
        <td width="160" style="vertical-align: top;">
            <img title="<?php echo str_replace('"', '\'', $post['title']) ?>" alt="<?php echo str_replace('"', '\'', $post['title']) ?>" width="150" src="<?php echo $image ?>" border="0"/>
        </td>
        <?php endif; ?>
        <td style="vertical-align: top;">
            <p>
                <?php if ($post['ingredients']) : ?>
                    <?php echo $post['ingredients']; ?>
                <?php else: ?>
                    <?php echo substr(strip_tags($post['method'], '<br><ul><li>'), 0, 200) . '...'; ?>
                <?php endif ?>
            </p>
        </td>
    </tr>
</table>

