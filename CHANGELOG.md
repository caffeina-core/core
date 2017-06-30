## 1.7.7

`65ce6bd` **[New] Redirect::to HTTP status support** _by Stefano Azzolini_  
`c292dfb` **[Fix] Changed request host parameter for backwards compatibility** _by Gabriele Diener_  
`e7339a6` **[New] added getter for attachments in envelope** _by Gabriele Diener_  
`0114529` **[Fix] Fixed Request::URI problem on php self webserver** _by Stefano Azzolini_  
`08ce7be` **[] Fixed Request::URI bug on some servers** _by Stefano Azzolini_  
`1437d3d` **Update Redirect.php** _by Stefano Azzolini_  
`60b62ec` **Removed hhvm from tests** _by Stefano Azzolini_  
`253fae6` **[New] Added Filters as a trait** _by Stefano Azzolini_  
`156be42` **Fix check message filter** _by Gabriele Diener_  
`7719e99` **Saving work done by : stefano.azzolini** _by Stefano Azzolini_  

## 1.7.6

`4904d60` **Fixed** _by Stefano Azzolini_  
`626acb0` **Fixed typo** _by Stefano Azzolini_  

## 1.7.5

`9abbb59` **[New] HTTP::proxy support** _by Stefano Azzolini_  
`bbdca3a` **New nested test** _by Stefano Azzolini_  
`360df87` **[Chg] Added JSON_BIGINT_AS_STRING to standard json response** _by Stefano Azzolini_  
`9fe8111` **[Fix] Now, also SQL::insert honors the primary_key override, like SQL::update does.** _by Stefano Azzolini_  
`c8222da` **[Fix] Strenghtened Request URI, IP and HOST from some proxy related issues.** _by Stefano Azzolini_  
`6e06d0e` **Fixed AWS-SES problem with multipart emails** _by Stefano Azzolini_  
`a32c4f1` **Added console event to Email::SMTP driver** _by Stefano Azzolini_  
`9f954a4` **[New] Added CSV->SQL method** _by Stefano Azzolini_  
`61b2ac4` **Fix Persistence onSave** _by Gabriele Diener_  
`b615d9b` **Fixed Model::create security by checking decslred properties in  method.** _by Stefano Azzolini_  
`76217f6` **Update README.md** _by Stefano Azzolini_  
`efce409` **Update README.md** _by Stefano Azzolini_  
`36299e6` **Update README.md** _by Stefano Azzolini_  
`6a42719` **Fix Request::host with forwarded host** _by Gabriele Diener_  
`7236b10` **[Fix] Fixed null saving for Persistence when object method is falsy** _by Stefano Azzolini_  
`d946595` **Some cleanings on request class** _by Stefano Azzolini_  
`d7b6300` **[Fix] filter_input breaks HTTP_X_FORWARDED_HOST** _by Stefano Azzolini_  
`09f82bf` **[New] HTTP/2 Server Push support** _by Stefano Azzolini_  
`d861eff` **Cleaned Check and Errors hooks** _by Stefano Azzolini_  
`13968e4` **Force int value on model's count** _by Gabriele Diener_  
`e1e49a8` **Added instance of view as the second parameter in route** _by Gabriele Diener_  
`cd30c07` **[Fix] Response::CORS allow-origin now defaults to * (all)** _by Stefano Azzolini_  
`222afed` **[New] Text::cut string extractor** _by Stefano Azzolini_  
`4424089` **Added raw_output parameter to hash's make methods** _by Gabriele Diener_  
`70ea7ee` **Fix shell execCommand** _by Gabriele Diener_  
`14a4913` **Added count method in Model and tests** _by Gabriele Diener_  
`39f2d62` **Cleaned Model class** _by Gabriele Diener_  
`65d75b3` **Fixed route group regression** _by Stefano Azzolini_  
`12101af` **Augmented route events** _by Stefano Azzolini_  
`1ffb85d` **Fix: Route::on event listener now renamed as Route::onEvent** _by Stefano Azzolini_  

## 1.7.4-pre

`360df87` **[Chg] Added JSON_BIGINT_AS_STRING to standard json response** _by Stefano Azzolini_  
`9fe8111` **[Fix] Now, also SQL::insert honors the primary_key override, like SQL::update does.** _by Stefano Azzolini_  
`c8222da` **[Fix] Strenghtened Request URI, IP and HOST from some proxy related issues.** _by Stefano Azzolini_  
`6e06d0e` **Fixed AWS-SES problem with multipart emails** _by Stefano Azzolini_  
`a32c4f1` **Added console event to Email::SMTP driver** _by Stefano Azzolini_  
`9f954a4` **[New] Added CSV->SQL method** _by Stefano Azzolini_  
`61b2ac4` **Fix Persistence onSave** _by Gabriele Diener_  
`b615d9b` **Fixed Model::create security by checking decslred properties in  method.** _by Stefano Azzolini_  
`76217f6` **Update README.md** _by Stefano Azzolini_  
`efce409` **Update README.md** _by Stefano Azzolini_  
`36299e6` **Update README.md** _by Stefano Azzolini_  
`6a42719` **Fix Request::host with forwarded host** _by Gabriele Diener_  
`7236b10` **[Fix] Fixed null saving for Persistence when object method is falsy** _by Stefano Azzolini_  
`d946595` **Some cleanings on request class** _by Stefano Azzolini_  
`d7b6300` **[Fix] filter_input breaks HTTP_X_FORWARDED_HOST** _by Stefano Azzolini_  
`09f82bf` **[New] HTTP/2 Server Push support** _by Stefano Azzolini_  
`d861eff` **Cleaned Check and Errors hooks** _by Stefano Azzolini_  
`13968e4` **Force int value on model's count** _by Gabriele Diener_  
`e1e49a8` **Added instance of view as the second parameter in route** _by Gabriele Diener_  
`cd30c07` **[Fix] Response::CORS allow-origin now defaults to * (all)** _by Stefano Azzolini_  
`222afed` **[New] Text::cut string extractor** _by Stefano Azzolini_  
`4424089` **Added raw_output parameter to hash's make methods** _by Gabriele Diener_  
`70ea7ee` **Fix shell execCommand** _by Gabriele Diener_  
`14a4913` **Added count method in Model and tests** _by Gabriele Diener_  
`39f2d62` **Cleaned Model class** _by Gabriele Diener_  
`65d75b3` **Fixed route group regression** _by Stefano Azzolini_  
`12101af` **Augmented route events** _by Stefano Azzolini_  
`1ffb85d` **Fix: Route::on event listener now renamed as Route::onEvent** _by Stefano Azzolini_  

