export const initAcTooltips = () => {
    document.querySelectorAll('[data-ac-tip]').forEach((element: HTMLElement) => {
        new Tooltip(element);
    });
}

export class Tooltip {

    element: HTMLElement
    content: string
    tip: HTMLElement;

    constructor(el: HTMLElement, content: string = '') {
        this.element = el;
        this.content = content ? content : el.dataset.acTip;
        this.tip = createTooltip(this.content);

        this.initEvents();
    }

    initEvents() {
        if (this.element.dataset.acTooltipInit === '1') {
            return;
        }

        this.element.dataset.acTooltipInit = '1';

        document.body.appendChild(this.tip);

        this.element.addEventListener('mouseenter', () => {
            const bodyOffset = document.body.getBoundingClientRect();
            const viewportOffset = this.element.getBoundingClientRect();

            this.tip.style.left = ((viewportOffset.left - bodyOffset.left) + this.element.offsetWidth / 2) + 'px';
            this.tip.style.top = ((viewportOffset.top - bodyOffset.top) + this.element.offsetHeight) + 'px';
            this.tip.classList.add('hover');
        });

        this.element.addEventListener('mouseleave', () => {
            this.tip.classList.remove('hover');
        });
    }

}


const createTooltip = (content: string) => {
    let tip = document.createElement('div');
    tip.classList.add('ac-tooltip');
    tip.innerHTML = content;

    return tip;
}