document.addEventListener("DOMContentLoaded", function () {
    const botones = document.querySelectorAll(".contactar-vendedor");
    const notificationBadge = document.querySelector('.notification-btn span');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Mostrar u ocultar panel de notificaciones
    window.showNotifications = function () {
        const notificationsPanel = document.getElementById('notifications-panel');
        notificationsPanel.classList.toggle('hidden');

        if (!notificationsPanel.classList.contains('hidden')) {
            if (notificationBadge) {
                notificationBadge.textContent = '0';
                notificationBadge.classList.add('hidden');
            }
        }
    };

    // Cerrar panel al hacer clic fuera
    document.addEventListener('click', function (event) {
        const notificationsPanel = document.getElementById('notifications-panel');
        const notificationButton = document.querySelector('.notification-btn');

        if (notificationsPanel && notificationButton) {
            if (!notificationsPanel.contains(event.target) && !notificationButton.contains(event.target)) {
                notificationsPanel.classList.add('hidden');
            }
        }
    });

    // Delegación de eventos
    const container = document.getElementById("notifications-container");
    if (container) {
        container.addEventListener("click", function (event) {
            if (event.target.closest(".delete-notification-btn")) {
                event.stopPropagation();
                const button = event.target.closest(".delete-notification-btn");
                const notificationId = button.dataset.id;

                if (confirm("¿Estás seguro de que quieres eliminar esta notificación?")) {
                    eliminarNotificacion(notificationId);
                }
                return;
            }

            const notificationItem = event.target.closest(".notification-item");
            if (notificationItem) {
                const notificationId = notificationItem.dataset.id;
                window.location.href = `/notificaciones/${notificationId}`;
            }
        });
    }

    // Eliminar notificación
    function eliminarNotificacion(id) {
        const idsToDelete = Array.isArray(id) ? id : [id];

        fetch(`/notificaciones/eliminar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ ids: idsToDelete })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarToast("Notificación eliminada.");
                    actualizarNotificaciones();
                } else {
                    alert("Error al eliminar notificación: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error al eliminar notificación:", error);
                alert("Hubo un problema al eliminar la notificación.");
            });
    }

    // Actualizar notificaciones
    function actualizarNotificaciones() {
        fetch(`/notificaciones/listar`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById("notifications-container");
                const unreadCount = data.filter(noti => !noti.leido).length;

                container.innerHTML = "";

                if (data.length === 0) {
                    container.innerHTML = "<p class='text-gray-500 text-center p-4'>No hay notificaciones.</p>";
                } else {
                    data.forEach(noti => {
                        const div = document.createElement("div");
                        div.classList.add("notification-item", "mb-2", "p-2", "rounded", "relative", "flex", "flex-col", "gap-1", "transition-colors", "duration-200", "cursor-pointer");

                        if (!noti.leido) {
                            div.classList.add("bg-blue-100", "hover:bg-blue-200");
                        } else {
                            div.classList.add("bg-gray-100", "hover:bg-gray-200");
                        }

                        div.dataset.id = noti.id_notificacion;
                        div.innerHTML = `
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-semibold text-gray-800">${noti.mensaje}</p>
                                ${noti.imagen_url ? `<img src="${noti.imagen_url}" alt="Producto" class="w-12 h-12 object-cover rounded-sm border border-gray-200">` : ''}
                                <button class="delete-notification-btn text-red-500 hover:text-red-700 ml-2 p-1 rounded-full text-xs" data-id="${noti.id_notificacion}" title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            ${noti.emisor_nombre ? `<p class="text-xs text-gray-600">De: ${noti.emisor_nombre}</p>` : ''}
                            ${noti.nombre_producto ? `<p class="text-xs text-gray-600">Producto: ${noti.nombre_producto}</p>` : ''}
                            ${noti.fecha ? `<p class="text-xs text-gray-500 text-right">${new Date(noti.fecha).toLocaleString()}</p>` : ''}
                        `;
                        container.appendChild(div);
                    });
                }

                if (notificationBadge) {
                    if (unreadCount > 0) {
                        notificationBadge.textContent = unreadCount;
                        notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.textContent = '0';
                        notificationBadge.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error("Error al obtener notificaciones:", error);
                const container = document.getElementById("notifications-container");
                if (container) {
                    container.innerHTML = "<p class='text-red-500 text-center p-4'>Error al cargar notificaciones.</p>";
                }
            });
    }

    // Contactar vendedor
    botones.forEach(boton => {
        boton.addEventListener("click", function () {
            const idProducto = this.dataset.idProducto;
            const idVendedor = this.dataset.idVendedor;

            if (confirm("¿Estás seguro de que quieres contactar con el vendedor de este producto?")) {
                fetch(`/notificaciones/insertar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        id_producto: idProducto,
                        id_vendedor: idVendedor
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            actualizarNotificaciones();
                            mostrarToast("¡Notificación enviada al vendedor!");
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error en la solicitud:", error);
                        alert("Hubo un problema al contactar al vendedor.");
                    });
            } else {
                alert("Contacto cancelado.");
            }
        });
    });

    // Mostrar toast
    function mostrarToast(mensaje) {
        const toast = document.getElementById("toast-notificacion");
        if (toast) {
            toast.textContent = mensaje;
            toast.style.display = "block";
            setTimeout(() => {
                toast.style.display = "none";
            }, 3000);
        }
    }

    // Cargar notificaciones al inicio
    actualizarNotificaciones();
});
