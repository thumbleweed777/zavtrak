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

{%��������, ��� ���������� ���������� <?php echo $title; ?>|����������� - ��������� <?php echo $title; ?> ����������� ��������!|�� ���������� <?php echo $title; ?> - �������� ���!|������ ������� ���������� ������� ����� � <?php echo $title; ?> �� ����������!|�������� ����� <?php echo $title; ?> - �� ������ ��?|�����, ���� � ���� ����� �������� ����� ����� ��� <?php echo $title; ?> - ������ �����.|����������� ����� <?php echo $title; ?> ���������� � ������ ������� ����� ����:|�������� ����� <?php echo $title; ?> - ���������� ��� �������|������ ���� � ���� ���-�� �����, � ��� ����� <?php echo $title; ?> - �� ����������!|<?php echo $title; ?> - �������� �����|<?php echo $title; ?> - ������������ �� ������.|<?php echo $title; ?> - ������������� � ��������� � ��������|��� �� ������, � ������ ����� ����� <?php echo $title; ?> ���������� ����� ������ ����|�������, ��� ������ ������ ���� <?php echo $title; ?> ����� ������� ������ � ���������|��� ��� ���������� ����� <?php echo $title; ?> - � ������� �����. ���������� �� ����� �� ����� - ������ � ������� � ����� �� �����.|�������� ����� <?php echo $title; ?> - �������� �� �����|%}
