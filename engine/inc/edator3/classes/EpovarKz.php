<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class EpovarKz extends ParserCore
{


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('h1', 0)->plaintext);

            $text = $this->convert1251($text);

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

            $text = '';
            foreach ($dom->find('td.title') as $in) {
                $text .= $in->plaintext . '<br>';
            }
            $text = $this->convert1251($text);
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

            $text = trim($dom->find('.steps ul', 0)->outertext);

            $text = $this->convert1251($text);

            $text = preg_replace('#<h3><span class="num">\?</span> Действие №\d+</h3>#', '', $text);

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

        unset($links[0]);

        return array_values( $links);
    }


    public function getPageLinksPath()
    {
        return 'a[href*=page]';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page=(\d+)#', "page=$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page=(\d+)#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('a[href*=view]');
        foreach ($fLinks as $fLink) {
            $links[] = $this->urlCorretor($fLink->href);
        }

        return $links;
    }


}
