@extends('layouts.admin')
@section('page-title', 'Booking Calendar')
@section('main-content')

{{-- Controls --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <div class="flex items-center gap-2">
        <button id="prevMonth" class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h2 id="monthLabel" class="text-xl font-bold text-gray-800 w-44 text-center"></h2>
        <button id="nextMonth" class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-chevron-right"></i>
        </button>
        <button id="todayBtn" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">Today</button>
    </div>

    <div class="flex items-center gap-3 flex-wrap">
        {{-- Filter --}}
        <select id="statusFilter" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">All Statuses</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
        </select>
        <select id="typeFilter" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">All Types</option>
            <option value="venue">Venues</option>
            <option value="suite">Suites</option>
        </select>
        <button onclick="printCalendar()" class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
            <i class="fas fa-print mr-1"></i>Print
        </button>
        <button onclick="exportCSV()" class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
            <i class="fas fa-file-csv mr-1"></i>Export
        </button>
    </div>
</div>

{{-- Occupancy Stats --}}
<div class="grid grid-cols-4 gap-4 mb-4" id="statsBar">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-purple-600" id="statTotal">-</div>
        <div class="text-xs text-gray-500 mt-1">Total Venues</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-red-500" id="statOccupied">-</div>
        <div class="text-xs text-gray-500 mt-1">Occupied Days</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-green-500" id="statAvailable">-</div>
        <div class="text-xs text-gray-500 mt-1">Available Days</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-2xl font-bold text-blue-500" id="statRate">-</div>
        <div class="text-xs text-gray-500 mt-1">Occupancy Rate</div>
    </div>
</div>

{{-- Legend --}}
<div class="flex flex-wrap gap-3 mb-4 bg-white rounded-lg shadow px-4 py-3">
    <span class="text-xs font-semibold text-gray-500 mr-2 self-center">Legend:</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-green-500 inline-block"></span> Confirmed</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-purple-500 inline-block"></span> Completed</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-yellow-400 inline-block"></span> Pending</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-gray-200 inline-block"></span> Available</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-300 inline-block"></span> Today</span>
    <span class="flex items-center gap-1 text-xs"><span class="w-3 h-3 rounded bg-gray-100 inline-block"></span> Past</span>
</div>

{{-- Notification --}}
<div id="notification" class="hidden mb-3 px-4 py-2 rounded-lg text-sm font-medium"></div>

{{-- Calendar Table --}}
<div class="bg-white rounded-lg shadow overflow-x-auto" id="calendarWrap">
    <div id="calendarLoading" class="py-20 text-center text-gray-400">
        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
        <p>Loading calendar...</p>
    </div>
    <table id="calendarTable" class="min-w-full hidden text-xs border-collapse">
        <thead id="calendarHead" class="bg-gray-50 sticky top-0 z-10"></thead>
        <tbody id="calendarBody"></tbody>
    </table>
</div>

{{-- Booking Detail Modal --}}
<div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800 text-base" id="modalTitle">Booking Details</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <div id="modalBody" class="px-6 py-4 space-y-3 text-sm"></div>
        <div class="px-6 py-4 border-t flex justify-end gap-2">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Close</button>
            <a id="modalViewBtn" href="#" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">View Full Booking</a>
        </div>
    </div>
</div>

<script>
let currentYear = {{ date('Y') }};
let currentMonth = {{ date('n') }};
let calendarData = null;
let statusFilter = '';
let typeFilter = '';

const statusColors = {
    confirmed: 'bg-green-500 text-white',
    completed: 'bg-purple-500 text-white',
    pending:   'bg-yellow-400 text-gray-800',
};

function showNotification(msg, type = 'info') {
    const el = document.getElementById('notification');
    el.className = `mb-3 px-4 py-2 rounded-lg text-sm font-medium ${type === 'error' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'}`;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}

async function loadCalendar() {
    document.getElementById('calendarLoading').classList.remove('hidden');
    document.getElementById('calendarTable').classList.add('hidden');

    try {
        const res = await fetch(`{{ route('admin.bookings.calendar.data') }}?year=${currentYear}&month=${currentMonth}`);
        calendarData = await res.json();
        renderCalendar();
    } catch (e) {
        showNotification('Failed to load calendar data.', 'error');
    }
}

function renderCalendar() {
    const data = calendarData;
    document.getElementById('monthLabel').textContent = data.month_name;

    // Stats
    document.getElementById('statTotal').textContent    = data.stats.total_venues;
    document.getElementById('statOccupied').textContent = data.stats.occupied;
    document.getElementById('statAvailable').textContent = data.stats.available;
    document.getElementById('statRate').textContent     = data.stats.occupancy_rate + '%';

    // Filter venues
    const venues = data.venues.filter(v => !typeFilter || v.type === typeFilter);

    // Build header
    const head = document.getElementById('calendarHead');
    head.innerHTML = '';
    let hRow = '<tr><th class="px-3 py-2 text-left text-gray-600 font-semibold border-b border-r bg-gray-50 sticky left-0 z-20 min-w-32">Venue</th>';
    data.days.forEach(d => {
        const todayCls = d.is_today ? 'bg-blue-50 text-blue-700' : d.is_weekend ? 'bg-gray-100' : '';
        hRow += `<th class="px-2 py-2 text-center border-b border-r min-w-20 ${todayCls}">
            <div class="font-bold">${d.day}</div>
            <div class="text-gray-400 font-normal">${d.day_name}</div>
        </th>`;
    });
    head.innerHTML = hRow + '</tr>';

    // Build body
    const body = document.getElementById('calendarBody');
    body.innerHTML = '';

    venues.forEach(venue => {
        let row = `<tr class="hover:bg-gray-50 transition">
            <td class="px-3 py-2 border-b border-r sticky left-0 bg-white z-10 min-w-32">
                <div class="font-semibold text-gray-800 text-xs">${venue.name}</div>
                <div class="text-gray-400 text-xs capitalize">${venue.type} · ${venue.capacity} pax</div>
            </td>`;

        data.days.forEach(d => {
            const bookings = (d.venues[venue.id] || []).filter(b => !statusFilter || b.status === statusFilter);
            const todayCls = d.is_today ? 'bg-blue-50' : d.is_past ? 'bg-gray-50' : '';

            if (bookings.length === 0) {
                const isPast = d.is_past;
                if (!isPast) {
                    row += `<td class="px-1 py-1 border-b border-r text-center ${todayCls} group cursor-pointer hover:bg-green-50 transition"
                        onclick="openCreateBooking('${d.date}', ${venue.id}, '${venue.name.replace(/'/g, "\\'")}')">
                        <span class="text-gray-200 group-hover:hidden text-xs">—</span>
                        <span class="hidden group-hover:inline-flex items-center justify-center w-full">
                            <span class="bg-green-500 text-white text-xs rounded px-1.5 py-0.5 font-semibold whitespace-nowrap">
                                <i class="fas fa-plus text-xs mr-0.5"></i>Book
                            </span>
                        </span>
                    </td>`;
                } else {
                    row += `<td class="px-1 py-1 border-b border-r text-center ${todayCls}">
                        <span class="text-gray-300 text-xs">—</span>
                    </td>`;
                }
            } else {
                let cells = bookings.map(b => {
                    const cls = statusColors[b.status] || 'bg-gray-300 text-gray-800';
                    return `<div class="${cls} rounded px-1 py-0.5 mb-0.5 cursor-pointer truncate max-w-20 text-xs leading-tight"
                        title="${b.client_name} · ${b.reference}"
                        onclick='openModal(${JSON.stringify(b)})'>
                        ${b.client_name.split(' ')[0]}
                    </div>`;
                }).join('');
                row += `<td class="px-1 py-1 border-b border-r ${todayCls}">${cells}</td>`;
            }
        });

        body.innerHTML += row + '</tr>';
    });

    document.getElementById('calendarLoading').classList.add('hidden');
    document.getElementById('calendarTable').classList.remove('hidden');
}

function openModal(b) {
    document.getElementById('modalTitle').textContent = b.reference;
    document.getElementById('modalViewBtn').href = `/admin/bookings/${b.id}`;

    const statusBadge = {
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-purple-100 text-purple-800',
        pending:   'bg-yellow-100 text-yellow-800',
    }[b.status] || 'bg-gray-100 text-gray-800';

    const payBadge = {
        paid:    'bg-green-100 text-green-800',
        partial: 'bg-yellow-100 text-yellow-800',
        unpaid:  'bg-red-100 text-red-800',
    }[b.payment_status] || 'bg-gray-100 text-gray-800';

    document.getElementById('modalBody').innerHTML = `
        <div class="flex justify-between">
            <span class="text-gray-500">Client</span>
            <span class="font-semibold">${b.client_name}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Email</span>
            <span>${b.client_email}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Phone</span>
            <span>${b.client_phone}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Date</span>
            <span>${b.booking_date}${b.number_of_days > 1 ? ' → ' + b.end_date : ''}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Time Slot</span>
            <span>${b.time_slots}</span>
        </div>
        ${b.package ? `<div class="flex justify-between"><span class="text-gray-500">Package</span><span>${b.package}</span></div>` : ''}
        <div class="flex justify-between">
            <span class="text-gray-500">Amount</span>
            <span class="font-semibold">₱${parseFloat(b.total_amount).toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Status</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${statusBadge}">${b.status.charAt(0).toUpperCase()+b.status.slice(1)}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-500">Payment</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${payBadge}">${b.payment_status.charAt(0).toUpperCase()+b.payment_status.slice(1)}</span>
        </div>
    `;
    document.getElementById('bookingModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

function printCalendar() {
    window.print();
}

function exportCSV() {
    if (!calendarData) return;
    const rows = [['Reference','Client','Email','Phone','Venue','Date','End Date','Days','Time Slot','Package','Amount','Status','Payment']];
    calendarData.days.forEach(d => {
        calendarData.venues.forEach(v => {
            (d.venues[v.id] || []).forEach(b => {
                rows.push([b.reference, b.client_name, b.client_email, b.client_phone, v.name,
                    b.booking_date, b.end_date, b.number_of_days, b.time_slots,
                    b.package || '', b.total_amount, b.status, b.payment_status]);
            });
        });
    });
    const csv = rows.map(r => r.map(c => `"${c}"`).join(',')).join('\n');
    const a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv);
    a.download = `bookings-${calendarData.month_name.replace(' ','-')}.csv`;
    a.click();
    showNotification('CSV exported successfully.');
}

document.getElementById('prevMonth').addEventListener('click', () => {
    const d = new Date(currentYear, currentMonth - 2);
    currentYear = d.getFullYear(); currentMonth = d.getMonth() + 1;
    loadCalendar();
});
document.getElementById('nextMonth').addEventListener('click', () => {
    const d = new Date(currentYear, currentMonth);
    currentYear = d.getFullYear(); currentMonth = d.getMonth() + 1;
    loadCalendar();
});
document.getElementById('todayBtn').addEventListener('click', () => {
    currentYear = {{ date('Y') }}; currentMonth = {{ date('n') }};
    loadCalendar();
});
document.getElementById('statusFilter').addEventListener('change', e => { statusFilter = e.target.value; if (calendarData) renderCalendar(); });
document.getElementById('typeFilter').addEventListener('change', e => { typeFilter = e.target.value; if (calendarData) renderCalendar(); });
document.getElementById('bookingModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });

function openCreateBooking(date, venueId, venueName) {
    const url = `{{ route('admin.bookings.create') }}?booking_date=${date}&venue_id=${venueId}`;
    window.location.href = url;
}

loadCalendar();
</script>

<style>
@media print {
    .sidebar, nav, button, select, #statsBar, #notification { display: none !important; }
    #calendarWrap { overflow: visible !important; }
}
</style>
@endsection
