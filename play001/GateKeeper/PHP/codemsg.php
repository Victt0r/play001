<?php

function Msg($code, $type, $text) {
  switch ($type) {
    case 'S': $type = 'SUCCESS';  break;
    case 'F': $type = 'FAIL';     break;
    case 'E': $type = 'ERROR';    break;
    case 'I': $type = 'INFO';     break;
  }
  return array('code'=>$code,'type'=>$type,'text'=>$text);
}

function codeLine($code, $vars=array()) {
  global $lang;
  $login    = $vars['login'];
  $oldlogin = $vars['oldLogin'];
  $newlogin = $vars['newLogin'];
  $table    = $vars['table'];
  $rows     = $vars['rows'];
  $columns  = $vars['columns'];
  $wrong    = $vars['wrong'];

  $lines = array(
    100 => array('S','eng'=>"User $login is registered!",
                     'rus'=>"Зарегистрирован пользователь $login!"),
    101 => array('F','eng'=>"Login $login already occupied",
                     'rus'=>"Логин $login уже занят"),
    102 => array('E','eng'=>"Not enough credentials to register!",
                     'rus'=>"Недостаточно данных для регистрации!"),
    103 => array('S','eng'=>"You are signed in now as $login!",
                     'rus'=>"Вы вошли в систему как $login!"),
    104 => array('F','eng'=>"Can't sign in. Incorrect password!",
                     'rus'=>"Не удаётся войти. Неправильный пароль!"),
    105 => array('F','eng'=>"Can't sign in. User $login not found!",
                     'rus'=>"Не удаётся войти. Логин $login не найден!"),
    106 => array('E','eng'=>"Not enough credentials to sign in!",
                     'rus'=>"Недостаточно данных для входа!"),
    107 => array('I','eng'=>"Session confirmed, you are signed in",
                     'rus'=>"Сессия подтверждена, вы в системе"),
    108 => array('I','eng'=>"No such session in act",
                     'rus'=>"Нет такой сессии"),
    109 => array('F','eng'=>"No complete session cookie found",
                     'rus'=>"Полные куки сессии не найдены"),
    110 => array('E','eng'=>"No complete session cookie provided",
                     'rus'=>"Полные куки сессии не предоставлены"),
    111 => array('S','eng'=>"Signed out",
                     'rus'=>"Осуществлён выход из системы"),
    112 => array('E','eng'=>"No complete session cookie provided",
                     'rus'=>"Полные куки сессии не предоставлены"),
    113 => array('I','eng'=>"You are not signed in!",
                     'rus'=>"Вы не в системе!"),
    126 => array('S','eng'=>"Session cookies - no more!",
                     'rus'=>"Куки сессии убраны!"),
    114 => array('S','eng'=>"Password changed for user $login",
                     'rus'=>"Пароль для $login изменён"),
    115 => array('F','eng'=>"Can't change password. Incorrect password!",
                     'rus'=>"Прежний пароль указан неверно!"),
    116 => array('F','eng'=>"Can't change password. No user $login found!",
                     'rus'=>"Пользователь $login не найден! Пароль не изменён"),
    117 => array('E','eng'=>"Not enough credentials to change password!",
                     'rus'=>"Недостаточно данных для изменения пароля!"),
    118 => array('S','eng'=>"Login changed from $oldlogin to $newlogin",
                     'rus'=>"Логин изменён с $oldlogin на $newlogin"),
    119 => array('F','eng'=>"Can't change login. Incorrect password!",
                     'rus'=>"Пользователь $login не найден! Логин не изменён"),
    120 => array('F','eng'=>"Can't change login. No user $oldlogin found!",
                     'rus'=>"Неудаётся изменить логин. Неправильный пароль!"),
    121 => array('E','eng'=>"Not enough credentials to change login!",
                     'rus'=>"Недостаточно данных для изменения логина"),
    122 => array('S','eng'=>"User $login removed!",
                     'rus'=>"Пользователь $login удалён!"),
    123 => array('F','eng'=>"Can't unregister. Incorrect password!",
                     'rus'=>"Удаление отменено. Неправильный пароль!"),
    124 => array('F','eng'=>"Can't unregister. No user with login $login found!",
                     'rus'=>"Пользователь с логином $login не найден"),
    125 => array('E','eng'=>"Not enough credentials to unregister!",
                     'rus'=>"Недостаточно данных для удаления пользователя!"),
    127 => array('S','eng'=>"$rows records of $columns fields delivered",
                     'rus'=>"$rows записей на $columns колонок доставлено"),
    128 => array('S','eng'=>"Query returned no data",
                     'rus'=>"Запрос не вернул данных"),
    129 => array('F','eng'=>"No table $table available",
                     'rus'=>"Таблица $table не обнаружена"),
    130 => array('F','eng'=>"No $wrong fields in the $table table",
                     'rus'=>"Поля $wrong в таблице $table не найдены"),
  );

  $line = $lines[$code];
  return Msg($code, $line[0], $line[$lang]);
}
?>
