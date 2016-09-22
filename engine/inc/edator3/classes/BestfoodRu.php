<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class BestfoodRu extends ParserCore
{

    public $PostLinksPath, $PageLinksPath, $PageLinksPattern, $charsetConvert;

    public function  __construct($charset = 'cp1251', $url, $pageF, $pageT, $config)
    {
        parent::__construct($charset, $url, $pageF, $pageT, $config);

        $this->PostLinksPath = '.rl a';
        $this->PageLinksPath = '[href*=nonexist]';
        $this->PageLinksPattern = 'list-(\d+)';
        $this->charsetConvert = false;
    }


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('h1', 1)->plaintext);

        } else {
            return false;
        }
        if ($this->charsetConvert) {
            $text = $this->convert1251($text);
        }

        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */


        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {

            if (!$dom->find('em', 0))
                return false;

            $text = $dom->find('em', 0)->innertext;

        #    $text = preg_replace('#<strong>Ингредиенты\:</strong>#i', '', $text);

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

            $text = trim($dom->find('.instructions', 0)->innertext);

            #    $text = preg_replace('#<br><b>Рецепт\s+прочитали.*$#i', '', $text);

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
