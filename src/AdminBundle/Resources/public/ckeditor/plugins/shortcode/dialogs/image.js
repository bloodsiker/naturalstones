/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('imageSC', function (editor) {
        return {
            title: editor.lang.shortcode.image.dialog_title,
            minWidth: 400,
            minHeight: 115,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.image.tab_basic_label,
                    elements: [
                        {
                            id: 'title',
                            type: 'textarea',
                            label: editor.lang.shortcode.image.title,
                            rows: 10,
                            inputStyle: 'height: 100%; white-space: pre-wrap',
                            setup: function (widget) {
                                this.setValue(widget.data.title.trim());
                            },
                            commit: function (widget) {
                                widget.setData('title', this.getValue() ? this.getValue() : ' ');
                            }
                        },
                        {
                            type: 'hbox',
                            widths: ['80%', '20%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'number',
                                    min: 1,
                                    label: editor.lang.shortcode.image.id,
                                    validate: CKEDITOR.dialog.validate.functions(
                                        CKEDITOR.dialog.validate.notEmpty(),
                                        CKEDITOR.dialog.validate.integer(),
                                        editor.lang.shortcode.image.err_id_empty
                                    ),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                    id: 'image_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.image.select_label,
                                    title: editor.lang.shortcode.image.select_title,
                                    style: 'margin-top: 17px;',
                                    hidden: true,
                                    filebrowser: {
                                        action: 'Browse',
                                        url: editor.config.filebrowserImageListUrl,
                                        target: 'basic-tab:id',
                                        onSelect : function(id) {
                                            var dialog = this.getDialog();
                                            dialog.getContentElement('basic-tab','id').setValue(id);
                                            document.getElementById(dialog.getButton('ok').domId).click();
                                        }
                                    }
                                }
                            ]
                        }
                    ]
                }
            ]
        };
    });
}());
