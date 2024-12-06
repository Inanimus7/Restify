$(document).ready(function() {
  $('.overlay-text').addClass('visible');

  $('#remove-overlay').on('click', function() {
      $('.overlay-text').slideUp("slow", function() {
          $(this).addClass('hidden');
          
          // Add the 'visible' class to trigger the CSS transition
          $('.form-container').addClass('visible');
          
          // Remove blur from the wrapper
          $('.wrapper').addClass('no-blur');
      });
  });

  window.togglePasswordVisibility = function() {
      var passwordInput = document.getElementById("formPassword");
      var showPasswordCheckbox = document.getElementById("showPassword");
      passwordInput.type = showPasswordCheckbox.checked ? "text" : "password"; 
  };
});

document.addEventListener("DOMContentLoaded", () => {
  const $email = document.querySelector("#emailAddress");
  const $password = document.querySelector("#formPassword");
  const $passwordError = document.querySelector(".password-error");
  const $emailError = document.querySelector(".email-error");
  const $submit = document.querySelector("#submitButton");

  let emailIsValid = false;
  let passwordIsValid = false;

  const getEmailValidation = (email) => {
      emailIsValid = email !== "" && /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
  };
  const getPasswordValidation = (password) => {
      passwordIsValid = password !== "" && password.length >= 8; // Changed to >= 8 for clarity
  };
  const checkSigninBtn = () => {
      $submit.disabled = !(emailIsValid && passwordIsValid);
  };
  $email.addEventListener("input", (e) => {
      getEmailValidation(e.target.value);
      if (e.target.value === "") {
          $email.classList.remove("is-invalid");
          $emailError.classList.add("d-none");
      } else if (!emailIsValid) {
          $email.classList.add("is-invalid");
          $emailError.classList.remove("d-none");
      } else {
          $email.classList.remove("is-invalid");
          $emailError.classList.add("d-none");
      }
      checkSigninBtn();
  });
  $password.addEventListener("input", (e) => {
      getPasswordValidation(e.target.value);
      if (e.target.value === "") {
          $password.classList.remove("is-invalid");
          $passwordError.classList.add("d-none");
      } else if (!passwordIsValid) {
          $password.classList.add("is-invalid");
          $passwordError.classList.remove("d-none");
      } else {
          $password.classList.remove("is-invalid");
          $passwordError.classList.add("d-none");
      }
      checkSigninBtn();
  });
  $emailError.classList.add("d-none");
  $passwordError.classList.add("d-none");
});
