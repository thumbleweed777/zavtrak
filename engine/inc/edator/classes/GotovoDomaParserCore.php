<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class GotovoDomaParserCore extends ParserCore
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
            preg_match('#<table\s+border="0">(.+)</table>#iUs', $dom->outertext, $t);

            echo ($dom->outertext);
            $text = preg_replace('#\n#iUs', '', $t[0]);
            $text = $this->rewriter->rewrite($text);
        } else {
            return false;
        }
var_dump($dom->outertext); die;

        return $text;
    }

    public function getMethod()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            $text = trim($dom->find('.text', 0)->innertext);

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
        array_unshift($links, $this->url);

        # var_dump($links); die;
        return $links;
    }


    public function getPageLinksPath()
    {
        return '.navigation a';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page/(\d+)/#', "page/$i/", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page/(\d+)/$#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('div div strong a');
        foreach ($fLinks as $fLink) {

            $lnk = $fLink->href;

            if (stripos($lnk, '.html'))
                $links[] = $lnk;
        }


        return $links;
    }

    public function urlCorretor($lnk_r)
    {
        return $lnk_r;
    }

}
