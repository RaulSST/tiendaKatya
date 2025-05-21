
        const comments = [];

        // Inicializar los comentarios con los datos del servidor
        if (window.initialComments) {
            window.initialComments.forEach(comment => {
                comments.push({
                    text: comment.texto,
                    date: new Date(comment.fecha_creacion),
                    id_comentario_db: comment.id,
                    usuario_id: comment.usuario_id,
                    usuario: comment.usuario
                });
            });
            renderComments();
        }

        // Agregar comentario
        function addComment() {
            const commentText = document.getElementById("new-comment").value.trim();
            if (commentText === "") return;

            const productId = getParameterByName('id');
            const usuarioId = window.loggedInUserId;

            if (usuarioId === null) {
                alert("Debes estar logueado para comentar.");
                return;
            }

            $.ajax({
                url: '../../php/guardarComentario.php',
                method: 'POST',
                dataType: 'json',
                data: { producto_id: productId, texto: commentText, usuario_id: usuarioId },
                success: function (response) {
                    console.log("Respuesta completa del servidor:", response);

                    if (response.success) {
                        comments.unshift({
                            text: commentText,
                            date: new Date(),
                            id_comentario_db: response.id_comentario,
                            usuario_id: response.usuario_id,
                            usuario: response.usuario
                        });
                        renderComments();
                        document.getElementById("new-comment").value = "";
                    } else {
                        console.error("Error al guardar el comentario:", response.message);
                        alert("Error al guardar el comentario.");
                    }
                },
                error: function () {
                    console.error("Error de conexión al servidor al guardar el comentario.");
                    alert("Error al comunicarse con el servidor.");
                }
            });
        }

        // Renderizar comentarios
        function renderComments() {
            const container = document.getElementById("comments-container");
            container.innerHTML = "";
            const loggedInUserId = window.loggedInUserId;
            console.log("Valor de loggedInUserId en renderComments:", loggedInUserId, typeof loggedInUserId);

            comments.forEach((comment, index) => {
                console.log("Comentario index:", index);
                console.log("  comment.usuario_id:", comment.usuario_id, typeof comment.usuario_id);
                const isAuthor = loggedInUserId !== null && Number(comment.usuario_id) === Number(loggedInUserId);
                console.log("  isAuthor:", isAuthor);
                const commentHTML = `
            <div class="review" data-index="${index}" data-comment-id="${comment.id_comentario_db || ''}" data-author-id="${comment.usuario_id}">
                <div class="user-info">
                    <i class="bi bi-person"></i>
                    <span class="name">${comment.usuario || 'Anónimo'}</span>
                </div>
                <div class="review-date">${new Intl.DateTimeFormat("es-ES", {
                    day: "2-digit",
                    month: "2-digit",
                    year: "numeric",
                }).format(comment.date)}</div>
                <div class="review-content">
                    <div class="review-text">${comment.text}</div>
                    ${isAuthor ? `
                        <button class="edit-comment" onclick="editComment(this)" ${comment.id_comentario_db ? '' : 'style="display:none;"'}>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="delete-comment" onclick="deleteComment(this)" ${comment.id_comentario_db ? '' : 'style="display:none;"'}>
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : ''}
                </div>
                <textarea class="edit-text" style="display:none;"></textarea>
                <div class="error-message" style="color: red; display: none; font-size: 0.875rem;">
                    El comentario no puede estar vacío
                </div>
                <button class="save-comment" style="display:none;" onclick="saveComment(this)">Guardar</button>
            </div>
        `;
                container.innerHTML += commentHTML;
            });
        }

        // Guardar comentario editado (con validación y verificación de usuario)
        function saveComment(button) {
            const review = button.closest(".review");
            const index = parseInt(review.getAttribute("data-index"));
            const comentarioIdDb = review.getAttribute("data-comment-id");
            const textarea = review.querySelector(".edit-text");
            const newText = textarea.value.trim();
            const errorMessage = review.querySelector(".error-message");
            const textElement = review.querySelector(".review-text");
            const editBtn = review.querySelector(".edit-comment");
            const deleteBtn = review.querySelector(".delete-comment");
            const usuarioId = window.loggedInUserId;

            if (newText === "") {
                errorMessage.style.display = "block";
                textarea.classList.add("input-error");
                return;
            }

            errorMessage.style.display = "none";
            textarea.classList.remove("input-error");

            if (comentarioIdDb) {
                $.ajax({
                    url: '../../php/editComentario.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { id_comentario: comentarioIdDb, texto: newText, usuario_id: usuarioId },
                    success: function (response) {
                        if (response.success) {
                            textElement.textContent = newText;
                            if (!isNaN(index)) {
                                comments[index].text = newText;
                            }
                        } else {
                            console.error("Error al editar el comentario:", response.message);
                            alert(response.message);
                        }
                    },
                    error: function () {
                        console.error("Error de conexión al servidor al editar el comentario.");
                        alert("Error al comunicarse con el servidor.");
                    }
                });
            } else {
                textElement.textContent = newText;
                if (!isNaN(index)) {
                    comments[index].text = newText;
                }
            }

            textElement.style.display = "block";
            textarea.style.display = "none";
            button.style.display = "none";
            editBtn.style.display = "inline-block";
            deleteBtn.style.display = "inline-block";
        }


        // Eliminar comentario
        function deleteComment(button) {
            const review = button.closest(".review");
            const index = parseInt(review.getAttribute("data-index"));
            const comentarioIdDb = review.getAttribute("data-comment-id");
            const usuarioId = window.loggedInUserId;

            if (usuarioId === null) {
                alert("Debes estar logueado para eliminar comentarios.");
                return;
            }

            if (comentarioIdDb) {
                $.ajax({
                    url: '../../php/eliminarComentario.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { id_comentario: comentarioIdDb, usuario_id: usuarioId },
                    success: function (response) {
                        if (response.success) {
                            comments.splice(index, 1);
                            renderComments();
                        } else {
                            console.error("Error al eliminar el comentario:", response.message);
                            alert(response.message);
                        }
                    },
                    error: function () {
                        console.error("Error de conexión al servidor al eliminar el comentario.");
                        alert("Error al comunicarse con el servidor.");
                    }
                });
            } else {
                comments.splice(index, 1);
                renderComments();
            }
        }

        function editComment(button) {
            const review = button.closest(".review");
            const comentarioIdDb = review.getAttribute("data-comment-id");
            const loggedInUserId = window.loggedInUserId;

            if (loggedInUserId === null) {
                alert("Debes estar logueado para editar comentarios.");
                return;
            }

            if (comentarioIdDb) {
                $.ajax({
                    url: '../../php/verificarComentario.php',
                    method: 'POST',
                    dataType: 'json',
                    data: { id_comentario: comentarioIdDb },
                    success: function (response) {
                        if (response.puede_editar) {
                            const textElement = review.querySelector(".review-text");
                            const textarea = review.querySelector(".edit-text");
                            const saveBtn = review.querySelector(".save-comment");
                            const deleteBtn = review.querySelector(".delete-comment");

                            textarea.style.display = "block";
                            saveBtn.style.display = "inline-block";
                            textarea.value = textElement.textContent;
                            textElement.style.display = "none";
                            button.style.display = "none";
                            deleteBtn.style.display = "none";
                        } else {
                            alert(response.message || "No tienes permiso para editar este comentario.");
                            console.warn("Usuario (ID:", loggedInUserId, ") intentó editar comentario (ID:", comentarioIdDb, ") sin permiso.");
                        }
                    },
                    error: function () {
                        console.error("Error al verificar el permiso de edición.");
                        alert("Error al comunicarse con el servidor.");
                    }
                });
            } else {
                alert("Este comentario no se puede editar.");
            }
        }


        // Ordenar comentarios
        function sortComments() {
            const sortBy = document.getElementById("sort-comments").value;
            comments.sort((a, b) => {
                return sortBy === "recientes" ? b.date - a.date : a.date - b.date;
            });
            renderComments();
        }