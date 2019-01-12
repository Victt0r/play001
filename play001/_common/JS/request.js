'use strict';

function request(type, url, cb, reportcb, falldata, fallcb, simonly, rawphp) {
  if (simonly) falldata ? fallcb(falldata) : fallcb();
  else {
    const xhr = new XMLHttpRequest();
    xhr.open(type, url, true);
    xhr.onload = () => {
      if (fallcb) falldata ? fallcb(falldata) : fallcb();
      else if (cb) {
        if (xhr.status >= 200 && xhr.status < 400) {
          if (!xhr.response.startsWith('<?php') || rawphp) {
            if (xhr.response !== '') cb(xhr.response);
            else cb() || reportcb ? reportcb('php-response was empty') :0 ;
          }
          else if (reportcb && !rawphp) {
            reportcb('php-file content returned instead of php-response');
            falldata ? cb(falldata) : 0;
          }
        }
        else {
          if (reportcb)
            reportcb('unfortunately request.status is ' + xhr.status);
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
}
