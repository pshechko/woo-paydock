
<?php

/**
 * Twitter OAuth class
 *
 * @author Abraham Williams <abraham@abrah.am>
 */
class TwitterOAuth {

    /** @var string */
    private $apiHost = "https://api.twitter.com";

    /** @var string */
    private $uploadHost = "https://upload.twitter.com";

    /** @var string */
    private $apiVersion = "1.1";

    /** @var int */
    private $timeout = 5;

    /** @var int */
    private $connectionTimeout = 5;

    /**
     * Decode JSON Response as associative Array
     *
     * @see http://php.net/manual/en/function.json-decode.php
     *
     * @var bool
     */
    private $decodeJsonAsArray = false;

    /** @var string */
    private $userAgent = 'TwitterOAuth (+https://twitteroauth.com)';

    /** @var array */
    private $proxy = array();

    /** @var string|null */
    private $lastApiPath;

    /** @var int|null */
    private $lastHttpCode;

    /** @var array */
    private $lastHttpHeaders = array();

    /** @var array */
    private $lastHttpInfo = array();

    /** @var string|null */
    private $lastHttpMethod;

    /** @var array */
    private $lastXHeaders = array();

    /** @var array|object|null */
    private $lastResponse;

    /** @var Consumer */
    private $consumer;

    /** @var Token */
    private $token;

    /** @var HmacSha1 */
    private $signatureMethod;

    /**
     * Constructor
     *
     * @param string      $consumerKey      The Application Consumer Key
     * @param string      $consumerSecret   The Application Consumer Secret
     * @param string|null $oauthToken       The Client Token (optional)
     * @param string|null $oauthTokenSecret The Client Token Secret (optional)
     */
    public function __construct($consumerKey, $consumerSecret, $oauthToken = null, $oauthTokenSecret = null) {
        $this->resetLastResult();
        $this->signatureMethod = new HmacSha1();
        $this->consumer = new Consumer($consumerKey, $consumerSecret);
        if (!empty($oauthToken) && !empty($oauthTokenSecret)) {
            $this->token = new Token($oauthToken, $oauthTokenSecret);
        }
    }

    /**
     * @param string $host
     */
    public function setApiHost($host) {
        $this->apiHost = $host;
    }

    /**
     * @param string $version
     */
    public function setApiVersion($version) {
        $this->apiVersion = $version;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout) {
        $this->timeout = (int) $timeout;
    }

    /**
     * @param int $timeout
     */
    public function setConnectionTimeout($timeout) {
        $this->connectionTimeout = (int) $timeout;
    }

    /**
     * @param bool $value
     */
    public function setDecodeJsonAsArray($value) {
        $this->decodeJsonAsArray = (bool) $value;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = (string) $userAgent;
    }

    /**
     * @param array $proxy
     */
    public function setProxy(array $proxy) {
        $this->proxy = $proxy;
    }

    /**
     * @return null|string
     */
    public function lastApiPath() {
        return $this->lastApiPath;
    }

    /**
     * @return int|null
     */
    public function lastHttpCode() {
        return $this->lastHttpCode;
    }

    /**
     * @return null|string
     */
    public function lastHttpMethod() {
        return $this->lastHttpMethod;
    }

    /**
     * @return array
     */
    public function lastXHeaders() {
        return $this->lastXHeaders;
    }

    /**
     * @return array|null|object
     */
    public function lastResponse() {
        return $this->lastResponse;
    }

    /**
     * Resets the last response information
     */
    public function resetLastResult() {
        $this->lastApiPath = null;
        $this->lastHttpCode = null;
        $this->lastHttpInfo = array();
        $this->lastHttpHeaders = array();
        $this->lastHttpMethod = null;
        $this->lastXHeaders = array();
        $this->lastResponse = array();
    }

