(function(){
    document.addEventListener('keyup', function(e){
        if (e.key === 'Tab') document.body.classList.add('show-focus');
    });
    const y = document.getElementById('year');
    if (y) y.textContent = new Date().getFullYear();
})();
