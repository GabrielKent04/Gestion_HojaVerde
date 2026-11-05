<?php
// 1. Reanudar la sesión activa
session_start();

// 2. Limpiar todas las variables de sesión
session_unset();

// 3. Destruir la sesión en el servidor
session_destroy();

// 4. Redirigir inmediatamente a la vista de login
header("Location: login.php");
exit();