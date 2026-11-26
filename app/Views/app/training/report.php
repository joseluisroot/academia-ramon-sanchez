<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">INC-004 · CONTROL DEPORTIVO</span>
  <h1>Resumen de asistencias</h1>
  <p>Conteos históricos de marcas registradas en entrenamientos finalizados. No representan porcentajes ni asistencia esperada.</p>
</div>
<div class="portal-panel">
  <div class="registry-toolbar">
    <h2>Asistencia registrada por atleta</h2>
    <a class="registry-cancel" href="<?= esc(site_url('app/entrenamientos'), 'attr') ?>">Volver a entrenamientos</a>
  </div>
  <p class="portal-muted">Solo se incluyen sesiones finalizadas y marcas con categoría histórica conservada. Las marcas anteriores a la actualización del historial pueden no aparecer aquí; se conservan en su sesión original. "Sin marcar" nunca equivale a "Ausente".</p>
  <?php if (! $supervisor): ?>
    <p class="portal-muted">Se muestran únicamente resultados de categorías que tienes actualmente asignadas.</p>
  <?php endif ?>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Atleta</th><th scope="col">Categoría al marcar</th><th scope="col">Presente</th><th scope="col">Ausente</th><th scope="col">Justificado</th><th scope="col">Total marcado</th></tr></thead>
      <tbody>
        <?php foreach ($results as $item): ?>
          <tr>
            <td><?= esc($item['first_name'] . ' ' . $item['last_name']) ?></td>
            <td><?= esc($item['category_code']) ?></td>
            <td><?= (int) $item['present_count'] ?></td>
            <td><?= (int) $item['absent_count'] ?></td>
            <td><?= (int) $item['excused_count'] ?></td>
            <td><?= (int) $item['marked'] ?></td>
          </tr>
        <?php endforeach ?>
        <?php if ($results === []): ?>
          <tr><td colspan="6">Todavía no hay marcas históricas con categoría registrada en sesiones finalizadas.</td></tr>
        <?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
