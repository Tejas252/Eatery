(function () {
  'use strict';

  var addModal = document.getElementById('tableAddModal');
  var editModal = document.getElementById('tableEditModal');
  var deleteModal = document.getElementById('tableDeleteModal');
  var editForm = document.getElementById('tableEditForm');
  var modals = [addModal, editModal, deleteModal];

  function parseTable(el) {
    try {
      return JSON.parse(el.getAttribute('data-table'));
    } catch (e) {
      return null;
    }
  }

  function openModal(modal) {
    if (!modal) return;
    modal.hidden = false;
    document.body.classList.add('admin-no-scroll');
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.hidden = true;
    if (!document.querySelector('.admin-modal:not([hidden])')) {
      document.body.classList.remove('admin-no-scroll');
    }
  }

  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      closeModal(btn.closest('.admin-modal'));
    });
  });

  document.querySelectorAll('.admin-modal__backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function () {
      closeModal(backdrop.closest('.admin-modal'));
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      modals.forEach(closeModal);
    }
  });

  var addBtn = document.getElementById('tableAddBtn');
  var addBtnEmpty = document.getElementById('tableAddBtnEmpty');

  function openAddModal() {
    openModal(addModal);
  }

  if (addBtn) {
    addBtn.addEventListener('click', openAddModal);
  }

  if (addBtnEmpty) {
    addBtnEmpty.addEventListener('click', openAddModal);
  }

  function fillEdit(table) {
    editForm.querySelector('[name="original_table_no"]').value = table.table_no;
    editForm.querySelector('[name="table_no"]').value = table.table_no;
    editForm.querySelector('[name="table_size"]').value = table.table_size;
    editForm.querySelector('[name="table_status"]').value = table.table_status;
  }

  function fillDelete(table) {
    document.getElementById('deleteTableNo').value = table.table_no;
    document.getElementById('deleteTableLabel').textContent = 'Table ' + table.table_no;
    document.getElementById('deleteTableCapacity').textContent = table.table_size + ' guests';
  }

  document.querySelectorAll('[data-table-edit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var table = parseTable(btn);
      if (!table) return;
      fillEdit(table);
      openModal(editModal);
    });
  });

  document.querySelectorAll('[data-table-delete]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var table = parseTable(btn);
      if (!table) return;
      fillDelete(table);
      openModal(deleteModal);
    });
  });
})();
