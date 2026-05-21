document.addEventListener('DOMContentLoaded', function() {

    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookId = this.getAttribute('data-book-id');
            const originalText = this.innerText;

            this.innerText = '⏳ Додавання...';
            this.disabled = true;

            fetch('/coursework/cart/add-ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.innerText = '✅ Додано!';
                        this.style.backgroundColor = 'var(--success)';

                        setTimeout(() => {
                            this.innerText = originalText;
                            this.style.backgroundColor = '';
                            this.disabled = false;
                        }, 2000);
                    } else {
                        alert('Помилка: ' + data.message);
                        this.innerText = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Помилка мережі:', error);
                    this.innerText = originalText;
                    this.disabled = false;
                });
        });
    });

    const qtyInputs = document.querySelectorAll('.cart-qty-input');

    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const bookId = this.getAttribute('data-book-id');
            const newQty = parseInt(this.value);

            fetch('/coursework/cart/update-ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId, quantity: newQty })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.value = data.quantity;

                        if (data.quantity === 0) {
                            const row = document.getElementById(`cart-row-${bookId}`);
                            if (row) row.remove();
                        } else {
                            document.getElementById(`subtotal-${bookId}`).innerText = data.subtotal;
                        }

                        document.getElementById('grand-total').innerText = data.total_price;

                        if (data.cart_empty) {
                            document.getElementById('cart-container').innerHTML = '<p style="color: var(--text-muted);">Ваш кошик порожній.</p>';
                        }
                    } else {
                        alert('Помилка оновлення: ' + data.message);
                    }
                })
                .catch(error => console.error('Помилка мережі:', error));
        });
    });

    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const submitBtn = document.getElementById('btn-register');

    if (emailInput && emailError && submitBtn) {
        // Подія 'blur' спрацьовує, коли користувач клікає за межі інпуту
        emailInput.addEventListener('blur', function() {
            const emailValue = this.value.trim();
            if (emailValue === '') {
                emailError.innerText = '';
                return;
            }

            fetch('/coursework/register/check-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: emailValue })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.exists) {
                            emailError.innerText = '❌ Цей Email вже зареєстрований в системі!';
                            emailError.style.color = 'var(--danger)';
                            this.style.borderColor = 'var(--danger)';

                            submitBtn.disabled = true;
                            submitBtn.style.opacity = '0.5';
                            submitBtn.style.cursor = 'not-allowed';
                        } else {
                            emailError.innerText = '✅ Цей Email вільний';
                            emailError.style.color = 'var(--success)';
                            this.style.borderColor = 'var(--success)';

                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '';
                            submitBtn.style.cursor = '';
                        }
                    }
                })
                .catch(error => console.error('Помилка мережі:', error));
        });

        emailInput.addEventListener('input', function() {
            emailError.innerText = '';
            this.style.borderColor = '';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '';
            submitBtn.style.cursor = '';
        });
    }
});