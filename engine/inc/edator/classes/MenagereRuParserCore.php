<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class MenagereRuParserCore extends ParserCore
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
            $text = trim($dom->find('i b', 0)->innertext);
            $text = preg_replace('#\n#iUs', '', $text);
            $text = $this->rewriter->rewrite($text);
        } else {
            return false;
        }


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

        $links = array_unique( parent::getPageLinks());
        unset($links[0]);

        return $links;
    }


    public function getPageLinksPath()
    {
        return '.nomer a';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page=(\d+)#', "page=$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page=(\d+)$#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('td.news a');
        foreach ($fLinks as $fLink) {

            $lnk = preg_replace('#index\.php#', $fLink->href, $this->url);
            $links[] = $lnk;
        }

        return $links;
    }

    public function urlCorretor($lnk_r)
    {
        return preg_replace('#index\.php#', $lnk_r, $this->url);
    }

}
