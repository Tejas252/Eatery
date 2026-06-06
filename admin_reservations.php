<?php
include 'assets/php/config.php';
session_start();

$adminPageTitle = 'Table Reservations';
$adminPageSubtitle = 'Manage restaurant tables, capacity, and availability';
$adminActiveNav = 'reservations';
$adminExtraCss = ['assets/css/admin-tables.css'];
$adminExtraJs = ['assets/js/admin-tables.js'];

require_once 'assets/php/admin_helpers.php';
require_once 'assets/php/table_admin_helpers.php';

$tables = table_admin_fetch_all($conn);
$stats = table_admin_stats($conn);
$hasTables = count($tables) > 0;
$statusOptions = table_admin_status_options();

include 'assets/php/admin_header.php';
?>

<section class="admin-stats">
  <article class="admin-stat-card admin-stat-card--accent">
    <span class="admin-stat-card__label">Available</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['available']; ?></span>
    <span class="admin-stat-card__hint">Ready to book</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Reserved</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['reserved']; ?></span>
    <span class="admin-stat-card__hint">Currently booked</span>
  </article>
  <article class="admin-stat-card">
    <span class="admin-stat-card__label">Total Tables</span>
    <span class="admin-stat-card__value"><?php echo (int) $stats['total']; ?></span>
    <span class="admin-stat-card__hint">In restaurant</span>
  </article>
</section>

<section class="admin-card">
  <div class="admin-card__header admin-table-card__header">
    <h2 class="admin-card__title">All Tables</h2>
    <button type="button" class="admin-btn admin-btn--primary admin-btn--sm" id="tableAddBtn">+ Add Table</button>
  </div>
  <div class="admin-card__body">
    <?php if (!$hasTables) : ?>
      <div class="admin-empty">
        <p class="admin-empty__title">No tables configured</p>
        <p class="admin-empty__text">Add your first table to start accepting reservations.</p>
        <button type="button" class="admin-btn admin-btn--primary" style="margin-top:1rem;" id="tableAddBtnEmpty">+ Add Table</button>
      </div>
    <?php else : ?>
      <div class="admin-table-wrap">
        <table class="admin-table admin-table--reservations">
          <thead>
            <tr>
              <th>Table No</th>
              <th>Capacity</th>
              <th>Status</th>
              <th class="admin-table__actions-col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tables as $table) :
              $tableJson = table_admin_json_attr($table);
              $deleteCheck = table_admin_delete_allowed($conn, $table);
              ?>
              <tr>
                <td data-label="Table No"><?php echo (int) $table['table_no']; ?></td>
                <td data-label="Capacity">
                  <span class="admin-table-capacity"><?php echo (int) $table['table_size']; ?> guests</span>
                </td>
                <td data-label="Status"><?php echo admin_table_status_badge($table['table_status']); ?></td>
                <td class="admin-table__actions-cell" data-label="Actions">
                  <div class="admin-table-actions">
                    <button
                      type="button"
                      class="admin-btn admin-btn--sm admin-btn--edit"
                      data-table-edit
                      data-table="<?php echo $tableJson; ?>"
                    >Edit</button>
                    <button
                      type="button"
                      class="admin-btn admin-btn--sm admin-btn--danger"
                      data-table-delete
                      data-table="<?php echo $tableJson; ?>"
                      <?php echo $deleteCheck['allowed'] ? '' : 'disabled title="' . htmlspecialchars($deleteCheck['reason']) . '"'; ?>
                    >Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="admin-modal" id="tableAddModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tableAddTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="tableAddTitle">Add Table</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <form action="assets/php/manage_table.php" method="post">
      <div class="admin-modal__body">
        <div class="admin-table-form-grid">
          <div class="admin-field">
            <label class="admin-label" for="add_table_no">Table Number</label>
            <input type="number" class="admin-input admin-input--no-icon" id="add_table_no" name="table_no" min="1" max="999" required>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="add_table_size">Capacity (Guests)</label>
            <input type="number" class="admin-input admin-input--no-icon" id="add_table_size" name="table_size" min="1" max="99" required>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="add_table_status">Status</label>
            <select class="admin-select" id="add_table_status" name="table_status" required>
              <?php foreach ($statusOptions as $value => $label) : ?>
                <option value="<?php echo htmlspecialchars($value); ?>"<?php echo $value === 'non' ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Cancel</button>
        <button type="submit" name="create" class="admin-btn admin-btn--primary">Save Table</button>
      </div>
    </form>
  </div>
</div>

<div class="admin-modal" id="tableEditModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tableEditTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="tableEditTitle">Edit Table</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <form action="assets/php/manage_table.php" method="post" id="tableEditForm">
      <div class="admin-modal__body">
        <input type="hidden" name="original_table_no" value="">
        <div class="admin-table-form-grid">
          <div class="admin-field">
            <label class="admin-label" for="edit_table_no">Table Number</label>
            <input type="number" class="admin-input admin-input--no-icon" id="edit_table_no" name="table_no" min="1" max="999" required readonly>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="edit_table_size">Capacity (Guests)</label>
            <input type="number" class="admin-input admin-input--no-icon" id="edit_table_size" name="table_size" min="1" max="99" required>
          </div>
          <div class="admin-field">
            <label class="admin-label" for="edit_table_status">Status</label>
            <select class="admin-select" id="edit_table_status" name="table_status" required>
              <?php foreach ($statusOptions as $value => $label) : ?>
                <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Cancel</button>
        <button type="submit" name="update" class="admin-btn admin-btn--primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="admin-modal" id="tableDeleteModal" hidden>
  <div class="admin-modal__backdrop" data-modal-close></div>
  <div class="admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tableDeleteTitle">
    <div class="admin-modal__header">
      <h3 class="admin-modal__title" id="tableDeleteTitle">Delete Table</h3>
      <button type="button" class="admin-modal__close" data-modal-close aria-label="Close">&times;</button>
    </div>
    <form action="assets/php/manage_table.php" method="post">
      <div class="admin-modal__body">
        <input type="hidden" name="table_no" id="deleteTableNo" value="">
        <p>Are you sure you want to delete <strong id="deleteTableLabel"></strong> (<span id="deleteTableCapacity"></span>)?</p>
        <p class="admin-table__muted">This action cannot be undone.</p>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="admin-btn admin-btn--ghost" data-modal-close>Cancel</button>
        <button type="submit" name="delete" class="admin-btn admin-btn--danger">Delete Table</button>
      </div>
    </form>
  </div>
</div>

<?php include 'assets/php/admin_footer.php'; ?>
