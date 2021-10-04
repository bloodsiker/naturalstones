/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'ru', {
        quote: {
            button_title: 'Вставить код цитаты',
            dialog_title: 'Цитата',
            tab_basic_label: 'Основные параметры',
            tab_advanced_label: 'Дополнительные параметры',
            id: 'ID',
            text: 'Текст',
            image: 'Изображение',
            err_id_empty: 'ID цитаты не может быть пустым.',
            err_text_empty: 'Текст цитаты не может быть пустым.',
            image_select_label: 'Выбрать',
            image_select_title: 'Выбрать изображение из медиа-хранилища'
        },
        gallery: {
            button_title: 'Вставить код галереи',
            dialog_title: 'Галерея',
            tab_basic_label: 'Основные параметры',
            id: 'ID галереи',
            select_label: 'Выбрать',
            select_title: 'Выбрать галерею',
            err_id_empty: 'ID галереи не может быть пустым.',
            wrong_format: 'Неправильный формат данных'
        },
        quiz: {
            button_title: 'Вставить код викторины',
            dialog_title: 'Викторина',
            tab_basic_label: 'Основные параметры',
            id: 'ID галереи',
            select_label: 'Выбрать',
            select_title: 'Выбрать викторину',
            err_id_empty: 'ID викторины не может быть пустым.',
            wrong_format: 'Неправильный формат данных'
        },
        adaptiveIframe: {
            button_title: 'Вставить код iFrame',
            dialog_title: 'Адаптивный iFrame',
            tab_basic_label: 'Основные параметры',
            src: 'Ссылка на iFrame',
            select_label: 'Выбрать',
            err_id_empty: 'iFrame ID не может быть пустым.'
        },
        readAlso: {
            button_title: 'Вставить блок «Читай также»',
            dialog_title: 'Читай также / Смотри также',
            tab_basic_label: 'Основные параметры',
            select_default: 'Выберите площадку',
            err_id_empty: 'Контент блок не может быть пустым.',
            select_placeholder: 'Выберите площадку',
            select_content_1: 'Контент блок #1',
            select_content_2: 'Контент блок #2',
            select_content_3: 'Контент блок #3',
            select_content_4: 'Контент блок #4',
            select_content_5: 'Контент блок #5'
        }
    });
}());
