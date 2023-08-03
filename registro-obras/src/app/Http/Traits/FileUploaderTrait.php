<?php

namespace App\Http\Traits;

trait FileUploaderTrait {
    public function getUploadMaxSize()
    {
        return $this->file_upload_max_size();
    }

    public function getFormattedMaxSize()
    {
        return $this->format_size(
            $this->file_upload_max_size()
        );
    }
    
    // https://api.drupal.org/api/drupal/includes%21file.inc/function/file_upload_max_size/7.x
    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    private function file_upload_max_size() {
        static $max_size = -1;
        
        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = $this->parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }
            
            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }
    
    // https://api.drupal.org/api/drupal/includes%21common.inc/function/parse_size/7.x
    private function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }
    
    private function format_size($size, $langcode = NULL) {
        if ($size < 1024) {
            return $size > 1 ? $size . 'bytes' : '1 byte';
        } else {
            $size = $size / 1024;

            $units = [ 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];

            foreach ($units as $unit) {
                if (round($size, 2) >= 1024) {
                    $size = $size / 1024;
                } else break;
            }
        }
        
        return round($size, 2) . $unit;
    }
}