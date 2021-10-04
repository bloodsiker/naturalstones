/*jslint todo: true*/
/*global CKEDITOR*/

// todo: Треш, но "надо срочно, на вчера". Переписать при первой же возможности.

(function () {
    'use strict';

    CKEDITOR.plugins.add('imageTitle', {
        lang: ['en', 'ru', 'uk']
    });

    CKEDITOR.addCss('.post__photo { position: relative; display: block; } .post__photo img { display: block; max-width: 100%; transition: transform 0.1s ease; } .post__photo br { display: none; } .post__photo .text__photo__main-title, .post__photo .text__photo__sub-title, .post__photo .text__photo__about-title, .post__photo .text__photo__about-author { display: block; text-align: center; font-style: italic; font-family: "PT Serif", sans-serif; font-size: 14px; color: #777777; line-height: 18px; } .post__photo .text__photo__about-title { margin-top: 5px; } .post__photo .img-text--above { position: absolute; bottom: 0px; left: 0; width: 100%; padding: 60px 15px 10px; background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent); } .post__photo .img-text--above .text__photo__main-title, .post__photo .img-text--above .text__photo__sub-title { color: #fff; } .post__photo__description { text-align: justify; font-size: 17px; font-size: 1.0625rem; margin-top: 5px; line-height: 28px; } .post__photo--big-title { overflow: hidden; } .post__photo--big-title:hover img, .post__photo--big-title:hover .post__photo__img-holder { transform: scale(1.07); } .post__photo--big-title:hover .dark_title .text__photo__main-title { color: #A97628; } .post__photo--big-title .text__photo__main-title { padding-bottom: 25px; font-size: 50px; line-height: 50px; font-style: normal; font-family: "Playfair Display"; font-weight: bold; } .post__photo--big-title .text__photo__sub-title { font-family: Roboto, sans-serif; text-transform: uppercase; font-style: normal; letter-spacing: 3px; } .post__photo--big-title .dark_title .text__photo__sub-title, .post__photo--big-title .dark_title .text__photo__main-title { color: #111111; } .post__photo__img-holder { transition: transform 0.1s ease; } .post__photo__img-bg { float: left; width: 25%; -webkit-background-size: cover; background-size: cover; background-position: 50% 50%; height: 370px; }');

    CKEDITOR.on('dialogDefinition', function (event) {
        var dialogName = event.data.name,
            dialogDefinition = event.data.definition,
            onOkOldImplementation = dialogDefinition.onOk,
            onShowOldImplementation = dialogDefinition.onShow,
            editor = event.editor;

        if (dialogName === 'image') {
            var createTitleElement = function () {
                var title = editor.document.createElement('span');
                title.addClass('text__photo__about-title');

                return title;
            }, createCopyrightElement = function () {
                var copyright = editor.document.createElement('em');
                copyright.addClass('text__photo__about-author');

                return copyright;
            };

            dialogDefinition.removeContents('advanced');

            dialogDefinition.addContents({
                id: 'tabTitle',
                label: editor.lang.imageTitle.dialog_title,
                elements: [
                    {
                        id: 'txtTitle',
                        type: 'text',
                        label: editor.lang.imageTitle.title
                    },
                    {
                        id: 'txtCopyright',
                        type: 'text',
                        label: editor.lang.imageTitle.copyright
                    },
                    {
                        id: 'imageTitleAttr',
                        type: 'text',
                        label: editor.lang.imageTitle.title_attr
                    }
                ]
            });

            dialogDefinition.onOk = function (e) {
                var dialog = this,
                    img,
                    wrapper,
                    title,
                    copyright,
                    titleValue,
                    copyrightValue,
                    imageTitleAttr;

                onOkOldImplementation.apply(this, e);

                titleValue = dialog.getValueOf('tabTitle', 'txtTitle');
                copyrightValue = dialog.getValueOf('tabTitle', 'txtCopyright');
                imageTitleAttr = dialog.getValueOf('tabTitle', 'imageTitleAttr');

                if (e.sender.imageEditMode === false) {
                    wrapper = editor.document.createElement('div');
                    wrapper.addClass('post__photo');

                    title = createTitleElement();
                    title.setText(titleValue);

                    copyright = createCopyrightElement();
                    copyright.setText(copyrightValue);

                    img = editor.getSelection().getStartElement().findOne('img');
                    img.setAttribute('title', imageTitleAttr);
                    if (img.getParent().getName() === 'a') {
                        wrapper.append(img.getParent());
                    } else {
                        wrapper.append(img);
                    }

                    wrapper.append(title);
                    wrapper.append(copyright);
                    editor.insertElement(wrapper);
                } else {
                    wrapper = editor.getSelection().getStartElement().getParent();

                    if (wrapper && wrapper.getName() === 'a') {
                        wrapper = wrapper.getParent();
                    }
                    wrapper.findOne('img').setAttribute('title', imageTitleAttr);

                    if (wrapper) {
                        title = wrapper.findOne('span.text__photo__about-title');
                        if (!title) {
                            title = createTitleElement();
                            wrapper.append(title);
                        }
                        title.setText(titleValue);

                        copyright = wrapper.findOne('em.text__photo__about-author');
                        if (!copyright) {
                            copyright = createCopyrightElement();
                            wrapper.append(copyright);
                        }
                        copyright.setText(copyrightValue);
                    }
                }
            };

            dialogDefinition.onShow = function (e) {
                onShowOldImplementation.apply(this, e);
                var dialog = this,
                    selection = editor.getSelection(),
                    element = selection.getStartElement(),
                    wrapper,
                    title,
                    copyright,
                    imageTitleAttr;

                if (element) {
                    wrapper = element.getParent();
                    if (wrapper && wrapper.getName() === 'a') {
                        wrapper = wrapper.getParent();
                    }
                
                    if (wrapper) {
                        title = wrapper.findOne('span.text__photo__about-title');
                        copyright = wrapper.findOne('em.text__photo__about-author');
                        imageTitleAttr = wrapper.findOne('img').getAttribute('title');
                    }

                    if (title) {
                        dialog.setValueOf('tabTitle', 'txtTitle', title.getText());
                    }

                    if (copyright) {
                        dialog.setValueOf('tabTitle', 'txtCopyright', copyright.getText());
                    }

                    if (imageTitleAttr) {
                        dialog.setValueOf('tabTitle', 'imageTitleAttr', imageTitleAttr);
                    }
                }
            };
        }
    });
}());
