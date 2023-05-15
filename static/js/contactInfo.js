// Hidden phone number
var p1 = '0692';
var p2 = '650';
var p3 = '239';
var phn = `${p2}-${p3}-${p1}`;

var e1 = 'org';
var e2 = 'weefree';
var a  = '@';

function showPhone() {
  var shows = document.getElementsByName("show-phone");
  var phns  = document.getElementsByName("phone-number");
  for(var i = 0; i < shows.length; i++) {
    shows[i].style.display="none";
    phns[i].innerHTML = `<a href='tel:${phn}'>${phn}</a>`;
  }
}
function showEmail(start) {
  var shows = document.getElementsByName("show-email");
  var emls  = document.getElementsByName("email-address");
  for(var i = 0; i < shows.length; i++) {
    shows[i].style.display="none";
    emls[i].innerHTML = `<a href='mailto:${start}${a}${e2}med.${e1}'>${start}${a}${e2}med.${e1}</a>`;
  }
}
