<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/engine/inc/videonator/template/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/engine/inc/videonator/template/js/fancybox/jquery.fancybox-1.3.4.css"/>


<br clear="all"/>
<div class="auto">

    <form id="new_form" action="" method="post">
        <label for="cat_name">����.</label> <input id="cat_name" type="text" name="cat_name"/>
        <label for="cat_limit">�����.</label> <input id="cat_limit" type="text" name="cat_limit"/>
        <label for="cat_query">������</label> <input id="cat_query" type="text" value="<?php echo $cat['query'] ?>" name="cat_query"/>
        <label for="cat_words">��. �����</label>
        <textarea id="cat_words" rows="2" cols="30" name="cat_words"></textarea>
        <a href="#" onclick="$('#new_form').submit(); return false;" style="color: white" resid="" class="button1">��������</a>
    </form>

</div>

<div style="padding-top:5px;padding-bottom:2px;">
    <table id="rounded-corner" width="90%">
        <tr>
            <th>id</th>
            <th>���������</th>
            <th>������</th>
            <th>��. �����</th>
            <th>���������</th>
            <th>�����</th>
            <th>��������.</th>
            <th>����.</th>
        </tr>

        <?php foreach ($results as $res) : ?>

        <tr>
            <td><?php echo $res['id'] ?></td>
            <td> <?php echo $res['title'] ?></td>
            <td> <?php echo $res['query'] ?></td>
            <td> <?php echo $res['words'] ?></td>
            <td><?php echo $res['v_count'] ?> </td>
            <td><?php echo $res['limit'] ?> </td>
            <td>
                <a href="#" onclick="$.fancybox({
                    'href'  : '?mod=videonator&action=cat_edit&id=<?php echo $res['id'] ?>'
                    });" accesskey="">�������������</a>

            </td>
            <td>

                <a style="color: #ff0000;" href="#" onclick="

                    if (confirm('�� ��������?')) {

                    $.fancybox({
                    'href'            : '?mod=videonator&action=cat_full_delete&id=<?php echo $res['id'] ?>'
                    });
                    }
                    " accesskey="">�������</a>
            </td>
        </tr>
        <?php endforeach; ?>


    </table>
</div>



<style type="text/css">
    #cat_name {
        width: 150px;
    }

    #cat_query {
        width: 350px;
    }

    #cat_limit {
        width: 40px;
    }

    #rounded-corner {
        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
        font-size: 12px;
        text-align: left;
        border-collapse: collapse;
        width: 100%;
        margin-top: 15px;
    }

    #rounded-corner thead th.rounded-company {
        background: #b9c9fe url('table-images/left.png') left -1px no-repeat;
    }

    #rounded-corner thead th.rounded-q4 {
        background: #b9c9fe url('table-images/right.png') right -1px no-repeat;
    }

    #rounded-corner th {
        padding: 8px;
        font-weight: normal;
        font-size: 13px;
        color: White;
        background: #00b7ea; /* Old browsers */
        background: -moz-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #00b7ea), color-stop(100%, #009ec3)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr = '#00b7ea', endColorstr = '#009ec3', GradientType = 0); /* IE6-9 */
        background: linear-gradient(top, #00b7ea 0%, #009ec3 100%); /* W3C */
    }

    #rounded-corner td {
        padding: 8px;

        border-top: 1px solid #fff;
        color: #669;
    }

    #rounded-corner tfoot td.rounded-foot-left {
        background: #e8edff url('table-images/botleft.png') left bottom no-repeat;
    }

    #rounded-corner tfoot td.rounded-foot-right {
        background: #e8edff url('table-images/botright.png') right bottom no-repeat;
    }

    #rounded-corner tbody tr:hover td {
        background: #d0dafd;
    }

    .button1 {
        cursor: pointer;
        margin-left: 25px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.6);
        background: -moz-linear-gradient(19% 75% 90deg, #FF4D01, #FF8924);
        background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FF8924), to(#FF4D01));
        color: white;
        font-family: arial, helvetica, sans-serif;
        font-size: 15px;
        font-weight: bold;
        padding: 8px 20px;
        background-color: #FF8924;
    }

    .button1:hover {
        background: -moz-linear-gradient(19% 75% 90deg, #EB4701, #F58423);
        background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#F58423), to(#EB4701));
    }


</style>




