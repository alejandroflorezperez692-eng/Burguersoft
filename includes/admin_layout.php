<?php
iniciarSesionSegura();
$navActivo   = $navActivo ?? '';
$nombreAdmin = ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '');
$rolAdmin    = $_SESSION['rol_usuario'] ?? 'Cliente';
$esAdmin     = $rolAdmin === 'Administrador';
?>
<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-header">
        <img src="../estilos/img/icono1.png" alt="Logo" class="logo">
        <hr class="sidebar-divider">
        <h2 class="nom-local" style="font-weight: 700;">El Oriente</h2>
    </div>
    <div class="sidebar-footer">
        <hr style="width:150px;align-self:center;">
        <div class="sidebar-item">
            <a href="../php/backups.php">
                <img src="../estilos/img/engranaje.png" class="icono-sidebar">
                COPIAS DE SEGURIDAD
            </a>
        </div>
        <?php if ($esAdmin): ?>
        <hr style="width:150px;align-self:center;">
        <div class="sidebar-item">
            <a href="../php/gestion_usuario.php">
                <img src="../estilos/img/equipo.png" class="icono-sidebar">
                GESTIÓN DE USUARIOS
            </a>
        </div>
        <?php endif; ?>
        <hr style="width:150px;align-self:center;">
        <div class="sidebar-item admin-name" style="text-align:center;">
            <a href="../php/config_cuenta.php">
                <img style="margin-left: -20px; margin-right: 7px;" src="../estilos/img/usuario.png" class="icono-sidebar">
                <span style="margin-left: 15px;"><?= htmlspecialchars($nombreAdmin) ?></span>
            </a>
        </div>
    </div>
</div>

<!-- TOP NAV -->
<nav class="header-nav">
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/inicio_admin.php"   class="nav-item <?= $navActivo==='inicio'  ? 'active':'' ?>"><img src="../estilos/img/casa.png"  class="icono"> Inicio</a>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/Menu.php"      class="nav-item <?= $navActivo==='menu'    ? 'active':'' ?>"><img src="../estilos/img/cena.png"   class="icono"> Menú</a>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/ventas.php"           class="nav-item <?= $navActivo==='ventas'  ? 'active':'' ?>"><img src="../estilos/img/insignia.png" class="icono"> Ventas</a>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/materia_prima.php"    class="nav-item <?= $navActivo==='materia' ? 'active':'' ?>"><img src="../estilos/img/tratamiento-a-base-de-hierbas.png" class="icono"> Materia Prima</a>
    <?php if ($esAdmin): ?>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/gestion_marca.php"   class="nav-item <?= $navActivo==='marca'   ? 'active':'' ?>"><img src="../estilos/img/marca-comercial.png" class="icono"> Gestión de Marca</a>
    <?php endif; ?>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/promociones.php"      class="nav-item <?= $navActivo==='promos'  ? 'active':'' ?>"><img src="../estilos/img/promocion.png" class="icono"> Promociones</a>
    <hr style="border-right:1px solid white;height:40px;">
    <a href="../php/logout.php" class="nav-item nav-logout"><img src="../estilos/img/cerrar-sesion.png" class="icono"> Salir</a>
</nav>
