export class BootstrapModal {
    constructor(url, modalClass) {
        this.modal = document.createElement('div');
        this.modal.classList.add('modal');
        if (undefined !== modalClass) {
            this.modal.classList.add(modalClass);
        }
        this.modal.setAttribute('tabindex', '-1');
        this.modal.setAttribute('role', 'dialog');

        this.$modal = $(this.modal);
        this.$modal.modal({ show: false });
        this.$modal.on('hidden.bs.modal', () => {
            this.close();
        });

        this.render(url);
    }

    submit(event) {
        event.preventDefault();
        const form = event.currentTarget;

        fetch(form.getAttribute('action'), {
            method: form.getAttribute('method'),
            body: new FormData(form),
        }).then(response => {
            return response.json().then(data => ({
                json: data,
                status: response.status,
                response: response,
            }));
        }).then((data) => {
            if (200 === data.status) {
                this.close();
            } else if (301 === data.status) {
                window.location.href = data.json.redirect_url;
            } else if (400 === data.status) {
                this.renderBody(data.json.content);
            } else {
                this.fail(data.response);
            }
        }).catch((response) => {
            this.fail(response);
        });
    }

    done(data) {
        this.trigger('ui:modal:success', data);
        this.$el.trigger('ui:modal:success', data);
        this.close();
    }

    fail(response) {
        console.warn('Something failed.', response);
        const modalBody = this.modal.querySelector('.modal-body');
        const alert = document.createElement('div');
        alert.classList.add('alert');
        alert.classList.add('alert-danger');
        alert.innerHTML = 'Une erreur est survenue.';
        modalBody.prepend(alert);
    }

    clickLink(event) {
        event.preventDefault();
        const link = event.currentTarget;
        this.render(link.getAttribute('href'));
    }

    close() {
        this.$modal.modal('hide');

        let backdrop = document.querySelector('.modal-backdrop');
        if (null !== backdrop) {
            backdrop.parentNode.removeChild(backdrop);
        }

        document.querySelector('body').classList.remove('modal-open');

        this.modal.parentNode.removeChild(this.modal);
    }

    render(url) {
        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then((json) => {
                this.renderBody(json.content);
                this.$modal.modal('show');
            })
        ;
    }

    renderBody(content) {
        this.modal.innerHTML = content;

        let form = this.modal.querySelector('form');
        if (null !== form) {
            form.addEventListener('submit', (event) => this.submit(event));
        }

        this.modal.querySelectorAll('a:not([href="#"]):not([data-bootstrap=modal-change-location])').forEach((link) => {
            link.addEventListener('click', event => this.clickLink(event));
        });

        this.modal.dispatchEvent(new Event('modal:body:rendered'));
    }
}

document.querySelectorAll('[data-bootstrap=modal]').forEach((el) => {
    el.addEventListener('click', (event) => {
        event.preventDefault();
        new BootstrapModal(event.currentTarget.getAttribute('href'), event.currentTarget.dataset.modalClass);
    });
});
