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


File::mount('assets','zip',[
	//'root' => sys_get_temp_dir().'/'.time().'_core_test.zip'
]);

File::write('assets://info/manifest.txt','YEAH!');
test(File::exists('assets://info/manifest.txt'),'File','ZIP Exists');
test(File::read('assets://info/manifest.txt')=='YEAH!','File','ZIP Read/Write');

test(array_search("assets://info/manifest.txt", File::search("*"))!==false,'File','ZIP Search');

