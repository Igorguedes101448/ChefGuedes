// Small helper: toggle password visibility for buttons with class 'toggle-pwd'
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.toggle-pwd').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const targetSelector = btn.getAttribute('data-target') || '#password';
      const pwd = document.querySelector(targetSelector);
      if(!pwd) return;
      pwd.type = pwd.type === 'password' ? 'text' : 'password';
      btn.textContent = pwd.type === 'password' ? 'Mostrar' : 'Ocultar';
    });
  });
  // backward compatibility for #togglePassword
  const oldToggle = document.querySelector('#togglePassword');
  if(oldToggle){ oldToggle.addEventListener('click', function(e){ e.preventDefault(); const pwd = document.querySelector('#password'); if(pwd){ pwd.type = pwd.type === 'password' ? 'text' : 'password'; oldToggle.textContent = pwd.type === 'password' ? 'Mostrar' : 'Ocultar'; } }); }
});
// Pequeno script: alternar mostrar/ocultar senha e animação simples
document.addEventListener('DOMContentLoaded', function(){
  const toggle = document.querySelector('#togglePassword');
  const pwd = document.querySelector('#password');
  if(toggle && pwd){
    toggle.addEventListener('click', function(e){
      e.preventDefault();
      if(pwd.type === 'password') pwd.type = 'text'; else pwd.type = 'password';
      toggle.textContent = pwd.type === 'password' ? 'Mostrar' : 'Ocultar';
    });
  }
});
