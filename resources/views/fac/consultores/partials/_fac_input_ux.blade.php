{{-- Presentación progresiva de validación HTML5. No cambia la validación del servidor. --}}
@once
    @push('styles')
        <style>
            form[data-fac-ux-form] .form-control,
            form[data-fac-ux-form] .form-select { border-radius: .55rem; transition: border-color .15s ease, box-shadow .15s ease; }
            form[data-fac-ux-form] .form-control:focus,
            form[data-fac-ux-form] .form-select:focus { border-color: #a6192e; box-shadow: 0 0 0 .2rem rgba(166,25,46,.12); }
            form[data-fac-ux-form] .fac-ux-required-mark { color: #a6192e; font-weight: 700; margin-left: .2rem; }
            form[data-fac-ux-form] .fac-ux-client-error { display: block; color: #b42318; font-size: .875rem; margin-top: .25rem; }
            form[data-fac-ux-form] .form-control.is-invalid,
            form[data-fac-ux-form] .form-select.is-invalid { border-color: #dc3545; }
            .fac-ux-error-summary { border-left: 4px solid #dc3545; }
        </style>
    @endpush
    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[data-fac-ux-form]').forEach(function (form) {
                const fields = Array.from(form.querySelectorAll('input, select, textarea'))
                    .filter(el => !['hidden','submit','button','reset'].includes(el.type));

                function labelFor(el) {
                    if (el.id) {
                        const label = Array.from(form.querySelectorAll('label[for]'))
                            .find(node => node.htmlFor === el.id);
                        if (label) return label;
                    }
                    return el.closest('.col, [class*="col-"], .mb-3, .form-group')?.querySelector('label.form-label') || null;
                }

                function showValidation(el) {
                    if (el.disabled || !el.willValidate) return;
                    const bad = !el.validity.valid;
                    el.classList.toggle('is-invalid', bad);
                    el.setAttribute('aria-invalid', bad ? 'true' : 'false');
                    let msg = el.parentElement?.querySelector(':scope > .fac-ux-client-error');
                    if (bad) {
                        if (!msg) {
                            msg = document.createElement('div');
                            msg.className = 'fac-ux-client-error';
                            el.insertAdjacentElement('afterend', msg);
                        }
                        msg.textContent = el.validationMessage;
                    } else if (msg) {
                        msg.remove();
                    }
                }

                fields.forEach(function (el) {
                    if (el.required && el.type !== 'checkbox' && el.type !== 'radio') {
                        const label = labelFor(el);
                        if (label && !label.querySelector('.fac-ux-required-mark') && !label.textContent.includes('*')) {
                            const mark = document.createElement('span');
                            mark.className = 'fac-ux-required-mark';
                            mark.textContent = '*';
                            mark.setAttribute('aria-hidden', 'true');
                            label.appendChild(mark);
                        }
                    }
                    el.addEventListener('invalid', function () { showValidation(el); });
                    el.addEventListener('input', function () {
                        if (el.classList.contains('is-invalid')) showValidation(el);
                    });
                    el.addEventListener('change', function () {
                        if (el.classList.contains('is-invalid')) showValidation(el);
                    });
                });
                form.addEventListener('submit', function () {
                    fields.forEach(function (el) {
                        if (el.willValidate && !el.validity.valid) showValidation(el);
                    });
                });
            });
        });
        </script>
    @endpush
@endonce
