<?php

class URLTest extends PHPUnit_Framework_TestCase {

    public function testBuildFromParse(){
      $original_url = "https://user:pass@www.alpha.beta.com:9080/path/to/resource.html";
      $url = new URL($original_url);
      $this->assertEquals($original_url, "$url");
    }

    public function testBuildFromScratch(){
      $url = new URL();
      $url->scheme = 'ftps';
      $url->host   = 'test.com';
      $url->port   = 9000;
      $url->path   = 'index.php';
      $this->assertEquals("ftps://test.com:9000/index.php", "$url");
    }

    public function testModify(){
      $url = new URL('https://user:pass@www.alpha.beta.com:9080/path/to/resource.html');
      $url->host   = 'www.gamma.theta.com';
      $this->assertEquals('https://user:pass@www.gamma.theta.com:9080/path/to/resource.html', "$url");
    }

    public function testParseQuery(){
      $url = new URL('https://user:pass@www.alpha.beta.com:9080/path/to/resource.html?query=string&another[]=2&another[]=3#fragment');
      $this->assertEquals('string', $url->query['query']);
    }

    public function testBuildQuery(){
      $url = new URL();
      $url->query['alpha']=123;
      $url->query['beta']=['a'=>1,'b'=>2];
      $this->assertEquals('?alpha=123&beta[a]=1&beta[b]=2', urldecode($url));
    }


}

