export default class Modal {
    el: HTMLElement
    dialog: HTMLElement

    constructor(el: HTMLElement) {
        if (!el) {
            return;
        }
        this.el = el;
        this.dialog = el.querySelector('.ac-modal__dialog');

        this.initEvents();
    }

    initEvents() {
        let self = this;

        document.addEventListener('keydown', (e: KeyboardEvent) => {
            const keyName = e.key;

            if (!this.isOpen()) {
                return;
            }

            if ('Escape' === keyName) {
                this.close();
            }
        });

        let dismissButtons = this.el.querySelectorAll('[data-dismiss="modal"], .ac-modal__dialog__close');
        if (dismissButtons.length > 0) {
            dismissButtons.forEach((b) => {
                b.addEventListener('click', (e) => {
                    e.preventDefault();
                    self.close();
                });
            });
        }

        this.el.addEventListener('click', (e: MouseEvent) => {
            if ((e.target as HTMLElement).classList.contains('ac-modal')) {
                self.close();
            }
        });
    }

    isOpen() {
        return this.el.classList.contains('-active');
    }

    close() {
        this.onClose();
        this.el.classList.remove('-active');
    }

    open() {
        //short delay in order to allow bubbling events to bind before opening
        setTimeout(() => {
            this.onOpen();
            this.el.removeAttribute('style');
            this.el.classList.add('-active');
        });
    }

    destroy() {
        this.el.remove();
    }

    onClose() {
    }

    onOpen() {
    }

}