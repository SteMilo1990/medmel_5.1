function userRegistration() {
  // Variabili associate ai campi del modulo
  var name = document.registration.name.value;
  var surname = document.registration.surname.value;
  var email = document.registration.email.value;
  var password = document.registration.password.value;
  var userCaptchaText = document.getElementById("captcha_text").value.replace(" ", "");
  userCaptchaText = userCaptchaText.toLowerCase();

  document.getElementById("error_field").innerHTML = "<br />";
  // Espressione regolare codice fiscale
  let email_reg_exp = /^[A-z0-9\.\+_-]+@[A-z0-9\._-]+\.[A-z]{2,6}$/;
  email = email.replace(/ +/g, '');
  password = password.replace(/ +/g, '');
  //Effettua il controllo sul campo name
  if ((name == "") || (name == "undefined")) {
    document.getElementById("error_field").innerHTML = "The field name is empty!";
    document.registration.name.focus();
    return false;
  }
  //Effettua il controllo sul campo surname
  else if ((surname == "") || (surname == "undefined")) {
    document.getElementById("error_field").innerHTML = "The field surname is empty!";
    document.registration.surname.focus();
    return false;
  }
  //Verifica l'uguaglianza tra i campi PASSWORD e CONFERMA PASSWORD

  //Effettua il controllo sul campo TELEFONO
  else if (!email_reg_exp.test(email) || (email == "") || (email == "undefined")) {
  document.getElementById("error_field").innerHTML = "Email is not valid!";
  document.registration.email.focus();
  return false;
  }
  else if ((password == "") || (password == "undefined")) {
  document.getElementById("error_field").innerHTML = "The field password is empty!";
  document.registration.password.select();
  return false;
  }
  else if (password.length < 10) {
  document.getElementById("error_field").innerHTML = "Password is too short!";
  document.registration.password.select();
  return false;
  }
  else if ((userCaptchaText == "") || (userCaptchaText == "undefined")) {
  document.getElementById("error_field").innerHTML = "The captcha field is empty!";
  document.registration.captchaText.select();
  return false;
  }  //INVIA IL MODULO
  else {
    if (userCaptchaText == trascr_text){
  //INVIA IL MODULO
      $.ajax({
            type: "POST",
            url: "./php/signin.php",
            data: {name: name, surname: surname, email: email, password: password }
        }).done(function( data ) {
            if(parseInt(data) == 0){

                 document.getElementById("error_field").innerHTML = "Email is already registered!";
            }
            else if(parseInt(data) == 1){
                alert("Registration complete!");
                hideOverlay();
            }
            else if( parseInt(data) == -1){
                document.getElementById("error_field").innerHTML = "An error occur, retry!";
            }
        });
      }else{
        document.getElementById("error_field").innerHTML = "Incorrect captcha!";
        document.registration.captcha_text.select();
        generateCaptcha();
      }
    }
}

function userLogin(){

    let email = document.login.email.value;
    email = email.replace(/ +/g, '');
    let password = document.login.password.value;
    password = password.replace(/ +/g, '');
    document.getElementById("error_field_login").innerHTML = "<br />";
    // Espressione regolare codice fiscale
    var email_reg_exp = /^[A-z0-9\.\+_-]+@[A-z0-9\._-]+\.[A-z]{2,6}$/;

    //Check email
    if (!email_reg_exp.test(email) || (email == "") || (email == "undefined") || (password == "") || (password == "undefined")) {
  
    document.getElementById("error_field_login").innerHTML = "Invalid credentials!";

    return false;
    }
    //INVIA IL MODULO
    else {
        $.ajax({
              type: "POST",
              url: "./php/loginUser.php",
              data: {email: email, password: password }
          }).done(function( data ) {
              if(parseInt(data) == 0 || parseInt(data) == -1){
                document.getElementById("error_field_login").innerHTML = "Invalid credentials";
               }
              else if(parseInt(data) == -2){
                 document.getElementById("error_field_login").innerHTML = "The account has been blocked, please <a href='mailto:stefano.milonia@gmail.com'>contact administrator</a>";
              }
              else{
                  var obj = jQuery.parseJSON(data);
                  var dim = Object.keys(obj).length;
                  if(dim <1) {
                      document.getElementById("error_field_login").innerHTML = "Login error, retry!";
                      return 0;
                  }

                  setupSession(obj[0], obj[1], obj[2], obj[3], obj[4] );

                  loginMsg();
                  hideOverlay();
                  loadSession();
                  return 1;
              }
          });
      }
}

function loginMsg(){
    let div = document.createElement("div");
    div.setAttribute("id","loginMsg");
    div.setAttribute("width","300px");
    div.setAttribute("height","300px");
    div.innerHTML = "Login successful!";
    div.setAttribute("style","background-color:white; text-align:center; position:absolute;top:10px; left:40%; right:40%;padding:20px;border:2px solid lightgray;border-radius:5px");
    div.setAttribute("z-index","10000");
    document.getElementById("bodyContainer").appendChild(div);
    setTimeout(function(){$("#loginMsg").fadeOut(1000);},1000);
    setTimeout(function(){div.remove();},2000);
}

