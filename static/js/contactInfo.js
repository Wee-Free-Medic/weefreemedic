// Hidden phone number
var p1 = '0692';
var p2 = '650';
var p3 = '239';
var phn = `${p2}-${p3}-${p1}`;

var e1 = 'info';

function showPhone() {
  document.getElementById("show-phone").style.display="none";
  document.getElementById("phone-number").innerHTML = `<a href='tel:${phn}'>${phn}</a>`;
}
function showEmail() {
  document.getElementById("show-email").style.display="none";
  document.getElementById("email-address").innerHTML = `<a href='mailto:${e1}@weefreemedic.org'>${e1}@weefreemedic.org</a>`;
}
