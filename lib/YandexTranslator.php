<?php

class YandexTranslator {
    private $api_key;
    private $api_root;

    // Constructor
    function YandexTranslator($api_key) {
        $this->api_key = $api_key;
        $this->api_root = 'https://translate.yandex.net/api/v1.5/tr.json';
    }

    public function getSupportedLanguages() {
        $endpoint = $this->api_root . '/getLangs';

        $params = array(
            'key' => $this->api_key,
            'ui' => 'en' // TODO: configurable?
        );

        $response = HTTPClient::quickGet($endpoint, null, $params);

        $json = json_decode($response, true);

        asort($json['langs']);

        return $json['langs'];
    }

    public function translate($text, $target_language) {
        $endpoint = $this->api_root . '/translate';
        $lang = isset($target_language) ? $target_language : 'en';

        $params = array(
            'key' => $this->api_key,
            'text' => $text,
            'lang' => $lang
        );

        return HTTPClient::quickGet($endpoint, null, $params);
    }
}

