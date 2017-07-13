/*global _peopletagAC: false*/
( function( window, $ ) {
    "use strict";

    var gsTranslate = window.gsTranslate,
        installdir = _peopletagAC.replace( "/main/peopletagautocomplete", "" ),
        $dialog = $( "<div class='gs-translate-dialog'>" ).dialog( { autoOpen: false } );

    $( document ).on( "click", ".gs-translate", function( e ) {
        e.preventDefault();

        var $this = $( this ),
            endpoint = installdir + "/main/translatenotice/translatenotice",
            text = $this.closest( ".notice" ).find( ".e-content" ).first().text(),
            params = {
                text: text
            };

        $.getJSON( endpoint, params, function( data ) {
            $dialog
                .text( data.text[ 0 ] )
                .append( "<p><a href='http://translate.yandex.com/'>Powered by Yandex.Translate</a></p>" )
                .dialog( "open" );
        } );
    } );
}( window, jQuery ) );