## 1.7.4

`360df87` **[Chg] Added JSON_BIGINT_AS_STRING to standard json response** _by Stefano Azzolini_  
`9fe8111` **[Fix] Now, also SQL::insert honors the primary_key override, like SQL::update does.** _by Stefano Azzolini_  
`c8222da` **[Fix] Strenghtened Request URI, IP and HOST from some proxy related issues.** _by Stefano Azzolini_  
`6e06d0e` **Fixed AWS-SES problem with multipart emails** _by Stefano Azzolini_  
`a32c4f1` **Added console event to Email::SMTP driver** _by Stefano Azzolini_  
`9f954a4` **[New] Added CSV->SQL method** _by Stefano Azzolini_  
`61b2ac4` **Fix Persistence onSave** _by Gabriele Diener_  
`b615d9b` **Fixed Model::create security by checking decslred properties in  method.** _by Stefano Azzolini_  
`76217f6` **Update README.md** _by Stefano Azzolini_  
`efce409` **Update README.md** _by Stefano Azzolini_  
`36299e6` **Update README.md** _by Stefano Azzolini_  
`6a42719` **Fix Request::host with forwarded host** _by Gabriele Diener_  
`7236b10` **[Fix] Fixed null saving for Persistence when object method is falsy** _by Stefano Azzolini_  
`d946595` **Some cleanings on request class** _by Stefano Azzolini_  
`d7b6300` **[Fix] filter_input breaks HTTP_X_FORWARDED_HOST** _by Stefano Azzolini_  
`09f82bf` **[New] HTTP/2 Server Push support** _by Stefano Azzolini_  
`d861eff` **Cleaned Check and Errors hooks** _by Stefano Azzolini_  
`13968e4` **Force int value on model's count** _by Gabriele Diener_  
`e1e49a8` **Added instance of view as the second parameter in route** _by Gabriele Diener_  
`cd30c07` **[Fix] Response::CORS allow-origin now defaults to * (all)** _by Stefano Azzolini_  
`222afed` **[New] Text::cut string extractor** _by Stefano Azzolini_  
`4424089` **Added raw_output parameter to hash's make methods** _by Gabriele Diener_  
`70ea7ee` **Fix shell execCommand** _by Gabriele Diener_  
`14a4913` **Added count method in Model and tests** _by Gabriele Diener_  
`39f2d62` **Cleaned Model class** _by Gabriele Diener_  
`65d75b3` **Fixed route group regression** _by Stefano Azzolini_  
`12101af` **Augmented route events** _by Stefano Azzolini_  
`1ffb85d` **Fix: Route::on event listener now renamed as Route::onEvent** _by Stefano Azzolini_  

## 1.7.3

`bdbd262` **[Chg] Changed Route::as to Route::tagged for 5.6 incompatibilities** _by Stefano Azzolini_  
`9d37038` **Cleaned Route Test** _by Stefano Azzolini_  
`0d54f83` **[New] Added named routes and reverse routing support** _by Stefano Azzolini_  
`0d861ec` **Added Response::on('sent') hook** _by Stefano Azzolini_  
`833b760` **Fixed fully optional root parameters in routes** _by Stefano Azzolini_  
`0934457` **Further route speed optimizations** _by Stefano Azzolini_  
`14ebf38` **Removed unused variable** _by Stefano Azzolini_  
`a364086` **Route::dispatch can now just return the route without invoking it** _by Stefano Azzolini_  
`7097ba3` **Further optimized route tree** _by Stefano Azzolini_  
`d19678e` **Polished new route tree optimizer code** _by Stefano Azzolini_  
`170c5b3` **Optimize routes on add methods** _by Stefano Azzolini_  
`b666eee` **Route auto-optimize subtree** _by Stefano Azzolini_  
`4139c3d` **Route sub-tree optimization** _by Stefano Azzolini_  
`75d07b6` **Fixed comma** _by Stefano Azzolini_  
`e425104` **[New] Route::reset for clear all stored routes.** _by Stefano Azzolini_  
`c1cd728` **Chg: Added PDO params to model** _by Gabriele Diener_  
`dd8ef34` **Fixes: some cleanings** _by Stefano Azzolini_  
`cf2cc75` **Fixed: mispelled property in HTTP_response** _by Stefano Azzolini_  
`a654703` **Fixed: The variable  does not seem to be defined in Envelope.php** _by Stefano Azzolini_  
`5d6dd3d` **Enum::__constructor now is final** _by Stefano Azzolini_  
`2730037` **[New] Enum data type support** _by Stefano Azzolini_  
`61861a5` **Fixed typo** _by Stefano Azzolini_  
`8bd09cc` **[Fix] Now  returns also port if different from 80** _by Stefano Azzolini_  
`824b6f0` **Docs** _by Stefano Azzolini_  
`e4e49c3` **Fix sql reduce** _by Gabriele Diener_  
`9744b20` **Added row index in each for CSV** _by Flavio De Stefano_  
`639a548` **Fix: SQL:exec error trigger now pass the driver-dependent error message.** _by Stefano Azzolini_  
`62e025d` **New: Added SQL reduce method works as array_reduce with results of query** _by Gabriele Diener_  
`6a867d9` **Chg: SQL each method return boolean value on fail or with callback** _by Gabriele Diener_  
`5739f2f` **Chg: SQL single method return a value from callback** _by Gabriele Diener_  
`78dfa01` **Build entire library as phar** _by Stefano Azzolini_  
`5499222` **Build antire library as phar** _by Stefano Azzolini_  
`5c8da7f` **New: Events trait** _by Stefano Azzolini_  
`05e8ae1` **Now fully optional routes works correctly in route groups** _by Stefano Azzolini_  
`213db09` **New route group prefix system** _by Stefano Azzolini_  
`08f9e37` **Cleaned route group code** _by Stefano Azzolini_  
`43ba309` **Fix: dynamic route groups no longer propagate personal extracted variables to every internal route.** _by Stefano Azzolini_  
`36e0157` **Fix php7 "Notice: Only variables should be passed by reference"** _by Gabriele Diener_  
`ed4221b` **Fixed PDO prepare interface restriction** _by Stefano Azzolini_  
`6e0e2bd` **Reduced memory footprint of SQL methods unbuffering queries.** _by Stefano Azzolini_  
`97b50b6` **Removed fake 5.7 test environment** _by Stefano Azzolini_  
`d253607` **Dropped support for PHP 5.5** _by Stefano Azzolini_  
`e2a3385` **[Chg] Dropped slow func_get_args for new variadic parameter** _by Stefano Azzolini_  
`c4e0cae` **Fix: Route::group now don't init body if request uri contains parts of route pattern** _by Stefano Azzolini_  
`2de4b64` **Cleaned route test dirtying output** _by Stefano Azzolini_  
`de899ec` **More tests on Options::load** _by Stefano Azzolini_  
`c9db42e` **Chg: Now 404 event behave like a Route. You can return Views or objects and don't need direct Response interactions anymore.** _by Stefano Azzolini_  
`114e61f` **Cleaned code on some classes** _by Stefano Azzolini_  
`88d6d55` **Chg: remove some useless Module methods** _by Stefano Azzolini_  
`1f9686e` **[Fix] Silenced "Cannot bind an instance to a static closure" warnings on route middleware** _by Gabriele Diener_  
`33c23b5` **New: Added new Check method : in_array** _by Stefano Azzolini_  
`b79f79f` **New: SQL::column can extract a single column from the returned query results** _by Stefano Azzolini_  
`023ff58` **New: Filter::add now supports multiple filters assigments of the same callback** _by Stefano Azzolini_  
`bb34f0e` **New: Added .env support with Options::loadENV** _by Stefano Azzolini_  
`f98586c` **Fix: remove issue with optionally dynamic route group indexes** _by Stefano Azzolini_  
`42664a2` **Fix: falsy values in Map (and in Options too) doesn't return null anymore.** _by Stefano Azzolini_  
`93324cf` **[New] Filter added to disable the automatic replacement of the empty variable in text render** _by Gabriele Diener_  
`6942990` **Fix: Fixed a loop regression in Text::render with malformed templates** _by Stefano Azzolini_  
`6384169` **Upd: Update Hash::murmur to latest source** _by Stefano Azzolini_  

