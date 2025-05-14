    $(document).ready(function() {
        // Маска для телефона
        $('#phone').mask('+7(000)000-00-00', {
            placeholder: '+7(XXX)XXX-XX-XX',
            clearIfNotMatch: true
        });
        
        // Валидация формы
        $('form').on('submit', function(e) {
            const phone = $('#phone').val();
            const phonePattern = /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/;
            
            if (!phonePattern.test(phone)) {
                alert('Пожалуйста, введите телефон в формате +7(XXX)XXX-XX-XX');
                e.preventDefault();
                return false;
            }
            return true;
        });
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