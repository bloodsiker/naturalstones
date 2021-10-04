/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'en', {
        quote: {
            button_title: 'Insert Quote shortcode',
            dialog_title: 'Quote',
            tab_basic_label: 'Basic settings',
            tab_advanced_label: 'Advanced settings',
            id: 'ID',
            text: 'Text',
            image: 'Image',
            err_id_empty: 'Quote ID cannot be empty.',
            err_text_empty: 'Quote text cannot be empty.',
            image_select_label: 'Select',
            image_select_title: 'Select image from media repository'
        },
        gallery: {
            button_title: 'Insert Gallery shortcode',
            dialog_title: 'Gallery',
            tab_basic_label: 'Basic settings',
            id: 'Gallery ID',
            select_label: 'Select',
            select_title: 'Select gallery from repository',
            err_id_empty: 'Gallery ID cannot be empty.',
            wrong_format: 'Wrong data format'
        },
        quiz: {
            button_title: 'Insert Quiz shortcode',
            dialog_title: 'Quiz',
            tab_basic_label: 'Basic settings',
            id: 'Quiz ID',
            select_label: 'Select',
            select_title: 'Select quiz from repository',
            err_id_empty: 'Quiz ID cannot be empty.',
            wrong_format: 'Wrong data format'
        },
        adaptiveIframe: {
            button_title: 'Insert Iframe shortcode',
            dialog_title: 'Adaptive iFrame',
            tab_basic_label: 'Basic settings',
            src: 'iFrame src',
            select_label: 'Select',
            err_id_empty: 'iFrame ID cannot be empty.'
        },
        readAlso: {
            button_title: 'Insert block «Read also»',
            dialog_title: 'Read also / See also',
            tab_basic_label: 'Basic settings',
            select_default: 'Select placeholder',
            err_id_empty: 'Placeholder cannot be empty.',
            select_placeholder: 'Select placeholder',
            select_content_1: 'Content Block #1',
            select_content_2: 'Content Block #2',
            select_content_3: 'Content Block #3',
            select_content_4: 'Content Block #4',
            select_content_5: 'Content Block #5'
        }
    });
}());
