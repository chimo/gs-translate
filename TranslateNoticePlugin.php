<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class TranslateNoticePlugin extends Plugin
{
    const VERSION = '0.1';

    public $id;
    public $secret;
    public $scope;
    public $grantType;

    function initialize() {
        $this->scope     = 'http://api.microsofttranslator.com/';
        $this->grantType = 'client_credentials';
        $this->authUrl   = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';
    }

    function getAccessToken() {
        $params = http_build_query(
            array(
                'client_id'     => $this->id,
                'client_secret' => $this->secret,
                'scope'         => $this->scope,
                'grant_type'    => $this->grantType
           )
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->authUrl);
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

    function onEndShowNoticeOptionItems($item) {
        if (!common_logged_in()) {
            return;
        }

        // TODO: server-side fallback
        $item->out->element('a', array('href' =>  '#', 'class' => 'gs-translate'), 'Translate this notice');
    }

    function onEndShowScripts($action) {
        // No free-loaders...
        if (!common_logged_in()) {
            return;
        }

        try {
            // TODO: Set this as cookie?
            $accessToken = $this->getAccessToken();

            $action->inlineScript('var gsTranslate = {}; gsTranslate.accessToken = "' . $accessToken . '"');
            $action->script($this->path('js/gs-translate.js'));
        } catch (Exception $e) {
            $action->inlineScript('// ' . $e->getMessage());
        }

        return true;
    }

    function onEndShowStyles($action) {
       $action->cssLink($this->path('css/gs-translate.css'));
    }

    /**
     * Plugin version data
     *
     * @param array &$versions array of version data
     *
     * @return value
     */
    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'TranslateNotice',
                            'version' => self::VERSION,
                            'author' => 'Stephane Berube',
                            'homepage' => 'https://github.com/chimo/gs-translate',
                            'description' =>
                            // TRANS: Plugin description.
                            _m('Translate notices.'));
        return true;
    }
}
