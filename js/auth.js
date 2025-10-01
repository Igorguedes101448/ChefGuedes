// Auth UI script: alterna entre login (right) e register (left) com animação
document.addEventListener('DOMContentLoaded', function(){
  const root = document.querySelector('.auth-card');
  const toRegister = document.querySelector('#toRegister');
  const toLogin = document.querySelector('#toLogin');

  if(toRegister) toRegister.addEventListener('click', function(e){
    e.preventDefault();
    root.classList.add('right-active');
  });
  if(toLogin) toLogin.addEventListener('click', function(e){
    e.preventDefault();
    root.classList.remove('right-active');
  });

  // Toggle password visibility for all password fields
  document.querySelectorAll('.toggle-pwd').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const target = document.querySelector(btn.getAttribute('data-target'));
      if(!target) return;
      if(target.type === 'password') { target.type = 'text'; btn.textContent = 'Ocultar'; }
      else { target.type = 'password'; btn.textContent = 'Mostrar'; }
    });
  });
});
