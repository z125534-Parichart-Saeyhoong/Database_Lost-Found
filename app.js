// Toggle Password
const password = document.getElementById("password");
const togglePasswordBtn = document.getElementById("togglePassword");

togglePasswordBtn.addEventListener("click", function () {
    const type = password.type === "password" ? "text" : "password";
    password.type = type;
    this.textContent = type === "password" ? "Show" : "Hide";
});

// Toggle Confirm Password
const confirmPassword = document.getElementById("confirmPassword");
const toggleConfirmPasswordBtn = document.getElementById("toggleConfirmPassword");

toggleConfirmPasswordBtn.addEventListener("click", function () {
    const type = confirmPassword.type === "password" ? "text" : "password";
    confirmPassword.type = type;
    this.textContent = type === "password" ? "Show" : "Hide";
});

// Validate Password Match + Redirect
document.getElementById("signupForm").addEventListener("submit", function (event) {
    event.preventDefault(); // prevent submit 

    const pass = password.value;
    const confirm = confirmPassword.value;

    if (pass !== confirm) {
        alert("Password and Confirm Password Don't match!");
        return;
    }

    alert("Account created successfully!");

    // 👉 
    window.location.href = "home.html"; 
});

// ------------------------
// SIGN IN FORM
// ------------------------
document.getElementById("signinForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const username = document.getElementById("signinUsername").value;
    const password = document.getElementById("signinPassword").value;

    // (wait backend)
    if (username === "" || password === "") {
        alert("Please fill in all fields");
        return;
    }

    alert("Sign in successful!");

    // 👉 
    window.location.href = "home.html";
});

