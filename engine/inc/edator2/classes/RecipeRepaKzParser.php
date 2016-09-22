<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alex
 * Date: 06.06.12
 * Time: 23:38
 * To change this template use File | Settings | File Templates.
 */
class RecipeRepaKzParser extends ParserCore
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
            $text = trim($dom->find('div.ingredients', 0)->innertext);
            $text = preg_replace('#<h4>Ингредиенты:</h4>#', '', $text);
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

            $text = trim($dom->find('.directions', 0)->innertext);
            $text = $this->convert1251($text);
            
            $text = preg_replace('#<h4>Способ приготовления:</h4>#', '', $text);


            $text = strip_tags($text, '<br><p><table><tr><td>');
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
        return preg_replace('#page/(\d+)/#', "page/$i/", $href);
    }

    public function getPageLinksPageFindPattern()
    {
        return '#page/(\d+)/#';
    }


    public function getPostLinks($url)
    {
        /* @var $dom simple_html_dom  */
        $links = array();
        #var_dump($url);
        $dom = $this->getDom($url);
        if ($dom instanceof simple_html_dom) {
            $fLinks = $dom->find('.posttitle a');
            foreach ($fLinks as $fLink) {
                $links[] = $fLink->href;
            }
        } else {
            return array($url);
        }
        return $links;
    }


}
