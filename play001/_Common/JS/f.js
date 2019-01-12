'use strict';

function Response(code, type, text) {
  switch (type) {
    case 'S': type = 'SUCCESS';   break;
    case 'F': type = 'FAIL';      break;
    case 'E': type = 'ERROR';     break;
    case 'I': type = 'INFO';      break;
  }
  this.msg = {code, type, text}
}

//let log = console.log; "plus"
function log(subj) {
  if (subj.msg) console.log(subj.msg.type+' '+subj.msg.code+': '+subj.msg.text);
  else console.log(subj);
}
