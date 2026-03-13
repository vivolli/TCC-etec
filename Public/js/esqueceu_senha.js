document.addEventListener('DOMContentLoaded', function(){
    const t = document.querySelector('.toast');
        const toasts = document.querySelectorAll('.toast');
        if(!toasts || toasts.length === 0) return;

        toasts.forEach(function(t){
            const close = t.querySelector('.close');
            if (close) {
                close.addEventListener('click', function(){
                    t.remove();
                });
            }

            setTimeout(()=>{
                t.classList.add('hide');
                setTimeout(()=>t.remove(),400);
            }, 4500);
        });
    setTimeout(()=>{
        t.classList.add('hide');
        setTimeout(()=>t.remove(),400);
    }, 4500);
});
