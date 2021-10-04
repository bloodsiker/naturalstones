/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'uk', {
        quote: {
            button_title: 'Вставити код цитати',
            dialog_title: 'Цитата',
            tab_basic_label: 'Основні параметри',
            tab_advanced_label: 'Додаткові параметри',
            id: 'ID',
            text: 'Текст',
            image: 'Зображення',
            err_id_empty: 'ID цитати не може бути пустим.',
            err_text_empty: 'Текст цитати не може бути пустим.',
            image_select_label: 'Обрати',
            image_select_title: 'Обрати зображення с медіа-сховища'
        },
        gallery: {
            button_title: 'Вставити код галереї',
            dialog_title: 'Галерея',
            tab_basic_label: 'Основні параметри',
            id: 'ID галереї',
            select_label: 'Обрати',
            select_title: 'Обрати галерею',
            err_id_empty: 'ID галереї не може бути пустим.',
            wrong_format: 'Неправильний формат даних'
        },
        quiz: {
            button_title: 'Вставить код вікторини',
            dialog_title: 'Вікторина',
            tab_basic_label: 'Основняе параметры',
            id: 'ID галереи',
            select_label: 'Выбрать',
            select_title: 'Выбрать вікторину',
            err_id_empty: 'ID вікторини не может быть пустым.',
            wrong_format: 'Неправильний формат даних'
        },
        adaptiveIframe: {
            button_title: 'Вставити код iFrame',
            dialog_title: 'Адаптивний iFrame',
            tab_basic_label: 'Основні параметри',
            src: 'Ссилка на iFrame',
            select_label: 'Вибрати',
            err_id_empty: 'iFrame ID не може бути пустим.'
        },
        readAlso: {
            button_title: 'Вставити блок «Читай також»',
            dialog_title: 'Читай також / Дивись також',
            tab_basic_label: 'Основні параметри',
            select_default: 'Оберіть площадку',
            err_id_empty: 'Контент блок не може бути пустим.',
            select_placeholder: 'Оберіть площадку',
            select_content_1: 'Контент блок #1',
            select_content_2: 'Контент блок #2',
            select_content_3: 'Контент блок #3',
            select_content_4: 'Контент блок #4',
            select_content_5: 'Контент блок #5'
        }
    });
}());
