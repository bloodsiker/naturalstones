/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('file', function (editor) {
        return {
            title: editor.lang.shortcode.file.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.file.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'number',
                                    min: 1,
                                    label: editor.lang.shortcode.file.id,
                                    validate: CKEDITOR.dialog.validate.functions(
                                        CKEDITOR.dialog.validate.notEmpty(),
                                        CKEDITOR.dialog.validate.integer(),
                                        editor.lang.shortcode.file.err_id_empty
                                    ),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                    id: 'file_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.file.select_label,
                                    title: editor.lang.shortcode.file.select_title,
                                    style: 'margin-top: 17px;',
                                    hidden: true,
                                    filebrowser: {
                                        action: 'Browse',
                                        url: editor.config.filebrowserFileListUrl,
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
