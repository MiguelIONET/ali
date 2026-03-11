class MiNavbar extends HTMLElement {
    constructor() {
        super();
    }

    async connectedCallback() {
        try {
            // ACTUALIZADO: Ahora busca "navbar.html"
            const respuesta = await fetch('/components/navbar.html');
            const html = await respuesta.text();
            this.innerHTML = html;

            this.configurarCierreOffcanvas();
            this.marcarEnlaceActivo();

        } catch (error) {
            console.error('Error cargando el navbar:', error);
        }
    }

    configurarCierreOffcanvas() {
        const enlaces = this.querySelectorAll('.offcanvas-body .nav-link');
        const contenedorOffcanvas = this.querySelector('#menuLateral');

        if (!contenedorOffcanvas) return;

        enlaces.forEach(enlace => {
            enlace.addEventListener('click', () => {
                if (contenedorOffcanvas.classList.contains('show')) {
                    const instanciaOffcanvas = bootstrap.Offcanvas.getInstance(contenedorOffcanvas);
                    if (instanciaOffcanvas) {
                        instanciaOffcanvas.hide();
                    }
                }
            });
        });
    }

    marcarEnlaceActivo() {
        const rutaActual = window.location.pathname;
        const enlaces = this.querySelectorAll('.nav-link');

        enlaces.forEach(enlace => {
            const href = enlace.getAttribute('href');
            if (rutaActual === href || (rutaActual === '/' && href === '/index.html')) {
                enlace.classList.add('active');
                enlace.setAttribute('aria-current', 'page');
            } else {
                enlace.classList.remove('active');
                enlace.removeAttribute('aria-current');
            }
        });
    }
}

// Seguimos usando 'mi-navbar' como etiqueta porque requiere el guion obligatorio
customElements.define('mi-navbar', MiNavbar);