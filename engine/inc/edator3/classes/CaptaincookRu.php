<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class CaptaincookRu extends ParserCore
{

    public $PostLinksPath, $PageLinksPath, $PageLinksPattern, $charsetConvert;

    public function  __construct($charset = 'cp1251', $url, $pageF, $pageT, $config)
    {
        parent::__construct($charset, $url, $pageF, $pageT, $config);

        $this->PostLinksPath = 'table.container tbody tr td.container_title a';
        $this->PageLinksPath = '.pages a';
        $this->PageLinksPattern = '-(\d+).html';
        $this->charsetConvert = false;
    }


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('.container_title', 0)->plaintext);

        } else {
            return false;
        }
        if ($this->charsetConvert) {
            $text = $this->convert1251($text);
        }
        $text = ucfirst($text);

        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */

        return false;

        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            if (!$dom->find('#ingredients', 0))
                return false;

            $text = $dom->find('#ingredients', 0)->outertext;

            $text = $this->rewriter->rewrite($text);

        } else {
            return false;
        }

        if ($this->charsetConvert) {
            $text = $this->convert1251($text);
        }
        return $text;
    }

    public function getMethod()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            $text = trim($dom->find('#fontnews', 0)->innertext);
            $text = strip_tags($text, '<br><p><table><tr><td><b><ul><li><ol>');
            $text = $this->rewriter->rewrite($text);

        } else {
            return false;
        }
        if ($this->charsetConvert) {
            $text = $this->convert1251($text);
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
        return $this->PageLinksPath;
    }

    public function getPageLinksReplace($href, $i)
    {
        $pattern = $this->PageLinksPattern;
        $replace = str_ireplace('(\d+)', $i, $pattern);
        return preg_replace("#{$pattern}#", $replace, $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return "#{$this->PageLinksPattern}#i";
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find($this->PostLinksPath);
        foreach ($fLinks as $fLink) {
            $links[] = $this->urlCorretor($fLink->href);
        }

        return $links;
    }


}
