var acceptCookie;
function checkCookies(){
  acceptCookie = document.cookie.indexOf("acceptCookies=1");
  if (acceptCookie == -1){
    document.getElementById("cookiebar").style.display = "block";
  }else{
    document.getElementById("cookiebar").style.display = "none";
  }
}
function acceptCookies(){
  document.cookie = "acceptCookies=1";
  checkCookies();
}

checkCookies();
