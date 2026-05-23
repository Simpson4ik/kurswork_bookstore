document.addEventListener('DOMContentLoaded', function() {

    // Патерн "Делегування подій" для додавання товарів до кошика (працює і для динамічного пошуку)
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-add-to-cart');
        if (button) {
            e.preventDefault();
            const bookId = button.getAttribute('data-book-id');
            const originalText = button.innerText;
            button.innerText = '⏳ Додавання...';
            button.disabled = true;

            fetch('/coursework/cart/add-ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.innerText = '✅ Додано!';
                        button.style.backgroundColor = 'var(--success)';
                        setTimeout(() => {
                            button.innerText = originalText;
                            button.style.backgroundColor = '';
                            button.disabled = false;
                        }, 2000);
                    } else {
                        alert('Помилка: ' + data.message);
                        button.innerText = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Помилка мережі:', error);
                    button.innerText = originalText;
                    button.disabled = false;
                });
        }
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

    function initTagDropdownSystem(inputId, containerListId, badgesContainerId, checkboxClass) {
        const input = document.getElementById(inputId);
        const listContainer = document.getElementById(containerListId);
        const badgesContainer = document.getElementById(badgesContainerId);
        if (!input || !listContainer || !badgesContainer) return;

        function updateBadges() {
            badgesContainer.innerHTML = '';
            const checkboxes = listContainer.querySelectorAll('.' + checkboxClass);
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const badge = document.createElement('div');
                    badge.className = 'tag-badge';
                    badge.innerHTML = cb.getAttribute('data-name') + ' <button type="button" class="btn-remove-tag" data-val="' + cb.value + '">✕</button>';
                    badgesContainer.appendChild(badge);
                }
            });
        }

        function filterList(filterValue) {
            const labels = listContainer.getElementsByTagName('label');
            let visibleCount = 0;
            for (let i = 0; i < labels.length; i++) {
                const text = labels[i].textContent || labels[i].innerText;
                if (text.toLowerCase().indexOf(filterValue) > -1 && visibleCount < 5) {
                    labels[i].style.display = '';
                    visibleCount++;
                } else {
                    labels[i].style.display = 'none';
                }
            }
        }

        input.addEventListener('focus', function() {
            listContainer.style.display = 'block';
            filterList(this.value.toLowerCase());
        });

        input.addEventListener('input', function() {
            listContainer.style.display = 'block';
            filterList(this.value.toLowerCase());
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !listContainer.contains(e.target)) {
                listContainer.style.display = 'none';
                input.value = '';
            }
        });

        listContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains(checkboxClass)) {
                updateBadges();
            }
        });

        badgesContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-tag')) {
                const val = e.target.getAttribute('data-val');
                const cb = listContainer.querySelector('input[value="' + val + '"]');
                if (cb) {
                    cb.checked = false;
                    updateBadges();
                }
            }
        });

        updateBadges();
    }

    initTagDropdownSystem('author_search', 'authors-checkbox-container', 'selected-authors-badges', 'author-item-checkbox');
    initTagDropdownSystem('genre_search', 'genres-checkbox-container', 'selected-genres-badges', 'genre-item-checkbox');
    initSingleDropdownSystem('publisher_search', 'publisher_id', 'publishers-list-container', 'publisher-item');

    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }
    const catalogSearch = document.getElementById('catalog-live-search');
    const minPriceInput = document.getElementById('filter-min-price');
    const maxPriceInput = document.getElementById('filter-max-price');
    const inStockCheckbox = document.getElementById('filter-in-stock');
    const genreCheckboxes = document.querySelectorAll('.filter-genre-checkbox');

    const booksGrid = document.querySelector('.books-grid');
    const pagination = document.querySelector('.pagination');

    function triggerFilters() {
        if (!booksGrid) return;

        const query = catalogSearch ? catalogSearch.value.trim() : '';
        const minPrice = minPriceInput ? minPriceInput.value.trim() : '';
        const maxPrice = maxPriceInput ? maxPriceInput.value.trim() : '';
        const inStock = inStockCheckbox ? inStockCheckbox.checked : false;

        const selectedGenres = [];
        const checkedBoxes = document.querySelectorAll('.filter-genre-checkbox:checked');
        checkedBoxes.forEach(box => selectedGenres.push(box.value));

        let url = `/coursework/books/search?q=${encodeURIComponent(query)}`;
        if (minPrice !== '') url += `&min_price=${encodeURIComponent(minPrice)}`;
        if (maxPrice !== '') url += `&max_price=${encodeURIComponent(maxPrice)}`;
        if (inStock) url += `&in_stock=true`;
        if (selectedGenres.length > 0) url += `&genres=${encodeURIComponent(selectedGenres.join(','))}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderBooksCatalog(data.books);

                    if (pagination) {
                        const isFilteringActive = query !== '' || minPrice !== '' || maxPrice !== '' || inStock || selectedGenres.length > 0;
                        pagination.style.display = isFilteringActive ? 'none' : '';
                    }
                }
            })
            .catch(error => console.error('Помилка фільтрації каталогу:', error));
    }

    // Навішуємо слухачі подій із захистом від брязкоту (Debounce) для текстових полів
    const debouncedFilter = debounce(triggerFilters, 350);

    if (catalogSearch) catalogSearch.addEventListener('input', debouncedFilter);
    if (minPriceInput) minPriceInput.addEventListener('input', debouncedFilter);
    if (maxPriceInput) maxPriceInput.addEventListener('input', debouncedFilter);

    if (inStockCheckbox) inStockCheckbox.addEventListener('change', triggerFilters);

    const genreContainerList = document.querySelector('.catalog-sidebar');
    if (genreContainerList) {
        genreContainerList.addEventListener('change', function(e) {
            if (e.target.classList.contains('filter-genre-checkbox')) {
                triggerFilters();
            }
        });
    }

    function renderBooksCatalog(books) {
        booksGrid.innerHTML = '';

        if (books.length === 0) {
            booksGrid.innerHTML = '<p style="color: var(--text-muted); grid-column: 1 / -1; text-align: center; font-style: italic; margin-top: 20px;">Книг за вказаними критеріями фільтрації не знайдено 📚</p>';
            return;
        }

        books.forEach(book => {
            const li = document.createElement('li');
            li.className = 'book-card';

            let coverHtml = '<div class="book-cover-placeholder">📚 Обкладинка відсутня</div>';
            if (book.cover_image) {
                coverHtml = `<img src="/coursework/public/uploads/${book.cover_image}" alt="${escapeHtml(book.title)}" class="book-cover">`;
            }

            li.innerHTML = `
                <div class="book-cover-container">
                    ${coverHtml}
                </div>
                <h3>
                    <a href="/coursework/book/${book.book_id}">
                        ${escapeHtml(book.title)}
                    </a>
                </h3>
                <p><strong>Автор(и):</strong> ${escapeHtml(book.authors_list || 'Не вказано')}</p>
                <p><strong>Жанр(и):</strong> ${escapeHtml(book.genres_list || 'Не вказано')}</p>
                <p>Рік видання: ${escapeHtml(book.publication_year)}</p>
                <p>Ціна: ${escapeHtml(book.price)} грн</p>
                <p>В наявності: ${escapeHtml(book.stock_quantity)} шт.</p>
                <button type="button" class="btn-add-to-cart" data-book-id="${book.book_id}">
                    Додати в кошик
                </button>
            `;
            booksGrid.appendChild(li);
        });
    }

    function escapeHtml(string) {
        if (!string) return '';
        return String(string)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});