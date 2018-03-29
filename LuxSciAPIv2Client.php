<?php

namespace LuxSciApiClient;


use LuxSciApiClient_Model_V2\SendEmailOrTextRequest;

class LuxSciAPIv2Client
{
    private $_params = [
        'host' => 'rest.luxsci.com'
    ];

    private $_timeout = 30;

    /**
     * LuxSciAPIv2Client constructor.
     * @param $token string API Token
     * @param $secret string API Secret
     * @param $user string Username
     * @param $pass string Password
     */
    public function __construct($token, $secret, $user, $pass)
    {
        $this->_params = array_merge($this->_params, [
            'secret' => $secret,
            'user' => $user,
            'pass' => $pass,
            'token' => $token,
        ]);
    }


    /**
     * Function definition to get auth token
     * @return array
     */
    public function auth()
    {
        $params = $this->_params;
        $path = "/perl/api/v2/auth";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $params['host'] . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        $date = time();

        if ($params['user']) {
            $json = array(
                'token' => $params['token'],
                'date' => $date,
                'user' => $params['user'],
                'pass' => $params['pass'],
                'signature' => hash_hmac('sha256', $params['token'] . "\n" . $date . "\n" . $params['user'] . "\n" . $params['pass'] . "\n", $params['secret']),
            );
        } else {
            $json = array(
                'token' => $params['token'],
                'date' => $date,
                'signature' => hash_hmac('sha256', $params['token'] . "\n" . $date . "\n", $params['secret']),
            );
        }

        try {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        } catch (\Exception $e) {
            return array('code' => '400', 'info' => array('success' => 0, 'error' => "Failure encoding JSON request: " . $e->getMessage()));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json", 'Content-Length: ' . strlen(json_encode($json))));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $this->callAPI($ch, false);
    }

    /**
     * Function definition to create api request
     * @param $method string
     * @param $auth string API Authentication Code
     * @param $path string API path
     * @param $data array API request data
     * @param bool $debug
     * @return array
     */
    public function request($method, $auth, $path, $data, $debug = false)
    {
        $method = strtoupper((isset($method) && $method !== '') ? $method : "GET");
        $path = (isset($path) && $path !== '') ? $path : "/perl/api/v2";
        $qs = "";

        if (preg_match("/PUT|POST/", $method)) {
            $json = json_encode($data);
            if (json_last_error()) {
                return array('code' => 400, 'info' => array('success' => 0, 'error' => "Failure encoding JSON request: " . json_last_error()));
            }

            // Must be sure to be trimmed before applying hmac.
            $json = preg_replace("/^\s+|\s+$/", "", $json);
            $hmac = hash('sha256', $json);
        } else {
            $list = array();
            foreach ($data as $key => $value) {
                array_push($list, Util::url_encode($key) . "=" . Util::url_encode($value));
            }
            $qs = join("&", $list);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $this->_params['host'] . $path . ($qs ? "?$qs" : ""));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Send the POST/PUT body
        if (isset($json)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        // Request signature
        $to_sign = $auth . "\n" . strtoupper($method) . "\n" . $path . "\n" . $qs . "\n";
        if (isset($hmac)) {
            $to_sign .= $hmac . "\n";
        } else {
            $to_sign .= NULL . "\n";
        }
        $sig = hash_hmac('sha256', $to_sign, $this->_params['secret']);
        curl_setopt($ch, CURLOPT_COOKIE, "signature=" . $auth . ":" . $sig);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return $this->callAPI($ch, $debug);
    }

    /**
     * @param SendEmailOrTextRequest $request
     * @return array|bool
     */
    public function sendEmailOrText(SendEmailOrTextRequest $request)
    {
        $authData = $this->auth();
        $authToken = $authData['data']['auth'];
        if (!$authToken) return false;
        $response = $this->request('POST', $authToken, '/perl/api/v2/user/' . $this->_params['user'] . '/email/compose/secureline/send', $request->toArray());
        return $response;
    }

    /**
     * Function definition to make api request
     * @param $ch
     * @param boolean $debug
     * @return array
     */
    private function callAPI($ch, $debug)
    {
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "LuxSci APIv2 PHP Client");

        $result = [];
        if ($debug) {
            try {
                print "<br>=================================================================";
                print "<br>REQUEST:----<br>";
                $result = Util::curl_exec($ch);
            } catch (\Exception $e) {
                print "ERROR IN CURL REQUEST<br>";
            }
        } else {
            $result = Util::curl_exec($ch);
        }

        try {
            $data = (array)json_decode($result['response']);
        } catch (\Exception $e) {
            if ($debug) {
                print "JSON Parse Exception: " . $e->getMessage() . "<br>";
            }
            $data = array('success' => 0, 'error' => "Non-JSON or unparsable data returned.");
        }

        return array('code' => $result['code'], 'data' => $data);
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }

    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
    }
}