## 1.7.2-beta

`c4e0cae` **Fix: Route::group now don't init body if request uri contains parts of route pattern** _by Stefano Azzolini_  
`2de4b64` **Cleaned route test dirtying output** _by Stefano Azzolini_  
`de899ec` **More tests on Options::load** _by Stefano Azzolini_  
`c9db42e` **Chg: Now 404 event behave like a Route. You can return Views or objects and don't need direct Response interactions anymore.** _by Stefano Azzolini_  
`114e61f` **Cleaned code on some classes** _by Stefano Azzolini_  
`88d6d55` **Chg: remove some useless Module methods** _by Stefano Azzolini_  
`1f9686e` **[Fix] Silenced "Cannot bind an instance to a static closure" warnings on route middleware** _by Gabriele Diener_  
`33c23b5` **New: Added new Check method : in_array** _by Stefano Azzolini_  
`b79f79f` **New: SQL::column can extract a single column from the returned query results** _by Stefano Azzolini_  
`023ff58` **New: Filter::add now supports multiple filters assigments of the same callback** _by Stefano Azzolini_  
`bb34f0e` **New: Added .env support with Options::loadENV** _by Stefano Azzolini_  
`f98586c` **Fix: remove issue with optionally dynamic route group indexes** _by Stefano Azzolini_  
`42664a2` **Fix: falsy values in Map (and in Options too) doesn't return null anymore.** _by Stefano Azzolini_  
`93324cf` **[New] Filter added to disable the automatic replacement of the empty variable in text render** _by Gabriele Diener_  
`6942990` **Fix: Fixed a loop regression in Text::render with malformed templates** _by Stefano Azzolini_  
`6384169` **Upd: Update Hash::murmur to latest source** _by Stefano Azzolini_  

## 1.7.2

`c4e0cae` **Fix: Route::group now don't init body if request uri contains parts of route pattern** _by Stefano Azzolini_  
`2de4b64` **Cleaned route test dirtying output** _by Stefano Azzolini_  
`de899ec` **More tests on Options::load** _by Stefano Azzolini_  
`c9db42e` **Chg: Now 404 event behave like a Route. You can return Views or objects and don't need direct Response interactions anymore.** _by Stefano Azzolini_  
`114e61f` **Cleaned code on some classes** _by Stefano Azzolini_  
`88d6d55` **Chg: remove some useless Module methods** _by Stefano Azzolini_  
`1f9686e` **[Fix] Silenced "Cannot bind an instance to a static closure" warnings on route middleware** _by Gabriele Diener_  
`33c23b5` **New: Added new Check method : in_array** _by Stefano Azzolini_  
`b79f79f` **New: SQL::column can extract a single column from the returned query results** _by Stefano Azzolini_  
`023ff58` **New: Filter::add now supports multiple filters assigments of the same callback** _by Stefano Azzolini_  
`bb34f0e` **New: Added .env support with Options::loadENV** _by Stefano Azzolini_  
`f98586c` **Fix: remove issue with optionally dynamic route group indexes** _by Stefano Azzolini_  
`42664a2` **Fix: falsy values in Map (and in Options too) doesn't return null anymore.** _by Stefano Azzolini_  
`93324cf` **[New] Filter added to disable the automatic replacement of the empty variable in text render** _by Gabriele Diener_  
`6942990` **Fix: Fixed a loop regression in Text::render with malformed templates** _by Stefano Azzolini_  
`6384169` **Upd: Update Hash::murmur to latest source** _by Stefano Azzolini_  
`99713dd` **Fix: CLI::write newlines beahaviour is now correct.** _by Stefano Azzolini_  
`1011bbd` **[Fix] Fixed driver name on event core.email.send** _by Gabriele Diener_  
`d14d2f3` **Added default core response autosend** _by Gabriele Diener_  
`665d506` **Fixed error name with mail for smtp** _by Gabriele Diener_  
`b8afbbb` **Fix native recipients** _by Gabriele Diener_  
`6532031` **Fix send email and proxy** _by Gabriele Diener_  
`65f1d1d` **Fix Envelope heade cache** _by Gabriele Diener_  
`e787233` **Fix envelope Content-Type** _by Gabriele Diener_  
`af41950` **Moved Event core.email.send in Email class and added map on driver results** _by Gabriele Diener_  

