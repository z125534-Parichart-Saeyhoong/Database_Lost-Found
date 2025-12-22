// ================================
// TOGGLE PASSWORD (SIGN UP)
// ================================
const password = document.getElementById("password");
const togglePasswordBtn = document.getElementById("togglePassword");

if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener("click", function () {
        const type = password.type === "password" ? "text" : "password";
        password.type = type;
        this.textContent = type === "password" ? "Show" : "Hide";
    });
}


// ================================
// TOGGLE CONFIRM PASSWORD (SIGN UP)
// ================================
const confirmPassword = document.getElementById("confirmPassword");
const toggleConfirmPasswordBtn = document.getElementById("toggleConfirmPassword");

if (toggleConfirmPasswordBtn) {
    toggleConfirmPasswordBtn.addEventListener("click", function () {
        const type = confirmPassword.type === "password" ? "text" : "password";
        confirmPassword.type = type;
        this.textContent = type === "password" ? "Show" : "Hide";
    });
}


// ================================
// SIGN UP FORM VALIDATION
// ================================
const signupForm = document.getElementById("signupForm");

if (signupForm) {
    signupForm.addEventListener("submit", function () {

        const pass = password.value;
        const confirm = confirmPassword.value;

        // ❌ ถ้า password ไม่ตรง → ไม่ให้ submit
        if (pass !== confirm) {
            alert("Password and Confirm Password don't match!");
            return false;
        }

        // ✅ ถ้าตรง → ปล่อยให้ form ส่งไป signup.php
        // ❗ ไม่มี preventDefault
        // ❗ ไม่มี window.location
    });
}


// ================================
// SIGN IN FORM (ปล่อยให้ PHP จัดการ)
// ================================
const signinForm = document.getElementById("signinForm");

if (signinForm) {
    signinForm.addEventListener("submit", function () {

        const username = document.getElementById("signinUsername").value;
        const password = document.getElementById("signinPassword").value;

        if (username === "" || password === "") {
            alert("Please fill in all fields");
            return false;
        }

        // ✅ ปล่อยให้ form ส่งไป signin.php
    });
}
