/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.add('quote', {
        requires: 'widget',
        icons: 'quote',
        lang: ['en', 'ru', 'uk'],
        init: function (editor) {
            CKEDITOR.dialog.add('quoteDialog', this.path + 'dialogs/quote.js');

            editor.addContentsCss(this.path + 'styles/style.css');

            editor.widgets.add('Quote', {
                button: editor.lang.quote.button_title,
                dialog: 'quoteDialog',
                template: '<div class="article-detail__quote"></div>',
                allowedContent: 'div(!article-detail__quote)',
                requiredContent: 'div(article-detail__quote)',
                init: function () {
                    this.setData('text', this.element.getText());
                },
                data: function () {
                    if (this.data.text) {
                        this.element.setText(this.data.text);
                    }
                },
                upcast: function (element) {
                    return element.name === 'div' && element.hasClass('article-detail__quote');
                }
            });
        }
    });
}());
