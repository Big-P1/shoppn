(function () {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9+\-\s]{7,15}$/;
    const priceRegex = /^\d{1,8}(\.\d{1,2})?$/;
    const allowedImages = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxImageBytes = 2 * 1024 * 1024;

    const passwordRules = {
        length: function (p) { return p.length >= 8; },
        uppercase: function (p) { return /[A-Z]/.test(p); },
        lowercase: function (p) { return /[a-z]/.test(p); },
        number: function (p) { return /[0-9]/.test(p); },
        special: function (p) { return /[^A-Za-z0-9]/.test(p); }
    };

    function showError(form, name, message) {
        const box = form.querySelector('[data-error-for="' + name + '"]');
        if (!box) { return; }
        box.textContent = message;
        const field = box.closest('.field');
        if (field) { field.classList.add('invalid'); }
    }

    function clearError(form, name) {
        const box = form.querySelector('[data-error-for="' + name + '"]');
        if (!box) { return; }
        box.textContent = '';
        const field = box.closest('.field');
        if (field) { field.classList.remove('invalid'); }
    }

    function clearAll(form) {
        form.querySelectorAll('[data-error-for]').forEach(function (box) {
            box.textContent = '';
            const field = box.closest('.field');
            if (field) { field.classList.remove('invalid'); }
        });
    }

    function value(form, name) {
        const el = form.elements[name];
        return el ? el.value : '';
    }

    function setLoading(form) {
        const button = form.querySelector('button[type="submit"]');
        if (!button) { return; }
        button.dataset.label = button.textContent;
        button.textContent = button.dataset.loading || 'Please wait...';
        button.disabled = true;
    }

    function resetLoading(form) {
        const button = form.querySelector('button[type="submit"]');
        if (!button || !button.dataset.label) { return; }
        button.textContent = button.dataset.label;
        button.disabled = false;
    }

    function checkImage(form, name, required) {
        const input = form.elements[name];
        if (!input) { return true; }
        const file = input.files && input.files[0];
        if (!file) {
            if (required) {
                showError(form, name, 'Please choose an image.');
                return false;
            }
            return true;
        }
        if (allowedImages.indexOf(file.type) === -1) {
            showError(form, name, 'Only JPG, PNG, GIF or WEBP images are allowed.');
            return false;
        }
        if (file.size > maxImageBytes) {
            showError(form, name, 'Image must be 2MB or smaller.');
            return false;
        }
        return true;
    }

    function updatePasswordRules(form) {
        const password = value(form, 'password');
        Object.keys(passwordRules).forEach(function (rule) {
            const item = form.querySelector('[data-rule="' + rule + '"]');
            if (!item) { return; }
            const passed = passwordRules[rule](password);
            const text = item.textContent.slice(2);
            item.textContent = (passed ? '✓ ' : '✕ ') + text;
            item.classList.toggle('ok', passed);
            item.classList.toggle('bad', !passed);
        });
    }

    function passwordStrong(password) {
        return Object.keys(passwordRules).every(function (rule) {
            return passwordRules[rule](password);
        });
    }

    function validateRegister(form) {
        let valid = true;

        const name = value(form, 'name').trim();
        if (name.length < 2) { showError(form, 'name', 'Please enter your full name.'); valid = false; }
        else if (name.length > 100) { showError(form, 'name', 'Name must be 100 characters or fewer.'); valid = false; }

        const email = value(form, 'email').trim();
        if (!emailRegex.test(email)) { showError(form, 'email', 'Please enter a valid email address.'); valid = false; }
        else if (email.length > 50) { showError(form, 'email', 'Email must be 50 characters or fewer.'); valid = false; }

        if (!passwordStrong(value(form, 'password'))) {
            showError(form, 'password', 'Password does not meet all the requirements.');
            valid = false;
        }

        if (value(form, 'country') === '') { showError(form, 'country', 'Please select your country.'); valid = false; }

        const city = value(form, 'city').trim();
        if (city.length < 2) { showError(form, 'city', 'Please enter your city.'); valid = false; }
        else if (city.length > 30) { showError(form, 'city', 'City must be 30 characters or fewer.'); valid = false; }

        if (!phoneRegex.test(value(form, 'contact').trim())) {
            showError(form, 'contact', 'Enter 7 to 15 digits. +, - and spaces are allowed.');
            valid = false;
        }

        if (!checkImage(form, 'customer_image', false)) { valid = false; }

        return valid;
    }

    function validateLogin(form) {
        let valid = true;

        if (!emailRegex.test(value(form, 'email').trim())) {
            showError(form, 'email', 'Please enter a valid email address.');
            valid = false;
        }

        if (value(form, 'password') === '') {
            showError(form, 'password', 'Please enter your password.');
            valid = false;
        }

        return valid;
    }

    function validateNameForm(form) {
        const field = form.dataset.nameField;
        const label = form.dataset.nameLabel || 'Name';
        const text = value(form, field).trim();

        if (text.length < 2) { showError(form, field, label + ' must be at least 2 characters.'); return false; }
        if (text.length > 100) { showError(form, field, label + ' must be 100 characters or fewer.'); return false; }

        return true;
    }

    function validateProduct(form) {
        let valid = true;

        if (value(form, 'cat_id') === '') { showError(form, 'cat_id', 'Please select a category.'); valid = false; }
        if (value(form, 'brand_id') === '') { showError(form, 'brand_id', 'Please select a brand.'); valid = false; }

        const title = value(form, 'title').trim();
        if (title.length < 2) { showError(form, 'title', 'Please enter a product title.'); valid = false; }
        else if (title.length > 200) { showError(form, 'title', 'Title must be 200 characters or fewer.'); valid = false; }

        const price = value(form, 'price').trim();
        if (!priceRegex.test(price) || parseFloat(price) <= 0) {
            showError(form, 'price', 'Enter a price greater than 0 (up to 2 decimal places).');
            valid = false;
        }

        if (value(form, 'desc').trim().length > 500) { showError(form, 'desc', 'Description must be 500 characters or fewer.'); valid = false; }
        if (value(form, 'keywords').trim().length > 100) { showError(form, 'keywords', 'Keywords must be 100 characters or fewer.'); valid = false; }

        if (!checkImage(form, 'product_image', form.dataset.imageRequired === '1')) { valid = false; }

        return valid;
    }

    const validators = {
        'register-form': validateRegister,
        'login-form': validateLogin,
        'product-form': validateProduct
    };

    function attach(form, validator) {
        form.setAttribute('novalidate', 'novalidate');

        form.addEventListener('submit', function (event) {
            clearAll(form);
            if (!validator(form)) {
                event.preventDefault();
                const firstInvalid = form.querySelector('.field.invalid input, .field.invalid select, .field.invalid textarea');
                if (firstInvalid) { firstInvalid.focus(); }
                return;
            }
            setLoading(form);
        });

        form.addEventListener('input', function (event) {
            if (event.target.name) { clearError(form, event.target.name); }
        });

        form.addEventListener('change', function (event) {
            if (event.target.name) { clearError(form, event.target.name); }
        });
    }

    const qtyRegex = /^[1-9][0-9]?$/;

    function showCartError(message) {
        const box = document.getElementById('cart-error');
        if (!box) { return; }
        box.textContent = message;
        box.hidden = false;
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function clearCartError() {
        const box = document.getElementById('cart-error');
        if (box) {
            box.textContent = '';
            box.hidden = true;
        }
        document.querySelectorAll('.qty-input.invalid').forEach(function (input) {
            input.classList.remove('invalid');
        });
    }

    function attachCart() {
        const updateForm = document.getElementById('update-form');
        const removeForm = document.getElementById('remove-form');

        if (updateForm) {
            updateForm.addEventListener('submit', function (event) {
                clearCartError();
                let firstBad = null;

                document.querySelectorAll('input[form="update-form"][name="qty[]"]').forEach(function (input) {
                    input.value = input.value.trim();
                    if (!qtyRegex.test(input.value)) {
                        input.classList.add('invalid');
                        if (!firstBad) { firstBad = input; }
                    }
                });

                if (firstBad) {
                    event.preventDefault();
                    showCartError('Quantity must be a whole number between 1 and 99.');
                    firstBad.focus();
                }
            });
        }

        if (removeForm) {
            removeForm.addEventListener('submit', function (event) {
                clearCartError();
                const checked = document.querySelectorAll('input[form="remove-form"][name="remove[]"]:checked');

                if (checked.length === 0) {
                    event.preventDefault();
                    showCartError('Tick at least one item to remove.');
                }
            });
        }

        document.querySelectorAll('.qty-input').forEach(function (input) {
            input.addEventListener('input', function () {
                input.classList.remove('invalid');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        attachCart();

        Object.keys(validators).forEach(function (id) {
            const form = document.getElementById(id);
            if (form) { attach(form, validators[id]); }
        });

        document.querySelectorAll('form[data-name-field]').forEach(function (form) {
            attach(form, validateNameForm);
        });

        const register = document.getElementById('register-form');
        if (register) {
            register.elements['password'].addEventListener('input', function () {
                updatePasswordRules(register);
            });
            updatePasswordRules(register);
        }

        const productForm = document.getElementById('product-form');
        if (productForm && productForm.elements['product_image']) {
            productForm.elements['product_image'].addEventListener('change', function (event) {
                const preview = document.getElementById('image-preview');
                const file = event.target.files && event.target.files[0];
                if (preview && file && allowedImages.indexOf(file.type) !== -1) {
                    preview.src = URL.createObjectURL(file);
                    preview.hidden = false;
                }
            });
        }
    });

    window.addEventListener('pageshow', function () {
        document.querySelectorAll('form').forEach(resetLoading);
    });
})();
