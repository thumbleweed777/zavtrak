<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class CulinarbookRu extends ParserCore
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

            if (!$dom->find('div[style=padding-left:10px; padding-top:3px;]', 0))
                return false;
            $text = '';
            foreach ($dom->find('.ingredient div.anyrow') as $in) {
                $text .= $in->plaintext.'<br>';
            }
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

            $text = trim($dom->find('.instructions', 0)->innertext);
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
        return 'a[href*=page]';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page/(\d+)\.html#', "page/$i.html", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page/(\d+)\.html#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('a[href*=item]');
        foreach ($fLinks as $fLink) {
            $links[] = $this->urlCorretor($fLink->href);
        }

        return $links;
    }


}
