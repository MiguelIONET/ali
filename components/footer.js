class MiFooter extends HTMLElement {
    constructor() {
        super();
        // Sin attachShadow para que Bootstrap pueda darle estilo
    }

    async connectedCallback() {
        try {
            // Buscamos el HTML del footer usando ruta absoluta
            const respuesta = await fetch('/components/footer.html');
            const html = await respuesta.text();
            this.innerHTML = html;
        } catch (error) {
            console.error('Error cargando el footer:', error);
        }
    }
}

// Registramos el componente
customElements.define('mi-footer', MiFooter);