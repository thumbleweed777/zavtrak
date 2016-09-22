<?php #Шаблон статической страницы ?>

<h1><?php echo $title ?></h1>
<br>
    <div style="margin-left: auto; margin-right: auto; width: 410px;"><img width="400" alt="<?php echo strtolower( $title) ?>" title="<?php echo strtolower( $title) ?>" src="/uploads/taginator/<?php echo $img_name ?>" /> </div>
    <br />
<br>

    <?php foreach ( $results as $result  ): ?>
        <h4><a href="/<?php echo $result['id'] ?>-<?php echo $result['alt_name'] ?>.html"><?php echo $result['title'] ?></a></h4>
        <p><?php echo $result['short_story'] ?></p>
        <hr />
    <?php endforeach ?>
