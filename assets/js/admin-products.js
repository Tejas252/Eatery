(function () {
  'use strict';

  var editModal = document.getElementById('productEditModal');
  var viewModal = document.getElementById('productViewModal');
  var deleteModal = document.getElementById('productDeleteModal');
  var editForm = document.getElementById('productEditForm');

  function parseProduct(el) {
    try {
      return JSON.parse(el.getAttribute('data-product'));
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
      [editModal, viewModal, deleteModal].forEach(closeModal);
    }
  });

  function fillView(product) {
    document.getElementById('viewProductImg').src = product.img_url;
    document.getElementById('viewProductImg').alt = product.product_name;
    document.getElementById('viewProductName').textContent = product.product_name;
    document.getElementById('viewProductSku').textContent = 'SKU #' + product.product_no;
    document.getElementById('viewProductCategory').textContent = product.product_type;
    document.getElementById('viewProductPrice').textContent = '$' + Number(product.product_price).toFixed(2);
    document.getElementById('viewProductStock').textContent = String(product.product_qty);
    document.getElementById('viewProductDesc').textContent = product.product_desc;
    document.getElementById('viewProductLink').href = product.view_url;
  }

  function fillEdit(product) {
    editForm.querySelector('[name="product_id"]').value = product.product_id;
    editForm.querySelector('[name="product_name"]').value = product.product_name;
    editForm.querySelector('[name="product_type"]').value = product.product_type;
    editForm.querySelector('[name="product_price"]').value = product.product_price;
    editForm.querySelector('[name="product_qty"]').value = product.product_qty;
    editForm.querySelector('[name="product_desc"]').value = product.product_desc;
    document.getElementById('editProductPreview').src = product.img_url;
    document.getElementById('editProductPreview').alt = product.product_name;
    document.getElementById('editProductSku').textContent = 'SKU #' + product.product_no;
    editForm.querySelector('[name="product_img"]').value = '';
  }

  function fillDelete(product) {
    document.getElementById('deleteProductId').value = product.product_id;
    document.getElementById('deleteProductName').textContent = product.product_name;
  }

  document.querySelectorAll('[data-product-view]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var product = parseProduct(btn);
      if (!product) return;
      fillView(product);
      openModal(viewModal);
    });
  });

  document.querySelectorAll('[data-product-edit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var product = parseProduct(btn);
      if (!product) return;
      fillEdit(product);
      openModal(editModal);
    });
  });

  document.querySelectorAll('[data-product-delete]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var product = parseProduct(btn);
      if (!product) return;
      fillDelete(product);
      openModal(deleteModal);
    });
  });

  if (editForm) {
    editForm.addEventListener('submit', function (e) {
      var name = editForm.querySelector('[name="product_name"]');
      var desc = editForm.querySelector('[name="product_desc"]');
      if (!name.value.trim() || !desc.value.trim()) {
        e.preventDefault();
        alert('Product name and description are required.');
        return;
      }
      if (name.value.length > 20) {
        e.preventDefault();
        alert('Product name must be 20 characters or fewer.');
      }
    });
  }

  var fileInput = editForm && editForm.querySelector('[name="product_img"]');
  if (fileInput) {
    fileInput.addEventListener('change', function () {
      var file = fileInput.files && fileInput.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (ev) {
        document.getElementById('editProductPreview').src = ev.target.result;
      };
      reader.readAsDataURL(file);
    });
  }
})();