## 1.7.1-beta

`d14d2f3` **Added default core response autosend** _by Gabriele Diener_  
`665d506` **Fixed error name with mail for smtp** _by Gabriele Diener_  
`b8afbbb` **Fix native recipients** _by Gabriele Diener_  
`6532031` **Fix send email and proxy** _by Gabriele Diener_  
`65f1d1d` **Fix Envelope heade cache** _by Gabriele Diener_  
`e787233` **Fix envelope Content-Type** _by Gabriele Diener_  
`af41950` **Moved Event core.email.send in Email class and added map on driver results** _by Gabriele Diener_  

## 1.7.1

`d14d2f3` **Added default core response autosend** _by Gabriele Diener_  
`665d506` **Fixed error name with mail for smtp** _by Gabriele Diener_  
`b8afbbb` **Fix native recipients** _by Gabriele Diener_  
`6532031` **Fix send email and proxy** _by Gabriele Diener_  
`65f1d1d` **Fix Envelope heade cache** _by Gabriele Diener_  
`e787233` **Fix envelope Content-Type** _by Gabriele Diener_  
`af41950` **Moved Event core.email.send in Email class and added map on driver results** _by Gabriele Diener_  
`efe7e09` **Removed sami docs** _by Stefano Azzolini_  
`1e9fe69` **Fix: Added random_bytes polifyll to Hash for PHP < 7** _by Stefano Azzolini_  
`48d72a1` **Fix: EmailTest** _by Stefano Azzolini_  
`9291beb` **Fix Envelope without multipart** _by Gabriele Diener_  
`0c22ef8` **Chg: reverted keys order on Dictionary::get via array map** _by Stefano Azzolini_  
`8e13321` **New: Dictionary (and also Options) can now get options via key=>key map** _by Stefano Azzolini_  
`9aab422` **New: Obfuscate Session IDs** _by Stefano Azzolini_  
`ceaa036` **Fix: silenced WorkTest** _by Stefano Azzolini_  
`1bf0e74` **New: Hash:random, gives you a fast random id** _by Stefano Azzolini_  
`4a7f99b` **Fix** _by Gabriele Diener_  
`ebf47d2` **Added alias of on method and test** _by Gabriele Diener_  
`814dc85` **Fix EXTENSION const** _by Gabriele Diener_  
`59dad73` **Added multi array string test in URL** _by Flavio De Stefano_  
`313c90c` **Added linting option in composer** _by Flavio De Stefano_  
`e0623f9` **Merged Defer module into Work** _by Stefano Azzolini_  
`19e79a4` **Added Text slugify and removeAccents** _by Stefano Azzolini_  
`71ebe65` **Improved Filter::add** _by Gabriele Diener_  
`260d881` **Cleaned Map and Dictionary Class** _by Stefano Azzolini_  
`1ded02d` **Prime/Disarm on Deferred class** _by Stefano Azzolini_  
`3b73d97` **Automatic deferred Response::send() after Route::dispatch** _by Stefano Azzolini_  
`8fed00f` **Unbounce on Response::send() - Still can be forced** _by Stefano Azzolini_  
`85165a7` **Reformatted Test code** _by Stefano Azzolini_  
`d8c731f` **Improved deferred** _by Gabriele Diener_  
`d47be38` **Added sent method** _by Gabriele Diener_  
`3702d4e` **Fix: resolved Text::render whitespace bug** _by Stefano Azzolini_  
`da06575` **Fix: cleaned code and removed some SQL methods madness** _by Stefano Azzolini_  
`1901f0e` **Fix: squashed some bugs** _by Stefano Azzolini_  
`2c16e6c` **Fixed email typo and added some tests for Deferred class** _by Stefano Azzolini_  
`5c9d707` **Fix** _by Gabriele Diener_  
`07962ba` **Add Deferred class** _by Gabriele Diener_  
`5c5d8be` **Fix: added core.email.send on proxy** _by Stefano Azzolini_  
`f4393d8` **Fixed Smtp::onSend** _by Stefano Azzolini_  
`328c86d` **Added some tests for Email and Proxy Email Driver** _by Stefano Azzolini_  

## 1.7.0-beta

`d8c731f` **Improved deferred** _by Gabriele Diener_  
`d47be38` **Added sent method** _by Gabriele Diener_  
`3702d4e` **Fix: resolved Text::render whitespace bug** _by Stefano Azzolini_  
`da06575` **Fix: cleaned code and removed some SQL methods madness** _by Stefano Azzolini_  
`1901f0e` **Fix: squashed some bugs** _by Stefano Azzolini_  
`2c16e6c` **Fixed email typo and added some tests for Deferred class** _by Stefano Azzolini_  
`5c9d707` **Fix** _by Gabriele Diener_  
`07962ba` **Add Deferred class** _by Gabriele Diener_  
`5c5d8be` **Fix: added core.email.send on proxy** _by Stefano Azzolini_  
`f4393d8` **Fixed Smtp::onSend** _by Stefano Azzolini_  
`328c86d` **Added some tests for Email and Proxy Email Driver** _by Stefano Azzolini_  

## 1.7.0

`d8c731f` **Improved deferred** _by Gabriele Diener_  
`d47be38` **Added sent method** _by Gabriele Diener_  
`3702d4e` **Fix: resolved Text::render whitespace bug** _by Stefano Azzolini_  
`da06575` **Fix: cleaned code and removed some SQL methods madness** _by Stefano Azzolini_  
`1901f0e` **Fix: squashed some bugs** _by Stefano Azzolini_  
`2c16e6c` **Fixed email typo and added some tests for Deferred class** _by Stefano Azzolini_  
`5c9d707` **Fix** _by Gabriele Diener_  
`07962ba` **Add Deferred class** _by Gabriele Diener_  
`5c5d8be` **Fix: added core.email.send on proxy** _by Stefano Azzolini_  
`f4393d8` **Fixed Smtp::onSend** _by Stefano Azzolini_  
`328c86d` **Added some tests for Email and Proxy Email Driver** _by Stefano Azzolini_  
`7fe22cb` **New Email subsystem** _by Stefano Azzolini_  
`5eb9ae9` **Merged latest commit from master** _by Stefano Azzolini_  

