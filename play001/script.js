


request('GET','style.css', data => {
  const pre = document.createElement('pre')
  pre.innerText = data
  document.getElementById('body').appendChild(pre)
})

