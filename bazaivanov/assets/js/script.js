document.getElementById('phone').addEventListener('input', function (e) {
    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
    e.target.value = !x[2] ? x[1] : x[1] + '(' + x[2] + ')' + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
});

document.addEventListener('DOMContentLoaded', function() {
    // Улучшение работы форм на мобильных устройствах
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            window.scrollTo({
                top: this.offsetTop - 100,
                behavior: 'smooth'
            });
        });
    });
    
    // Подтверждение важных действий
    const dangerousActions = document.querySelectorAll('a.btn-danger, form[action*="delete"]');
    dangerousActions.forEach(action => {
        action.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                e.preventDefault();
            }
        });
    });
});