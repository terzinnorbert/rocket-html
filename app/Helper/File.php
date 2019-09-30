<?php


namespace App\Helper;


class File
{
    public static function url($baseUrl, $name)
    {
        return $baseUrl . '/' . self::safeName($name);
    }

    public static function safeName($name)
    {
        return str_replace(['/', ' ', ',', ':',], ['_', '_', '', ''], urldecode($name));
    }

    public function save($destination, $content)
    {
        if (!is_dir(dirname($destination))) {
            $this->createPath(dirname($destination));
        }
        file_put_contents($destination, $content);
    }

    protected function createPath($path)
    {
        mkdir($path, 0777, true);
    }
}