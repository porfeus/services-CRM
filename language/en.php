<?php

return [
  'language_title' => 'English',
  //---

  //Шаблон сообщения при генерации аккаунтов
  'generate_message' =>implode(PHP_EOL, [
    'Login: {login}',
    'Pass: {password}',
    'Tariff: {time}',
    'To activate your account:',
    '1. Go to the page: http://example.com/loginASasAS',
    '2. Enter the login and password received when purchasing, activate the account.',
  ]),
  'дн.' => 'day(s)',
  'ч.' => 'hour(s)',
  'мин.' => 'min(s)',

  //Шаблон сообщения при генерации кодов подления
  'generate_codes_message' =>implode(PHP_EOL, [
    'Code: {code}',
    'Tariff: {time}',
    'To renew account activation:',
    '1. Go to the page: http://example.com/loginASasAS',
    '2. Enter activation code, activate account.',
  ]),

  //Текст "Согласен с условиями"
  'agree_text' => 'I agree with <a href="pages/rules_en.html" target="_blank">the terms</a>',
  'Согласитесь с условиями' => 'Agree to terms and conditions',

  //Текст кнопок продления
  'Продлить на {days} дней' => 'Renew on {days} days',

  //Текст блокировки аккаунта
  'Действие аккаунта заблокировано Администратором. Для выяснения причин обратитесь в службу поддержки' => 'The account is locked by the Administrator. To find out why, contact support',

  //Шаблон сообщения для уведомления пользователей об окончании активации
  'notification_subject' => 'Activation Notification',
  'notification_message' => 'Until the end of your _LOGIN_ account, _DATETIME_ remains.
You can renew your account by purchasing an extension code for it in your account.
If the account is not renewed within 5 days, it will be removed from the database, without the possibility of recovery.',

  'Вход' => 'Enter',
  'Логин' => 'Login',
  'Пароль' => 'Password',
  'Войти' => 'Submit',
  'Неправильный логин или пароль' => 'Incorrect login or password',
  'Неправильно введен проверочный код' => 'Incorrect verification code entered',
  'Докажите, что Вы не робот:' => 'Prove that you are not a robot:',
  '' => '',
  'Ошибка' => 'Error',
  'Заголовок сайта' => 'Database of services',
  'Пользователи' => 'Users',
  'Выход' => 'Exit',
  'Примечание' => 'Note',
  'Действия' => 'Actions',

  //Таблица
  'Первая' => 'First',
  'Последняя' => 'Last',
  'Далее' => 'Next',
  'Назад' => 'Prev',
  'Таблица пуста' => 'Table is empty',
  'Страница _PAGE_ из _PAGES_' => 'Page_PAGE_ of _PAGES_',
  'Нет записей для отображения' => 'There are no posts to display',
  'Пожалуйста, ждите...' => 'Please, wait...',
  'Таблица подготавливается...' => 'The table is being prepared ...',
  'Поиск:' => 'Search:',
  'Нет записей для отображения' => 'No data to display',
  //Конец. Таблица

  'лимит пользователей' => 'users limit',
  'активирован' => 'activated',
  'окончание активации' => 'activation end',
  'Нет' => 'No',
  'Сохранить' => 'Save',
  'Отправить' => 'Send',
  'Закрыть' => 'Close',
  'Требуется подтверждение' => 'Confirmation required',
  'Сохранить выбранный вами язык?' => 'Do you want to save the selected language?',
  'записей' => 'records',
  'Выбрать' => 'Choose',
  'Значение не может быть пустым' => 'Value can not be empty',
  'Внимание! Код чувствителен к регистру' => 'Attention! The code is case sensitive',
  'Достиг лимит пользователей онлайн на Вашем аккаунте' => 'Has reached the limit of online users on your account',
  'Вход админа' => 'Admin Login',
  'Вход пользователя' => 'User Login',
  'Доступ запрещен!' => 'Access denied!',
  'Подключенных услуг' => 'Connected services',
  'Введите e-mail' => 'Enter your e-mail',
  'Запрос e-mail' => 'Request for e-mail',
  'Если Вы хотите получать напоминание об окончании действия аккаунта, или системные уведомления, укажите ваш e-mail' => 'If you want to receive a reminder about the end of the account, or system notifications, enter your e-mail',
  'Кнопки продления' => 'Extension buttons',
  'Активировать' => 'Activate',
  'Введите код' => 'Enter a code',
  'Активация кода продления' => 'Activating the renewal code',
  'До полного удаления осталось' => 'Until complete deletion',
  'Действие аккаунта приостановлено в связи с истечением времени. Вы можете продлить услугу, купив код продления.' => 'The account has been suspended due to the expiration of the time. You can renew the service by purchasing the renewal code.',
  'Продлить аккаунт' => 'Renew account',
  'Услуги' => 'The services',
  'Коды продления' => 'Extension codes',
  'Данный код активации уже использован' => 'This activation code has already been used',
  'Неправильный код активации' => 'Incorrect activation code',
  'Код' => 'Code',
  'Название' => 'Name',
  'Формат' => 'Format',
  'Действие' => 'Action',
  'Открыть' => 'Open',
  'Скачать' => 'Download',
  'Перейти' => 'Go to',
  'Осталось' => 'Left',
  'архив будет удалён через' => 'archive will be deleted via',
  'Скрыть' => 'Hide',
  'не указан' => 'not specified',
  'Панель управления' => 'Control Panel',
  '' => '',
];
