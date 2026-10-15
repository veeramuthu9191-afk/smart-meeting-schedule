document.getElementById("signupForm").addEventListener("submit", function(event) {

    event.preventDefault();

    let username = document.getElementById("username").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if (username === "" || phone === "" || email === "" || password === "" || confirmPassword === "") {
        alert("Please fill all the fields.");
        return;
    }

    if (password !== confirmPassword) {
        alert("Password and Confirm Password do not match.");
        return;
    }

    alert("Signup Successful!");

});