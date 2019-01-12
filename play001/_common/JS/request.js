'use strict';

function request(type, url, cb, reportcb, falldata, fallcb) {
  const r = new XMLHttpRequest();
  r.open(type, url, true);
  r.onload = () => {
    if (cb) {
      if (r.status >= 200 && r.status < 400) {
        if (!r.response.startsWith('<?php')) {
          if (r.response !== '') cb(r.response);
          else cb();
        }
        else if (reportcb)
          reportcb('php file content returned instead of php-response');
      }
      else {
        if (reportcb) reportcb('request.status is ' + r.status);
        falldata ? cb(falldata) : cb();
      }
    }
    else if (fallcb) falldata ? fallcb(falldata) : fallcb();
  }
  r.onerror =
    e => reportcb(type + ' request to '+ url + ' produced ' + e);
  r.ontimeout =
    () => reportcb(type + ' request to '+ url + ' timed out!');
  r.send();
}
