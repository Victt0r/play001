<?php

require_once $_SERVER['DOCUMENT_ROOT'].'sandbox.php';
require_once 'codemsg.php';
require_once 'gkvars.php';
require_once 'permits.php';

require_once $commonsPath.'krumo.php';
require_once $commonsPath.'f.php';

require_once 'Auth/checks.php';


function ResponseOld($code, $type, $text, $data=null) {
  switch ($type) {
    case 'S': $type = 'SUCCESS';  break;
    case 'F': $type = 'FAIL';     break;
    case 'E': $type = 'ERROR';    break;
    case 'I': $type = 'INFO';     break;
  }
  $response = array('msg' => array('code'=>$code,'type'=>$type,'text'=>$text));
  if ($data) $response['data'] = $data;
  return $response;
}

function Response($msg, $data=null) {
  $response = array('msg' => $msg);
  if ($data) $response['data'] = $data;
  return $response;
}

switch ($_REQUEST['task']) {

  case 'reg': {
    list ($login, $pass) = f::request('login', 'pass');
    if ($login and $pass) {
      $q = "SELECT login FROM $tblUsers WHERE login = ?";
      if (f::getValue($db, $q, qp($login,'s')))
        echo json_encode(Response(codeLine(101, $login)));
      else {
        $hash = hashStr($pass);
        $q = "INSERT $tblUsers (login, passhash) VALUES (?, '$hash')";
        f::execute($db, $q, qp($login,'s'));
        echo json_encode(Response(codeLine(100, $login)));
      }
    }
    else echo json_encode(Response(codeLine(102)));
  } break;

  case 'login': {
    list ($login, $pass) = f::request('login', 'pass');
    if ($login and $pass) {
      $q = "SELECT id, passhash FROM $tblUsers WHERE BINARY login = ?";
      if (list ($userid, $hash) = f::getRecord($db, $q, qp($login,'s'))) {

        if (hashCheck($pass, $hash)) {
          $token = randStr();
          $q = "INSERT $tblSessions (user_id, token, bfp_hash)
                VALUES ($userid, '$token', '".hashStr(bfp())."')";
          f::execute($db, $q);

          $q = "DELETE FROM $tblSessions WHERE user_id = $userid AND dt_modify <
                  (SELECT min(dt_modify) FROM
                    (SELECT dt_modify FROM $tblSessions WHERE user_id = $userid
                      ORDER BY dt_modify DESC LIMIT $sessNum) AS tmp)";
          f::execute($db, $q);

          echo json_encode(Response(codeLine(103, $login),
            array('userid'=>$userid, 'token'=>$token, 'expire'=>$sessExpire)));
        }
        else echo json_encode(Response(codeLine(104)));
      }
      else echo json_encode(Response(codeLine(105, $login)));
    }
    else echo json_encode(Response(codeLine(106)));
  } break;

  case 'check': {
    list ($userid, $token) = f::request('userid', 'token');
    if ($userid and $token) {
      $q = "SELECT id, bfp_hash FROM $tblSessions WHERE user_id = ?
      AND token = ? AND dt_modify > NOW() - INTERVAL $sessExpire DAY";
      $p = qp($userid,'i', $token,'s');
      if (list ($id, $bfp) = f::getRecord($db, $q, $p) and bfpCheck($bfp)) {
        $token = randStr();
        $q = "UPDATE $tblSessions SET token = '$token' WHERE id = $id";
        f::execute($db, $q);
        echo json_encode(Response(codeLine(107),
                                array('token'=>$token, 'expire'=>$sessExpire)));
      }
      else echo json_encode(Response(codeLine(108)));
    }
    else echo json_encode(Response(codeLine(110)));
  } break;

  case 'logout': {
    list ($userid, $token) = f::request('userid', 'token');
    if ($userid and $token) {
      $q = "SELECT id, bfp_hash FROM $tblSessions WHERE user_id = ?
      AND token = ?";
      $p = qp($userid,'i', $token,'s');
      if (list ($id, $bfp) = f::getRecord($db, $q, $p) and bfpCheck($bfp))
        f::execute($db, "DELETE FROM $tblSessions WHERE id = $id");
    }
    else echo json_encode(Response(codeLine(112)));
  } break;

  case 'newpass': {
    list (       $login,  $oldpass,  $newpass ) =
      f::request('login', 'oldpass', 'newpass');
    if ($login and $oldpass and $newpass) {
      $q = "SELECT id, passhash FROM $tblUsers WHERE login = ?";
      if (list ($userid, $hash) = f::getRecord($db, $q, qp($login,'s'))) {

        if (hashCheck($oldpass, $hash)) {
          $hash = hashStr($newpass);
          $q = "UPDATE $tblUsers SET passhash = '$hash' WHERE id = $userid";
          f::execute($db, $q);
          echo json_encode(Response(codeLine(114, $login)));
        }
        else echo json_encode(Response(codeLine(115)));
      }
      else echo json_encode(Response(codeLine(116, $login)));
    }
    else echo json_encode(Response(codeLine(117)));
  } break;

  case 'rename': {
    list (       $oldlogin,  $pass,  $newlogin ) =
      f::request('oldlogin', 'pass', 'newlogin');
    if ($oldlogin and $pass and $newlogin) {
      $q = "SELECT id, passhash FROM $tblUsers WHERE login = ?";
      if (list ($userid, $hash) = f::getRecord($db, $q, qp($oldlogin, 's'))) {

        if (hashCheck($pass, $hash)) {
          $q = "UPDATE $tblUsers SET login = ? WHERE id = $userid";
          f::execute($db, $q, qp($newlogin,'s'));
          echo json_encode(Response(codeLine(118, $oldlogin, $newlogin)));
        }
        else echo json_encode(Response(codeLine(119)));
      }
      else echo json_encode(Response(codeLine(120, $oldlogin)));
    }
    else echo json_encode(Response(codeLine(121)));
  } break;

  case 'unreg': {
    list ($login, $pass) = f::request('login', 'pass');
    if ($login and $pass) {
      $q = "SELECT id, passhash FROM $tblUsers WHERE login = ?";
      if (list ($userid, $hash) = f::getRecord($db, $q, qp($login,'s'))) {

        if (hashCheck($pass, $hash)) {
          $q = "DELETE FROM $tblUsers WHERE id = $userid";
          f::execute($db, $q);
          echo json_encode(Response(codeLine(122, $login)));
        }
        else echo json_encode(Response(codeLine(123, $login)));
      }
      else echo json_encode(Response(codeLine(124, $login)));
    }
    else echo json_encode(Response(codeLine(125)));
  } break;

  case 'get': {
    list ($userid, $token) = f::request('userid', 'token');
    if ($userid and $token) {
      $q = "SELECT id, bfp_hash FROM $tblSessions WHERE user_id = ?
      AND token = ? AND dt_modify > NOW() - INTERVAL $sessExpire DAY";
      $p = qp($userid,'i', $token,'s');
      if (list ($id, $bfp) = f::getRecord($db, $q, $p) and bfpCheck($bfp)) {
        $token = randStr();
        $q = "UPDATE $tblSessions SET token = '$token' WHERE id = $id";
        f::execute($db, $q);
        $data['token' ] = $token;
        $data['expire'] = $sessExpire;
      } else $userid = false;
    }
    list ($tbl, $fields, $ownOnly) = f::request('table', 'fields', 'own');
    $table = $tblPrefix.$tbl;
    if ($fields) $fields = json_decode($fields);

    if (!$userid) {
      if (!isset($freeAccess[$tbl])) exit
        (json_encode(Response(129, 'F', "No table $tbl available")));
      if ($wrong = implode(array_diff($fields, $freeAccess[$tbl]), ', '))
        exit (json_encode(Response(130, 'F',
                       "Field(s) $wrong are not available in the $tbl table")));
      $ownOnly = false;
    }
    else {
      $freeAccess = array_merge_recursive($freeAccess, $userAccess);
      $privAccess = array_merge_recursive($freeAccess, $privAccess);
      if ($ownOnly  or !isset($freeAccess[$tbl]) or
          array_diff($fields, $freeAccess[$tbl])) {
        if (!isset($privAccess[$tbl])) exit
          (json_encode(Response(codeLine(129, $tbl) ,$data)));
        if ($wrong = implode(array_diff($fields, $privAccess[$tbl]), ', '))
          exit (json_encode(Response(codeLine(130, $wrong, $table), $data)));
        $ownOnly = true;
      }
    }

    if (!$fields) $fields = $ownOnly? $privAccess[$tbl] : $freeAccess[$tbl];
    $data['headers'] = $fields;
    $fields = implode($fields, ', ');

    $q = "SELECT $fields FROM $table WHERE 1"; # it's for further concatenation
    if ($ownOnly) {
      if ($table != $tblUsers) $q .= " AND user_id = $userid";
      else                     $q .= " AND      id = $userid";
    }
    $data['rows'] = f::getRecords($db, $q);
    if ($rows = sizeof($data['rows']) and $columns = sizeof($data['headers']))
      echo json_encode(Response(codeLine(127, $rows, $columns), $data));
    else echo json_encode(Response(codeLine(128), $data));
  } break;

  default: {}
}

?>
