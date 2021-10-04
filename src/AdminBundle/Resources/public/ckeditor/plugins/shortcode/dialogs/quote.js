/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('quote', function (editor) {
        return {
            title: editor.lang.shortcode.quote.dialog_title,
            minWidth: 400,
            minHeight: 200,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.quote.tab_basic_label,
                    elements: [
                        {
                            id: 'text',
                            type: 'textarea',
                            label: editor.lang.shortcode.quote.text,
                            rows: 5,
                            inputStyle: 'height: 100%; white-space: pre-wrap',
                            validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.shortcode.quote.err_text_empty),
                            setup: function (widget) {
                                this.setValue(widget.data.text);
                            },
                            commit: function (widget) {
                                widget.setData('text', this.getValue());
                            }
                        },
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'image',
                                    type: 'text',
                                    label: editor.lang.shortcode.quote.image,
                                    setup: function (widget) {
                                        this.setValue(widget.data.image);
                                    },
                                    commit: function (widget) {
                                        widget.setData('image', this.getValue());
                                    }
                                },
                                {
                                    id: 'image_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.quote.image_select_label,
                                    title: editor.lang.shortcode.quote.image_select_title,
                                    style: 'margin-top: 17px;',
                                    onClick: function () {
                                        //TODO: implement it
                                    }
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 'advanced-tab',
                    label: editor.lang.shortcode.quote.tab_advanced_label,
                    elements: [
                        {
                            id: 'id',
                            type: 'text',
                            label: editor.lang.shortcode.quote.id,
                            validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.shortcode.quote.err_id_empty),
                            setup: function (widget) {
                                this.setValue(widget.data.id);
                            },
                            commit: function (widget) {
                                widget.setData('id', this.getValue());
                            }
                        }
                    ]
                }
            ]
        };
    });
}());
