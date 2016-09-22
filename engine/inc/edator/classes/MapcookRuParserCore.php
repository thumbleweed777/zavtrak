<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class MapcookRuParserCore extends ParserCore
{


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('h1', 0)->plaintext);

        } else {
            return false;
        }
        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;
        if ($dom instanceof simple_html_dom) {
            $text = $dom->find('div.content div.center_right div.center ul', 1)->innertext;


            $text = preg_replace('#\n#iUs', '', $text);


            $text = $this->rewriter->rewrite($text);
            $text = str_ireplace(' /li>', '</li>', $text);

        } else {
            return false;
        }


        return '<ul style="list-style:none">' . $text . '</ul>';
    }

    public function getMethod()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            $text = trim($dom->find('div.wrapper-inner div.wrapper div.shadowLeft div.shadowRight div.container div.content div.center_right div.center p', 2)->innertext);

            $text = preg_replace('#<h1>.*</b>#iUs', '', $text);
            $text = preg_replace('#\n#iUs', '', $text);
            $text = str_ireplace('<br><b>Рецепт приготовления:</b>', '', $text);
            $text = preg_replace('#^\s*<br>#iUs', '', $text);

            $text = $this->rewriter->rewrite($text);
        } else {
            return false;
        }

        return $text;
    }

    public function getPageLinks()
    {

        $links = parent::getPageLinks();

        return $links;
    }


    public function getPageLinksPath()
    {
        return '.pagination a';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#cat_p=(\d+)#', "cat_p=$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#cat_p=(\d+)#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('.right a[href*=catalog]');
        foreach ($fLinks as $fLink) {

            $lnk = $fLink->href;

            if (stripos($lnk, 'catalog'))
                $links[] = 'http://' . parse_url($this->url, PHP_URL_HOST) . $lnk;
        }

        foreach ($links as $k => $l) {
            preg_match('#/catalog/(\d+)/#', $l, $yy);

            $links[$k] = 'http://www.mapcook.ru/' . $yy[1];
        }

        unset($links[0]);
        return $links;
    }

    public function urlCorretor($lnk_r)
    {
        return 'http://' . parse_url($this->url, PHP_URL_HOST) . $lnk_r;
    }

}
