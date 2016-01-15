<?php

// Build some templates
$TEMPLATE_DIR = sys_get_temp_dir();
@mkdir("$TEMPLATE_DIR/special");

file_put_contents("$TEMPLATE_DIR/special/hello.php",<<<'EOT'
Hello, <?= $this->name ?>!
EOT
);

file_put_contents("$TEMPLATE_DIR/test.php",<<<'EOT'
TESTIFICATE
EOT
);

file_put_contents("$TEMPLATE_DIR/global.php",<<<'EOT'
<?=$this->THE_DARKNESS?>
EOT
);

file_put_contents("$TEMPLATE_DIR/test_var.php",<<<'EOT'
<?=$this->var?>
EOT
);

file_put_contents("$TEMPLATE_DIR/index_pass.php",<<<'EOT'
[<?= $this->partial('special/hello') ?>]
EOT
);

file_put_contents("$TEMPLATE_DIR/index_override.php",<<<'EOT'
[<?= $this->partial('special/hello',['name'=>'Daryl']) ?>]
EOT
);

// Init View handler
View::using(new View\PHP($TEMPLATE_DIR));

test(View::from('test') == 'TESTIFICATE','View','Render');
test(View::from('test_var')->with(['var'=>1]) == '1','View','Render with parameters');
test(View::from('test_var',['var'=>1]) == '1','View','Render with parameters shorthand');

View::addGlobal('THE_DARKNESS','Jakie');
test(View::from('global') == 'Jakie','View','Global parameters');

test(View::from('special/hello',    ['name'=>'Rick']) == 'Hello, Rick!','View-PHP','Simple render with variables');
test(View::from('index_pass',       ['name'=>'Rick']) == '[Hello, Rick!]','View-PHP','Partials (passing variables)');
test(View::from('index_override',   ['name'=>'Rick']) == '[Hello, Daryl!]','View-PHP','Partials overriding variables');


test(View::exists('special/hello') && !View::exists('im/fake/template'),'View','Template exists');

