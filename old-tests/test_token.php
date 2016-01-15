<?php

test( Token::encode("TEST","1234") == "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE",'Token','Encode');

try {
	$dec = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE","1234");
} catch(Exception $e) {
	$dec = false;
}
test( $dec == "TEST",'Token','Decode');


try {
	$dec = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi.zPCpn5hHX3CdtmvSDt_apcanyuDjGT9W8KcCgTMyrXE","41231");
} catch(Exception $e) {
	$dec = false;
}
test( $dec === false,'Token','Wrong secret');

try {
	$dec = Token::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IlRFU1Qi","1234");
} catch(Exception $e) {
	$dec = false;
}
test( $dec === false,'Token','Invalid token');