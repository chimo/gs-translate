<?php

// Logic specific to Microsoft's Bing Translator

class BingTranslator {
    private $client_id;
    private $client_secret;
    private $scope;
    private $grant_type;
    private $auth_url;

    // Constructor
    function BingTranslator($client_id, $client_secret) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->scope = 'http://api.microsofttranslator.com/';
        $this->grant_type = 'client_credentials';
        $this->auth_url = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';
    }

    // Get list of supported languages
    function getLanguagesForTranslate() {
        // TODO
    }

    // Identify the language of a selected piece of text
    function detect() {
        // TODO
    }

    // Get an access token
    function getAccessToken() {
        $params = http_build_query(
            array(
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope'         => $this->scope,
                'grant_type'    => $this->grant_type
           )
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->auth_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $errno = curl_errno($ch);

        if ($errno) {
            $err = curl_error($ch);
            throw new Exception($err);
        }

        curl_close($ch);

        $json = json_decode($response);

        if ($json->error) {
            throw new Exception($json->error_description);
        }

        return $json->access_token;
    }
}
