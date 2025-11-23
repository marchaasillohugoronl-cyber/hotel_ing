// JS helper for client cart: update counter and show simple toast
(function(){
    function el(id){return document.getElementById(id);} 

    window.updateCartCounter = async function(){
        try{
            const base = (window.URL_BASE !== undefined) ? window.URL_BASE : '/';
            const res = await fetch(base + 'cliente/cart_api.php?action=get');
            const j = await res.json();
            if (j.status === 'ok' && j.cart){
                const items = j.cart.items || [];
                let count = 0;
                items.forEach(it=> count += (parseInt(it.cantidad)||0));
                const e = el('cart-count'); if (e) e.textContent = count;
            }
        }catch(e){ /* fail silently */ }
    }

    // tiny toast
    window.showCartToast = function(msg, timeout=2200){
        let t = document.getElementById('cart-toast');
        if (!t){
            t = document.createElement('div'); t.id='cart-toast';
            t.style.position='fixed'; t.style.right='18px'; t.style.bottom='18px';
            t.style.background='rgba(0,112,192,0.95)'; t.style.color='#0f0f0fff'; t.style.padding='10px 14px';
            t.style.borderRadius='8px'; t.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'; t.style.zIndex=9999;
            document.body.appendChild(t);
        }
        t.textContent = msg; t.style.opacity = '1';
        if (t._tm) clearTimeout(t._tm);
        t._tm = setTimeout(()=>{ t.style.opacity='0'; }, timeout);
    }

    // onload update
    document.addEventListener('DOMContentLoaded', ()=>{ window.updateCartCounter(); });
})();
