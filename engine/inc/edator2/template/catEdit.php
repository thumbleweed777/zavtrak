<h3>�������������� "<?php echo $cat['title'] ?>"</h3>
<div style="width:500px; height: 300px;">
    <form id="edit_form" action="<?php echo $_SERVER['PHP_SELF'] ?>?mod=<?php echo getModuleName() ?>&action=cat_save&id=<?php echo $cat['id'] ?>" method="post">

        <label for="cat_name">����.</label> <input id="cat_name" type="text" value="<?php echo $cat['title'] ?>" name="cat_name"/>
        <br/>
        <label for="cat_limit">�����.</label> <input id="cat_limit" type="text" value='<?php echo $cat['limit'] ?>' name="cat_limit"/>
        <br/>
        <label for="cat_query">���</label> <input id="cat_query" type="text" value='<?php echo $cat['query'] ?>' name="cat_query"/>
        <br/>
        <label for="p_from">� ��������</label> <input id="p_from" type="text" value='<?php echo $cat['p_from'] ?>' name="p_from"/>
        <span>�� </span> <input id="p_to" type="text" value='<?php echo $cat['p_to'] ?>' name="p_to"/>

        <br clear="all">
        <label>�� �������</label>
        <input type="checkbox" name="allow_main" <?php if($cat['allow_main'] == 1) echo 'checked=1'; ?> value="yes">
        <br clear="all">
        <label >������� ��������</label>
        <input type="checkbox" name="get_image"  <?php  if($cat['get_image'] == 1) echo 'checked=1'  ?> value="yes" >
        <br clear="all"><br clear="all"><br clear="all">

        <a href="#" onclick="$('#edit_form').submit(); return false;" style="color: white" resid="" class="button1">���������</a>

        <a class="button1" style="color: white" onclick="return confirm('Are you sure you want to delete?')" href="<?php echo $_SERVER['PHP_SELF'] ?>?mod=<?php echo getModuleName() ?>&action=cat_delete&id=<?php echo $cat['id'] ?>">�������</a>
    </form>

</div>