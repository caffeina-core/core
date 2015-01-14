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

test(array_search("temp://core-test.txt", File::search("*.txt"))!==false,'File','Search');

// ZIP
test(File::write('temp://tests/1.json','YEAH!')
	&& File::write('temp://my/test/test_file.txt','123'),'File','Native Write');
test(File::exists('temp://tests/1.json'),'File','Native Exists');
test(File::read('temp://tests/1.json')=='YEAH!','File','Native Read');
test(File::move('temp://tests/1.json','temp://tests/1.xml'),'File','Native Move');	
test(array_search("temp://my/test/test_file.txt", File::search("*"))!==false,'File','Native Search');

// ZIP
File::mount('assets','zip'); // created in temp dir
test(File::write('assets://info/manifest.txt','YEAH!')
	&& File::write('assets://some/good/test.txt','123'),'File','ZIP Write');
test(File::exists('assets://info/manifest.txt'),'File','ZIP Exists');
test(File::read('assets://info/manifest.txt')=='YEAH!','File','ZIP Read');
test(File::move('assets://info/manifest.txt','assets://info/manifest.info'),'File','ZIP Move');	
test(array_search("assets://some/good/test.txt", File::search("*"))!==false,'File','ZIP Search');

