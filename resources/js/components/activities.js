let calendar;
let selectedColor = '#3b82f6';
let events = [];

async function fetchEvents() {
    try {
        // const response = await fetch('/api/activities');
        // if (!response.ok) throw new Error('Network response was not ok');
        // const data = await response.json();
        const data = window.activities;
        events = data.map(activities => ({
            id: activities.id,
            title: activities.title,
            start: activities.start,
            end: activities.end,
            backgroundColor: activities.color || '#3b82f6',
            extendedProps: {
                note: activities.note || '',
                type: activities.type || ''
            }
        }));
        if (calendar) {
            calendar.removeAllEvents();
            calendar.addEventSource(events);
            calendar.render(); 
        }
    } catch (error) {
        console.error('Error fetching activities:', error);
        showNotification('Lỗi tải sự kiện!', 'error');
    }
}

// Fetch activities before initializing calendar
document.addEventListener('DOMContentLoaded', function() {
    fetchEvents();
});

document.addEventListener('DOMContentLoaded', async function() {
    const calendarEl = document.getElementById('calendar');
    await fetchEvents();

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: false,
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '01:00:00', 
        height: 'auto',
        locale: 'vi',
        firstDay: 1,
        dayHeaderFormat: {
            weekday: 'long',
            day: 'numeric',
            month: 'numeric'
        },
        events: events, 
        editable: true,
        droppable: true,
        eventResizableFromStart: true,
        eventClick: function(info) {
            editEvent(info.event);
        },
        eventDrop: function(info) {
            updateEvent(info.event);
        },
        eventResize: function(info) {
            updateEvent(info.event);
        },
        dateClick: function(info) {
            openEventModal(info.dateStr);
        }
    });

    calendar.render();

    const today = new Date();
    document.getElementById('currentDate').textContent = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();

    document.getElementById('eventDate').value = today.toISOString().split('T')[0];
    document.getElementById('startTime').value = '09:00';
    document.getElementById('endTime').value = '11:00';
});

// Color picker functionality
document.querySelectorAll('.color-option').forEach(function(el) {
    el.addEventListener('click', function() {
        document.querySelectorAll('.color-option').forEach(function(opt) {
            opt.classList.remove('selected');
        });
        el.classList.add('selected');
        document.getElementById('eventColor').value = el.getAttribute('data-color');
    });
});

document.getElementById('isRecurring').addEventListener('change', function() {
    const options = document.getElementById('recurringOptions');
    options.style.display = this.checked ? 'block' : 'none';
});

