<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class GotovimSamiParserCore extends ParserCore
{

    public function getTitle()
    {
        /* @var $dom simple_html_dom  */
        try {
            $dom = $this->contentDom;

            $text = $dom->find('h1', 0)->plaintext;
            $text = $this->rewriter->rewrite($text);
        } catch (Exception $e) {
            throw new Exception('error get dom ');
        }
        return $text;
    }

    public function getIngredients()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;
        $text = $dom->find('div.article p', 0)->innertext;
        $text = $this->rewriter->rewrite($text);

        return $text;
    }

    public function getMethod()
    {
        /* @var $dom simple_html_dom  */

        $dom = $this->contentDom;

        $text = '';

        foreach ($dom->find('div.article p') as $k => $v) {
            # var_dump($v);
            $text .= ($k > 1 && !preg_match('#href|div#', $v->outertext)) ? $v->outertext : '';
        }
        $text = $this->rewriter->rewrite($text);


        return $text;
    }


    public function getPageLinksPath()
    {
        return 'div.article div.page div a';
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

        $fLinks = $dom->find('div.name a');
        foreach ($fLinks as $fLink) {
            $links[] = $fLink->href;
        }

        return $links;
    }

}
