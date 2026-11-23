<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>
<div class="portal-intro">
  <span class="section-kicker">CONTROL DE ASISTENCIA</span>
  <h1>Entrenamiento del <?= esc($session['training_date']) ?></h1>
  <p><?= esc(substr($session['starts_at'],0,5) . '–' . substr($session['ends_at'],0,5)) ?> · <?= esc($session['location_name']) ?></p>
</div>
<?php if (session('error')): ?><div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
<?php if (session('message')): ?><div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div><?php endif ?>
<div class="portal-panel">
  <div class="registry-toolbar">
    <h2>Categorías participantes</h2>
    <a class="registry-cancel" href="<?= esc(site_url('/app/entrenamientos'), 'attr') ?>">Volver a sesiones</a>
  </div>
  <p class="portal-muted">
    <?php foreach ($categories as $category): ?><?= esc($category['category_code']) ?> &nbsp; <?php endforeach ?>
  </p>
  <p class="portal-muted">Estado de sesión: <strong><?= esc(match ($session['status']) {'scheduled' => 'Programada', 'completed' => 'Finalizada', 'cancelled' => 'Cancelada', default => 'En revisión'}) ?></strong></p>
  <?php if ($canClose): ?>
    <div class="registry-actions">
      <form method="post" action="<?= esc(site_url('app/entrenamientos/' . (int) $session['id'] . '/estado'), 'attr') ?>" onsubmit="return confirm('¿Finalizar este entrenamiento? Las asistencias quedarán bloqueadas. No se podrán añadir nuevas marcas.');">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="completed">
        <button class="portal-action-link registry-submit" type="submit">Finalizar sesión</button>
      </form>
      <form method="post" action="<?= esc(site_url('app/entrenamientos/' . (int) $session['id'] . '/estado'), 'attr') ?>" onsubmit="return confirm('¿Cancelar entrenamiento? Solo procede si no se registró ninguna asistencia.');">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="cancelled">
        <button class="registry-danger" type="submit">Cancelar sesión sin asistencia</button>
      </form>
    </div>
    <p class="portal-muted">Para finalizar se requiere al menos una asistencia y que la fecha del entrenamiento haya llegado. Los atletas sin marca permanecerán como «Sin marcar», no se convertirán en ausentes. El cierre no puede revertirse desde este formulario.</p>
  <?php endif ?>
  <?php if ($attendanceTotals !== []): ?>
    <p class="portal-muted">Marcas registradas:
      <?php foreach ($attendanceTotals as $total): ?>
        <?= esc(match ($total['attendance_status']) {'present' => 'Presentes', 'absent' => 'Ausentes', 'excused' => 'Justificados', default => 'Otro'}) ?>: <?= (int) $total['quantity'] ?> &nbsp;
      <?php endforeach ?>
    </p>
  <?php endif ?>
  <?php if ($statusHistory !== []): ?>
    <h3 class="registry-subheading">Movimientos de estado</h3>
    <div class="table-scroll">
      <table class="access-table">
        <thead><tr><th scope="col">Anterior</th><th scope="col">Nuevo</th><th scope="col">Fecha</th></tr></thead>
        <tbody>
          <?php foreach ($statusHistory as $entry): ?>
            <tr>
              <td><?= esc($entry['from_status']) ?></td>
              <td><?= esc($entry['to_status']) ?></td>
              <td><?= esc(\App\Services\TrainingClock::displayUtc($entry['changed_at'])) ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>
  <?php if (! $canRecord): ?>
    <p class="portal-muted">La asistencia está bloqueada si la fecha aún no ha llegado o si la sesión fue finalizada o cancelada.</p>
  <?php endif ?>
  <?php if ($legacyRoster): ?>
    <p class="portal-muted"><strong>Registro anterior:</strong> esta sesión fue cerrada sin una plantilla histórica. Solo se recuperan asistencias efectivamente registradas. Cuando no se conoce con certeza la categoría histórica, se indica que está sin verificar; no se inventan ausencias.</p>
  <?php endif ?>
  <?php if ($historicalRoster && ! $legacyRoster): ?>
    <p class="portal-muted">Plantilla congelada al finalizar esta sesión. Los cambios posteriores de categoría o estado del atleta no alteran este listado. Los atletas sin marca siguen como «Sin marcar».</p>
  <?php endif ?>
  <div class="table-scroll">
    <table class="access-table">
      <thead><tr><th scope="col">Atleta</th><th scope="col"><?= $historicalRoster ? 'Categoría histórica' : 'Categoría actual' ?></th><th scope="col">Asistencia registrada</th><th scope="col">Actualizar</th></tr></thead>
      <tbody>
        <?php foreach ($athletes as $athlete): ?>
          <tr>
            <td><strong><?= esc($athlete['first_name'] . ' ' . $athlete['last_name']) ?></strong></td>
            <td><?= esc($athlete['category_code']) ?></td>
            <td><?= esc(match ($athlete['attendance_status']) {'present' => 'Presente', 'absent' => 'Ausente', 'excused' => 'Justificado', default => 'Sin marcar'}) ?></td>
            <td>
              <?php if ($canRecord): ?>
                <form method="post" action="<?= esc(site_url('/app/entrenamientos/' . (int) $session['id'] . '/asistencia'), 'attr') ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="athlete_id" value="<?= (int) $athlete['id'] ?>">
                  <label>Estado
                    <select name="status" aria-label="Asistencia de <?= esc($athlete['first_name'] . ' ' . $athlete['last_name'], 'attr') ?>" required>
                      <option value="">Seleccionar</option>
                      <option value="present">Presente</option>
                      <option value="absent">Ausente</option>
                      <option value="excused">Justificado</option>
                    </select>
                  </label>
                  <button class="portal-action-link registry-submit" type="submit">Guardar</button>
                </form>
              <?php else: ?>—<?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
        <?php if ($athletes === []): ?><tr><td colspan="4">No hay atletas con información disponible para esta sesión y tu nivel de acceso.</td></tr><?php endif ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
