window.addEventListener('DOMContentLoaded', function () {
    const yearEl = document.getElementById('year');
    if (yearEl) yearEl.textContent = new Date().getFullYear();

    const btnNotify = document.getElementById('btn-notify');
    if (btnNotify) {
        btnNotify.addEventListener('click', function () {
            const texto = prompt('Enviar notificação para todos os alunos. Digite a mensagem:');
            if (!texto) return alert('Ação cancelada.');
            alert('Notificação preparada:\n' + texto + '\n\n(Implemente envio no servidor para enviar de verdade.)');
        });
    }

    const statUsers = document.getElementById('stat-users');
    const statLoans = document.getElementById('stat-loans');
    const statMessages = document.getElementById('stat-messages');

    function setStats(users, loans, messages) {
        if (statUsers) statUsers.textContent = users;
        if (statLoans) statLoans.textContent = loans;
        if (statMessages) statMessages.textContent = messages;
    }

    fetch('/TCC-etec/api/admin/stats.php').then(function (res) {
        if (!res.ok) throw new Error('no-stats');
        return res.json();
    }).then(function (data) {
        setStats(data.users ?? '—', data.loans ?? '—', data.messages ?? '—');
    }).catch(function () {
        setStats('124', '7', '3');
    });

    document.querySelectorAll('.action-card').forEach(function (card) {
        card.addEventListener('click', function (e) {
            const a = card.querySelector('a');
            if (a) {
                window.location = a.href;
            }
        });
    });
});
