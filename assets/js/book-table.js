(function () {
  'use strict';

  var section = document.getElementById('book-table');
  if (!section) {
    return;
  }

  var API_URL = 'assets/php/book_table_api.php';
  var isLoggedIn = section.dataset.loggedIn === '1';
  var maxGuests = parseInt(section.dataset.maxGuests, 10) || 5;
  var hasActiveBooking = section.dataset.hasActiveBooking === '1';
  var isSubmitting = false;

  var bookingFlow = document.getElementById('bookTableBookingFlow');
  var activeReservation = document.getElementById('bookTableActiveReservation');
  var subtitle = document.getElementById('bookTableSubtitle');
  var guestInput = document.getElementById('bookingGuests');
  var guestDecrease = document.getElementById('bookingGuestsDecrease');
  var guestIncrease = document.getElementById('bookingGuestsIncrease');
  var tableGrid = document.getElementById('bookTableGrid');
  var bookBtn = document.getElementById('bookTableSubmit');
  var refreshBtn = document.getElementById('bookTableRefresh');
  var messageBox = document.getElementById('bookTableMessage');
  var toastStack = document.getElementById('cartToastStack');

  var selectedTableNo = null;

  var SUBTITLE_BOOKING =
    'Choose your party size, pick an available table, and confirm your reservation in seconds.';
  var SUBTITLE_ACTIVE =
    'Your table is reserved. Browse the menu when you are ready.';

  function clampGuests(value) {
    var guests = parseInt(value, 10);
    if (isNaN(guests) || guests < 1) {
      guests = 1;
    }
    return Math.min(guests, maxGuests);
  }

  function showToast(message, type, title) {
    if (!toastStack) {
      showInlineMessage(message, type);
      return;
    }

    var toast = document.createElement('div');
    toast.className = 'cart-toast cart-toast--' + (type || 'success');
    toast.innerHTML =
      '<span class="cart-toast__icon">' + (type === 'error' ? '!' : '\u2713') + '</span>' +
      '<div class="cart-toast__body">' +
        '<p class="cart-toast__title">' + (title || (type === 'error' ? 'Booking failed' : 'Booking confirmed')) + '</p>' +
        '<p class="cart-toast__message"></p>' +
      '</div>';
    toast.querySelector('.cart-toast__message').textContent = message;
    toastStack.appendChild(toast);

    window.setTimeout(function () {
      toast.classList.add('is-leaving');
      window.setTimeout(function () {
        toast.remove();
      }, 250);
    }, 3800);
  }

  function showInlineMessage(message, type) {
    if (!messageBox) {
      return;
    }
    messageBox.textContent = message;
    messageBox.className = 'book-table-message is-visible book-table-message--' + (type || 'success');
  }

  function renderActiveReservation(booking) {
    if (!activeReservation || !booking || !booking.table_no) {
      return;
    }

    var tableNoEl = activeReservation.querySelector('[data-field="table-no"]');
    var capacityEl = activeReservation.querySelector('[data-field="capacity"]');
    var guestsEl = activeReservation.querySelector('[data-field="guests"]');
    var statusEl = activeReservation.querySelector('[data-field="status"]');

    if (tableNoEl) {
      tableNoEl.textContent = String(booking.table_no);
    }
    if (guestsEl) {
      guestsEl.textContent = booking.guests + ' guest' + (booking.guests === 1 ? '' : 's');
    }
    if (statusEl) {
      statusEl.textContent = booking.status
        ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1)
        : 'Confirmed';
    }
    if (capacityEl) {
      capacityEl.textContent = booking.capacity ? booking.capacity + ' seats' : '';
    }
  }

  function setViewMode(active, booking) {
    hasActiveBooking = active;
    section.dataset.hasActiveBooking = active ? '1' : '0';

    if (bookingFlow) {
      bookingFlow.hidden = active;
    }
    if (activeReservation) {
      activeReservation.hidden = !active;
    }
    if (subtitle) {
      subtitle.textContent = active ? SUBTITLE_ACTIVE : SUBTITLE_BOOKING;
    }

    if (active && booking) {
      renderActiveReservation(booking);
    }
  }

  function renderTables(tables) {
    if (!tableGrid || hasActiveBooking) {
      return;
    }

    tableGrid.classList.remove('is-loading');
    tableGrid.innerHTML = '';

    if (!tables || !tables.length) {
      tableGrid.innerHTML =
        '<div class="book-table-empty">' +
          '<p class="book-table-empty__title">No tables found</p>' +
          '<p class="book-table-empty__text">Please contact the restaurant for assistance.</p>' +
        '</div>';
      return;
    }

    var availableCount = 0;

    tables.forEach(function (table) {
      if (table.selectable) {
        availableCount++;
      }

      var card = document.createElement('button');
      card.type = 'button';
      card.className = 'book-table-card book-table-card--' + table.status_class;
      card.dataset.tableNo = String(table.table_no);
      card.dataset.selectable = table.selectable ? '1' : '0';
      card.disabled = !table.selectable;
      card.setAttribute('aria-label', 'Table ' + table.table_no + ', seats ' + table.capacity + ', ' + table.status_label);

      if (selectedTableNo === table.table_no && table.selectable) {
        card.classList.add('is-selected');
      }

      card.innerHTML =
        '<span class="book-table-card__icon">&#127869;</span>' +
        '<p class="book-table-card__number">Table ' + table.table_no + '</p>' +
        '<p class="book-table-card__capacity">Seats ' + table.capacity + '</p>' +
        '<span class="book-table-card__badge book-table-card__badge--' + table.status_class + '">' + table.status_label + '</span>';

      if (table.selectable) {
        card.addEventListener('click', function () {
          selectedTableNo = table.table_no;
          renderTables(tables);
          showInlineMessage('Table ' + table.table_no + ' selected. Confirm your booking below.', 'success');
        });
      }

      tableGrid.appendChild(card);
    });

    if (availableCount === 0) {
      var notice = document.createElement('div');
      notice.className = 'book-table-empty';
      notice.innerHTML =
        '<p class="book-table-empty__title">No tables available</p>' +
        '<p class="book-table-empty__text">Try a different guest count.</p>';
      tableGrid.appendChild(notice);
    }
  }

  function setLoading(loading) {
    if (hasActiveBooking) {
      return;
    }
    if (tableGrid) {
      tableGrid.classList.toggle('is-loading', loading);
    }
    if (bookBtn) {
      bookBtn.disabled = loading || isSubmitting || !isLoggedIn;
      bookBtn.classList.toggle('is-loading', isSubmitting);
    }
    if (refreshBtn) {
      refreshBtn.disabled = loading;
    }
  }

  async function loadTables() {
    if (hasActiveBooking) {
      return;
    }

    var guests = clampGuests(guestInput.value);
    guestInput.value = guests;
    setLoading(true);

    try {
      var response = await fetch(API_URL + '?action=tables&guests=' + guests, {
        credentials: 'same-origin'
      });
      var data = await response.json();

      if (!data.success) {
        throw new Error(data.message || 'Could not load tables.');
      }

      if (data.has_active_booking && data.booking) {
        setViewMode(true, data.booking);
        return;
      }

      setViewMode(false);
      renderTables(data.tables);
    } catch (error) {
      showToast(error.message || 'Could not load tables.', 'error');
    } finally {
      setLoading(false);
    }
  }

  async function submitBooking() {
    if (hasActiveBooking) {
      showToast('You already have an active table reservation.', 'error');
      return;
    }

    if (!isLoggedIn || isSubmitting) {
      if (!isLoggedIn) {
        showToast('Please log in to book a table.', 'error');
      }
      return;
    }

    if (!selectedTableNo) {
      showToast('Please select an available table from the floor plan.', 'error');
      return;
    }

    isSubmitting = true;
    setLoading(true);

    try {
      var body = new FormData();
      body.append('action', 'book');
      body.append('table_no', String(selectedTableNo));
      body.append('guests', String(clampGuests(guestInput.value)));

      var response = await fetch(API_URL, {
        method: 'POST',
        body: body,
        credentials: 'same-origin'
      });
      var data = await response.json();

      if (!data.success) {
        if (data.has_active_booking && data.booking) {
          setViewMode(true, data.booking);
        }
        throw new Error(data.message || 'Booking failed.');
      }

      selectedTableNo = null;
      setViewMode(true, data.booking);
      showToast(data.message, 'success', 'Table booked');
    } catch (error) {
      showToast(error.message || 'Booking failed.', 'error');
      if (!hasActiveBooking) {
        showInlineMessage(error.message || 'Booking failed.', 'error');
        await loadTables();
      }
    } finally {
      isSubmitting = false;
      setLoading(false);
    }
  }

  if (guestDecrease) {
    guestDecrease.addEventListener('click', function () {
      guestInput.value = clampGuests(parseInt(guestInput.value, 10) - 1);
      selectedTableNo = null;
      loadTables();
    });
  }

  if (guestIncrease) {
    guestIncrease.addEventListener('click', function () {
      guestInput.value = clampGuests(parseInt(guestInput.value, 10) + 1);
      selectedTableNo = null;
      loadTables();
    });
  }

  if (guestInput) {
    guestInput.addEventListener('change', function () {
      guestInput.value = clampGuests(guestInput.value);
      selectedTableNo = null;
      loadTables();
    });
  }

  if (refreshBtn) {
    refreshBtn.addEventListener('click', loadTables);
  }

  if (bookBtn) {
    bookBtn.addEventListener('click', submitBooking);
  }

  if (!hasActiveBooking) {
    loadTables();
  }
})();
