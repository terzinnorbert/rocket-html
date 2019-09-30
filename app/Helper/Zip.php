<?php

namespace App\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class Zip
{
    public function create($source, $destination)
    {
        $zip = new ZipArchive();
        if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
            $source = realpath($source);
            if (is_dir($source)) {
                $this->addDirectory($zip, $source);
            } elseif (is_file($source)) {
                $zip->addFile($source, basename($source));
            }
        }

        return $zip->close();
    }

    protected function addDirectory(ZipArchive $zip, $source)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source,
            RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = realpath($file);
            if (is_dir($file)) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } else {
                if (is_file($file)) {
                    $zip->addFile($file, str_replace($source . '/', '', $file));
                }
            }
        }
    }
}