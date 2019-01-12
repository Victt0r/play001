'use strict';

// data-model object with methods to feed itself with JSON to collect data
const gk = (()=>{

  var gk_php = 'GateKeeper/PHP/gk.php';

  // 'Clerk/PHP/clerk.php' is set by default
  function setPath(path_to_gk_php) { gk_php = path_to_gk_php }

  function SignUp(login, pass) {
    if (login && pass)
      request('POST', gk_php+'?task=reg'+'&login='+login+'&pass='+pass,
             response => log(JSON.parse(response)), log);
    else log(new Response(102, 'E', 'Not enough credentials to register!'));
  }

  function SignIn(login, pass) {
    if (login && pass)
      request('POST', gk_php+'?task=login'+'&login='+login+'&pass='+pass,
             response => {
        response = JSON.parse(response);      let d;
        if (d = response.data) {
          cookie.set('userid', d.userid, d.expire);
          cookie.set('token',  d.token,  d.expire);
        }
        log(response);
      }, log);
    else log(new Response(106, 'E', 'Not enough credentials to sign in!'));
  }

  function isSignedIn() {
    const userid = cookie.get('userid'), token = cookie.get('token');
    if (userid && token)
      request('POST', gk_php+'?task=check'+'&userid='+userid+'&token='+token,
             response => {
        response = JSON.parse(response);      let d;
        if (d = response.data) {
        cookie.set('userid', userid,  d.expire);
        cookie.set('token',  d.token, d.expire);
      }
      else if (drop_sess_on_deny) abandon(1);
      log(response);
    }, log);
    else log(new Response(109, 'F', 'No complete session cookie found'));
  }

  function SignOut() {
    const userid = cookie.get('userid'), token = cookie.get('token');
    if (userid && token) {
      request('POST', gk_php+'?task=logout'+'&userid='+userid+'&token='+token,
              0, log);
      cookie.remove('userid');
      cookie.remove('token');
      log(new Response(111, 'S', 'Signed out'));
    }
    else log(new Response(113, 'I', 'You are not signed in!'));
  }

  function abandon(silent) {
    cookie.remove('userid');
    cookie.remove('token');
    if (!silent) log(new Response(126, 'S', 'Session cookies - no more!'));
  }

  function ChangePassword(login, oldpass, newpass) {
    if (login && oldpass && newpass)
      request('POST', gk_php+'?task=newpass'+
             '&login='+login+'&oldpass='+oldpass+'&newpass='+newpass,
             response => log(JSON.parse(response), log));
    else log(new Response(117, 'E',
                          'Not enough credentials to change password!'));
  }

  function ChangeLogin(oldlogin, pass, newlogin) {
    if (oldlogin && pass && newlogin)
      request('POST', gk_php+'?task=rename'+
             '&oldlogin='+oldlogin+'&pass='+pass+'&newlogin='+newlogin,
             response => log(JSON.parse(response), log));
    else log(new Response(121, 'E',
                          'Not enough credentials to change login!'));
  }

  function UnRegister(login, pass) {
    if (login && pass)
      request('POST', gk_php+'?task=unreg'+'&login='+login+'&pass='+pass,
             response => log(JSON.parse(response), log));
    else log(new Response(125, 'E', 'Not enough credentials to unregister!'));
  }

  function getData(table, fields, own=0) {
    const userid = cookie.get('userid'), token = cookie.get('token');
    let creds = (userid && token) ?
        '&userid='+userid+'&token='+token+'&own='+own : '';
    request('POST', gk_php+'?task=get'+'&table='+table+'&fields='+
           JSON.stringify(fields)+creds, response => {
      response = JSON.parse(response);      let d;
      if (d = response.data) {
        if (d.token) {
          cookie.set('userid', userid,  d.expire);
          cookie.set('token',  d.token, d.expire);
        }
        if (d.headers) {
          log(d.headers);
          log(d.rows);
        }
      }
      else if (drop_sess_on_deny) abandon(1);
      log(response);
    }, log);
  }

  const gk = {setPath,
              SignUp, SignIn, isSignedIn, SignOut, abandon,
              ChangePassword, ChangeLogin, UnRegister,
              getData}

  return gk;
})();
