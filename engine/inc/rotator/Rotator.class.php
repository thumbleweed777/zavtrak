<?php


if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}


class Rotator
{
    protected $config, $tpl, $dom;


    public function __construct($p_config)
    {

        define(ROTATOR_DIR, ENGINE_DIR . '/inc/rotator');
        define(ROTATOR_TPL_DIR, ROTATOR_DIR . '/template');

        require_once ENGINE_DIR . '/vendor/util/ctTemplate.class.php';
        require_once ENGINE_DIR . '/vendor/simplehtmldom/simple_html_dom.php';

        $config = parse_ini_file(ENGINE_DIR . '/data/rotator.config.ini');

        $this->config = array(
            'show_title' => $config['show_title'],
            'rows' => $config['rows'],
            'columns' => $config['columns'],
            'img_width' => $config['img_width'],
            'img_height' => $config['img_height'],
            'cats' => $config['cats'],
            'title_pos' => $config['title_pos'],
        );

        $this->config = array_merge($this->config, $p_config);


        $this->config['limit'] = $this->config['rows'] * $this->config['columns'];

        $this->tpl = new ctTemplate();
        $this->tpl->setBaseDir(ROTATOR_TPL_DIR);


    }


    public function  __toString()
    {

        $html = $this->tpl->loadTemplate('block', array('config' => $this->config, 'posts' => $this->getPosts()));

        return $html;
    }


    protected function getPosts()
    {
        global $db;

        $cats = implode('|', $this->config['cats']);
        $table = PREFIX . "_post";

        $query = "SELECT *
                   FROM (
                     SELECT id FROM dle_post WHERE category REGEXP '[[:<:]]({$cats})[[:>:]]' ORDER BY RAND() LIMIT {$this->config['limit']}
                   )
                   AS ids JOIN dle_post ON dle_post.id = ids.id
                   ";

        #$q = "SELECT id, title, alt_name, full_story FROM {$table} WHERE category REGEXP '[[:<:]]({$cats})[[:>:]]' AND id IN({$in}) LIMIT {$this->config['limit']} ";
        $posts = $db->super_query($query, true);

        # var_dump($posts);

        $r_posts = array();
        foreach ($posts as $post) {
            $n_p['id'] = $post['id'];
            $n_p['title'] = $post['title'];
            $n_p['alt_name'] = $post['alt_name'];
            $n_p['img'] = $this->getImage($post['full_story']);

            $r_posts[] = $n_p;
        }

        return $r_posts;

    }


    protected function getImage($string)
    {
        $dom = str_get_html($string);

        $img = $dom->find('img', 0);

        $dom->__destruct();
        $dom = null;
        unset($dom);

        if (!$img || strlen(trim($img->src)) == 0) {
            return '/uploads/no_foto.png';
        }

        return $img->src;
    }

}

