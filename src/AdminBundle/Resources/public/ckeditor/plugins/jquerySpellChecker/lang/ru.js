/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('jquerySpellChecker', 'ru', {
        label: 'Проверка правописания',
        success: 'Все слова правильные.',
        loading: 'Загрузка...',
        ignoreWord: 'Пропустить слово',
        noSuggestions: '(Нет вариантов)'
    });
}());
