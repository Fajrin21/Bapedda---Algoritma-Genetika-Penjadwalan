document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("exampleInputPassword");
  const repeatPasswordInput = document.getElementById("exampleRepeatPassword");
  const toggleButton = document.getElementById("togglePassword");
  const passwordHelp = document.getElementById("passwordHelp");
  const valInputPassword = document.getElementById("valInputPassword");
  const emailInput = document.getElementById("exampleInputEmail");
  const valInputEmail = document.getElementById("valInputEmail");

  toggleButton.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      repeatPasswordInput.type = "text";
      toggleButton.textContent = "Sembunyikan Password";
    } else {
      passwordInput.type = "password";
      repeatPasswordInput.type = "password";
      toggleButton.textContent = "Lihat Password";
    }
  });

  function validateEmail() {
    var emailInputValue = emailInput.value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(emailInputValue)) {
      valInputEmail.textContent = "Email Tidak Valid";
      emailInput.classList.add("custom-input");
      return false;
    } else {
      valInputEmail.textContent = "";
      emailInput.classList.remove("custom-input");
      return true;
    }
  }

  function validatePassword() {
    if (passwordInput.value.length < 8) {
      valInputPassword.textContent = "Panjang minimal 8 karakter";
      passwordInput.classList.add("custom-input");
      return false;
    } else {
      valInputPassword.textContent = "";
      passwordInput.classList.remove("custom-input");
      return true;
    }
  }

  function repeadPassword() {
    if (passwordInput.value !== repeatPasswordInput.value) {
      passwordHelp.textContent = "Password harus sama";
      repeatPasswordInput.classList.add("custom-input");
      return false;
    } else {
      passwordHelp.textContent = "";
      repeatPasswordInput.classList.remove("custom-input");
      return true;
    }
  }

  passwordInput.addEventListener("input", validatePassword);
  emailInput.addEventListener("input", validateEmail);
  repeatPasswordInput.addEventListener("input", repeadPassword);
});