## 1.6.9

`b6ba98b` **add filter to smtp** _by Gabriele Diener_  
`991789a` **add args to filter** _by Gabriele Diener_  

## 1.6.8

`a6c1704` **Fixed phpunit.xml** _by Stefano Azzolini_  

## 1.6.7

`0670c32` **Fixed Map static problem** _by Stefano Azzolini_  
`37e77d8` **Update README.md** _by Stefano Azzolini_  
`8e04bf5` **Added the Map class. An instanciable Dictionary** _by Stefano Azzolini_  
`22a2642` **Hash::murmurhash3** _by Stefano Azzolini_  

## 1.6.6

`61552c9` **Fixed nasty bug on Object::fetch** _by Stefano Azzolini_  

## 1.6.5

`4608a16` **Update version** _by Gabriele Diener_  
`71dfa19` **Fix** _by Gabriele Diener_  

## 1.6.4

`5ed2767` **Added method clear for Email** _by Gabriele Diener_  
`0c61338` **Saving work done by : stefano.azzolini** _by Stefano Azzolini_  

## 1.6.3

`f183bf8` **SQL::update no longer auto-set primary key** _by Stefano Azzolini_  

## 1.6.2

`d28f2ea` **1.6.2** _by Stefano Azzolini_  
`1307abb` **Update Smtp.php** _by Stefano Azzolini_  

## 1.6.1

`a5af0d4` **Email::SES fixed up** _by Stefano Azzolini_  
`fcc3a04` **Update .gitattributes** _by Stefano Azzolini_  
`a559346` **added docs assets** _by Stefano Azzolini_  

## 1.6.0

`1497e8e` **1.6.0** _by Stefano Azzolini_  
`5395de2` **Added URL class** _by Stefano Azzolini_  
`c5fb5d3` **Saving work done by : stefano.azzolini** _by Stefano Azzolini_  

## 1.5.3

`d01505c` **1.5.3** _by Stefano Azzolini_  
`d086da2` **Fixed Response::send always downloading file.** _by Stefano Azzolini_  
`a370b98` **Edit length and add min_length, max_length in Check::method** _by Gabriele Diener_  

## 1.5.2

`5a1f727` **1.5.2** _by Stefano Azzolini_  
`27e8c7e` **Fixed Route** _by Stefano Azzolini_  
`b985d0a` **Saving work done by : stefano.azzolini** _by Stefano Azzolini_  
`57f4b67` **Honor Response::type** _by Stefano Azzolini_  
`c87aa30` **Optimized Route-** _by Stefano Azzolini_  
`d056e8e` **TEST : Check method required must pass true to int(0)** _by Stefano Azzolini_  
`f140fcc` **[New] Augmented Check Module.** _by Stefano Azzolini_  

## 1.5.0

`f49c091` **[New] Hash::uuid** _by Stefano Azzolini_  

## 1.4.2

`bda1e72` **Fix native email regression** _by Stefano Azzolini_  
`4ab1887` **Added Request::server** _by Stefano Azzolini_  

## 1.4.1

`c9558f6` **Removed a nasty bug** _by Stefano Azzolini_  
`f965e67` **Fixed some small issues** _by Stefano Azzolini_  

## 1.4.0

`5105daf` **1.4.0** _by Stefano Azzolini_  
`8b2545c` **Password::compare for preventing time-based attacks** _by Stefano Azzolini_  

## 1.3.0

`ae56ac0` **Content Negotiation** _by Stefano Azzolini_  
`bd6c181` **Retrieve Request content negotiation** _by Stefano Azzolini_  
`34ccd6c` **Colors in test** _by Stefano Azzolini_  
`ebb03d7` **Request::data load correctly form data now** _by Stefano Azzolini_  

## 1.2.1

`9ccaa1e` **1.2.1** _by Stefano Azzolini_  
`dd2c6c9` **Fixed autoloading in PHPUnit** _by Stefano Azzolini_  
`89e1bdc` **Migrated old tests to PHPUnit** _by Stefano Azzolini_  
`4723032` **Updated CI script** _by Stefano Azzolini_  
`0608f0f` **File Test** _by Stefano Azzolini_  
`7e7a08b` **Delete .styleci.yml** _by Stefano Azzolini_  
`da0fc48` **Update .travis.yml** _by Stefano Azzolini_  
`5dd14b8` **Fix styleci** _by Stefano Azzolini_  
`5f38cb5` **Ervice test** _by Stefano Azzolini_  
`2df3e59` **Object test** _by Stefano Azzolini_  
`6184c5a` **Dictionary test** _by Stefano Azzolini_  
`07408a9` **Dictionary test** _by Stefano Azzolini_  
`ddbc0b3` **View test** _by Stefano Azzolini_  
`8fafdd5` **Added docs directory** _by Stefano Azzolini_  
`2655369` **Deprecated Error class for Errors module** _by Stefano Azzolini_  
`71f49e4` **1.2.0** _by Stefano Azzolini_  
`2310a6d` **Moved REST to API Bundle** _by Stefano Azzolini_  
`977446e` **Merged SQL tests** _by Stefano Azzolini_  

## 1.1.1

`4340cc1` **version bump** _by Stefano Azzolini_  
`5ded0da` **Update Sql Test for multiple delete** _by Gabriele Diener_  
`7f1b49d` **Fix pdostatement for delete method** _by Gabriele Diener_  
`0333c41` **Added tests for PHP7** _by Stefano Azzolini_  
`b12f20f` **[FIX] Now SQL:all also support row looper** _by Stefano Azzolini_  
`c3c7c64` **Fixed mail : additional_headers security change for PHP >= 5.5.25** _by Stefano Azzolini_  
`eeba5b6` **Update .gitattributes** _by Stefano Azzolini_  
`66d9160` **Model->primaryKey()** _by Stefano Azzolini_  
`d27dba0` **Model::all() now recognize properly primary key** _by Stefano Azzolini_  
`bff11eb` **fixed Model::all** _by Stefano Azzolini_  
`85b96aa` **Update Model.php** _by Stefano Azzolini_  
`d75eeee` **Model::all() accepts now a 1-based pagination ($page,$limit)** _by Stefano Azzolini_  
`099eb9b` **Fixed Session get bug** _by Stefano Azzolini_  
`5f46ce1` **Test token** _by Stefano Azzolini_  
`0fa2a6d` **Updated service class** _by Stefano Azzolini_  

