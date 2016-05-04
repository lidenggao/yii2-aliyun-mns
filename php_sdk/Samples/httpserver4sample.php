<?php
require_once(dirname(__FILE__).'/httpserver/httpserver.php');

class HttpServerForSample extends HttpServer
{
    public function __construct()
    {
        parent::__construct(array(
            'port' => 8002,
        ));
    }

    function getByURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    public function verify($data, $signature, $pubKey)
    {
        $res = openssl_get_publickey($pubKey);
        $result = (bool) openssl_verify($data, base64_decode($signature), $res);
        openssl_free_key($res);

        return $result;
    }

    function getHeader($request, $name)
    {
        return $request->headers[$name][0];
    }

    function route_request($request, $data)
    {
        echo "Received Message: \n";
        $tmpHeaders = array();
        foreach ($request->lc_headers as $key => $value)
        {
            if (0 === strpos($key, 'x-mns-'))
            {
                $tmpHeaders[$key] = $value[0];
            }
        }
        ksort($tmpHeaders);
        $canonicalizedMNSHeaders = implode("\n", array_map(function ($v, $k) { return $k . ":" . $v; }, $tmpHeaders, array_keys($tmpHeaders)));

        $method = $request->method;
        $contentMd5 = $this->getHeader($request, 'Content-md5');
        $contentType = $this->getHeader($request, 'Content-Type');
        $date = $this->getHeader($request, 'Date');
        $canonicalizedResource = $request->request_uri;
        $stringToSign = strtoupper($method) . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedMNSHeaders . "\n" . $canonicalizedResource;

        $publicKeyURL = base64_decode($this->getHeader($request, 'x-mns-signing-cert-url'));
        $publicKey = $this->getByURL($publicKeyURL);
        $signature = $this->getHeader($request, 'Authorization');
        $pass = $this->verify($stringToSign, $signature, $publicKey);
        if ( ! $pass)
        {
            echo "verify signature fail";
            return;
        }
        echo "======================================= \n";
        echo $data;
        echo "======================================= \n";
        return $this->text_response(200, "Success!");
    }

    public function stop()
    {
        $this->server->running = false;
    }
}

$httpserver = new HttpServerForSample();
$httpserver->run_forever();

?>
