<?php

// To the end of: www/config.php

define('DELETE_CACHE', true);
if (defined('DELETE_CACHE') && is_dir('./cache')) {
    // clean up twig folder
    if (is_dir('./cache/twig')) {
        $rrmdir = function ($dir) use (&$rrmdir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                    $rrmdir($dir."/".$object);
                    else
                    unlink($dir."/".$object);
                }
                }
                rmdir($dir);
            }
        };
        $rrmdir('./cache/twig');
    }
    // clean up cache folder
    foreach (glob('./cache/*.php') as $cache_file) unlink($cache_file);
}