## 1.1.0

`8effea4` **1.1.0** _by Stefano Azzolini_  
`1bd3aef` **[NEW] Module Service** _by Stefano Azzolini_  
`5c9aa65` **[Redirect] new back method** _by Stefano Azzolini_  
`13ab07a` **Fixed in_array** _by Gabriele Diener_  
`5d99615` **Fixed Illegal offset type** _by Gabriele Diener_  
`4271ccb` **[NEW] Added Filter::with(["filter1","filter2",...]) FCFS list support** _by Stefano Azzolini_  
`4925be9` **PK removed in updateWhere** _by fedeferio_  
`d27d5f1` **[Response] added MIME charset support** _by Stefano Azzolini_  
`c1e83b4` **[SQL] added `updateWhere`** _by Stefano Azzolini_  
`eecbb60` **Do not create session on `Session::get`, get default instead** _by Stefano Azzolini_  
`52af111` **Fixed redirect via javascript bug** _by Stefano Azzolini_  
`4210d6f` **Added filter to redirect to url** _by Stefano Azzolini_  
`c9280ec` **Added filter to View rendering** _by Stefano Azzolini_  
`813183e` **Update Token.php** _by Stefano Azzolini_  
`94fe7c2` **Update Token.php** _by Stefano Azzolini_  
`d0d8df3` **Update ZIP.php** _by Stefano Azzolini_  
`d393ebc` **[ZIP] added addDirectory** _by Stefano Azzolini_  
`c2d0972` **Added file response option** _by fedeferio_  
`3263cc4` **Resolved route test regression** _by Stefano Azzolini_  
`36dbe45` **Shortcuts for middlewares** _by Stefano Azzolini_  
`0693cbf` **Fixed a regression on route middlewares** _by Stefano Azzolini_  
`0e578a6` **Support for SQL::connect named parameters interface** _by Stefano Azzolini_  
`c95ca6a` **CSV fixed regression e983393ccd2e4ba59ee86d5ebd45e9e6** _by Stefano Azzolini_  
`9433dab` **inizio del branch per il 1.0.0** _by Stefano Azzolini_  

## 0.9.9

`c5a2467` **Update composer.json** _by Stefano Azzolini_  
`fe851a7` **typo** _by Stefano Azzolini_  
`59b2c69` **Update PHP.php** _by Stefano Azzolini_  

## 0.9.8

`7eb822e` **Update composer.json** _by Stefano Azzolini_  
`b9b0c94` **Update View.php** _by Stefano Azzolini_  
`6341c04` **Update View.php** _by Stefano Azzolini_  
`bcc1bed` **Update View.php** _by Stefano Azzolini_  
`f615cc4` **Update PHP.php** _by Stefano Azzolini_  
`cc1fed5` **Update Adapter.php** _by Stefano Azzolini_  
`2625aff` **Update Adapter.php** _by Stefano Azzolini_  
`048c6b0` **Update PHP.php** _by Stefano Azzolini_  
`321a23b` **Update composer.json** _by Filippo Mangione_  
`448dbbc` **Coverage Status** _by Stefano Azzolini_  
`d2e2a65` **view adapter** _by Stefano Azzolini_  
`91f75fd` **cleanings** _by Stefano Azzolini_  
`95bb080` **Cleaned code and raised PHP minimum requirement to 5.5** _by Stefano Azzolini_  
`56edf92` **CLI::edit** _by Stefano Azzolini_  
`5689b2a` **test path dictionary** _by Stefano Azzolini_  
`6bf8fab` **Response new option for passing json_encode flags** _by Stefano Azzolini_  
`5837c16` **removed zip tests** _by Stefano Azzolini_  
`d8a0f4b` **Update phpci.yml** _by Stefano Azzolini_  
`0205e71` **Update phpci.yml** _by Stefano Azzolini_  
`433e9a7` **Update phpci.yml** _by Stefano Azzolini_  
`07eb5a4` **Update phpci.yml** _by Stefano Azzolini_  
`54ca049` **Update phpci.yml** _by Stefano Azzolini_  
`4212237` **Update phpci.yml** _by Stefano Azzolini_  
`3034baf` **Create phpci.yml** _by Stefano Azzolini_  
`bf65d5b` **View tests : typo** _by Stefano Azzolini_  
`ae780c2` **View tests** _by Stefano Azzolini_  
`8a79ac7` **Fix grammar in README.md** _by Garrett Yamada_  
`865f990` **Update HTTP.php** _by Stefano Azzolini_  
`b7f6c62` **Update Route.php** _by Stefano Azzolini_  
`a059d67` **Create .styleci.yml** _by Stefano Azzolini_  
`ed94648` **compilePatternAsRegex speed up** _by Stefano Azzolini_  
`66450f1` **Added `*` => `.+` to routes** _by Stefano Azzolini_  
`932bfb1` **new Email Drivers** _by Stefano Azzolini_  

## 0.9.2

