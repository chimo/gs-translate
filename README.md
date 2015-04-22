GNU social Translate Notice
===========================

When you are logged in, this plugin adds a 'translate' link to each notice:  
![screenshot: translate button](https://chimo.github.io/gs-translate/screenshot_button.png "translate button")

Clicking on it translates the notice:  
![screenshot: translated notice](https://chimo.github.io/gs-translate/screenshot_translation.png "translated notice")

Configure
---------

1. Subscribe to the Microsoft Translator API: https://datamarket.azure.com/dataset/1899a118-d202-492c-aa16-ba21c33c06cb
2. Register your application on the Azure DataMarket: https://datamarket.azure.com/developer/applications/
3. Add the following to your GNU social config.php (make sure to replace the 'secret' value with yours):

    addPlugin('TranslateNotice', array(
        'id'     => 'gs-translate',
        'secret' => 'CHANGEME'
    ));
