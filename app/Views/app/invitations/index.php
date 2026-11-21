<?= $this->extend('layouts/private') ?>
<?= $this->section('content') ?>

<div class="portal-intro">
    <span class="section-kicker">ADMINISTRACIÓN DE CUENTAS</span>
    <h1>Invitar un nuevo usuario</h1>
    <p>Envía una invitación personal para que la persona configure su contraseña. El enlace es individual, vence en 48 horas y solamente puede utilizarse una vez.</p>
</div>

<div class="portal-panel invitation-panel">
    <h2>Datos de la invitación</h2>

    <?php if (session('error') !== null): ?>
        <div class="auth-alert auth-alert-error" role="alert"><?= esc(session('error')) ?></div>
    <?php endif ?>
    <?php if (session('message') !== null): ?>
        <div class="auth-alert auth-alert-success" role="status"><?= esc(session('message')) ?></div>
    <?php endif ?>

    <form class="invitation-form" method="post" action="<?= esc(site_url('app/invitaciones'), 'attr') ?>">
        <?= csrf_field() ?>
        <label for="invite-email">Correo de la persona que será invitada</label>
        <input id="invite-email" type="email" name="email" autocomplete="off" maxlength="254" value="<?= esc(old('email') ?? '', 'attr') ?>" required>

        <label for="invite-role">Tipo de acceso</label>
        <select id="invite-role" name="role" required>
            <?php foreach ($roles as $key => $label): ?>
                <option value="<?= esc($key, 'attr') ?>" <?= old('role') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
            <?php endforeach ?>
        </select>

        <p class="portal-muted">La invitación inicial no concede acceso a expedientes de otros atletas ni a operaciones financieras. Los roles sensibles requieren aprobación institucional separada.</p>
        <button class="auth-submit" type="submit">Enviar invitación <span aria-hidden="true">→</span></button>
    </form>
</div>


<div class="portal-panel invitation-history">
    <h2>Invitaciones recientes</h2>
    <p class="portal-muted">Puedes cancelar una invitación pendiente para invalidar su enlace. Para volver a invitar al mismo correo, cancela primero la invitación anterior y envía una nueva desde el formulario.</p>
    <div class="table-scroll">
        <table class="access-table">
            <thead>
                <tr><th scope="col">Correo</th><th scope="col">Perfil</th><th scope="col">Estado</th><th scope="col">Vence</th><th scope="col">Acción</th></tr>
            </thead>
            <tbody>
            <?php foreach ($invitations as $invitation): ?>
                <?php
                    $expired = $invitation['status'] === 'pending' && strtotime((string) $invitation['expires_at']) <= time();
                    $statusLabel = $expired ? 'Vencida' : match ($invitation['status']) {
                        'pending' => 'Pendiente',
                        'accepted' => 'Aceptada',
                        'cancelled' => 'Cancelada',
                        default => 'No disponible',
                    };
                ?>
                <tr>
                    <td><?= esc((string) $invitation['email']) ?></td>
                    <td><?= esc((string) ($roles[$invitation['role']] ?? $invitation['role'])) ?></td>
                    <td><?= esc($statusLabel) ?></td>
                    <td><?= esc((string) $invitation['expires_at']) ?></td>
                    <td>
                        <?php if ($invitation['status'] === 'pending' && ! $expired): ?>
                            <form method="post" action="<?= esc(site_url('app/invitaciones/cancelar'), 'attr') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="invitation_id" value="<?= (int) $invitation['id'] ?>">
                                <button class="invitation-cancel-button" type="submit">Cancelar</button>
                            </form>
                        <?php else: ?>
                            <span class="access-locked"><?= $invitation['status'] === 'accepted' ? 'Invitación finalizada' : 'Sin operaciones disponibles' ?></span>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if ($invitations === []): ?>
                <tr><td colspan="5">Todavía no se han enviado invitaciones.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
