<?php
session_start();
$mensaje = '';
$redireccionar = false; // Nueva variable para controlar la redirección

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nueva-contrasena'])) {
    $nuevaContrasena = $_POST['nueva-contrasena'];
    $confirmarContrasena = $_POST['confirmar-contrasena'];

    if ($nuevaContrasena === $confirmarContrasena) {
        // Aquí debes actualizar la contraseña en la base de datos
        $correo = $_SESSION['correo_enviado'];
        $hashContrasena = password_hash($nuevaContrasena, PASSWORD_BCRYPT);

        $host = 'localhost';
        $dbname = 'u349915096_tiendaKatya';
        $user = 'u349915096_raul';
        $pass = 'Soysebastian100';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE email = :email");
            $stmt->bindParam(':contrasena', $hashContrasena);
            $stmt->bindParam(':email', $correo);
            $stmt->execute();

            // En lugar de asignar un mensaje, marcamos para redireccionar
            $redireccionar = true;
        } catch (PDOException $e) {
            $mensaje = "❌ Error al actualizar la contraseña: " . $e->getMessage();
        }
    } else {
        $mensaje = "❌ Las contraseñas no coinciden.";
    }
}

// Realizar la redirección si la variable $redireccionar es true
if ($redireccionar) {
    header("Location: ../pages/pagesAutentication/login.php");
    exit(); // Asegúrate de detener la ejecución del script después de la redirección
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
        }

        .center-box {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 500px;
            margin: 0 auto;
            margin-top: 200px;
        }

        .password-input-container {
            position: relative;
            width: 270px;
            margin: 0 auto;
        }

        input[type="password"],
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 250px;
            height: 13px;
            margin-top: 5px;
            margin-bottom: 5px; /* Reducido el margen inferior para el feedback */
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-feedback {
            font-size: 0.9em;
            margin-top: 0;
            margin-bottom: 10px;
        }

        button {
            width: 200px;
            padding: 10px;
            background-color: #ba3b81;
            color: white;
            border: 1px solid #ba3b81;
            border-radius: 4px;
            margin-top: 15px !important;
            font-weight: 500;
            transition: 0.5s;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: white;
            color: #ba3b81;
            border: 1px solid #ba3b81;
        }

        .msg {
            margin-top: 15px;
            font-weight: bold;
        }

        h2 {
            margin-top: 0px;
            margin-bottom: 3px;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: 500;
            font-size: 19px;
            color: black;
        }
    </style>
</head>

<body>

    <div class="center-box">
        <h2>Cambiar Contraseña</h2>

        <form method="POST" onsubmit="return validatePasswords()">
            <div>
                <label for="nueva-contrasena">Nueva Contraseña <span style="color:#fd3d57;">*</span></label><br>
                <div class="password-input-container">
                    <input type="password" id="nueva-contrasena" name="nueva-contrasena" required oninput="validateNewPassword()">
                    <i class="bi bi-eye-slash password-toggle" data-input="nueva-contrasena"></i>
                </div>
                <div class="password-feedback" id="nueva-contrasena-feedback"></div>
            </div>

            <div>
                <label for="confirmar-contrasena">Confirmar Contraseña <span style="color:#fd3d57;">*</span></label><br>
                <div class="password-input-container">
                    <input type="password" id="confirmar-contrasena" name="confirmar-contrasena" required oninput="validateConfirmPassword()">
                    <i class="bi bi-eye-slash password-toggle" data-input="confirmar-contrasena"></i>
                </div>
                <div class="password-feedback" id="confirmar-contrasena-feedback"></div>
            </div>

            <button type="submit">Actualizar Contraseña</button>
        </form>

        <div class="msg"><?= $mensaje ?></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggles = document.querySelectorAll('.password-toggle');

            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const inputId = this.getAttribute('data-input');
                    const passwordInput = document.getElementById(inputId);

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.classList.remove('bi-eye-slash');
                        this.classList.add('bi-eye');
                    } else {
                        passwordInput.type = 'password';
                        this.classList.remove('bi-eye');
                        this.classList.add('bi-eye-slash');
                    }
                });
            });
        });

        const nuevaContrasenaInput = document.getElementById('nueva-contrasena');
        const confirmarContrasenaInput = document.getElementById('confirmar-contrasena');
        const nuevaContrasenaFeedback = document.getElementById('nueva-contrasena-feedback');
        const confirmarContrasenaFeedback = document.getElementById('confirmar-contrasena-feedback');

        function validatePassword(password, feedbackElement) {
            if (password.length < 8) {
                feedbackElement.textContent = "Debe tener al menos 8 caracteres.";
                feedbackElement.style.color = "red";
                return false;
            } else if (!/[A-Z]/.test(password)) {
                feedbackElement.textContent = "Debe incluir una letra mayúscula.";
                feedbackElement.style.color = "red";
                return false;
            } else if (!/[a-z]/.test(password)) {
                feedbackElement.textContent = "Debe incluir una letra minúscula.";
                feedbackElement.style.color = "red";
                return false;
            } else if (!/\d/.test(password)) {
                feedbackElement.textContent = "Debe incluir al menos un número.";
                feedbackElement.style.color = "red";
                return false;
            } else {
                feedbackElement.textContent = "Contraseña segura";
                feedbackElement.style.color = "green";
                return true;
            }
        }

        function validateNewPassword() {
            validatePassword(nuevaContrasenaInput.value, nuevaContrasenaFeedback);
            validateConfirmPasswordMatch();
        }

        function validateConfirmPassword() {
            validatePassword(confirmarContrasenaInput.value, confirmarContrasenaFeedback);
            validateConfirmPasswordMatch();
        }

        function validateConfirmPasswordMatch() {
            if (confirmarContrasenaInput.value !== nuevaContrasenaInput.value) {
                confirmarContrasenaFeedback.textContent = "Las contraseñas no coinciden.";
                confirmarContrasenaFeedback.style.color = "red";
                return false;
            } else if (confirmarContrasenaInput.value !== "" && nuevaContrasenaInput.value !== "" && nuevaContrasenaFeedback.style.color === "green") {
                confirmarContrasenaFeedback.textContent = "Las contraseñas coinciden";
                confirmarContrasenaFeedback.style.color = "green";
                return true;
            } else if (nuevaContrasenaInput.value !== "") {
                confirmarContrasenaFeedback.textContent = "Debe coincidir con la nueva contraseña.";
                confirmarContrasenaFeedback.style.color = "orange";
                return false;
            } else {
                confirmarContrasenaFeedback.textContent = "";
                return true;
            }
        }

        function validatePasswords() {
            const isNewPasswordValid = validatePassword(nuevaContrasenaInput.value, nuevaContrasenaFeedback);
            const isConfirmPasswordValid = validatePassword(confirmarContrasenaInput.value, confirmarContrasenaFeedback);
            const doPasswordsMatch = validateConfirmPasswordMatch();

            return isNewPasswordValid && isConfirmPasswordValid && doPasswordsMatch;
        }
    </script>

</body>

</html>