// Open modal for new event
function openEventModal(dateStr) {
    if (dateStr) {
        document.getElementById('eventDate').value = dateStr.split('T')[0];
        if (dateStr.includes('T')) {
            const time = dateStr.split('T')[1].substring(0, 5);
            document.getElementById('startTime').value = time;
            const endTime = new Date(`2000-01-01 ${time}`);
            endTime.setHours(endTime.getHours() + 1);
            document.getElementById('endTime').value = endTime.toTimeString().substring(0, 5);
        }
    }
    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

document.getElementById('saveEvent').addEventListener('click', function () {
    const title = document.getElementById('eventTitle').value;
    const date = document.getElementById('eventDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const type = document.getElementById('eventType').value;
    const note = document.getElementById('eventNote').value;
    const isRecurring = document.getElementById('isRecurring').checked;

    const formAddAvtivity = document.querySelector('#eventForm');

    if (!title || !date || !startTime || !endTime) {
        alert('Vui lòng điền đầy đủ thông tin!');
        return;
    }

    const newEvent = {
        id: Date.now().toString(),
        title: title,
        start: `${date}T${startTime}:00`,
        end: `${date}T${endTime}:00`,
        className: type,
        backgroundColor: selectedColor,
        extendedProps: {
            note: note,
            type: type
        }
    };

    events.push(newEvent);
    calendar.addEvent(newEvent);

    formAddAvtivity.submit();

    if (isRecurring) {
        const recurringType = document.getElementById('recurringType').value;
        createRecurringEvents(newEvent, recurringType);
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
    modal.hide();
    document.getElementById('eventForm').reset();
    document.getElementById('eventDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('startTime').value = '09:00';
    document.getElementById('endTime').value = '10:00';

    showNotification('Đã thêm sự kiện thành công!', 'success');
});

function createRecurringEvents(baseEvent, type) {
    const startDate = new Date(baseEvent.start);
    const endDate = new Date(baseEvent.end);
    let increment;

    switch (type) {
        case 'daily':
            increment = 1;
            break;
        case 'weekly':
            increment = 7;
            break;
        case 'monthly':
            increment = 30;
            break;
        default:
            increment = 7;
    }

    for (let i = 1; i <= 10; i++) {
        const newStartDate = new Date(startDate);
        const newEndDate = new Date(endDate);

        if (type === 'monthly') {
            newStartDate.setMonth(newStartDate.getMonth() + i);
            newEndDate.setMonth(newEndDate.getMonth() + i);
        } else {
            newStartDate.setDate(newStartDate.getDate() + (increment * i));
            newEndDate.setDate(newEndDate.getDate() + (increment * i));
        }

        const recurringEvent = {
            id: Date.now().toString() + '_' + i,
            title: baseEvent.title,
            start: newStartDate.toISOString(),
            end: newEndDate.toISOString(),
            className: baseEvent.className,
            backgroundColor: baseEvent.backgroundColor,
            extendedProps: baseEvent.extendedProps
        };

        events.push(recurringEvent);
        calendar.addEvent(recurringEvent);
    }
}

// Edit existing event
function editEvent(event) {
    document.getElementById('eventTitle').value = event.title;
    document.getElementById('eventDate').value = event.start.toISOString().split('T')[0];
    document.getElementById('startTime').value = event.start.toTimeString().substring(0, 5);
    document.getElementById('endTime').value = event.end.toTimeString().substring(0, 5);
    document.getElementById('eventType').value = event.extendedProps.type || 'study';
    document.getElementById('eventNote').value = event.extendedProps.note || '';

    // Update color picker
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    const colorOption = document.querySelector(`[data-color="${event.backgroundColor}"]`);
    if (colorOption) {
        colorOption.classList.add('selected');
        selectedColor = event.backgroundColor;
    }

    // Store event ID for updating
    document.getElementById('saveEvent').dataset.eventId = event.id;
    document.querySelector('.modal-title').innerHTML = '<i class="fas fa-edit me-2"></i>Chỉnh sửa sự kiện';

    const modal = new bootstrap.Modal(document.getElementById('eventModal'));
    modal.show();
}

// Update existing event
function updateEvent(event) {
    const eventIndex = events.findIndex(e => e.id === event.id);
    if (eventIndex !== -1) {
        events[eventIndex] = {
            id: event.id,
            title: event.title,
            start: event.start.toISOString(),
            end: event.end.toISOString(),
            className: event.className,
            backgroundColor: event.backgroundColor,
            extendedProps: event.extendedProps
        };
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Navigation functions
function previousWeek() {
    calendar.prev();
    updateDateDisplay();
}

function nextWeek() {
    calendar.next();
    updateDateDisplay();
}

function updateDateDisplay() {
    const currentDate = calendar.getDate();
    const formattedDate = currentDate.getDate() + '/' + (currentDate.getMonth() + 1) + '/' + currentDate.getFullYear();
    document.getElementById('currentDate').textContent = formattedDate;
}

// Add navigation event listeners
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fa-chevron-left')) {
        previousWeek();
    } else if (e.target.classList.contains('fa-chevron-right')) {
        nextWeek();
    }
});

// Reset modal when closed
document.getElementById('eventModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('eventForm').reset();
    document.getElementById('saveEvent').removeAttribute('data-event-id');
    document.querySelector('.modal-title').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Sự kiện mới';
    document.getElementById('recurringOptions').style.display = 'none';

    // Reset color picker
    document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
    document.querySelector('.color-option.blue').classList.add('selected');
    selectedColor = '#3b82f6';

    // Reset default values
    const today = new Date();
    document.getElementById('eventDate').value = today.toISOString().split('T')[0];
    document.getElementById('startTime').value = '09:00';
    document.getElementById('endTime').value = '10:00';
});

// Handle view change
document.querySelector('select').addEventListener('change', function() {
    switch (this.value) {
        case 'Ngày':
            calendar.changeView('timeGridDay');
            break;
        case 'Tuần':
            calendar.changeView('timeGridWeek');
            break;
        case 'Tháng':
            calendar.changeView('dayGridMonth');
            break;
    }
});

// Navigation
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.menu-item');
    const currentUrl = window.location.pathname; 

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentUrl) {
            link.classList.add('active');
        }
        link.addEventListener('click', function(e) {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N for new event
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openEventModal();
    }

    // Arrow keys for navigation
    if (e.key === 'ArrowLeft' && e.altKey) {
        e.preventDefault();
        previousWeek();
    } else if (e.key === 'ArrowRight' && e.altKey) {
        e.preventDefault();
        nextWeek();
    }
});

// Double click to create event
document.getElementById('calendar').addEventListener('dblclick', function(e) {
    const rect = this.getBoundingClientRect();
    const y = e.clientY - rect.top;
    const hour = Math.floor((y - 60) / 60) + 7; 

    if (hour >= 7 && hour <= 22) {
        const today = calendar.getDate();
        const dateStr = today.toISOString().split('T')[0];
        const timeStr = hour.toString().padStart(2, '0') + ':00';
        openEventModal(`${dateStr}T${timeStr}`);
    }
});

calendar.on('eventReceive', function(info) {
    showNotification('Đã di chuyển sự kiện thành công!', 'success');
});

calendar.on('eventDrop', function(info) {
    showNotification('Đã cập nhật thời gian sự kiện!', 'success');
});

calendar.on('eventResize', function(info) {
    showNotification('Đã thay đổi thời lượng sự kiện!', 'success');
});

function searchEvents(query) {
    const filteredEvents = events.filter(event => 
        event.title.toLowerCase().includes(query.toLowerCase()) ||
        (event.extendedProps.note && event.extendedProps.note.toLowerCase().includes(query.toLowerCase()))
    );

    calendar.removeAllEvents();
    calendar.addEventSource(filteredEvents);
}

function exportCalendar() {
    const dataStr = JSON.stringify(events, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'calendar_export.json';
    link.click();
}

function importCalendar(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const importedEvents = JSON.parse(e.target.result);
            events = [...events, ...importedEvents];
            calendar.removeAllEvents();
            calendar.addEventSource(events);
            showNotification('Đã import lịch thành công!', 'success');
        } catch (error) {
            showNotification('Lỗi import file!', 'error');
        }
    };
    reader.readAsText(file);
}