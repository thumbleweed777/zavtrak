<table width="100%" cellspacing="0">
    <tr>

        <td width="160" style="vertical-align: top;">
            <!--thisIsSparta-->
        </td>


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

