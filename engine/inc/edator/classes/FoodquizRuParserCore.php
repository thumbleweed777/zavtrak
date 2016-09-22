<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class FoodquizRuParserCore extends ParserCore
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
            $text = trim($dom->find('a.PostHeader', 0)->plaintext);

        } else {
            return false;
        }
        $text = $this->convert1251($text);
        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;
        if ($dom instanceof simple_html_dom) {
            $text = $dom->find('.art-article p', 1);


            $text = preg_replace('#\n#iUs', '', $text);

            $text = $this->convert1251($text);
            $text = $this->rewriter->rewrite($text);
            $text = str_ireplace(' /li>', '</li>', $text);

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
            $text= '';
            foreach ($dom->find('.art-article p') as $k => $v) {
                # var_dump($v);
                $text .= ($k > 2 && !preg_match('#href|div#', $v->outertext)) ? $v->outertext : '';
            }

            $text = preg_replace('#<h1>.*</b>#iUs', '', $text);
            $text = preg_replace('#\n#iUs', '', $text);
            $text = str_ireplace('<br><b>Рецепт приготовления:</b>', '', $text);
            $text = preg_replace('#^\s*<br>#iUs', '', $text);
            $text = $this->convert1251($text);
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
        array_unshift($links, $this->url);

        return $links;
    }


    public function getPageLinksPath()
    {
        return 'a.pagenav';
    }

    public function getPageLinksReplace($href, $i)
    {
        return preg_replace('#ctranitsa-(\d+)#', "ctranitsa-$i", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#ctranitsa-(\d+)#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($url);

        $fLinks = $dom->find('div.art-PostContent table.contentpane tbody tr td form table tbody tr td a');
        foreach ($fLinks as $fLink) {

            $lnk = $fLink->href;

             if (!stripos($lnk, 'ctranitsa') && !stripos($lnk, 'tableOrdering'))
              $links[] = 'http://' . parse_url($this->url, PHP_URL_HOST) . $lnk;
        }


        return $links;
    }

    public function urlCorretor($lnk_r)
    {
        return 'http://' . parse_url($this->url, PHP_URL_HOST) . $lnk_r;
    }

}
