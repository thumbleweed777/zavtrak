<?php
/**
 * Created by Aleksandr Moroz.
 * Date: 06.06.12
 * Time: 17:25
 */
class ParserCore
{

    var $contentDom, $http, $url, $pageF, $pageT, $config, $rewriter, $charset;


    public function  __construct($charset = 'cp1251', $url, $pageF, $pageT, $config)
    {

        $this->http = new Zend_Http_Client(null, array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
        $this->url = $url;
        $this->pageF = $pageF;
        $this->pageT = $pageT;
        $this->config = $config;
        $this->charset = $charset;
        $this->rewriter = new Rewriter($config['synonims']);
    }


    /**
     *
     * @return string
     */
    public function getTitle()
    {


    }

    /**
     *
     * @return string
     */
    public function getIngredients()
    {


    }

    /**
     *
     * @return string
     */
    public function getMethod()
    {


    }

    /**
     *
     * @return array
     */
    public function getPageLinks()
    {

        /* @var $dom simple_html_dom  */
        $links = array();

        $dom = $this->getDom($this->url);

        if ($dom instanceof simple_html_dom) {
            if ($fLinks = $dom->find($this->getPageLinksPath())) {
                $max = 1;
                foreach ($fLinks as $link) {
                    preg_match($this->getPageLinksPageFindPattern(), $link->href, $p);

                    $max = ($p[1] > $max) ? $p[1] : $max;
                }

                $links = array();

                $start = $this->pageF > 0 ? $this->pageF : 1;
                $end = $this->pageT > 0 ? $this->pageT : $max;

                if ($this->pageF == 0)
                    $links[] = $this->urlCorretor($this->url);

                for ($i = $start; $i < $end + 1; $i++) {
                    $lnk_r = $this->getPageLinksReplace($fLinks[0]->href, $i);
                    $lnk = $this->urlCorretor($lnk_r);
                    $links[] = $this->urlCorretor($lnk);

                }
            } else {
                $links[] = $this->url;
            }
        }

        $links = array_unique($links);
        return $links;
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

    protected function urlCorretor($lnk_r)
    {
        $lnk_r = trim($lnk_r);
        if (preg_match('#\&amp;#', $lnk_r))
            $lnk_r = htmlspecialchars_decode($lnk_r);

        $parseUrl = parse_url($this->url);

        if (preg_match('#http://#', $lnk_r)) {
              return $lnk_r;
        } elseif (preg_match('#^/#', $lnk_r)) {
                return 'http://' . $parseUrl['host'] . $lnk_r;
        } else {
             return 'http://' . $parseUrl['host']  . '/'.$lnk_r;
        }

    }

    /**
     * @param $url string
     * @return simple_html_dom
     */
    public function setContent($url)
    {
        //reset dom
        if ($this->contentDom instanceof simple_html_dom) {
            $this->contentDom->__destruct();
            $this->contentDom = null;
        }

        $this->contentDom = $this->getDom($url, $this->charset);
        #  var_dump( $url); die;

    }

    /**
     *
     * @return simple_html_dom
     */
    public function getDom($url, $charset = 'cp1251', $lowercase = true, $forceTagsClosed = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT)
    {
        if (!$str = $this->getContent($url)) return false;

        $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $charset, $defaultBRText);
        if (empty($str)) {
            $dom->clear();
            throw new Exception('Cant load DOM ' . $url);
            return false;
        }

        $dom->load($str, $lowercase, $stripRN);

        if (!$dom  instanceof simple_html_dom) {
            throw new Exception('Cant load DOM ' . $url);
        }
        return $dom;
    }

    /**
     *
     *
     * @return string
     */
    public function getContent($url)
    {

        $this->http->getAdapter()->setCurlOption(CURLOPT_INTERFACE, $this->getIp());
        $this->http->getAdapter()->setCurlOption(CURLOPT_TIMEOUT, 15);
        $this->http->getAdapter()->setCurlOption(CURLOPT_FOLLOWLOCATION, true);

        $this->http->setUri($url);


        try {
            $response = $this->http->request('GET');

        } catch (Exception $e) {

            return false;


        }


        if ($response->getStatus() == 200) {
            return $response->getBody();
        } else {
            return false;
        }

    }

    function getGoogleImg($k, $cat_alias, $alias)
    {
        $k = mb_convert_encoding($k, 'UTF-8', 'CP1251');

        $http = new Zend_Http_Client('http://images.google.com/images?tbs=isz:m&as_q=' . urlencode($k), array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
        $http->getAdapter()->setCurlOption(CURLOPT_INTERFACE, get_ip_array());
        $response = $http->request('GET');
        $html = str_get_html($response->getBody());
        foreach ($html->find('a') as $element) {

            $result = $element->href;

            if (preg_match('#(?:http://)?(http(s?)://([^\s]*)\.(jpg|png))#', $result, $imagelink)) {

                $img = save_thumb($imagelink[1], $cat_alias, $alias);
                if ($img) {
                    return $img;
                }
            }

        }

        return false;
    }

    /**
     * @return string
     */
    private function getIp()
    {
        ob_start();
        $ips = array();
        $ifconfig = system("ifconfig");
        echo $ifconfig;
        $ifconfig = ob_get_contents();
        ob_end_clean();
        $ifconfig = explode(chr(10), $ifconfig);
        for ($i = 0; $i < count($ifconfig); $i++) {
            $t = explode(" ", $ifconfig[$i]);
            if ($t[0] == "\tinet" && $t[1] !== '127.0.0.1' && preg_match('#91\.238\.245#', $t[1])) {
                array_push($ips, $t[1]);
            }
        }
        #var_dump($ips); die;
        # return '192.168.2.105';
        return $ips[rand(0, count($ips) - 1)];
    }

    public function convert1251($string)
    {
        $string = str_ireplace('?', '1/2', $string);
        $string = mb_convert_encoding($string, 'cp1251', 'utf-8');
        $string = preg_replace('#\?{2,50}#iUs', '', $string);
        return $string;
    }

}
