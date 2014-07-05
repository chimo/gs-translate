<?php

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/TranslateNotice/lib/BingTranslator.php';

class TranslateNoticePlugin extends Plugin
{
    const VERSION = '0.0.1';

    private $translator;

    function initialize() {
        $this->translator = new BingTranslator($this->client_id, $this->client_secret);
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
            $accessToken = $this->translator->getAccessToken();

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
