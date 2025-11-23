// JS for client dashboard: sidebar toggle and keep cart counter in sync
(function(){
    function qs(s){return document.querySelector(s)}
    const root = qs('.client-dashboard');
    const toggleKey = 'hostal_sidebar_collapsed';

    function applyState(){
        const collapsed = localStorage.getItem(toggleKey) === '1';
        if (!root) return;
        if (collapsed) root.classList.add('collapsed'); else root.classList.remove('collapsed');
    }

    function addToggleButton(){
        const sidebar = qs('.client-sidebar');
        if(!sidebar) return;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'sidebar-toggle';
        btn.setAttribute('aria-label','Toggle sidebar');
        btn.style.cssText = 'position:absolute;right:-18px;top:12px;background:#004080;color:#fff;border-radius:50%;width:36px;height:36px;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.12)';
        btn.textContent = '☰';
        btn.addEventListener('click', ()=>{
            const cur = localStorage.getItem(toggleKey) === '1';
            localStorage.setItem(toggleKey, cur? '0':'1');
            applyState();
        });
        sidebar.style.position='relative';
        sidebar.appendChild(btn);
    }

    document.addEventListener('DOMContentLoaded', ()=>{
        applyState();
        addToggleButton();
        if (window.updateCartCounter) window.updateCartCounter();
    });
})();
