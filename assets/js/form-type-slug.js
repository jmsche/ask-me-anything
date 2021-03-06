const slugify = require('slugify');
slugify.extend({
    "$": "",
    "%": "",
    "&": "",
    "<": "",
    ">": "",
    "|": "",
    "¢": "",
    "£": "",
    "¤": "",
    "¥": "",
    "₠": "",
    "₢": "",
    "₣": "",
    "₤": "",
    "₥": "",
    "₦": "",
    "₧": "",
    "₨": "",
    "₩": "",
    "₪": "",
    "₫": "",
    "€": "",
    "₭": "",
    "₮": "",
    "₯": "",
    "₰": "",
    "₱": "",
    "₲": "",
    "₳": "",
    "₴": "",
    "₵": "",
    "₸": "",
    "₹": "",
    "₽": "",
    "₿": "",
    "∂": "",
    "∆": "",
    "∑": "",
    "∞": "",
    "♥": "",
    "元": "",
    "円": "",
    "﷼": "",
});

class Slugger {
    constructor(field) {
        this.field = field;
        this.setTargetElement();
        this.locked = true;
        this.field.setAttribute('readonly', 'readonly');

        this.$modal = $(`#${this.field.dataset.modalId}`);
        this.$modal.modal({ show: false });

        if ('' === this.field.value) {
            this.currentSlug = '';
            this.updateValue();
            this.listenTarget();
        } else {
            this.currentSlug = this.field.value;
        }

        this.appendLockButton();
    }

    setTargetElement() {
        this.target = document.getElementById(this.field.dataset.target);
        if (null === this.target) {
            throw `Wrong target specified for slug widget ("${this.field.dataset.target}").`;
        }
    }

    /**
     * Append a "lock" button to control slug behaviour (auto or manual)
     */
    appendLockButton() {
        this.lockButton = this.field.parentNode.querySelector('button');
        this.lockButtonIcon = this.lockButton.querySelector('i');

        this.$modal.find('[data-accept=modal]').on('click', () => {
            this.unlock();
            this.$modal.modal('hide');
        });

        this.lockButton.addEventListener('click', () => {
            if (this.locked) {
                this.$modal.modal('show');
            } else {
                this.lock();
            }
        });
    }

    /**
     * Unlock the widget input (manual mode)
     */
    unlock() {
        this.locked = false;
        this.lockButtonIcon.classList.replace('fa-lock', 'fa-lock-open');
        this.field.removeAttribute('readonly');
    }

    /**
     * Lock the widget input (auto mode)
     */
    lock() {
        this.locked = true;
        this.lockButtonIcon.classList.replace('fa-lock-open', 'fa-lock');

        // Locking it back changes the value either to default value, or recomputes it
        if ('' !== this.currentSlug) {
            this.field.value = this.currentSlug;
        } else {
            this.updateValue();
        }

        this.field.setAttribute('readonly', 'readonly');
    }

    updateValue() {
        this.field.value = slugify(this.target.value, {
            remove: /[^A-Za-z0-9\s-]/g,
            lower: true,
            strict: true,
        });
    }

    /**
     * Observe the target field and slug it
     */
    listenTarget() {
        this.target.addEventListener('keyup', (data) => {
            if ('readonly' === this.field.getAttribute('readonly')) {
                this.updateValue();
            }
        });
    }
}

document.querySelectorAll('[data-slug-field]').forEach(function(field) {
    new Slugger(field);
});
