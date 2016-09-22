<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class EdaserverRu extends ParserCore
{

    public $PostLinksPath, $PageLinksPath, $PageLinksPattern, $charsetConvert;

    public function  __construct($charset = 'cp1251', $url, $pageF, $pageT, $config)
    {
        parent::__construct($charset, $url, $pageF, $pageT, $config);

        $this->PostLinksPath = 'table[width*=95] a';
        $this->PageLinksPath = '[href*=page]';
        $this->PageLinksPattern = 'page=(\d+)';
        $this->charsetConvert = false;
    }


    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        $dom = $this->contentDom;

        if ($dom instanceof simple_html_dom) {
            $text = trim($dom->find('p strong', 0)->plaintext);

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

            $text = '';
            foreach ($dom->find('html body table tbody tr td p') as $k => $f) {
                if ($k > 0)
                    $text .= trim($f->innertext);
            }


            $text = preg_replace('#<br><b>Рецепт\s+прочитали.*$#i', '', $text);

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


    protected function urlCorretor($lnk_r)
    {
        $lnk_r = trim($lnk_r);
        if (preg_match('#\&amp;#', $lnk_r))
            $lnk_r = htmlspecialchars_decode($lnk_r);

        $parseUrl = parse_url($this->url);

        $parseUrl['path'] =preg_replace('#[^/]+$#', '', $parseUrl['path'] );

        if (preg_match('#http://#', $lnk_r)) {
              return $lnk_r;
        } elseif (preg_match('#^/#', $lnk_r)) {
                return 'http://' . $parseUrl['host'] . $lnk_r;
        } else {
           return 'http://' . $parseUrl['host'] .$parseUrl['path'] . $lnk_r;
        }

    }

}
