document.addEventListener('DOMContentLoaded', () => {

    const notificationListContent = document.getElementById('notificationListContent');
    const detailContent = document.getElementById('detailContent');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const deleteAllBtn = document.getElementById('deleteAllBtn');

    /* -------------------------------------------------------------
     * RUTAS LARAVEL
     * -------------------------------------------------------------*/
    const RUTAS = {
        marcarLeido: "/notificaciones/marcar-leido",
        eliminarVarias: "/notificaciones/eliminar-varias",
        eliminarTodas: "/notificaciones/eliminar-todas",
        enviarRespuesta: "/notificaciones/enviar-respuesta-rapida"
    };

    /* -------------------------------------------------------------
     * Función de AJAX con fetch
     * -------------------------------------------------------------*/
    async function sendRequest(url, data = {}, method = "POST") {
        try {
            const response = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: method === "GET" ? null : JSON.stringify(data)
            });

            const result = await response.json();
            return result;

        } catch (error) {
            console.error("Error AJAX:", error);
            return { success: false, message: "Error inesperado" };
        }
    }

    /* -------------------------------------------------------------
     * Adaptación TOTAL de abrir detalle de notificación
     * -------------------------------------------------------------*/
    async function openNotificationDetail(notificationId) {
        const target = document.querySelector(`.notification-item[data-id="${notificationId}"]`);

        if (!target) return;

        document.querySelectorAll('.notification-item.active')
            .forEach(el => el.classList.remove('active'));

        target.classList.add('active');

        const {
            id, mensaje, fecha, productoNombre, productoDescripcion,
            productoImagen, productoPrecio, productoId,
            idUsuarioEmisor, emisorNombre, emisorCorreo, emisorTelefono,
            tipoNotificacion
        } = target.dataset;

        const isRespuesta = tipoNotificacion === "respuesta";

        detailContent.classList.remove('no-selection');
        detailContent.innerHTML = `
            <h3>${productoNombre}</h3>
            <span class="detail-date">${new Date(fecha).toLocaleString()}</span>

            <div class="product-info-section">
                ${productoImagen ? `<img src="${productoImagen}" class="product-image">` : ""}
                <div class="product-details-text">
                    <p><strong>Precio:</strong> ${productoPrecio}</p>
                    <p><strong>Descripción:</strong> ${productoDescripcion}</p>
                    <p><a href="/productos/detalle/${productoId}" target="_blank">Ver Producto</a></p>
                </div>
            </div>

            <div class="message-section">
                <p><strong>${isRespuesta ? "Respuesta del vendedor:" : "Mensaje del comprador:"}</strong> ${mensaje}</p>
            </div>

            <hr>

            <div class="interested-party-info">
                <h4>${isRespuesta ? "Vendedor:" : "Comprador:"}</h4>
                <p><strong>Nombre:</strong> ${emisorNombre}</p>
                <p><strong>Email:</strong> <a href="mailto:${emisorCorreo}">${emisorCorreo}</a></p>
                <p><strong>Teléfono:</strong> <a href="tel:${emisorTelefono}">${emisorTelefono}</a></p>
            </div>

            ${!isRespuesta ? renderQuickReply(idUsuarioEmisor, productoId, emisorCorreo, emisorNombre, emisorTelefono, productoNombre) : ""}
        `;

        // Enviar respuesta rápida si aplica
        addQuickReplyEvents();

        // Marcar como leído
        if (target.classList.contains("unread")) {
            const r = await sendRequest(RUTAS.marcarLeido, { ids: [id] });
            if (r.success) target.classList.remove("unread");
        }
    }

    function renderQuickReply(idComprador, idProducto, correo, nombre, telefono, productoNombre) {
        return `
            <hr>
            <h4>Responder rápido:</h4>
            <select id="msgSelect" class="form-control mb-2">
                <option value="">Selecciona un mensaje...</option>
                <option value="Gracias por comunicarte, el producto sigue disponible.">Gracias por comunicarte...</option>
                <option value="Claro, puedo darte más información del producto.">Puedo darte más información...</option>
                <option value="Puedes escribirme al WhatsApp para más detalles.">Escríbeme al WhatsApp...</option>
            </select>

            <button id="btnEnviarRespuesta" class="btn btn-primary" disabled>Enviar</button>

            <input type="hidden" id="respIdComprador" value="${idComprador}">
            <input type="hidden" id="respIdProducto" value="${idProducto}">
            <input type="hidden" id="respCorreo" value="${correo}">
            <input type="hidden" id="respNombre" value="${nombre}">
            <input type="hidden" id="respTelefono" value="${telefono}">
            <input type="hidden" id="respNombreProducto" value="${productoNombre}">
        `;
    }

    function addQuickReplyEvents() {
        const select = document.getElementById("msgSelect");
        const btn = document.getElementById("btnEnviarRespuesta");

        if (!select || !btn) return;

        select.addEventListener("change", () => {
            btn.disabled = !select.value;
        });

        btn.addEventListener("click", async () => {
            const data = {
                destinatarioEmail: document.getElementById("respCorreo").value,
                destinatarioNombre: document.getElementById("respNombre").value,
                destinatarioId: document.getElementById("respIdComprador").value,
                destinatarioTelefono: document.getElementById("respTelefono").value,
                mensaje: select.value,
                nombreProducto: document.getElementById("respNombreProducto").value,
                idProducto: document.getElementById("respIdProducto").value
            };

            const r = await sendRequest(RUTAS.enviarRespuesta, data);

            if (r.success) {
                alert("Mensaje enviado correctamente.");
                select.value = "";
                btn.disabled = true;
            } else {
                alert("Error: " + r.message);
            }
        });
    }

    /* -------------------------------------------------------------
     * LISTENERS PARA ACCIONES DE LISTA
     * -------------------------------------------------------------*/
    notificationListContent.addEventListener('click', async (event) => {

        // Eliminar una sola
        if (event.target.classList.contains('delete-single-btn')) {
            const id = event.target.dataset.id;

            if (confirm("¿Eliminar esta notificación?")) {
                const r = await sendRequest(RUTAS.eliminarVarias, { ids: [id] });

                if (r.success) {
                    document.querySelector(`.notification-item[data-id="${id}"]`).remove();
                }
            }
            return;
        }

        // Abrir detalle
        const item = event.target.closest('.notification-item');
        if (item && !event.target.classList.contains("notification-checkbox")) {
            openNotificationDetail(item.dataset.id);
        }
    });

    /* -------------------------------------------------------------
     * ELIMINAR SELECCIONADAS
     * -------------------------------------------------------------*/
    deleteSelectedBtn.addEventListener('click', async () => {
        const ids = [...document.querySelectorAll('.notification-checkbox:checked')]
            .map(cb => cb.dataset.id);

        if (ids.length === 0) return alert("No has seleccionado ninguna.");

        if (confirm(`¿Eliminar ${ids.length} notificaciones?`)) {
            const r = await sendRequest(RUTAS.eliminarVarias, { ids });
            if (r.success) {
                ids.forEach(id => {
                    document.querySelector(`.notification-item[data-id="${id}"]`).remove();
                });
            }
        }
    });

    /* -------------------------------------------------------------
     * ELIMINAR TODAS
     * -------------------------------------------------------------*/
    deleteAllBtn.addEventListener('click', async () => {
        if (confirm("¿Eliminar TODAS las notificaciones?")) {
            const r = await sendRequest(RUTAS.eliminarTodas);

            if (r.success) {
                notificationListContent.innerHTML = `<p>No tienes notificaciones.</p>`;
            }
        }
    });

});
