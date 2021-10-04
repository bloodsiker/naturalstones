/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('jquerySpellChecker', 'uk', {
        label: 'Перевірка правопису',
        success: 'Усі слова правильні.',
        loading: 'Загрузка...',
        ignoreWord: 'Пропустити слово',
        noSuggestions: '(Немає варіантів)'
    });
}());
