// JBRD COIN - app.js

// Header scroll
window.addEventListener('scroll', function () {
  var header = document.getElementById('header');
  if (header) {
    header.classList.toggle('scrolled', window.scrollY > 40);
  }
});

// Avatar dropdown
function toggleMenu() {
  var dd = document.getElementById('userDropdown');
  if (dd) dd.classList.toggle('hidden');
}

document.addEventListener('click', function (e) {
  var menu = document.querySelector('.user-menu');
  var dd   = document.getElementById('userDropdown');
  if (menu && dd && !menu.contains(e.target)) {
    dd.classList.add('hidden');
  }
});

// Toggle password
function togglePass(inputId, btn) {
  var input      = document.getElementById(inputId);
  var isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  btn.querySelector('svg').innerHTML = isPassword
    ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>'
    : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

// Validacion contrasenas registro2
var formRegistro = document.getElementById('formRegistro');
if (formRegistro) {
  formRegistro.addEventListener('submit', function (e) {
    var p1 = document.getElementById('pass1').value;
    var p2 = document.getElementById('pass2').value;
    if (p1 !== p2) {
      e.preventDefault();
      alert('Las contrasenas no coinciden.');
    }
  });
}