`4271ccb` **[NEW] Added Filter::with(["filter1","filter2",...]) FCFS list support** _by Stefano Azzolini_  
`4925be9` **PK removed in updateWhere** _by fedeferio_  
`d27d5f1` **[Response] added MIME charset support** _by Stefano Azzolini_  
`c1e83b4` **[SQL] added `updateWhere`** _by Stefano Azzolini_  
`eecbb60` **Do not create session on `Session::get`, get default instead** _by Stefano Azzolini_  
`52af111` **Fixed redirect via javascript bug** _by Stefano Azzolini_  
`4210d6f` **Added filter to redirect to url** _by Stefano Azzolini_  
`c9280ec` **Added filter to View rendering** _by Stefano Azzolini_  
`813183e` **Update Token.php** _by Stefano Azzolini_  
`94fe7c2` **Update Token.php** _by Stefano Azzolini_  
`d0d8df3` **Update ZIP.php** _by Stefano Azzolini_  
`d393ebc` **[ZIP] added addDirectory** _by Stefano Azzolini_  
`c2d0972` **Added file response option** _by fedeferio_  
`3263cc4` **Resolved route test regression** _by Stefano Azzolini_  
`36dbe45` **Shortcuts for middlewares** _by Stefano Azzolini_  
`0693cbf` **Fixed a regression on route middlewares** _by Stefano Azzolini_  
`0e578a6` **Support for SQL::connect named parameters interface** _by Stefano Azzolini_  
`c95ca6a` **CSV fixed regression e983393ccd2e4ba59ee86d5ebd45e9e6** _by Stefano Azzolini_  
`c5a2467` **Update composer.json** _by Stefano Azzolini_  
`fe851a7` **typo** _by Stefano Azzolini_  
`59b2c69` **Update PHP.php** _by Stefano Azzolini_  
`9433dab` **inizio del branch per il 1.0.0** _by Stefano Azzolini_  
`7eb822e` **Update composer.json** _by Stefano Azzolini_  
`b9b0c94` **Update View.php** _by Stefano Azzolini_  
`6341c04` **Update View.php** _by Stefano Azzolini_  
`bcc1bed` **Update View.php** _by Stefano Azzolini_  
`f615cc4` **Update PHP.php** _by Stefano Azzolini_  
`cc1fed5` **Update Adapter.php** _by Stefano Azzolini_  
`2625aff` **Update Adapter.php** _by Stefano Azzolini_  
`048c6b0` **Update PHP.php** _by Stefano Azzolini_  
`321a23b` **Update composer.json** _by Filippo Mangione_  
`448dbbc` **Coverage Status** _by Stefano Azzolini_  
`d2e2a65` **view adapter** _by Stefano Azzolini_  
`91f75fd` **cleanings** _by Stefano Azzolini_  
`95bb080` **Cleaned code and raised PHP minimum requirement to 5.5** _by Stefano Azzolini_  
`56edf92` **CLI::edit** _by Stefano Azzolini_  
`5689b2a` **test path dictionary** _by Stefano Azzolini_  
`6bf8fab` **Response new option for passing json_encode flags** _by Stefano Azzolini_  
`5837c16` **removed zip tests** _by Stefano Azzolini_  
`d8a0f4b` **Update phpci.yml** _by Stefano Azzolini_  
`0205e71` **Update phpci.yml** _by Stefano Azzolini_  
`433e9a7` **Update phpci.yml** _by Stefano Azzolini_  
`07eb5a4` **Update phpci.yml** _by Stefano Azzolini_  
`54ca049` **Update phpci.yml** _by Stefano Azzolini_  
`4212237` **Update phpci.yml** _by Stefano Azzolini_  
`3034baf` **Create phpci.yml** _by Stefano Azzolini_  
`bf65d5b` **View tests : typo** _by Stefano Azzolini_  
`ae780c2` **View tests** _by Stefano Azzolini_  
`8a79ac7` **Fix grammar in README.md** _by Garrett Yamada_  
`865f990` **Update HTTP.php** _by Stefano Azzolini_  
`b7f6c62` **Update Route.php** _by Stefano Azzolini_  
`a059d67` **Create .styleci.yml** _by Stefano Azzolini_  
`ed94648` **compilePatternAsRegex speed up** _by Stefano Azzolini_  
`66450f1` **Added `*` => `.+` to routes** _by Stefano Azzolini_  
`932bfb1` **new Email Drivers** _by Stefano Azzolini_  

## 0.9.11

`4271ccb` **[NEW] Added Filter::with(["filter1","filter2",...]) FCFS list support** _by Stefano Azzolini_  
`4925be9` **PK removed in updateWhere** _by fedeferio_  
`d27d5f1` **[Response] added MIME charset support** _by Stefano Azzolini_  
`c1e83b4` **[SQL] added `updateWhere`** _by Stefano Azzolini_  
`eecbb60` **Do not create session on `Session::get`, get default instead** _by Stefano Azzolini_  
`52af111` **Fixed redirect via javascript bug** _by Stefano Azzolini_  

## 0.9.10