function userChangePassword(){
    let email = localStorage.getItem("session_email");
    let oldPassword = localStorage.getItem("session_psw");
    console.log(oldPassword);
    let userCaptchaText = document.getElementById("captcha_text_change_password").value.replace(" ", "");
    console.log(userCaptchaText);
    let newPassword = document.changePasswordForm.newPassword.value;
    console.log(newPassword);
    newPassword = newPassword.replace(/ +/g, '');
    let rePassword = document.changePasswordForm.rePassword.value;
    rePassword = rePassword.replace(/ +/g, '');
    console.log(rePassword);
    document.getElementById("error_field_change_password").innerHTML = "<br />";
    // console.log(newPassword);
    // console.log(rePassword);
    if(email == '' || email == null || oldPassword == '' || oldPassword == null || String(email)=="undefined" || String(oldPassword) == "undefined"){
        try{

            document.getElementById("error_field_change_password").innerHTML = "To use the editor, please log in";
        }catch{}
        return false;
    }
    else if(newPassword.length <10){
        document.getElementById("error_field_change_password").innerHTML = "New Password is too short!";
        document.changePasswordForm.newPassword.focus();
        return false;
    }
    else if(newPassword != rePassword){
        document.getElementById("error_field_change_password").innerHTML = "Confirmed field is different!";
        document.changePasswordForm.rePassword.focus();
        return false;
    }
    else if ((userCaptchaText == "") || (userCaptchaText == "undefined")) {
    document.getElementById("error_field_change_password").innerHTML = "The captcha field is empty!";
    document.changePasswordForm.captcha_text_change_password.select();
    return false;
    }
    else if (userCaptchaText != trascr_text) {
    document.getElementById("error_field_change_password").innerHTML = "The captcha is incorrect!";
    document.changePasswordForm.captcha_text_change_password.select();
    return false;
    }
    //INVIA IL MODULO
    else {
        $.ajax({
              type: "POST",
              url: "./php/changePassword.php",
              data: {email: email, oldPassword: oldPassword, newPassword:newPassword }
          }).done(function( data ) {
             // console.log(data);
              if( parseInt(data) == -1){
                  document.getElementById("error_field_change_password").innerHTML = "Error!";
                      console.logg("hey");

              }
              else{
                  $.ajax({
                        type: "POST",
                        url: "./php/loginUser.php",
                        data: {email: email, password: newPassword }
                    }).done(function( data ) {
                        let obj = jQuery.parseJSON(data);
                        let dim = Object.keys(obj).length;

                        setupSession(obj[0], obj[1], obj[2], obj[3], obj[4] );

                        alert("Password changed!");
                        hideOverlay();
                        loadSession();
                        return 1;

                    });
              }
          });
      }
}
function userRecoveryPassword(){
    let email = document.recoveryPasswordForm.emailRecovery.value;
    let userCaptchaText = document.getElementById("captcha_text_recovery_password").value.replace(" ", "");

    document.getElementById("error_field_recovery_password").innerHTML = "<br />";
    // Espressione regolare codice fiscale
    let email_reg_exp = /^[A-z0-9\.\+_-]+@[A-z0-9\._-]+\.[A-z]{2,6}$/;
    //Check email
    if (!email_reg_exp.test(email) || (email == "") || (String(email) == "undefined")) {
    document.getElementById("error_field_recovery_password").innerHTML = "Email is not valid!";
    document.recoveryPasswordForm.emailRecovery.focus();
    return false;
    }
    else if ((userCaptchaText == "") || (userCaptchaText == "undefined")) {
    document.getElementById("error_field_recovery_password").innerHTML = "The captcha field is empty!";
    document.recoveryPasswordForm.captcha_text_recovery_password.select();
    return false;
    }
    else if (userCaptchaText != trascr_text) {
    document.getElementById("error_field_recovery_password").innerHTML = "The captcha is incorrect!";
    document.recoveryPasswordForm.captcha_text_recovery_password.select();
    return false;
    }
    else{
        let div = document.createElement("div");
        div.setAttribute("id","waitMsg");
        div.setAttribute("width","300px");
        div.setAttribute("height","300px");
        div.innerHTML = "Resetting password...";
        div.setAttribute("style","background-color:white; text-align:center; position:absolute;top:10px; width: 20%; left:40%; margin:auto;padding:20px;border:2px solid lightgray;border-radius:5px");
        div.setAttribute("z-index","10000");
        document.getElementById("overlayPanel").appendChild(div);

        $.ajax({
              type: "POST",
              url: "./php/recoveryPassword.php",
              data: {email: email }
          }).done(function( data ) {

              if(parseInt(data) == -1){
                setTimeout(function(){$("#waitMsg").fadeOut(1000);},1000);
                setTimeout(function(){div.remove();},2000);
                document.getElementById("error_field_recovery_password").innerHTML = "Email is not valid!";
              }else{
                setTimeout(function(){$("#waitMsg").fadeOut(1000);},1000);
                setTimeout(function(){div.remove();},2000);
                alert('New password was send to your email, please check your inbox and spam');
                hideOverlay();
                return 1;
              }
          });
    }
}

function getUserID(){
    return getUserAttribute(0);
}
function getUserName(){
    return getUserAttribute(1);
}

function getUserAttribute(i){
    //0:id_user 1:name 2:surname 3:email 4:password
    let email = localStorage.getItem("session_email");
    let password = localStorage.getItem("session_psw");
    if(email == null || password == null){
        return -1;
    }
    else {
        if(i==0) return localStorage.getItem("session_iduser");
        else if(i==1) return localStorage.getItem("session_name");
        else if(i==2) return localStorage.getItem("session_surname");
        else if(i==3) return localStorage.getItem("session_email");
        else if(i==4) return localStorage.getItem("session_psw");
    }
    return -1;
}
function setupSession(id_user, name, surname, email, password){
    if(id_user == null) localStorage.removeItem("session_iduser");
    else localStorage.setItem("session_iduser", id_user);

    if(name == null) localStorage.removeItem("session_name");
    else localStorage.setItem("session_name", name);

    if(surname == null) localStorage.removeItem("session_surname");
    else localStorage.setItem("session_surname", surname);

    if(email == null) localStorage.removeItem("session_email");
    else localStorage.setItem("session_email", email);

    if(password == null) localStorage.removeItem("session_psw");
    else localStorage.setItem("session_psw", password);
}
