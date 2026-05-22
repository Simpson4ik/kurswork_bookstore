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

    const removeButtons = document.querySelectorAll('.btn-remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!confirm('Видалити книгу?')) return;
            const bookId = this.getAttribute('data-book-id');
            const row = document.getElementById(`cart-row-${bookId}`);
            fetch('/coursework/cart/remove-ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            document.getElementById('grand-total').innerText = data.total_price;
                            if (data.cart_empty) {
                                document.getElementById('cart-container').innerHTML = '<p style="color: var(--text-muted);">Ваш кошик порожній.</p>';
                            }
                        }, 300);
                    }
                })
                .catch(error => console.error('Помилка мережі:', error));
        });
    });

    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const submitBtn = document.getElementById('btn-register');
    const registerForm = document.getElementById('register-form');

    if (emailInput && emailError && submitBtn) {
        let isEmailChecking = false;
        emailInput.addEventListener('blur', function() {
            const emailValue = this.value.trim();
            if (emailValue === '') {
                emailError.innerText = '';
                return;
            }
            isEmailChecking = true;
            submitBtn.disabled = true;
            fetch('/coursework/register/check-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: emailValue })
            })
                .then(response => response.json())
                .then(data => {
                    isEmailChecking = false;
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
                .catch(error => {
                    isEmailChecking = false;
                    console.error('Помилка мережі:', error);
                });
        });

        emailInput.addEventListener('input', function() {
            emailError.innerText = '';
            this.style.borderColor = '';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '';
            submitBtn.style.cursor = '';
        });

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                if (isEmailChecking) {
                    e.preventDefault();
                    alert('Будь ласка, зачекайте, триває асинхронна перевірка пошти.');
                    return;
                }
                if (emailError.innerText.includes('❌')) {
                    e.preventDefault();
                    alert('Реєстрація неможлива. Виправте помилку з Email.');
                }
            });
        }
    }



    const addBookForm = document.getElementById('add-book-form');
    if (addBookForm) {
        addBookForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/coursework/admin/book/store', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = '/coursework/admin/dashboard';
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error('Помилка мережі:', error));
        });
    }

    const editBookForm = document.getElementById('edit-book-form');
    if (editBookForm) {
        editBookForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const bookId = this.getAttribute('data-book-id');
            const formData = new FormData(this);
            fetch(`/coursework/admin/book/update/${bookId}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = '/coursework/admin/dashboard';
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error('Помилка мережі:', error));
        });
    }


    const authorSearchInput = document.getElementById('author_search');
    const authorContainer = document.getElementById('authors-checkbox-container');
    if (authorSearchInput && authorContainer) {
        authorSearchInput.addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const labels = authorContainer.getElementsByTagName('label');
            for (let i = 0; i < labels.length; i++) {
                const text = labels[i].textContent || labels[i].innerText;
                if (text.toLowerCase().indexOf(filterValue) > -1) {
                    labels[i].style.display = '';
                } else {
                    labels[i].style.display = 'none';
                }
            }
        });
    }

    const genreSearchInput = document.getElementById('genre_search');
    const genreContainer = document.getElementById('genres-checkbox-container');
    if (genreSearchInput && genreContainer) {
        genreSearchInput.addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const labels = genreContainer.getElementsByTagName('label');
            for (let i = 0; i < labels.length; i++) {
                const text = labels[i].textContent || labels[i].innerText;
                if (text.toLowerCase().indexOf(filterValue) > -1) {
                    labels[i].style.display = '';
                } else {
                    labels[i].style.display = 'none';
                }
            }
        });
    }

    const publisherSearchInput = document.getElementById('publisher_search');
    const publisherSelect = document.getElementById('publisher_id');
    if (publisherSearchInput && publisherSelect) {
        publisherSearchInput.addEventListener('input', function() {
            const filterValue = this.value.toLowerCase();
            const options = publisherSelect.options;
            let firstMatchFound = false;

            for (let i = 0; i < options.length; i++) {
                if (options[i].value === "") continue;
                const text = options[i].text;
                if (text.toLowerCase().indexOf(filterValue) > -1) {
                    options[i].style.display = '';
                    if (!firstMatchFound && filterValue !== '') {
                        options[i].selected = true;
                        firstMatchFound = true;
                    }
                } else {
                    options[i].style.display = 'none';
                }
            }
            if (filterValue === '') {
                options[0].selected = true;
            }
        });
    }
});