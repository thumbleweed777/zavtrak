<?php if ($saved !== null) : ?>
<link rel="stylesheet" type="text/css" href="/engine/inc/receptonator/template/css/notice/notice.css">
<div style="width: 99%;margin-left: auto; margin-right: auto;">
    <?php if ($saved == true) : ?>
    <div class="msg msg-ok"><p> ��������� ������� ���������</p></div>
    <?php endif; ?>
    <?php if ($saved == false) : ?>
    <div class="msg msg-error"><p>�������� �������� ��� ���������� ��������</p></div>
    <?php endif; ?>
</div>
<?php endif; ?>
<style type="text/css">
    input[type=text] {
        width: 300px;
    }

    textarea {
        width: 400px;
    }
</style>

<table width="100%">
    <tbody>
    <tr>
        <td width="4"><img width="4" height="4" border="0" src="engine/skins/images/tl_lo.gif"></td>
        <td background="engine/skins/images/tl_oo.gif"><img width="1" height="4" border="0"
                                                            src="engine/skins/images/tl_oo.gif"></td>
        <td width="6"><img width="6" height="4" border="0" src="engine/skins/images/tl_ro.gif"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img width="4" height="1" border="0"
                                                            src="engine/skins/images/tl_lb.gif"></td>
        <td bgcolor="#FFFFFF" style="padding:5px;">


            <table width="100%">
                <tbody>
                <tr>
                    <td height="29" bgcolor="#EFEFEF" style="padding-left:10px;">
                        <div class="navigation">��������� ������</div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="unterline"></div>
            <form method="post" action="">
                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody>

                    <?php echo $form; ?>

                    <tr>
                        <td align="center" colspan="3">
                            <input type="submit" value="���������" class="edit">
                    </tr>
                    </tbody>
                </table>
            </form>
        </td>
        <td background="engine/skins/images/tl_rb.gif"><img width="6" height="1" border="0"
                                                            src="engine/skins/images/tl_rb.gif"></td>
    </tr>
    <tr>
        <td><img width="4" height="6" border="0" src="engine/skins/images/tl_lu.gif"></td>
        <td background="engine/skins/images/tl_ub.gif"><img width="1" height="6" border="0"
                                                            src="engine/skins/images/tl_ub.gif"></td>
        <td><img width="6" height="6" border="0" src="engine/skins/images/tl_ru.gif"></td>
    </tr>
    </tbody>
</table>



<p> � ������ ������� ��������� ��������� �� ���������. ������ ���� � ������� ����������� ������� ��� ����������(��) ��������: </p>
<p><b>{include file="/engine/modules/rotator.php"}</b></p>
<p>�� ��� ��������� ����� �� ��������� ������������ � ���� ��������</p>
<p>������ ������� �������� �� <b>�����</b> �����������:</p>
<p><b>{include file="/engine/modules/rotator.php?&columns=2&rows=2&show_title=1&title_pos=bottom&img_width=50&img_height=50&cats=181,182"}</b></p>
    <p>�������� ����������:</p>
        <ul style="list-style: none">
            <li><b>columns</b> - ���������� �������  </li>
            <li><b>rows</b> - ���������� �����  </li>
            <li><b>show_title</b> - ���������� �����? 1 - �� 0 - ���</li>
            <li><b>title_pos</b> - ������� ������, top | bottom </li>
            <li><b>img_width, img_height</b>  - ������ ������ ��������  </li>
            <li><b>cats</b> - id ��������� � ������� ����� �����, ��������� ����� �������. ������ cats=1,2,3,4,5,6,7 </li>
        </ul>


<p>����������� ������� ����� ����������� � ����� ������. ��� ������ � ������������ ���������� ����� ������������ DLE-���� ���� <b>[category=X]�����[/ category]</b></p>

<p>777 - �� ����� <b>/engine/vendor/phpThumb/cache/source</b>,  � ��� ��������� �������� 777 �� ����� data � �������</p>

<p>-----------------------------</p>
<p>/engine/inc/rotator/template/block.php - ������ �����</p>