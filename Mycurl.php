<?php

/**
 * Class Mycurl
 */
class Mycurl
{

    public $useragent          = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
    public $_url;
    public $_followlocation;
    public $_timeout;
    public $_maxRedirects;
    public $_cookieFileLocation = './cookie/cookie.txt';
    public $_post;
    public $_postFields;
    public $_referer            = "http://www.google.com";
    public $_session;
    public $_webpage;
    public $_includeHeader;
    public $_noBody;
    public $_status;
    public $_binaryTransfer;
    public $domain;
    public $ch;
    public $authentication = 0;
    public $auth_name = '';
    public $auth_pass = '';

    /**
     * @param $use
     */
    public function useAuth($use)
    {
        $this->authentication = 0;
        if ($use == true) {
            $this->authentication = 1;
        }
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->auth_name = $name;
    }

    /**
     * @param $pass
     */
    public function setPass($pass)
    {
        $this->auth_pass = $pass;
    }

    /**
     * Mycurl constructor.
     *
     * @param null $url
     * @param bool $followlocation
     * @param int  $timeOut
     * @param int  $maxRedirecs
     * @param bool $binaryTransfer
     * @param bool $includeHeader
     * @param bool $noBody
     */
    public function __construct(
        $url = null,
        $followlocation = true,
        $timeOut = 30,
        $maxRedirecs = 4,
        $binaryTransfer = false,
        $includeHeader = false,
        $noBody = false
    ) {
        $this->_url            = $url;
        $this->_followlocation = $followlocation;
        $this->_timeout        = $timeOut;
        $this->_maxRedirects   = $maxRedirecs;
        $this->_noBody         = $noBody;
        $this->_includeHeader  = $includeHeader;
        $this->_binaryTransfer = $binaryTransfer;

        $this->_cookieFileLocation = dirname(__FILE__) . '/cookie/cookie' . session_id() . '.txt';

        $this->domain = 'https://www.lacentrale.fr/';
    }

    /**
     * @param $referer
     */
    public function setReferer($referer)
    {
        $this->_referer = $referer;
    }

    /**
     * @param $path
     */
    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    /**
     * @param $postFields
     */
    public function setPost($postFields)
    {
        $this->_post       = true;
        $this->_postFields = $postFields;
    }

    /**
     * @param $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->useragent = $userAgent;
    }

    /**
     * @param $url
     */
    public function createCurl($url)
    {
        $this->_url = $this->domain . $url;
        if (!isset($this->ch) || get_resource_type($this->ch) !== 'curl') {
            $this->ch = curl_init();

            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->_timeout);
            curl_setopt($this->ch, CURLOPT_MAXREDIRS, $this->_maxRedirects);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, ["Cookie: NAPP=100"]);

            if ($this->authentication == 1) {
                curl_setopt($this->ch, CURLOPT_USERPWD, $this->auth_name . ':' . $this->auth_pass);
            }
            if ($this->_post) {
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->_postFields);
            }

            if ($this->_includeHeader) {
                curl_setopt($this->ch, CURLOPT_HEADER, true);
            }

            if ($this->_noBody) {
                curl_setopt($this->ch, CURLOPT_NOBODY, true);
            }
            /*
              if($this->_binary)
              {
              curl_setopt($this->ch,CURLOPT_BINARYTRANSFER,true);
              }
             */
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->useragent);
            curl_setopt($this->ch, CURLOPT_REFERER, $this->_referer);
        }

        curl_setopt($this->ch, CURLOPT_URL, $this->_url);
        $this->_webpage = curl_exec($this->ch);
        $this->_status  = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    /**
     *
     */
    public function curlClose()
    {
        curl_close($this->ch);
    }

    /**
     * @return mixed
     */
    public function getHttpStatus()
    {
        return $this->_status;
    }

    /**
     * @return mixed
     */
    public function __tostring()
    {
        return $this->_webpage;
    }

    /**
     * @return mixed
     */
    public function getWebPage()
    {
        return $this->_webpage;
    }
}