    /**
     * Make URLs for user browser navigation.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return string
     */
    public function url($path, array $parameters) {
        $this->resetLastResult();
        $this->lastApiPath = $path;
        $query = http_build_query($parameters);
        $response = "{$this->apiHost}/{$path}?{$query}";
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * Make /oauth/* requests to the API.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return array
     * @throws TwitterOAuthException
     */
    public function oauth($path, array $parameters = array()) {
        $this->resetLastResult();
        $this->lastApiPath = $path;
        $url = "{$this->apiHost}/{$path}";
        $result = $this->oAuthRequest($url, 'POST', $parameters);
        if ($this->lastHttpCode() == 200) {
            $response = Util::parseParameters($result);
            $this->lastResponse = $response;

            return $response;
        } else {
            throw new TwitterOAuthException($result);
        }
    }

    /**
     * Make GET requests to the API.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return array|object
     */
    public function get($path, array $parameters = array()) {
        //echo"<p>" . $this->apiHost . "||" . $path . "||";
        //echo"<p>Rarameters</p>";
        //foreach ($parameters as $value) {
       //     echo "<p>" . $key . "->" . $value."</p>";
        //}
        //echo"</p>";
        return $this->http('GET', $this->apiHost, $path, $parameters);
    }

    /**
     * Make POST requests to the API.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return array|object
     */
    public function post($path, array $parameters = array()) {
        return $this->http('POST', $this->apiHost, $path, $parameters);
    }

    /**
     * Upload media to upload.twitter.com.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return array|object
     */
    public function upload($path, array $parameters = array()) {
        $file = file_get_contents($parameters['media']);
        $base = base64_encode($file);
        $parameters['media'] = $base;
        return $this->http('POST', $this->uploadHost, $path, $parameters);
    }

    /**
     * @param string $method
     * @param string $host
     * @param string $path
     * @param array  $parameters
     *
     * @return array|object
     */
    public function http($method, $host, $path, array $parameters) {
        $this->resetLastResult();
        $url = "{$host}/{$this->apiVersion}/{$path}.json";
        //echo "<p>".$url."</p>";
        $this->lastApiPath = $path;
        $result = $this->oAuthRequest($url, $method, $parameters);
        $response = $this->jsonDecode($result);
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * Format and sign an OAuth / API request
     *
     * @param string $url
     * @param string $method
     * @param array $parameters
     *
     * @return string
     * @throws TwitterOAuthException
     */
    private function oAuthRequest($url, $method, array $parameters) {
        $this->lastHttpMethod = $method;
        $request = Request::fromConsumerAndToken($this->consumer, $this->token, $method, $url, $parameters);
        if (array_key_exists('oauth_callback', $parameters)) {
            // Twitter doesn't like oauth_callback as a parameter.
            unset($parameters['oauth_callback']);
        }
        $request->signRequest($this->signatureMethod, $this->consumer, $this->token);
       // echo "<p>".$request->toHeader()."</p>";
        return $this->request($request->getNormalizedHttpUrl(), $method, $request->toHeader(), $parameters);
    }

    /**
     * Make an HTTP request
     *
     * @param $url
     * @param $method
     * @param $headers
     * @param $postfields
     *
     * @return string
     * @throws TwitterOAuthException
     */
    private function request($url, $method, $headers, $postfields) {
        /* Curl settings */
        $options = array(
            // CURLOPT_VERBOSE => true,
            CURLOPT_CAINFO => __DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem',
            CURLOPT_CAPATH => __DIR__,
            CURLOPT_CONNECTTIMEOUT => $this->connectionTimeout,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array($headers, 'Expect:'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $this->userAgent,
        );
        
        //echo "<p>".$options[CURLOPT_URL]."</p>";

        if (!empty($this->proxy)) {
            $options[CURLOPT_PROXY] = $this->proxy['CURLOPT_PROXY'];
            $options[CURLOPT_PROXYUSERPWD] = $this->proxy['CURLOPT_PROXYUSERPWD'];
            $options[CURLOPT_PROXYPORT] = $this->proxy['CURLOPT_PROXYPORT'];
            $options[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
            $options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
        }
        
        //$postfields
               // foreach ($postfields as $option=>$value){
       //echo "<p>".$option."-|-".$value."</p>";
    //}

        switch ($method) {
            case 'GET':
                if (!empty($postfields)) {
                    $options[CURLOPT_URL] .= '?' . Util::buildHttpQuery($postfields);
                }
                break;
            case 'POST':
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = Util::buildHttpQuery($postfields);
                break;
        }
        //$options[CURLOPT_URL]=$options[CURLOPT_URL]."&result_type=mixed";

        $curlHandle = curl_init();
    //foreach ($options as $option=>$value){
       //echo $options[CURLOPT_URL];
    //}
        curl_setopt_array($curlHandle, $options);
        $response = curl_exec($curlHandle);

        $curlErrno = curl_errno($curlHandle);
        switch ($curlErrno) {
            case 28:
                throw new TwitterOAuthException('Request timed out.');
            case 51:
                throw new TwitterOAuthException('The remote servers SSL certificate or SSH md5 fingerprint failed validation.');
            case 56:
                throw new TwitterOAuthException('Response from server failed or was interrupted.');
        }

        $this->lastHttpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        if (empty($this->proxy)) {
            list($header, $body) = explode("\r\n\r\n", $response, 2);
        } else {
            list($connect, $header, $body) = explode("\r\n\r\n", $response, 3);
        }
        list($this->lastHttpHeaders, $this->lastXHeaders) = $this->parseHeaders($header);
        $this->lastHttpInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        return $body;
    }

    /**
     * @param string $string
     *
     * @return array|object
     */
    private function jsonDecode($string) {
        // BUG: https://bugs.php.net/bug.php?id=63520
        if (defined('JSON_BIGINT_AS_STRING')) {
            return json_decode($string, $this->decodeJsonAsArray, 512, JSON_BIGINT_AS_STRING);
        } else {
            return json_decode($string, $this->decodeJsonAsArray);
        }
    }

    /**
     * Get the header info to store.
     *
     * @param string $header
     *
     * @return array
     */
    private function parseHeaders($header) {
        $headers = array();
        $xHeaders = array();
        foreach (explode("\r\n", $header) as $i => $line) {
            $i = strpos($line, ':');
            if (!empty($i)) {
                list ($key, $value) = explode(': ', $line);
                $key = str_replace('-', '_', strtolower($key));
                $headers[$key] = trim($value);
                if (substr($key, 0, 1) == 'x') {
                    $xHeaders[$key] = trim($value);
                }
            }
        }
        return array($headers, $xHeaders);
    }

}

class HmacSha1 extends SignatureMethod {

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return "HMAC-SHA1";
    }

    /**
     * {@inheritDoc}
     */
    public function buildSignature(Request $request, Consumer $consumer, Token $token = null) {
        $signatureBase = $request->getSignatureBaseString();
        $request->baseString = $signatureBase;

        $parts = array($consumer->secret, null !== $token ? $token->secret : "");

        $parts = Util::urlencodeRfc3986($parts);
        $key = implode('&', $parts);

        return base64_encode(hash_hmac('sha1', $signatureBase, $key, true));
    }

}

abstract class SignatureMethod {

    /**
     * Needs to return the name of the Signature Method (ie HMAC-SHA1)
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Build up the signature
     * NOTE: The output of this function MUST NOT be urlencoded.
     * the encoding is handled in OAuthRequest when the final
     * request is serialized
     *
     * @param Request $request
     * @param Consumer $consumer
     * @param Token $token
     *
     * @return string
     */
    abstract public function buildSignature(Request $request, Consumer $consumer, Token $token = null);

    /**
     * Verifies that a given signature is correct
     *
     * @param Request $request
     * @param Consumer $consumer
     * @param Token $token
     * @param string $signature
     *
     * @return bool
     */
    public function checkSignature(Request $request, Consumer $consumer, Token $token, $signature) {
        $built = $this->buildSignature($request, $consumer, $token);

        // Check for zero length, although unlikely here
        if (strlen($built) == 0 || strlen($signature) == 0) {
            return false;
        }

        if (strlen($built) != strlen($signature)) {
            return false;
        }

        // Avoid a timing leak with a (hopefully) time insensitive compare
        $result = 0;
        for ($i = 0; $i < strlen($signature); $i++) {
            $result |= ord($built{$i}) ^ ord($signature{$i});
        }

        return $result == 0;
    }

}

class Consumer {

    /** @var string  */
    public $key;

    /** @var string  */
    public $secret;

    /** @var string|null  */
    public $callbackUrl;

    /**
     * @param string $key
     * @param string $secret
     * @param null $callbackUrl
     */
    public function __construct($key, $secret, $callbackUrl = null) {
        $this->key = $key;
        $this->secret = $secret;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string
     */
    public function __toString() {
        return "Consumer[key=$this->key,secret=$this->secret]";
    }

}

class Token {

    /** @var string */
    public $key;

    /** @var string */
    public $secret;

    /**
     * @param string $key    The OAuth Token
     * @param string $secret The OAuth Token Scret
     */
    public function __construct($key, $secret) {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Generates the basic string serialization of a token that a server
     * would respond to request_token and access_token calls with
     *
     * @return string
     */
    public function __toString() {
        return sprintf("oauth_token=%s&oauth_token_secret=%s", Util::urlencodeRfc3986($this->key), Util::urlencodeRfc3986($this->secret)
        );
    }

}

class Request {

    protected $parameters;
    protected $httpMethod;
    protected $httpUrl;
    // for debug purposes
    public $baseString;
    public static $version = '1.0';
    public static $POST_INPUT = 'php://input';

    /**
     * Constructor
     *
     * @param string     $httpMethod
     * @param string     $httpUrl
     * @param array|null $parameters
     */
    public function __construct($httpMethod, $httpUrl, array $parameters = array()) {
        $parameters = array_merge(Util::parseParameters(parse_url($httpUrl, PHP_URL_QUERY)), $parameters);
        $this->parameters = $parameters;
        $this->httpMethod = $httpMethod;
        $this->httpUrl = $httpUrl;
    }

    /**
     * attempt to build up a request from what was passed to the server
     *
     * @param string|null $httpMethod
     * @param string|null $httpUrl
     * @param array|null  $parameters
     *
     * @return Request
     */
    public static function fromRequest($httpMethod = null, $httpUrl = null, array $parameters = null) {
        $scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
        $httpUrl = ($httpUrl) ? $httpUrl : $scheme .
                '://' . $_SERVER['SERVER_NAME'] .
                ':' .
                $_SERVER['SERVER_PORT'] .
                $_SERVER['REQUEST_URI'];
        $httpMethod = ($httpMethod) ? $httpMethod : $_SERVER['REQUEST_METHOD'];

        // We weren't handed any parameters, so let's find the ones relevant to
        // this request.
        // If you run XML-RPC or similar you should use this to provide your own
        // parsed parameter-list
        if (null !== $parameters) {
            // Find request headers
            $headers = Util::getHeaders();

            // Parse the query-string to find GET parameters
            $parameters = Util::parseParameters($_SERVER['QUERY_STRING']);

            // It's a POST request of the proper content-type, so parse POST
            // parameters and add those overriding any duplicates from GET
            if ($httpMethod == "POST" && isset($headers['Content-Type']) && strstr($headers['Content-Type'], 'application/x-www-form-urlencoded')
            ) {
                $post_data = Util::parseParameters(file_get_contents(self::$POST_INPUT));
                $parameters = array_merge($parameters, $post_data);
            }

            // We have a Authorization-header with OAuth data. Parse the header
            // and add those overriding any duplicates from GET or POST
            if (isset($headers['Authorization']) && substr($headers['Authorization'], 0, 6) == 'OAuth '
            ) {
                $headerParameters = Util::splitHeader($headers['Authorization']);
                $parameters = array_merge($parameters, $headerParameters);
            }
        }

        return new Request($httpMethod, $httpUrl, $parameters);
    }

    /**
     * pretty much a helper function to set up the request
     *
     * @param Consumer $consumer
     * @param Token    $token
     * @param string   $httpMethod
     * @param string   $httpUrl
     * @param array    $parameters
     *
     * @return Request
     */
    public static function fromConsumerAndToken(
    Consumer $consumer, Token $token = null, $httpMethod, $httpUrl, array $parameters = array()
    ) {
        $defaults = array(
            "oauth_version" => Request::$version,
            "oauth_nonce" => Request::generateNonce(),
            "oauth_timestamp" => time(),
            "oauth_consumer_key" => $consumer->key
        );
        if (null !== $token) {
            $defaults['oauth_token'] = $token->key;
        }

        $parameters = array_merge($defaults, $parameters);

        return new Request($httpMethod, $httpUrl, $parameters);
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool   $allowDuplicates
     */
    public function setParameter($name, $value, $allowDuplicates = true) {
        if ($allowDuplicates && isset($this->parameters[$name])) {
            // We have already added parameter(s) with this name, so add to the list
            if (is_scalar($this->parameters[$name])) {
                // This is the first duplicate, so transform scalar (string)
                // into an array so we can add the duplicates
                $this->parameters[$name] = array($this->parameters[$name]);
            }

            $this->parameters[$name][] = $value;
        } else {
            $this->parameters[$name] = $value;
        }
    }

    /**
     * @param $name
     *
     * @return string|null
     */
    public function getParameter($name) {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param $name
     */
    public function removeParameter($name) {
        unset($this->parameters[$name]);
    }

    /**
     * The request parameters, sorted and concatenated into a normalized string.
     *
     * @return string
     */
    public function getSignableParameters() {
        // Grab all parameters
        $params = $this->parameters;

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }

        return Util::buildHttpQuery($params);
    }

    /**
     * Returns the base string of this request
     *
     * The base string defined as the method, the url
     * and the parameters (normalized), each urlencoded
     * and the concated with &.
     *
     * @return string
     */
    public function getSignatureBaseString() {
        $parts = array(
            $this->getNormalizedHttpMethod(),
            $this->getNormalizedHttpUrl(),
            $this->getSignableParameters()
        );

        $parts = Util::urlencodeRfc3986($parts);

        return implode('&', $parts);
    }

    /**
     * Returns the HTTP Method in uppercase
     *
     * @return string
     */
    public function getNormalizedHttpMethod() {
        return strtoupper($this->httpMethod);
    }

    /**
     * parses the url and rebuilds it to be
     * scheme://host/path
     *
     * @return string
     */
    public function getNormalizedHttpUrl() {
        $parts = parse_url($this->httpUrl);

        $scheme = (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
        $port = (isset($parts['port'])) ? $parts['port'] : (($scheme == 'https') ? '443' : '80');
        $host = (isset($parts['host'])) ? strtolower($parts['host']) : '';
        $path = (isset($parts['path'])) ? $parts['path'] : '';

        if (($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')
        ) {
            $host = "$host:$port";
        }
        return "$scheme://$host$path";
    }

    /**
     * Builds a url usable for a GET request
     *
     * @return string
     */
    public function toUrl() {
        $postData = $this->toPostdata();
        $out = $this->getNormalizedHttpUrl();
        if ($postData) {
            $out .= '?' . $postData;
        }
        return $out;
    }

    /**
     * Builds the data one would send in a POST request
     *
     * @return string
     */
    public function toPostdata() {
        return Util::buildHttpQuery($this->parameters);
    }

    /**
     * Builds the Authorization: header
     *
     * @param string|null $realm
     *
     * @return string
     * @throws TwitterOAuthException
     */
    public function toHeader($realm = null) {
        $first = true;
        if ($realm) {
            $out = 'Authorization: OAuth realm="' . Util::urlencodeRfc3986($realm) . '"';
            $first = false;
        } else {
            $out = 'Authorization: OAuth';
        }

        foreach ($this->parameters as $k => $v) {
            if (substr($k, 0, 5) != "oauth") {
                continue;
            }
            if (is_array($v)) {
                throw new TwitterOAuthException('Arrays not supported in headers');
            }
            $out .= ($first) ? ' ' : ',';
            $out .= Util::urlencodeRfc3986($k) . '="' . Util::urlencodeRfc3986($v) . '"';
            $first = false;
        }
        return $out;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toUrl();
    }

    /**
     * @param SignatureMethod $signatureMethod
     * @param Consumer        $consumer
     * @param Token           $token
     */
    public function signRequest(SignatureMethod $signatureMethod, Consumer $consumer, Token $token = null) {
        $this->setParameter("oauth_signature_method", $signatureMethod->getName(), false);
        $signature = $this->buildSignature($signatureMethod, $consumer, $token);
        $this->setParameter("oauth_signature", $signature, false);
    }

    /**
     * @param SignatureMethod $signatureMethod
     * @param Consumer        $consumer
     * @param Token           $token
     *
     * @return string
     */
    public function buildSignature(SignatureMethod $signatureMethod, Consumer $consumer, Token $token = null) {
        return $signatureMethod->buildSignature($this, $consumer, $token);
    }

    /**
     * @return string
     */
    public static function generateNonce() {
        return md5(microtime() . mt_rand());
    }

}

class Util {

    /**
     * @param $input
     *
     * @return array|mixed|string
     */
    public static function urlencodeRfc3986($input) {
        if (is_array($input)) {
            return array_map(array(__NAMESPACE__ . '\Util', 'urlencodeRfc3986'), $input);
        } elseif (is_scalar($input)) {
            return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
        } else {
            return '';
        }
    }

    /**
     * This decode function isn't taking into consideration the above
     * modifications to the encoding process. However, this method doesn't
     * seem to be used anywhere so leaving it as is.
     *
     * @param string $string
     *
     * @return string
     */
    public static function urldecodeRfc3986($string) {
        return urldecode($string);
    }

    /**
     * Utility function for turning the Authorization: header into
     * parameters, has to do some unescaping
     * Can filter out any non-oauth parameters if needed (default behaviour)
     * May 28th, 2010 - method updated to tjerk.meesters for a speed improvement.
     *
     * @see http://code.google.com/p/oauth/issues/detail?id=163
     *
     * @param string $header
     * @param bool   $onlyAllowOauthParams
     *
     * @return array
     */
    public static function splitHeader($header, $onlyAllowOauthParams = true) {
        $params = array();
        $pattern = '/(' . ($onlyAllowOauthParams ? 'oauth_' : '') . '[a-z_-]*)=(:?"([^"]*)"|([^,]*))/';
        if (preg_match_all($pattern, $header, $matches)) {
            foreach ($matches[1] as $i => $h) {
                $params[$h] = Util::urldecodeRfc3986(empty($matches[3][$i]) ? $matches[4][$i] : $matches[3][$i]);
            }
            if (isset($params['realm'])) {
                unset($params['realm']);
            }
        }
        return $params;
    }

    /**
     * Helper to try to sort out headers for people who aren't running apache
     *
     * @return array
     */
    public static function getHeaders() {
        if (function_exists('apache_request_headers')) {
            // we need this to get the actual Authorization: header
            // because apache tends to tell us it doesn't exist
            $apacheHeaders = apache_request_headers();

            // sanitize the output of apache_request_headers because
            // we always want the keys to be Cased-Like-This and arh()
            // returns the headers in the same case as they are in the
            // request
            $headers = array();
            foreach ($apacheHeaders as $key => $value) {
                $key = str_replace(" ", "-", ucwords(strtolower(str_replace("-", " ", $key))));
                $headers[$key] = $value;
            }
        } else {
            // otherwise we don't have apache and are just going to have to hope
            // that $_SERVER actually contains what we need
            $headers = array();
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
            }
            if (isset($_ENV['CONTENT_TYPE'])) {
                $headers['Content-Type'] = $_ENV['CONTENT_TYPE'];
            }

            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == "HTTP_") {
                    // this is chaos, basically it is just there to capitalize the first
                    // letter of every word that is not an initial HTTP and strip HTTP
                    // code from przemek
                    $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }

    /**
     * This function takes a input like a=b&a=c&d=e and returns the parsed
     * parameters like this
     * array('a' => array('b','c'), 'd' => 'e')
     *
     * @param mixed $input
     *
     * @return array
     */
    public static function parseParameters($input) {
        if (!isset($input) || !$input) {
            return array();
        }

        $pairs = explode('&', $input);

        $parameters = array();
        foreach ($pairs as $pair) {
            $split = explode('=', $pair, 2);
            $parameter = Util::urldecodeRfc3986($split[0]);
            $value = isset($split[1]) ? Util::urldecodeRfc3986($split[1]) : '';

            if (isset($parameters[$parameter])) {
                // We have already recieved parameter(s) with this name, so add to the list
                // of parameters with this name

                if (is_scalar($parameters[$parameter])) {
                    // This is the first duplicate, so transform scalar (string) into an array
                    // so we can add the duplicates
                    $parameters[$parameter] = array($parameters[$parameter]);
                }

                $parameters[$parameter][] = $value;
            } else {
                $parameters[$parameter] = $value;
            }
        }
        return $parameters;
    }

    /**
     * @param $params
     *
     * @return string
     */
    public static function buildHttpQuery($params) {
        if (!$params) {
            return '';
        }

        // Urlencode both keys and values
        $keys = Util::urlencodeRfc3986(array_keys($params));
        $values = Util::urlencodeRfc3986(array_values($params));
        $params = array_combine($keys, $values);

        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($params, 'strcmp');

        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                // If two or more parameters share the same name, they are sorted by their value
                // Ref: Spec: 9.1.1 (1)
                // June 12th, 2010 - changed to sort because of issue 164 by hidetaka
                sort($value, SORT_STRING);
                foreach ($value as $duplicateValue) {
                    $pairs[] = $parameter . '=' . $duplicateValue;
                }
            } else {
                $pairs[] = $parameter . '=' . $value;
            }
        }
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        return implode('&', $pairs);
    }

}
