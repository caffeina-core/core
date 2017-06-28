<?php

namespace Core\HTTP\Transport;

interface Driver {
    public function request(string $method, $data, array $headers, array $options=null) : \Core\HTTP\Response;
}