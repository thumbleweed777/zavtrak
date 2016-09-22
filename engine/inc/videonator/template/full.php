<h2><?php echo $title; ?></h2>
<table width="100%" border="0" cellspacing="10" align="center">
    <tbody>
    <tr>
        <td>
            <div style="text-align: center;"><img alt="<?php echo str_replace('"', '\'', $title) ?>" title="<?php echo str_replace('"', '\'', $title) ?>" width="250" src="<?php echo $image; ?>" border="0" alt=""/></div>
            <div></div>
        </td>
        <td>
            <div style="padding-left: 15px; height: 200px; overflow: -moz-scrollbars-vertical;">
                <?php echo $full; ?>
            </div>

        </td>
    </tr>
    <tr>
        <td style="text-align: center;" colspan="2">
            <object width="480" height="360">
                <param name="movie" value="http://www.youtube.com/v/<?php echo $code; ?>?version=3&amp;hl=ru_RU"></param>
                <param name="allowFullScreen" value="true"></param>
                <param name="allowscriptaccess" value="always"></param>
                <embed src="http://www.youtube.com/v/<?php echo $code; ?>?version=3&amp;hl=ru_RU" type="application/x-shockwave-flash" width="480" height="360" allowscriptaccess="always" allowfullscreen="true"></embed>
            </object>
        </td>
    </tr>
    </tbody>
</table>

{%Надеемся, Вам понравился видеоролик <?php echo $title; ?>|Согласитесь - видеролик <?php echo $title; ?> заслуживает внимания!|Вы посмотрели <?php echo $title; ?> - заходите еще!|Всегда приятно посмотреть хорошее видео и <?php echo $title; ?> не исключение!|Классный ролик <?php echo $title; ?> - не правда ли?|Думаю, если в сети будет побольше таких видео как <?php echo $title; ?> - станет лучше.|Рекомендуем кроме <?php echo $title; ?> посмотреть и другие похожие видео ниже:|Шикарное видео <?php echo $title; ?> - посмотрите еще похожие|Каждый день в сети что-то новое, и это видео <?php echo $title; ?> - не исключение!|<?php echo $title; ?> - классный ролик|<?php echo $title; ?> - пересмотрите на досуге.|<?php echo $title; ?> - рекомендуется к просмотру с друзьями|Что ни говори, а раньше такое видео <?php echo $title; ?> посмотреть нигде нельзя было|Здорово, что сейчас ролики типа <?php echo $title; ?> можно увидеть онлайн в интернете|Для Вас посмотреть ролик <?php echo $title; ?> - в порядке вещей. Технологии не стоят на месте - раньше и мечтать о таком не могли.|Отличное видео <?php echo $title; ?> - побольше бы таких|%}
