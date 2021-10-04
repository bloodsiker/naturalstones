/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('quiz', function (editor) {
        return {
            title: editor.lang.shortcode.quiz.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.quiz.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'text',
                                    label: editor.lang.shortcode.quiz.id,
                                    validate: CKEDITOR.dialog.validate.regex(/^[0-9]+$/, editor.lang.shortcode.quiz.wrong_format),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                            ]
                        }
                    ]
                }
            ]
        };
    });
}());
