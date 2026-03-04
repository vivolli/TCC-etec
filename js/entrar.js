(function(){
    const params = new URLSearchParams(window.location.search);
    const redirect = params.get('redirect');
    if (redirect) {
        const form = document.querySelector('.login-form');
        if (form) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'redirect';
            input.value = redirect;
            form.appendChild(input);
        }
    }
})();

(function(){
    try {
        const path = window.location.pathname || '';
        let checkUrl = '/TCC-etec/php/login/alunos/checar_sessao.php';
        let redirectTo = '/TCC-etec/php/sou_aluno/index.php';
        if (path.indexOf('/php/login/adms/') !== -1) {
            checkUrl = '/TCC-etec/php/login/adms/checar_sessao.php';
            redirectTo = '/TCC-etec/php/secretaria/secretaria.php';
        }

        fetch(checkUrl, {cache: 'no-store'})
            .then(r => r.json())
            .then(data => {
                if (data && data.logado) {
                    window.location.href = redirectTo;
                }
            }).catch(()=>{});
    } catch (e) {}
})();

(function(){
    const params = new URLSearchParams(window.location.search);
    const flash = document.getElementById('flash');
    if (!flash) return;

    if (params.has('error')) {
        const msg = params.get('error');
        flash.style.display = 'block';
        flash.style.background = '#fff1f2';
        flash.style.borderLeft = '4px solid #dc2626';
        flash.style.color = '#7f1d1d';
        flash.textContent = decodeURIComponent(msg);
        history.replaceState(null, '', window.location.pathname);
    } else if (params.has('success')) {
        const msg = params.get('success');
        flash.style.display = 'block';
        flash.style.background = '#f0fdf4';
        flash.style.borderLeft = '4px solid #16a34a';
        flash.style.color = '#065f46';
        flash.textContent = decodeURIComponent(msg);
        history.replaceState(null, '', window.location.pathname);
    }
})();
