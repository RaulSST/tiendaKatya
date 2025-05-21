<?php
session_start();
require 'conexionBD.php'; // Conexión a la base de datos

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $disponibilidad = $_POST['disponibilidad'] ?? '';
    $categoria_id = $_POST['categoria'] ?? '';

    // Validación de los campos
    if (empty($nombre) || empty($precio) || empty($disponibilidad) || empty($categoria_id)) {
        $_SESSION['mensaje_error'] = "Todos los campos son obligatorios.";
        header('Location: ../pages/pagesAdmin/admin.php');
        exit;
    }

    // Verifica si se cargó la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = $_FILES['imagen'];
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $imagen_extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($imagen_extension), $extensiones_permitidas)) {
            // Obtener el nombre de la categoría (no el ID) para la carpeta
            $query = $conn->prepare("SELECT nombre FROM categorias WHERE id = ?");
            $query->bind_param("i", $categoria_id);
            $query->execute();
            $resultado = $query->get_result();
            $categoria_data = $resultado->fetch_assoc();
            $query->close();

            if (!$categoria_data) {
                $_SESSION['mensaje_error'] = "Categoría no encontrada.";
                header('Location: ../pages/pagesAdmin/admin.php');
                exit;
            }

            $nombre_categoria = $categoria_data['nombre'];
            
            // --- CAMBIOS CLAVE AQUÍ ---

            // Ruta de la subcarpeta de categoría (ej. 'hogar/')
            $subcarpeta_categoria = strtolower(str_replace(' ', '_', $nombre_categoria)) . '/';

            // Ruta ABSOLUTA en el servidor donde se almacenarán los archivos
            // public_html/img/productos/
            $directorio_base_imagenes = realpath(__DIR__ . '/../img/productos/');

            // Verificar si el directorio base de imágenes existe
            if (!$directorio_base_imagenes || !is_dir($directorio_base_imagenes)) {
                 $_SESSION['mensaje_error'] = "Error: El directorio base de imágenes 'img/productos/' no existe o no es accesible.";
                 header('Location: ../pages/pagesAdmin/admin.php');
                 exit;
            }

            // Construir el directorio completo para la categoría
            $directorio_destino_real = $directorio_base_imagenes . '/' . $subcarpeta_categoria;

            // Asegurarse de que el directorio de la categoría exista
            if (!is_dir($directorio_destino_real)) {
                if (!mkdir($directorio_destino_real, 0777, true)) { // 0777 permisos, true para recursivo
                    $_SESSION['mensaje_error'] = "Error: No se pudo crear el directorio para la categoría de imágenes.";
                    header('Location: ../pages/pagesAdmin/admin.php');
                    exit;
                }
            }
            // --- FIN CAMBIOS CLAVE ---

            // Guardar con nombre único
            $nombre_imagen = uniqid() . '.' . $imagen_extension;
            $ruta_completa_destino_fisico = $directorio_destino_real . $nombre_imagen;

            if (move_uploaded_file($imagen['tmp_name'], $ruta_completa_destino_fisico)) {
                // Guardar SOLO la ruta relativa a img/productos/ en la base de datos
                $ruta_guardada_en_db = $subcarpeta_categoria . $nombre_imagen; // <-- ¡Esto es lo que pediste!

                // Insertar en la base de datos
                $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, disponibilidad, categoria_id, imagen) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sdiss", $nombre, $precio, $disponibilidad, $categoria_id, $ruta_guardada_en_db);

                if ($stmt->execute()) {
                    $_SESSION['mensaje'] = "Producto añadido con éxito.";
                    header('Location: ../pages/pagesAdmin/admin.php');
                    exit;
                } else {
                    $_SESSION['mensaje_error'] = "Error al insertar el producto en la base de datos: " . $stmt->error;
                    header('Location: ../pages/pagesAdmin/admin.php');
                    exit;
                }

                $stmt->close();
            } else {
                $_SESSION['mensaje_error'] = "Error al mover la imagen al directorio de destino.";
                header('Location: ../pages/pagesAdmin/admin.php');
                exit;
            }
        } else {
            $_SESSION['mensaje_error'] = "Solo se permiten imágenes JPG, JPEG, PNG y GIF.";
            header('Location: ../pages/pagesAdmin/admin.php');
            exit;
        }
    } else {
        $_SESSION['mensaje_error'] = "Error al subir la imagen o no se seleccionó ninguna imagen.";
        header('Location: ../pages/pagesAdmin/admin.php');
        exit;
    }
}
?>