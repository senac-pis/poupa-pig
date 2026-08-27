<?php
// === DADOS: mexa só aqui pra mudar o menu ===
$menu = [
    ['label' => 'Início',      'icon' => 'bi-house-door',    'url' => 'index.php'],
    ['label' => 'Dashboard',   'icon' => 'bi-speedometer2',  'url' => '#'],
    ['label' => 'Transações',  'icon' => 'bi-table',         'url' => '#'],
    ['label' => 'Categorias',  'icon' => 'bi-grid',          'url' => '#'],
    ['label' => 'Perfil',      'icon' => 'bi-person-circle', 'url' => '#'],
];
?>
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-bg-dark">
  <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
    <i class="bi bi-piggy-bank fs-4 me-2"></i>
    <span class="fs-4">Poupa Pig</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <?php foreach ($menu as $item): ?>
      <li class="nav-item">
        <a href="<?= $item['url'] ?>" class="nav-link text-white">
          <i class="bi <?= $item['icon'] ?> me-2"></i>
          <?= htmlspecialchars($item['label']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
  <hr>
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-person-circle fs-5 me-2"></i>
      <strong>Usuário</strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
      <li><a class="dropdown-item" href="#">Configurações</a></li>
      <li><a class="dropdown-item" href="#">Perfil</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="#">Sair</a></li>
    </ul>
  </div>
</div>