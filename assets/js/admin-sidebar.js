document.addEventListener('DOMContentLoaded', function(){
  const toggle = document.getElementById('sidebar-toggle');
  const admin = document.querySelector('.admin-container');
  if (!toggle || !admin) return;

  // Toggle sidebar visibility
  toggle.addEventListener('click', function(e){
    e.stopPropagation();
    admin.classList.toggle('sidebar-open');
  });

  // Close when clicking outside the sidebar
  document.addEventListener('click', function(e){
    if (!admin.classList.contains('sidebar-open')) return;
    const sidebar = admin.querySelector('.sidebar');
    if (!sidebar.contains(e.target) && e.target !== toggle) {
      admin.classList.remove('sidebar-open');
    }
  });

  // Close on ESC
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') admin.classList.remove('sidebar-open');
  });
});
