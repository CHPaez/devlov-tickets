<?php
// Standalone extractor: find __('...') / __("...") calls across the client+staff UI
// and print unique msgids, one per line, so we can hand-translate the high-visibility ones.

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags) ?: [];
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) ?: [] as $dir) {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

$roots = [
    '/app/include',
    '/app/css',
    '/app/scp',
    '/app/assets/default',
];

$strings = [];
foreach ($roots as $root) {
    foreach (glob_recursive("$root/*.php") as $f) {
        $src = file_get_contents($f);
        // __('...') or __("...") — non-greedy, handles escaped quotes minimally
        if (preg_match_all('/\b__\(\s*([\'"])((?:\\\\.|(?!\1).)*)\1/s', $src, $m)) {
            foreach ($m[2] as $s) {
                $s = str_replace(["\\'", '\\"'], ["'", '"'], $s);
                if (trim($s) === '') continue;
                $strings[$s] = true;
            }
        }
    }
}

ksort($strings);
fwrite(STDERR, "Found " . count($strings) . " unique strings\n");
foreach (array_keys($strings) as $s) {
    echo $s . "\n";
}
