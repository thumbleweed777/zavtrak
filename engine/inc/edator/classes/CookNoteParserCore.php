<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class CookNoteParserCore extends ParserCore
{
    public function  __construct($charset = 'cp1251', $url, $pageF, $pageT, $config)
    {
        parent::__construct($charset, $url, $pageF, $pageT, $config);

        $this->charset = 'utf-8';
    }


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;
        if ($dom instanceof simple_html_dom) {
            $text = $dom->find('h1', 0)->plaintext;
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
            $text = $dom->find('.field-field-products p', 0)->innertext;
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

        $text = '';
        if ($dom instanceof simple_html_dom) {
            foreach ($dom->find('.field-field-process p') as $k => $v) {
                # var_dump($v);
                $text .= (!preg_match('#href|div#', $v->outertext)) ? $v->outertext : '';
            }
            $text = $this->convert1251($text);
            $text = $this->rewriter->rewrite($text);
        } else {
            return false;
        }
        return $text;
    }


    public function getPageLinksPath()
    {
        return 'li.pager-item a';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#page=(\d+)#', "page=$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#(\d+)$#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('.node-title a');
        foreach ($fLinks as $fLink) {
            $lnk = preg_match('#http://#', $fLink->href) ? $fLink->href : 'http://' . parse_url($this->url, PHP_URL_HOST) . $fLink->href;
            $links[] = $lnk;
        }

        return $links;
    }

}