`4210d6f` **Added filter to redirect to url** _by Stefano Azzolini_  
`c9280ec` **Added filter to View rendering** _by Stefano Azzolini_  
`813183e` **Update Token.php** _by Stefano Azzolini_  
`94fe7c2` **Update Token.php** _by Stefano Azzolini_  
`d0d8df3` **Update ZIP.php** _by Stefano Azzolini_  
`d393ebc` **[ZIP] added addDirectory** _by Stefano Azzolini_  
`c2d0972` **Added file response option** _by fedeferio_  
`3263cc4` **Resolved route test regression** _by Stefano Azzolini_  
`36dbe45` **Shortcuts for middlewares** _by Stefano Azzolini_  
`0693cbf` **Fixed a regression on route middlewares** _by Stefano Azzolini_  
`0e578a6` **Support for SQL::connect named parameters interface** _by Stefano Azzolini_  
`c95ca6a` **CSV fixed regression e983393ccd2e4ba59ee86d5ebd45e9e6** _by Stefano Azzolini_  
`c5a2467` **Update composer.json** _by Stefano Azzolini_  
`fe851a7` **typo** _by Stefano Azzolini_  
`59b2c69` **Update PHP.php** _by Stefano Azzolini_  
`9433dab` **inizio del branch per il 1.0.0** _by Stefano Azzolini_  
`7eb822e` **Update composer.json** _by Stefano Azzolini_  
`b9b0c94` **Update View.php** _by Stefano Azzolini_  
`6341c04` **Update View.php** _by Stefano Azzolini_  
`bcc1bed` **Update View.php** _by Stefano Azzolini_  
`f615cc4` **Update PHP.php** _by Stefano Azzolini_  
`cc1fed5` **Update Adapter.php** _by Stefano Azzolini_  
`2625aff` **Update Adapter.php** _by Stefano Azzolini_  
`048c6b0` **Update PHP.php** _by Stefano Azzolini_  
`321a23b` **Update composer.json** _by Filippo Mangione_  
`448dbbc` **Coverage Status** _by Stefano Azzolini_  
`d2e2a65` **view adapter** _by Stefano Azzolini_  
`91f75fd` **cleanings** _by Stefano Azzolini_  
`95bb080` **Cleaned code and raised PHP minimum requirement to 5.5** _by Stefano Azzolini_  
`56edf92` **CLI::edit** _by Stefano Azzolini_  
`5689b2a` **test path dictionary** _by Stefano Azzolini_  
`6bf8fab` **Response new option for passing json_encode flags** _by Stefano Azzolini_  
`5837c16` **removed zip tests** _by Stefano Azzolini_  
`d8a0f4b` **Update phpci.yml** _by Stefano Azzolini_  
`0205e71` **Update phpci.yml** _by Stefano Azzolini_  
`433e9a7` **Update phpci.yml** _by Stefano Azzolini_  
`07eb5a4` **Update phpci.yml** _by Stefano Azzolini_  
`54ca049` **Update phpci.yml** _by Stefano Azzolini_  
`4212237` **Update phpci.yml** _by Stefano Azzolini_  
`3034baf` **Create phpci.yml** _by Stefano Azzolini_  
`bf65d5b` **View tests : typo** _by Stefano Azzolini_  
`ae780c2` **View tests** _by Stefano Azzolini_  
`8a79ac7` **Fix grammar in README.md** _by Garrett Yamada_  
`865f990` **Update HTTP.php** _by Stefano Azzolini_  
`b7f6c62` **Update Route.php** _by Stefano Azzolini_  
`a059d67` **Create .styleci.yml** _by Stefano Azzolini_  
`ed94648` **compilePatternAsRegex speed up** _by Stefano Azzolini_  
`66450f1` **Added `*` => `.+` to routes** _by Stefano Azzolini_  
`932bfb1` **new Email Drivers** _by Stefano Azzolini_  
`482b110` **0.9.2 bump** _by Stefano Azzolini_  
`dd64858` **HHVM tests** _by root_  
`b175414` **test: add cache** _by Stefano Azzolini_  
`8f05594` **Email: SMTP Driver** _by Stefano Azzolini_  
`d5948a9` **Fix RouteGroup::add(RouteGroup) issue** _by Stefano Azzolini_  
`f014ef6` **Filesystem cleanup** _by Stefano Azzolini_  
`38d53f7` **File:ZIP adapter** _by Stefano Azzolini_  
`7a2f1f5` **typo** _by Stefano Azzolini_  
`fb817eb` **[FileInterface] delete** _by Stefano Azzolini_  
`bcc7629` **[New] Defer module** _by Stefano Azzolini_  
`d093c18` **Models, Tests and default error mode on SILENT** _by Stefano Azzolini_  

## 0.9.1

`2994f29` **Update composer.json** _by Stefano Azzolini_  
`1655ea8` **Fixed regression on Model+Persistence** _by Stefano Azzolini_  
`beb3969` **CSV fixes on file r/w permissions** _by Stefano Azzolini_  

## 0.9.0

`3842bf1` **Persistence:: autodiscover table + id with const _PRIMARY_KEY_** _by Stefano Azzolini_  
`3d12666` **Model::where method** _by Stefano Azzolini_  
`3e7874d` **[New] Model ORM** _by Stefano Azzolini_  
`8c74550` **SQL::run** _by Stefano Azzolini_  

## 0.8.6

`7086bc1` **0.8.6** _by Stefano Azzolini_  
`b2232b3` **Update SQL.php** _by Stefano Azzolini_  
`bad9ac5` **fix typo** _by Stefano Azzolini_  
`536e88b` **Fix Issue #13** _by Stefano Azzolini_  
`59ec0db` **fix each** _by Stefano Azzolini_  
`aa51c36` **[Request] BaseURI** _by Stefano Azzolini_  

## 0.8.5

`f6604db` **Update composer.json** _by Stefano Azzolini_  
`eb70cf4` **update 0.8.4 version** _by Stefano Azzolini_  

## 0.8.4

`5772ae5` **Fix issue #9** _by Stefano Azzolini_  
`cae8939` **Paths** _by Stefano Azzolini_  
`36f29d1` **Paths** _by Stefano Azzolini_  

## 0.8.3

`c0e2153` **release 0.8.3** _by Stefano Azzolini_  
`f9f433a` **debug code removal** _by Stefano Azzolini_  

## 0.8.2

`6a777c9` **0.8.2 release** _by Stefano Azzolini_  
`0ec8839` **[New] CSV module** _by Stefano Azzolini_  

## 0.8.1

`19c9c8c` **Release 0.8.1** _by Stefano Azzolini_  
`a02449e` **[New] ZIP Archive creator** _by Stefano Azzolini_  
`8c9ac9b` **[New] Write-Only CSV Module** _by Stefano Azzolini_  
`531e0fe` **Update README.md** _by Stefano Azzolini_  
`90b8b85` **[New] HTTP::info()** _by Stefano Azzolini_  
`e349bb6` **fixed peristenceOptions** _by Stefano Azzolini_  
`884dd9a` **Silence "Cannot bind an instance to a static closure" warnings** _by Stefano Azzolini_  
`3c17f89` **Update SQL.php** _by Stefano Azzolini_  
`5300f90` **Update .gitattributes** _by Stefano Azzolini_  
`c22ccba` **Create .scrutinizer.yml** _by Stefano Azzolini_  
`cc12c7b` **whops** _by Stefano Azzolini_  
`30daa4a` **Fixed Persistence bug #8** _by Stefano Azzolini_  
`ba2ea48` **fixed fatal** _by Stefano Azzolini_  
`a78c63a` **SQL : optimizations** _by Stefano Azzolini_  
`41c4cb5` **fixed bug in SQL::insertOrUpdate** _by Stefano Azzolini_  
`4422f06` **SQL : You can now pass objects or arrays as parameters** _by Stefano Azzolini_  
`acdc25b` **[New] Request UA and IP** _by Stefano Azzolini_  
`9b15b74` **Added .gitattributes for dist releases** _by Stefano Azzolini_  
`a344f5e` **[new] Check methods : true,false** _by Stefano Azzolini_  
`96882c5` **fixed a problem of array/object in Check::valid** _by Stefano Azzolini_  
`32adc58` **Update Check.php** _by Stefano Azzolini_  
`653c66f` **Softer json content negotiation** _by Stefano Azzolini_  
`4b08e9f` **Removed debug code** _by Stefano Azzolini_  
`69e1254` **fixed a bug in Persistence::load** _by Stefano Azzolini_  
`277f230` **0.8.0** _by Stefano Azzolini_  

