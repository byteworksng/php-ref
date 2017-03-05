#!/usr/bin/env php
<?php

/*
 * This file is part of the pinepain/php-ref PHP extension.
 *
 * Copyright (c) 2016-2017 Bogdan Padalko <pinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source or visit
 * http://opensource.org/licenses/MIT
 */

chdir(__DIR__ . DIRECTORY_SEPARATOR . '..');

$contents = [
    'stubs'                        => 'doc',
    'tests'                        => 'test',
    'config.m4'                    => 'src',
    'config.w32'                   => 'src',
    'LICENSE'                      => 'doc',
    'php_ref.h'                    => 'src',
    'php_ref_functions.c'          => 'src',
    'php_ref_functions.h'          => 'src',
    'php_ref_notifier_exception.c' => 'src',
    'php_ref_notifier_exception.h' => 'src',
    'php_ref_reference.c'          => 'src',
    'php_ref_reference.h'          => 'src',
    'README.md'                    => 'doc',
    'ref.c'                        => 'src',
];

$rules = [
    'test' => [
        '/\.phpt$/',
        '/^\..+\.php$/',
    ],
];

$files = [];

$files[] = '<!-- begin files list -->';

foreach ($contents as $location => $role) {
    if (is_dir($location)) {

        $dir_files = [];
        /** @var SplFileInfo $filename */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($location)) as $filename) {
            if ($filename->isDir()) {
                continue;
            }

            $location = $filename->getPathname();

            if (!is_file($location)) {
                throw new Exception("'{$location}' is not a file");
            }


            if (isset($rules[$role])) {
                $matches = false;
                foreach ($rules[$role] as $rule) {
                    if ($matches = preg_match($rule, $filename->getFilename())) {
                        break;
                    }
                }

                if (!$matches) {
                    continue;
                }
            }

            $dir_files[] = "            <file name=\"{$location}\" role=\"{$role}\" />";
        }

        sort($dir_files);

        $files = array_merge($files, $dir_files);

        continue;
    }

    if (!is_file($location)) {
        throw new Exception("'{$location}' is not a file");
    }

    $files[] = "            <file name=\"{$location}\" role=\"{$role}\" />";
}

$files[] = '            <!-- end files list -->';

$package = file_get_contents('package.xml');

$start = preg_quote('<!-- begin files list -->');
$end   = preg_quote('<!-- end files list -->');

$package = preg_replace("/{$start}.+{$end}/s", implode("\n", $files), $package);

file_put_contents('package-new.xml', $package);
