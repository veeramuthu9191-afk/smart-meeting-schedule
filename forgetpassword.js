document.getElementById("forgotForm").addEventListener("submit", function(event){

    event.preventDefault();

    let email = document.getElementById("email").value.trim();
    let otp = document.getElementById("otp").value.trim();
    let newPassword = document.getElementById("newPassword").value.trim();
    let confirmPassword = document.getElementById("confirmPassword").value.trim();

    if(email === ""){
        alert("Please enter your email.");
        return;
    }

    if(otp === ""){
        alert("Please enter OTP.");
        return;
    }

    if(newPassword === ""){
        alert("Please enter new password.");
        return;
    }

    if(newPassword !== confirmPassword){
        alert("Password and Confirm Password do not match.");
        return;
    }

    alert("Password Reset Successfully!");
});