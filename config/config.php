<?php

return [
  //admin
  'admin_login' => 'admin', //Логин админа
  'admin_password' => '12345', //Пароль админа
  'admin_email' => 'gugavaeezd@mail.ru', //E-mail админа
  'show_captcha_admin' => 0, //Запрашивать капчу у админа (true - да, false - нет)

  //logo and Bg
  'bg_img' => 'bg.jpg', //Фоновая картинка на странице входа (картинка в папке img)
  'logo_img' => 'logo.png', //Логотип на странице входа (картинка в папке img)
  'bg_img-panel' => 'bg_in.jpg', //Фоновая картинка в панели пользователя (картинка в папке img)

  //users
  'default_language' => 'ru', //Язык пользователей по-умолчанию
  'need_agree' => true, //Требовать согласие с условиями (true - да, false - нет)
  'show_captcha_user' => 0, //Запрашивать капчу у пользователя (true - да, false - нет)

  //database
  'DB_DRIVER'   => 'mysql', //Драйвер БД
  'DB_HOSTNAME' => 'localhost', //Сервер БД
  'DB_USERNAME' => 'user12345', //Логин пользователя БД
  'DB_PASSWORD' => 'wFNf768', //Пароль пользователя БД
  'DB_DATABASE' => 'sites2_db', //База данных

  'txt_archives_life_min' => 5, //Время жизни архивов (в мин.) в группе текстовых файлов
  'inactive_delete_days' => 5, //Через сколько дней удалять пользователей по истечении активации

  //pages
  'users_show_num' => 100, //Число записей на странице пользователей
  'services_show_num' => 20, //Число записей на странице услуг
  'codes_show_num' => 20, //Число записей на странице кодов продления
  'buttons_show_num' => 20, //Число записей на странице кнопок продления
  'services_columns' => 4, //Число колонок в меню выбора услуг

  //notification
  'notification_days' => [1,2,3,4,5], //За сколько дней уведомлять о завершении активации
  'notification_limit' => 5, //Лимит отправки писем за один раз
];
