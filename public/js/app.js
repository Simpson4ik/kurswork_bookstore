document.addEventListener('DOMContentLoaded', function() {

    const basePath = window.BASE_PATH !== undefined ? window.BASE_PATH : '/coursework';

    function updateSelectColor(select) {
        const val = select.value;
        if (val === 'Нове') select.style.borderColor = '#38bdf8';
        else if (val === 'Підтверджено') select.style.borderColor = '#fbbf24';
        else if (val === 'Відправлено') select.style.borderColor = '#a78bfa';
        else if (val === 'Виконано') select.style.borderColor = '#10b981';
        else if (val === 'Скасовано') select.style.borderColor = '#ef4444';
    }

    document.querySelectorAll('.admin-status-select').forEach(updateSelectColor);

    document.addEventListener('click', function(e) {
        const addButton = e.target.closest('.btn-add-to-cart');
        if (addButton) {
            e.preventDefault();
            const bookId = addButton.getAttribute('data-book-id');
            const originalText = addButton.innerText;
            addButton.innerText = '⏳ Додавання...';
            addButton.disabled = true;

            fetch(`${basePath}/cart/add-ajax`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        addButton.innerText = ' Додано!';
                        addButton.style.backgroundColor = 'var(--success)';
                        setTimeout(() => {
                            addButton.innerText = originalText;
                            addButton.style.backgroundColor = '';
                            addButton.disabled = false;
                        }, 2000);
                    } else {
                        alert('Помилка: ' + data.message);
                        addButton.innerText = originalText;
                        addButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error(error);
                    addButton.innerText = originalText;
                    addButton.disabled = false;
                });
            return;
        }

        const removeButton = e.target.closest('.btn-remove-item');
        if (removeButton) {
            e.preventDefault();
            if (!confirm('Видалити книгу?')) return;
            const bookId = removeButton.getAttribute('data-book-id');
            const row = document.getElementById(`cart-row-${bookId}`);
            fetch(`${basePath}/cart/remove-ajax`, {
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
                .catch(error => console.error(error));
            return;
        }

        const deleteBookButton = e.target.closest('.btn-delete-book');
        if (deleteBookButton) {
            e.preventDefault();
            const bookId = deleteBookButton.getAttribute('data-book-id');
            const row = document.getElementById(`book-row-${bookId}`);
            const deleteUrl = deleteBookButton.getAttribute('href');

            fetch(deleteUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error(error));
            return;
        }

        const deleteAuthorButton = e.target.closest('.btn-delete-author');
        if (deleteAuthorButton) {
            e.preventDefault();
            const id = deleteAuthorButton.getAttribute('data-author-id');
            const row = document.getElementById(`author-row-${id}`);
            const url = deleteAuthorButton.getAttribute('href');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success && row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                })
                .catch(error => console.error(error));
            return;
        }

        const deleteGenreButton = e.target.closest('.btn-delete-genre');
        if (deleteGenreButton) {
            e.preventDefault();
            const id = deleteGenreButton.getAttribute('data-genre-id');
            const row = document.getElementById(`genre-row-${id}`);
            const url = deleteGenreButton.getAttribute('href');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success && row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                })
                .catch(error => console.error(error));
            return;
        }

        const deletePublisherButton = e.target.closest('.btn-delete-publisher');
        if (deletePublisherButton) {
            e.preventDefault();
            const id = deletePublisherButton.getAttribute('data-publisher-id');
            const row = document.getElementById(`publisher-row-${id}`);
            const url = deletePublisherButton.getAttribute('href');

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success && row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => row.remove(), 300);
                    }
                })
                .catch(error => console.error(error));
            return;
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('cart-qty-input')) {
            const input = e.target;
            const bookId = input.getAttribute('data-book-id');
            const newQty = parseInt(input.value);
            fetch(`${basePath}/cart/update-ajax`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ book_id: bookId, quantity: newQty })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        input.value = data.quantity;
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
                .catch(error => console.error(error));
            return;
        }

        if (e.target.classList.contains('admin-status-select')) {
            const select = e.target;
            const orderId = select.getAttribute('data-order-id');
            const newStatus = select.value;

            fetch(`${basePath}/admin/orders/update-status-ajax`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: newStatus })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateSelectColor(select);
                        alert(data.message);
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error(error));
        }
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
            fetch(`${basePath}/register/check-email`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: emailValue })
            })
                .then(response => response.json())
                .then(data => {
                    isEmailChecking = false;
                    if (data.success) {
                        if (data.exists) {
                            emailError.innerText = ' Цей Email вже зареєстрований в системі!';
                            emailError.style.color = 'var(--danger)';
                            this.style.borderColor = 'var(--danger)';
                            submitBtn.disabled = true;
                            submitBtn.style.opacity = '0.5';
                            submitBtn.style.cursor = 'not-allowed';
                        } else {
                            emailError.innerText = ' Цей Email вільний';
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
                    console.error(error);
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
            fetch(`${basePath}/admin/book/store`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = `${basePath}/admin/dashboard`;
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error(error));
        });
    }

    const editBookForm = document.getElementById('edit-book-form');
    if (editBookForm) {
        editBookForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const bookId = this.getAttribute('data-book-id');
            const formData = new FormData(this);
            fetch(`${basePath}/admin/book/update/${bookId}`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.href = `${basePath}/admin/dashboard`;
                    } else {
                        alert('Помилка: ' + data.message);
                    }
                })
                .catch(error => console.error(error));
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

    function initSingleDropdownSystem(inputId, hiddenId, containerListId, itemClass) {
        const input = document.getElementById(inputId);
        const hiddenInput = document.getElementById(hiddenId);
        const listContainer = document.getElementById(containerListId);
        if (!input || !hiddenInput || !listContainer) return;

        function filterList(filterValue) {
            const items = listContainer.getElementsByClassName(itemClass);
            let visibleCount = 0;
            for (let i = 0; i < items.length; i++) {
                const text = items[i].textContent || items[i].innerText;
                if (text.toLowerCase().indexOf(filterValue) > -1 && visibleCount < 5) {
                    items[i].style.display = 'block';
                    visibleCount++;
                } else {
                    items[i].style.display = 'none';
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

        listContainer.addEventListener('click', function(e) {
            const item = e.target.closest('.' + itemClass);
            if (item) {
                const id = item.getAttribute('data-id');
                const name = item.getAttribute('data-name');
                input.value = name;
                hiddenInput.value = id;
                listContainer.style.display = 'none';
            }
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !listContainer.contains(e.target)) {
                listContainer.style.display = 'none';
            }
        });
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

        let url = `${basePath}/books/search?q=${encodeURIComponent(query)}`;
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
            .catch(error => console.error(error));
    }

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
                coverHtml = `<img src="${basePath}/public/uploads/${book.cover_image}" alt="${escapeHtml(book.title)}" class="book-cover">`;
            }

            li.innerHTML = `
                <div class="book-cover-container">
                    ${coverHtml}
                </div>
                <h3>
                    <a href="${basePath}/book/${book.book_id}">
                        ${escapeHtml(book.title)}
                    </a>
                </h3>
                <p><strong>Автор(и):</strong> ${escapeHtml(book.authors_list || 'Не вказано')}</p>
                <p><strong>Жанр(и):</strong> ${escapeHtml(book.genres_list || 'Не вказано')}</p>
                <p>Рік видання: ${escapeHtml(book.publication_year)}</p>
                <p>Ціна: ${escapeHtml(book.price)} грн</p>
                <p>В наявності: ${escapeHtml(book.stock_quantity)} шт.</p>
                ${parseInt(book.stock_quantity) > 0
                ? `<button type="button" class="btn-add-to-cart" data-book-id="${book.book_id}">Додати в кошик</button>`
                : `<button type="button" disabled style="background-color: var(--border); color: var(--text-muted); cursor: not-allowed; box-shadow: none;">Немає в наявності</button>`
            }
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

    const profileForm = document.getElementById('profile-update-form');
    const profileStatus = document.getElementById('profile-status-message');
    const saveProfileBtn = document.getElementById('btn-save-profile');

    if (profileForm && profileStatus && saveProfileBtn) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const originalBtnText = saveProfileBtn.innerText;
            saveProfileBtn.innerText = ' Синхронізація...';
            saveProfileBtn.disabled = true;
            profileStatus.innerText = '';

            const payload = {
                first_name: document.getElementById('first_name').value.trim(),
                last_name: document.getElementById('last_name').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                email: document.getElementById('email').value.trim()
            };

            fetch(`${basePath}/profile/update-ajax`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(response => response.json())
                .then(data => {
                    saveProfileBtn.innerText = originalBtnText;
                    saveProfileBtn.disabled = false;

                    if (data.success) {
                        profileStatus.innerText = '✅ ' + data.message;
                        profileStatus.style.color = 'var(--success)';

                        const welcomeBadge = document.querySelector('.user-welcome');
                        if (welcomeBadge) {
                            welcomeBadge.innerText = 'Вітаємо, ' + payload.first_name + '!';
                        }
                    } else {
                        profileStatus.innerText = '❌ ' + data.message;
                        profileStatus.style.color = 'var(--danger)';
                    }
                })
                .catch(error => {
                    console.error(error);
                    saveProfileBtn.innerText = originalBtnText;
                    saveProfileBtn.disabled = false;
                    profileStatus.innerText = ' Сталася помилка мережевого з\'єднання.';
                    profileStatus.style.color = 'var(--danger)';
                });
        });
    }
});