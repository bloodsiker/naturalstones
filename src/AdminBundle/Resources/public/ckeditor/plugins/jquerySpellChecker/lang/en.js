/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('jquerySpellChecker', 'en', {
        label: 'Check Spell',
        success: 'There are no incorrectly spelt words.',
        loading: 'Loading...',
        ignoreWord: 'Ignore world',
        noSuggestions: '(No suggestions)'
    });
}());
