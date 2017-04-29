<?php
$path = __DIR__ . "/src/Core";
$fqcns = [];

$allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
$phpFiles = new RegexIterator($allFiles, '/\.php$/');
foreach ($phpFiles as $phpFile) {
    $content = file_get_contents($phpFile->getRealPath());
    $tokens = token_get_all($content);
    $namespace = '';
    for ($index = 0; isset($tokens[$index]); $index++) {
        if (!isset($tokens[$index][0])) {
            continue;
        }
        if (T_NAMESPACE === $tokens[$index][0]) {
            $index += 2; // Skip namespace keyword and whitespace
            while (isset($tokens[$index]) && is_array($tokens[$index])) {
                $namespace .= $tokens[$index++][1];
            }
        }
        if (T_CLASS === $tokens[$index][0] || T_TRAIT === $tokens[$index][0] || T_INTERFACE === $tokens[$index][0]) {
            $index += 2; // Skip class keyword and whitespace
            $fqcns[] = trim($namespace.'\\'.$tokens[$index][1],'\\');
        }
    }
}

$classes = json_encode($fqcns);
echo <<<"EOS"
spl_autoload_register(function(\$c){
  static \$l = $classes;
  return in_array(\$c,\$l) && class_alias("Core\\\\\$c",\$c, false);
});
EOS;
