setTimeout(function(){ document.querySelectorAll('.alert').forEach(function(a){ a.style.opacity='0'; setTimeout(function(){a.remove();},400); }); }, 6000);
document.querySelectorAll('form[data-confirm]').forEach(function(f){
  f.addEventListener('submit', function(ev){ if(!confirm(f.dataset.confirm)) ev.preventDefault(); });
});
