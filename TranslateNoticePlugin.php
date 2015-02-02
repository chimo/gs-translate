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

        return true;
    }

    function onAutoload($cls) {
        $dir = dirname(__FILE__);

        switch ($cls) {
            case 'TranslatenoticeAction':
                include_once $dir . '/actions/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
                return false;

            case 'RenewtokenAction':
                include_once $dir . '/actions/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
                return false;

            case 'Translate_notice':
                include_once $dir . '/classes/' . $cls . '.php';
                return false;

            default:
                return true;
        }
    }

    function onRouterInitialized($m) {
        $m->connect(
            'settings/translatenotice', array(
                'action' => 'translatenotice'
                )
            );

        $m->connect(
            'main/translatenotice/renewtoken', array(
                'action' => 'renewtoken'
                )
            );

        return true;
    }

    function onEndShowNoticeOptionItems($item) {
        if (!common_logged_in()) {
            return;
        }

        // TODO: server-side fallback
        $item->out->element('a', array('href' =>  '#', 'class' => 'gs-translate'), 'Translate this notice');

        return true;
    }

    function onEndAccountSettingsNav($action) {
        $action->elementStart('li');
        $action->element('a', array('href' => common_local_url('translatenotice')), 'Translate Notices'); // TODO: TRANS
        $action->elementEnd('li');

        return true;
    }

    function onEndShowScripts($action) {
        if (!common_logged_in()) {
            return;
        }

        // Tell the JS which language we should translate to
        $targetLanguage = Translate_notice::getKV('user_id', common_current_user()->id);
        if ($targetLanguage === false) {
            $targetLanguage = 'en';
        } else {
            $targetLanguage = $targetLanguage->target_lang;
        }

        try {
            // Pass the access token to the JS
            // TODO: Set this as cookie?
            $accessToken = $this->translator->getAccessToken();

            $action->inlineScript(
                'var gsTranslate = {};' .
                'gsTranslate.accessToken = {' .
                    'token: "' . $accessToken->token . '",' .
                    'expires: "' . $accessToken->expires .
                '"};' .
                'gsTranslate.targetLanguage = "' . $targetLanguage . '";'
            );

            $action->script($this->path('js/gs-translate.js'));
        } catch (Exception $e) {
            $action->inlineScript('// ' . $e->getMessage());
        }

        return true;
    }

    function onEndShowStyles($action) {
       $action->cssLink($this->path('css/gs-translate.css'));

        return true;
    }

    function onCheckSchema() {
        $schema = Schema::get();
        $schema->ensureTable('translate_notice', Translate_notice::schemaDef());

        return true;
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
