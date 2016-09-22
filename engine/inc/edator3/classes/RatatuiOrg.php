<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class RatatuiOrg extends ParserCore
{


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('title', 0)->plaintext);
            $text = preg_replace('#&raquo;.*#', '', $text);

        } else {
            return false;
        }
        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */

        return false;
    }

    public function getMethod()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            $text = trim($dom->find('div[id*=news-id-]', 0)->innertext);
            $text = preg_replace('#\[thumb\].*\[/thumb\]#', '', $text);
            $text = strip_tags($text, '<br><p><table><tr><td><b><ul><li>');
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
        return '.navigation a';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page/(\d+)#', "page/$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page/(\d+)#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('a.ntitle');
        foreach ($fLinks as $fLink) {
            $links[] = $fLink->href;
        }

        return $links;
    }


}
