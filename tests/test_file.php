<?php

File::mount('temp','native',[
	'root' => sys_get_temp_dir(),
]);

File::mount('mem','memory');

test(json_encode(File::mounts()) == '["temp","mem"]','File','Mount');


File::write('mem://my/file.txt','Hello World!');

test(File::exists('mem://my/file.txt'),'File','Exists');
test(File::read('mem://my/file.txt')=='Hello World!','File','Read');
test(File::read('mem://my/file.txt')=='Hello World!','File','Write');

File::append('mem://my/cool/data.txt','1');
File::append('mem://my/cool/data.txt','2');
File::append('mem://my/cool/data.txt','3');

test(File::read('mem://my/cool/data.txt')=='123','File','Append');
test(File::read('mem://my/./cool/foo/../data.txt')=='123','File','Resolve path');

File::write('temp://core-test.txt','TESTIFICATE');
test(File::read('core-test.txt')=='TESTIFICATE','File','Find');

test(implode('|',File::search('*.txt'))=="temp://core-test.txt|mem://my/file.txt|mem://my/cool/data.txt",'File','Search');
