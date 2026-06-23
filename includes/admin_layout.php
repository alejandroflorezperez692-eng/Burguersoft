<?php
iniciarSesionSegura();
$navActivo   = $navActivo ?? '';
$nombreAdmin = ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '');
$rolAdmin    = $_SESSION['rol_usuario'] ?? 'Cliente';
$esAdmin     = $rolAdmin === 'Administrador';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../estilos/img/icono1.png" alt="Logo" class="logo">
        <hr class="sidebar-divider">
        <span class="nom-local">El Oriente</span>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-item <?= $navActivo === 'inicio' ? 'active' : '' ?>">
            <a href="../php/inicio_admin.php">
                <img src="../estilos/img/casa.png" class="icono-sidebar" alt="">
                Inicio
            </a>
        </div>

        <div class="sidebar-item <?= $navActivo === 'menu' ? 'active' : '' ?>">
            <a href="../php/Menu.php">
                <img src="../estilos/img/cena.png" class="icono-sidebar" alt="">
                Menú
            </a>
        </div>

        <div class="sidebar-item <?= $navActivo === 'ventas' ? 'active' : '' ?>">
            <a href="../php/ventas.php">
                <img src="../estilos/img/insignia.png" class="icono-sidebar" alt="">
                Ventas
            </a>
        </div>

        <div class="sidebar-item <?= $navActivo === 'materia' ? 'active' : '' ?>">
            <a href="../php/materia_prima.php">
                <img src="../estilos/img/tratamiento-a-base-de-hierbas.png" class="icono-sidebar" alt="">
                Materia Prima
            </a>
        </div>

        <div class="sidebar-item <?= $navActivo === 'compras' ? 'active' : '' ?>">
            <a href="../php/compras.php">
                <img src="../estilos/img/buy.png" class="icono-sidebar" alt="">
            Compras
            </a>
        </div>

        

        <?php if ($esAdmin): ?>
        <div class="sidebar-item <?= $navActivo === 'marca' ? 'active' : '' ?>">
            <a href="../php/gestion_marca.php">
                <img src="../estilos/img/marca-comercial.png" class="icono-sidebar" alt="">
                Marcas
            </a>
        </div>
        <?php endif; ?>

        <div class="sidebar-item <?= $navActivo === 'promociones' ? 'active' : '' ?>">
            <a href="../php/promociones.php">
                <img src="../estilos/img/promocion.png" class="icono-sidebar" alt="">
                Promociones
            </a>
        </div>

        <div class="sidebar-item <?= $navActivo === 'backups' ? 'active' : '' ?>">
            <a href="../php/backups.php">
                <img src="../estilos/img/engranaje.png" class="icono-sidebar" alt="">
                Copias de seguridad
            </a>
        </div>

        <?php if ($esAdmin): ?>
        <div class="sidebar-item <?= $navActivo === 'usuarios' ? 'active' : '' ?>">
            <a href="../php/gestion_usuario.php">
                <img src="../estilos/img/equipo.png" class="icono-sidebar" alt="">
                Usuarios
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<nav class="header-nav">

    <a href="../php/config_cuenta.php" class="nav-item <?= $navActivo === 'config_cuenta' ? 'active' : '' ?> admin-name ">
        <img src="../estilos/img/usuario.png" class="icono-sidebar" alt="perfil">
        <span><?= htmlspecialchars($nombreAdmin) ?></span>
    </a>
    <a href="../php/logout.php" class="nav-item nav-logout">
        <img src="../estilos/img/cerrar-sesion.png" class="icono" alt="">Salir
    </a>
</nav>