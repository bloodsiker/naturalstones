/**
 * oEmbed Plugin plugin
 * Licensed under the MIT license
 * jQuery Embed Plugin: http://code.google.com/p/jquery-oembed/ (MIT License)
 * Plugin for: http://ckeditor.com/license (GPL/LGPL/MPL: http://ckeditor.com/license)
 */

(function() {
        CKEDITOR.plugins.add('oembed',
            {
                icons: 'oembed',
                hidpi: true,
                requires: 'widget,dialog',
                lang: 'ar,ca,cs,de,en,es,fr,nl,pl,pt-br,ru,tr', // %REMOVE_LINE_CORE%
                version: '1.18.1',
                onLoad: function() {
                    CKEDITOR.document.appendStyleSheet(this.path + 'css/oembed.css');
                },
                init: function(editor) {
                    // Load jquery?
                    loadjQueryLibaries();
                    editor.addContentsCss(this.path + 'css/oembed.css');

                    CKEDITOR.tools.extend(CKEDITOR.editor.prototype,
                        {
                            oEmbed: function(url, maxWidth, maxHeight, responsiveResize) {

                                if (url.length < 1 || url.indexOf('http') < 0) {
                                    alert(editor.lang.oembed.invalidUrl);
                                    return false;
                                }

                                function embed() {
                                    if (maxWidth == null || maxWidth == 'undefined') {
                                        maxWidth = null;
                                    }

                                    if (maxHeight == null || maxHeight == 'undefined') {
                                        maxHeight = null;
                                    }

                                    if (responsiveResize == null || responsiveResize == 'undefined') {
                                        responsiveResize = false;
                                    }

                                    embedCode(url, editor, false, maxWidth, maxHeight, responsiveResize);
                                }

                                if (typeof(jQuery.fn.oembed) === 'undefined') {
                                    CKEDITOR.scriptLoader.load(
                                        CKEDITOR.getUrl(
                                            CKEDITOR.plugins.getPath('oembed') + 'libs/jquery.oembed.min.js'),
                                        function() {
                                            embed();
                                        });
                                } else {
                                    embed();
                                }

                                return true;
                            }
                        });

                    editor.widgets.add('oembed',
                        {
                            draggable: false,
                            mask: true,
                            dialog: 'oembed',
                            allowedContent: {
                                div: {
                                    styles: 'text-align,float',
                                    attributes: '*',
                                    classes: editor.config.oembed_WrapperClass != null
                                        ? editor.config.oembed_WrapperClass
                                        : "embeddedContent"
                                },
                                'div(embeddedContent,oembed-provider-*) iframe': {
                                    attributes: '*'
                                },
                                'div(embeddedContent,oembed-provider-*) blockquote': {
                                    attributes: '*'
                                },
                                'div(embeddedContent,oembed-provider-*) embed': {
                                    attributes: '*'
                                }
                            },
                            template:
                                '<div class="' +
                                (editor.config.oembed_WrapperClass != null
                                    ? editor.config.oembed_WrapperClass
                                    : "embeddedContent") +
                                '">' +
                                '</div>',
                            upcast: function(element) {
                                return element.name == 'div' &&
                                    element.hasClass(editor.config.oembed_WrapperClass != null
                                        ? editor.config.oembed_WrapperClass
                                        : "embeddedContent");
                            },
                            init: function() {
                                var data = {
                                    title: this.element.data('title') || '',
                                    oembed: this.element.data('oembed') || '',
                                    maxWidth: this.element.data('maxWidth') || 560,
                                    maxHeight: this.element.data('maxHeight') || 315,
                                    oembed_provider: this.element.data('oembed_provider') || ''
                                };

                                this.setData(data);
                                this.element.addClass('oembed-provider-' + data.oembed_provider);

                                this.on('dialog',
                                    function(evt) {
                                        evt.data.widget = this;
                                    },
                                    this);
                            }
                        });

                    editor.ui.addButton('oembed',
                        {
                            label: editor.lang.oembed.button,
                            command: 'oembed',
                            toolbar: 'insert,10'
                        });

                    String.prototype.beginsWith = function(string) {
                        return (this.indexOf(string) === 0);
                    };

                    function loadjQueryLibaries() {
                        if (typeof(jQuery) === 'undefined') {
                            CKEDITOR.scriptLoader.load('//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js',
                                function() {
                                    jQuery.noConflict();
                                    if (typeof(jQuery.fn.oembed) === 'undefined') {
                                        CKEDITOR.scriptLoader.load(
                                            CKEDITOR.getUrl(CKEDITOR.plugins.getPath('oembed') +
                                                'libs/jquery.oembed.min.js')
                                        );
                                    }
                                });

                        } else if (typeof(jQuery.fn.oembed) === 'undefined') {
                            CKEDITOR.scriptLoader.load(CKEDITOR.getUrl(CKEDITOR.plugins.getPath('oembed') +
                                'libs/jquery.oembed.min.js'));
                        }
                    }

                    function repairHtmlOutput(provider, oldOutput, width, height) {
                        switch (provider.toLowerCase()) {
                            case "slideshare":
                                return oldOutput.replace(/width=\"\d+\" height=\"\d+\"/, "width=\"" + width + "\" height=\"" + height + "\"");
                            case "spotify":
                                return oldOutput.replace(/width=\"\d+\" height=\"\d+\"/, "width=\"" + width + "\" height=\"" + height + "\"");
                            case "instagram":
                                return oldOutput = oldOutput.replace(/<a\W*href=".*instagram\.com.*">.*|[\u2028].*<\/a>/g, 'INSTAGRAM');
                            case "twitter":
                                oldOutput = oldOutput.replace(/<p\W*lang=".*"\W*dir=".*">.*<\/p>/g, '');
                                return oldOutput.replace(/(?!<blockquote\W+class="twitter-tweet"\W*[^>]+>)&(.*?)(?=<a\W*href=".*twitter\.com.*">)/g, 'TWITTER');
                            default:
                                return oldOutput.replace(/([#0-9]\u20E3)|[\xA9\xAE\u203C\u2047-\u2049\u2122\u2139\u3030\u303D\u3297\u3299][\uFE00-\uFEFF]?|[\u2190-\u21FF][\uFE00-\uFEFF]?|[\u2300-\u23FF][\uFE00-\uFEFF]?|[\u2460-\u24FF][\uFE00-\uFEFF]?|[\u25A0-\u25FF][\uFE00-\uFEFF]?|[\u2600-\u27BF][\uFE00-\uFEFF]?|[\u2900-\u297F][\uFE00-\uFEFF]?|[\u2B00-\u2BF0][\uFE00-\uFEFF]?|(?:\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDEFF])[\uFE00-\uFEFF]?/g, '');
                        }
                    }

                    function embedCode(url,
                                       instance,
                                       maxWidth,
                                       maxHeight,
                                       responsiveResize,
                                       widget,
                                       title) {
                        if (title === '') {
                            title = Math.floor(Math.random() * 9999) + 1;
                            // alert(editor.lang.oembed.titleError);
                            // return false;
                        }
                        jQuery('body').oembed(url,
                            {
                                onEmbed: function (e) {
                                    var elementAdded = false,
                                        provider = jQuery.fn.oembed.getOEmbedProvider(url);

                                    if (title !== '') {
                                        widget.element.data('title', title);
                                    }

                                    if (typeof e.code === 'string') {
                                        while (widget.element.$.firstChild) {
                                            widget.element.$.removeChild(widget.element.$.firstChild);
                                        }

                                        widget.element.appendHtml(repairHtmlOutput(provider.name, e.code, maxWidth, maxHeight));
                                        widget.element.data('oembed', url);
                                        widget.element.data('oembed_provider', provider.name);
                                        widget.element.addClass('oembed-provider-' + provider.name);

                                        elementAdded = true;
                                    } else if (typeof e.code[0].outerHTML === 'string') {

                                        while (widget.element.$.firstChild) {
                                            widget.element.$.removeChild(widget.element.$.firstChild);
                                        }

                                        widget.element.appendHtml(repairHtmlOutput(provider.name, e.code[0].outerHTML, maxWidth, maxHeight));
                                        widget.element.data('oembed', url);
                                        widget.element.data('oembed_provider', provider.name);
                                        widget.element.addClass('oembed-provider-' + provider.name);

                                        elementAdded = true;
                                    } else {
                                        alert(editor.lang.oembed.noEmbedCode);
                                    }
                                },
                                onError: function(externalUrl) {
                                    if (externalUrl.indexOf("vimeo.com") > 0) {
                                        alert(editor.lang.oembed.noVimeo);
                                    } else {
                                        alert(editor.lang.oembed.Error);
                                    }

                                },
                                afterEmbed: function() {
                                    editor.fire('change');
                                },
                                maxHeight: maxHeight,
                                maxWidth: maxWidth,
                                useResponsiveResize: responsiveResize,
                                embedMethod: 'editor',
                                title: title,
                                expandUrl: false
                            });
                    }

                    CKEDITOR.dialog.add('oembed',
                        function(editor) {
                            return {
                                title: editor.lang.oembed.title,
                                minWidth: CKEDITOR.env.ie && CKEDITOR.env.quirks ? 568 : 550,
                                minHeight: 155,
                                onShow: function() {
                                    var data = {
                                        title: this.widget.element.data('title') || '',
                                        oembed: this.widget.element.data('oembed') || '',
                                        maxWidth: this.widget.element.data('maxWidth'),
                                        maxHeight: this.widget.element.data('maxHeight')
                                    };

                                    this.widget.setData(data);
                                },

                                onOk: function() {
                                },
                                contents: [
                                    {
                                        label: editor.lang.common.generalTab,
                                        id: 'general',
                                        elements: [
                                            {
                                                type: 'html',
                                                id: 'oembedHeader',
                                                html:
                                                    '<div style="white-space:normal;width:500px;padding-bottom:10px">' +
                                                    editor.lang.oembed.pasteUrl +
                                                    '</div>'
                                            }, {
                                                type: 'text',
                                                id: 'embedCode',
                                                focus: function() {
                                                    this.getElement().focus();
                                                },
                                                label: editor.lang.oembed.url,
                                                title: editor.lang.oembed.pasteUrl,
                                                setup: function(widget) {
                                                    if (widget.data.oembed) {
                                                        this.setValue(widget.data.oembed);
                                                    }
                                                },
                                                commit: function(widget) {
                                                    var dialog = CKEDITOR.dialog.getCurrent(),
                                                        title = dialog.getValueOf('general', 'embedTitle'),
                                                        inputCode = dialog.getValueOf('general', 'embedCode')
                                                            .replace(/\s/g, ""),
                                                        maxWidth = null,
                                                        maxHeight = null,
                                                        responsiveResize = false,
                                                        editorInstance = dialog.getParentEditor();

                                                    if (inputCode.length < 1 || inputCode.indexOf('http') < 0) {
                                                        alert(editor.lang.oembed.invalidUrl);
                                                        return false;
                                                    }

                                                    embedCode(inputCode,
                                                        editorInstance,
                                                        maxWidth,
                                                        maxHeight,
                                                        responsiveResize,
                                                        widget,
                                                        title);

                                                    widget.setData('title', title);
                                                    widget.setData('oembed', inputCode);
                                                    widget.setData('maxWidth', maxWidth);
                                                    widget.setData('maxHeight', maxHeight);
                                                }
                                            }, {
                                                type: 'text',
                                                id: 'embedTitle',
                                                focus: function() {
                                                    this.getElement().focus();
                                                },
                                                label: editor.lang.oembed.embedTitle,
                                                setup: function(widget) {
                                                    if (widget.data.title) {
                                                        this.setValue(widget.data.title);
                                                    }
                                                },
                                            },
                                        ]
                                    }
                                ]
                            };
                        });
                }
            });
    }
)();
