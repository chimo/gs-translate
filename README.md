GNU social Translate Notice
===========================

When you are logged in, this plugin adds a 'translate' link to each notice.  
Screenshot: http://code.chromic.org/file/data/avhyek77d2ouyi7c3pkx/PHID-FILE-2zpo3puv6kxzfjgpichg/screenshot_button.png

Clicking on it translates the notice.  
Screenshot: http://code.chromic.org/file/data/vzljybhegi4tqao4rpus/PHID-FILE-wq3xtynuibbdyl5sglgb/screenshot_translation.png 

Configure
---------

1. Subscribe to the Microsoft Translator API: https://datamarket.azure.com/dataset/1899a118-d202-492c-aa16-ba21c33c06cb
2. Register your application on the Azure DataMarket: https://datamarket.azure.com/developer/applications/
3. Add the following to your GNU social config.php (make sure to replace the 'secret' value with yours):

    addPlugin('TranslateNotice', array(
        'id'     => 'gs-translate',
        'secret' => 'CHANGEME'
    ));
