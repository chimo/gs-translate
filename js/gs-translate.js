(function () {
    'use strict';

    var gsTranslate = window.gsTranslate,
        token = window.gsTranslate.accessToken,
        encode = encodeURIComponent,
        translate;

    translate = function (text) {
        var appId        = 'Bearer ',    // FIXME legacy: http://msdn.microsoft.com/en-us/library/hh454950.aspx
            to           = gsTranslate.targetLanguage || 'en',
            callbackName = 'gsTranslate.callback',
            script;

        script = document.createElement('script');

        script.src = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate' +
            '?appId=' + encode(appId) + encode(token) +
            '&to=' + encode(to) +
            '&text=' + encode(text) +
            '&oncomplete=' + encode(callbackName);

        document.body.appendChild(script);
    };

    $(document).on('click', '.gs-translate', function (e) {
        e.preventDefault();

        var $this = $(this),
            text = $this.closest('.notice').find('.e-content').first().text();

        translate(text);
    });


    gsTranslate.callback = function (response) {
//        console.log(response);
        alert(response);
    };
}());
