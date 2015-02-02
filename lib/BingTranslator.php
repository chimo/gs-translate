<?php
// Logic specific to Microsoft's Bing Translator

class AccessToken {
    public $token;
    public $expires;

    function AccessToken($token) {
        $this->token = $token;

        if (preg_match('/&ExpiresOn=(\d+)&/', $token, $matches)) {
            $this->expires = $matches[1] . "000"; // Bing returns seconds, we want milliseconds
        }
    }
}

class BingTranslator {
    private $client_id;
    private $client_secret;
    private $scope;
    private $grant_type;
    private $auth_url;
    private $access_token;

    // Constructor
    function BingTranslator($client_id, $client_secret) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        // Populate our access token
        $this->getAccessToken();
    }

    // Checks if access token is expired
    // and requests a new one if so
    function renewAccessTokenIfExpired() {
        $now = new DateTime();

        // Access token expired, get another one
        if ($now->getTimestamp() > $this->access_token->expires) {
            $this->getAccessToken();
        }
    }

    // Get human-readable names from ISO 639-1 language codes
    function getLanguageNames($codes) {
        $this->renewAccessTokenIfExpired();

        // XML representation of the codes we're interested in
        $params = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        if (sizeof($codes) > 0) {
            foreach ($codes as $code) {
                $params .= '<string>' . $code . '</string>';
            }
        } else {
            return array();
        }
        $params .= '</ArrayOfstring>';

        $url = 'http://api.microsofttranslator.com/V2/Http.svc/GetLanguageNames?locale=en'; // TODO: configurable locale
        $header = 'Authorization: Bearer ' . $this->access_token->token;

        $response = $this->http($url, $header, $params);

        $xmlObj = simplexml_load_string($response);
        $languages = array();

        foreach ($xmlObj->string as $language) {
            $languages[] = $language;
        }

        return $languages;
    }

    // Get list of supported languages
    function getLanguagesForTranslate() {
        $this->renewAccessTokenIfExpired();

        $url = 'http://api.microsofttranslator.com/V2/Http.svc/GetLanguagesForTranslate';
        $header = 'Authorization: Bearer ' . $this->access_token->token;

        $response = $this->http($url, $header);

        $xmlObj = simplexml_load_string($response);
        if ($xmlObj === false) {
            // TODO: Exception
            return array();
        }

        $languageCodes = array();
        foreach ($xmlObj->string as $language) {
            $languageCodes[] = $language;
        }

        return $languageCodes;
    }

    // Identify the language of a selected piece of text
    function detect($text) {
        $this->renewAccessTokenIfExpired();

        $url = 'http://api.microsofttranslator.com/V2/Http.svc/Detect?text=';
        $header = 'Authorization: Bearer ' . $this->access_token->token;

        $response = $this->http($url, $header);

        $xmlObj = simplexml_load_string($response);
        foreach((array)$xmlObj[0] as $val){
            $languageCode = $val;
        }

        return $languageCode;
    }

    // Simple curl wrapper
    private function http($url, $headers = null, $postParams = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($postParams !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        }

        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($headers, 'Content-Type: text/xml'));
        }

        $response = curl_exec($ch);

        $errno = curl_errno($ch);

        if ($errno) {
            $err = curl_error($ch);
            throw new Exception($err);
        }

        curl_close($ch);

        return $response;
    }

    // Get an access token
    function getAccessToken() {
        $scope = 'http://api.microsofttranslator.com/';
        $grant_type = 'client_credentials';
        $url = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';

        $params = http_build_query(
            array(
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope'         => $scope,
                'grant_type'    => $grant_type
           )
        );

        $response = $this->http($url, null, $params);

        $json = json_decode($response);

        if (isset($json->error)) {
            throw new Exception($json->error_description);
        }

        return $this->access_token = new AccessToken($json->access_token);
    }
}
