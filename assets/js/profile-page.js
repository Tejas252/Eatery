(function () {
  'use strict';

  var form = document.getElementById('profileForm');
  if (!form) {
    return;
  }

  var editBtn = document.getElementById('profileEditBtn');
  var cancelBtn = document.getElementById('profileCancelBtn');
  var saveBtn = document.getElementById('profileSaveBtn');
  var editActions = document.getElementById('profileEditActions');
  var messageBox = document.getElementById('profileFormMessage');
  var fullNameInput = document.getElementById('profileFullName');
  var mobileInput = document.getElementById('profileMobile');
  var identityName = document.querySelector('.profile-identity__name');

  var snapshot = {
    full_name: fullNameInput ? fullNameInput.value : '',
    mobile: mobileInput ? mobileInput.value : ''
  };

  function showMessage(text, type) {
    if (!messageBox) {
      return;
    }
    messageBox.textContent = text;
    messageBox.className = 'profile-form__message is-' + type;
    messageBox.hidden = false;
  }

  function hideMessage() {
    if (messageBox) {
      messageBox.hidden = true;
      messageBox.textContent = '';
    }
  }

  function setEditable(editing) {
    form.classList.toggle('is-editing', editing);

    [fullNameInput, mobileInput].forEach(function (input) {
      if (!input) {
        return;
      }
      var field = input.closest('.profile-field');
      input.readOnly = !editing;
      if (field) {
        field.classList.toggle('is-editable', editing);
      }
    });

    if (editActions) {
      editActions.hidden = !editing;
    }

    if (editing && fullNameInput) {
      fullNameInput.focus();
    }

    if (!editing) {
      hideMessage();
    }
  }

  function restoreSnapshot() {
    if (fullNameInput) {
      fullNameInput.value = snapshot.full_name;
    }
    if (mobileInput) {
      mobileInput.value = snapshot.mobile;
    }
  }

  if (editBtn) {
    editBtn.addEventListener('click', function () {
      snapshot.full_name = fullNameInput ? fullNameInput.value : '';
      snapshot.mobile = mobileInput ? mobileInput.value : '';
      setEditable(true);
    });
  }

  if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
      restoreSnapshot();
      setEditable(false);
    });
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (!saveBtn || saveBtn.disabled) {
      return;
    }

    saveBtn.disabled = true;
    saveBtn.classList.add('is-loading');
    hideMessage();

    try {
      var body = new FormData();
      body.append('full_name', fullNameInput ? fullNameInput.value.trim() : '');
      body.append('mobile', mobileInput ? mobileInput.value.trim() : '');

      var response = await fetch('profile_update.php', {
        method: 'POST',
        body: body,
        credentials: 'same-origin'
      });
      var data = await response.json();

      if (!data.success) {
        throw new Error(data.message || 'Could not save profile.');
      }

      snapshot.full_name = data.profile.full_name;
      snapshot.mobile = data.profile.mobile;

      if (fullNameInput) {
        fullNameInput.value = snapshot.full_name;
      }
      if (mobileInput) {
        mobileInput.value = snapshot.mobile;
      }
      if (identityName) {
        identityName.textContent = snapshot.full_name || identityName.textContent;
      }

      setEditable(false);
      showMessage(data.message, 'success');
    } catch (error) {
      showMessage(error.message || 'Could not save profile.', 'error');
    } finally {
      saveBtn.disabled = false;
      saveBtn.classList.remove('is-loading');
    }
  });
})();
