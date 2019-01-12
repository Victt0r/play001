'use strict';

function request(type, url, cb, reportcb, falldata, fallcb) {
  const xhr = new XMLHttpRequest();
  xhr.open(type, url, true);
  xhr.onload = () => {
    if (fallcb) falldata ? fallcb(falldata) : fallcb();
    else if (cb) {
      if (xhr.status >= 200 && xhr.status < 400) {
        if (!xhr.response.startsWith('<?php')) {
          if (xhr.response !== '') cb(xhr.response);
          else cb() || reportcb ? reportcb('php-response was empty') :0 ;
        }
        else if (reportcb)
          reportcb('php-file content returned instead of php-response');
      }
      else {
        if (reportcb) reportcb('request.status is ' + xhr.status);
        falldata ? cb(falldata) : cb();
      }
    }
  }
  xhr.onerror =
    e => reportcb(type + ' request to '+ url + ' produced ' + e);
  xhr.ontimeout =
    () => reportcb(type + ' request to '+ url + ' timed out!');
  xhr.send();
}
