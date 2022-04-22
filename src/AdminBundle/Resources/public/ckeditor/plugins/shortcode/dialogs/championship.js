/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('championship', function (editor) {
        return {
            title: editor.lang.shortcode.championship.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.championship.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'number',
                                    min: 1,
                                    label: editor.lang.shortcode.championship.id,
                                    validate: CKEDITOR.dialog.validate.functions(
                                        CKEDITOR.dialog.validate.notEmpty(),
                                        CKEDITOR.dialog.validate.integer(),
                                        editor.lang.shortcode.championship.err_id_empty
                                    ),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                    id: 'championship_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.championship.select_label,
                                    title: editor.lang.shortcode.championship.select_title,
                                    style: 'margin-top: 17px;',
                                    hidden: true,
                                    filebrowser: {
                                        action: 'Browse',
                                        url: editor.config.filebrowserChampionshipListUrl,
                                        target: 'basic-tab:id',
                                        onSelect : function(id) {
                                            var dialog = this.getDialog();
                                            dialog.getContentElement('basic-tab','id').setValue(id);
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            id: 'tour',
                            type: 'text',
                            label: editor.lang.shortcode.championship.tour,
                            default: '',
                            setup: function (widget) {
                                this.setValue(widget.data.tour);
                            },
                            commit: function (widget) {
                                widget.setData('tour', this.getValue());
                            }
                        },
                        {
                            id: 'stage',
                            type: 'select',
                            label: editor.lang.shortcode.championship.stage,
                            items: [],
                            onLoad: function() {
                                var widget = this;
                                $.ajax({
                                    type: 'GET',
                                    url: editor.config.filebrowserChampionshipMatchStagesUrl,
                                    dataType: 'json',
                                    success: function(data, textStatus, jqXHR) {
                                        for (var i = 0; i < data.length; i++) {
                                            widget.add(data[i]['label'], data[i]['value']);
                                        }
                                    },
                                });
                            },
                            setup: function (widget) {
                                this.setValue(widget.data.stage);
                            },
                            commit: function (widget) {
                                widget.setData('stage', this.getValue());
                            }
                        }
                    ]
                }
            ]
        };
    });
}());
