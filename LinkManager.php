<?php

class LinkManager {

    public $errors = array();
    private $url;
    private $title;
    private $img;
    private $meta;
    private $hn;
    private $hn_points;

    public function __construct($url) {
        $this->url = $url;
        if ($this->validateURL() == false) {
            throw new Exception("The provided URL is not valid");
        }

        $this->get_OgMeta();
    }

    public function setTitle($title) {
        $trim_title = htmlspecialchars(stripslashes(trim($title)));
        $titleLength = strlen($trim_title);

        if ($titleLength < 3) {
            throw new Exception("Min length for title is 3 charachters");
        } else if ($titleLength > 80) {
            throw new Exception("Max length for title is 80 charachters");
        }
        $this->title = $trim_title;
    }

    public function getLink() {
        $link = array();

        /*
         * Check if the user has forced a title
         */
        if ($this->title == null) {
            $this->setTitleFromMeta();
        }
        //
        $this->addImage();
        //
        $this->checkHackerNews();
        $link['url'] = $this->url;
        $link['title'] = $this->title;
        $link['img'] = $this->img;
        $link['hn'] = $this->hn;
        $link['hn_points'] = $this->hn_points;
        $link['description'] = $this->meta['og:description'];
        $link['meta'] = $this->meta['og:type'];

        return $link;
    }

    private function setTitleFromMeta() {
        if ($this->meta['og:title'] == null || strlen($this->title) < 3) {
            $this->title = $this->get_titleTag();
        } else {
            $this->title = $this->meta['og:title'];
        }
    }

    private function addImage() {
        if ($this->checkRemoteFile($this->meta['og:image'])) {
            $this->img = $this->meta['og:image'];
        } else {
            $this->img = 'http://s21.postimg.org/byf2ytgnb/imgnotfound.png'; // Place Holder
        }
    }

    private function validateURL() {
        if (!filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            return true;
        } else {
            return false;
        }
    }

    private function get_OgMeta() {
        $html = file_get_contents($this->url);
        libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings
        $doc = new DomDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $metas = $xpath->query($query);
        $rmetas = array();
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            $rmetas[$property] = $content;
        }
        $this->meta = $rmetas;
    }

    private function checkRemoteFile($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== FALSE) {
            return true;
        } else {
            return false;
        }
    }

    private function checkHackerNews() {
        $json = file_get_contents('https://hn.algolia.com/api/v1/search_by_date?query=' . urlencode($this->url));
        $obj = json_decode($json);

        if ($obj->nbHits == 0) {
            return null;
        }
        $result = array();

        $url_ = rtrim($this->url, "/");

        foreach ($obj->hits as $hit) {
            $hit_ = rtrim($hit->url, "/");

            if ($hit_ == $url_) {
                $this->hn = 'https://news.ycombinator.com/item?id=' . $hit->objectID;
                $this->hn_points = ($hit->points);
            }
        }
    }

    private function get_titleTag() {
        $title = array();
        $str = file_get_contents($this->url);
        if (strlen($str) > 0) {
            $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
            preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case
            return $title[1];
        }
    }

}
