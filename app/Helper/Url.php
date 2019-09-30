<?php


namespace App\Helper;


class Url
{
    public function safe($url)
    {
        $url = $this->addEndSlash($url);
        return $this->addScheme($url);
    }

    protected function addEndSlash($url)
    {
        return trim($url, '/') . '/';
    }

    protected function addScheme($url)
    {
        return null === parse_url($url, PHP_URL_SCHEME) ?
            'https://' . $url : $url;
    }


}