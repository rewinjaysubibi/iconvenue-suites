<script>
// Global variables
let selectedPricing = null;
let selectedAddons = {};
let venueType = '{{ $venue->type }}';

// Check whether a given date string (YYYY-MM-DD) is today in local time
function isDateToday(dateStr) {
    const now = new Date();
    const todayStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}`;
    return dateStr === todayStr;
}

// Image Carousel Functions
let carouselIntervals = {};

function nextSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const thumbs = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-thumb`);
    let currentIndex = -1;
    items.forEach((item, index) => {
        if (item.classList.contains('opacity-100')) {
            currentIndex = index;
            item.classList.remove('opacity-100');
            item.classList.add('opacity-0');
        }
    });
    const nextIndex = (currentIndex + 1) % items.length;
    items[nextIndex].classList.remove('opacity-0');
    items[nextIndex].classList.add('opacity-100');
    updateThumbnails(thumbs, nextIndex);
    updateCounter(nextIndex + 1);
}

function prevSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const thumbs = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-thumb`);
    let currentIndex = -1;
    items.forEach((item, index) => {
        if (item.classList.contains('opacity-100')) {
            currentIndex = index;
            item.classList.remove('opacity-100');
            item.classList.add('opacity-0');
        }
    });
    const prevIndex = currentIndex === 0 ? items.length - 1 : currentIndex - 1;
    items[prevIndex].classList.remove('opacity-0');
    items[prevIndex].classList.add('opacity-100');
    updateThumbnails(thumbs, prevIndex);
    updateCounter(prevIndex + 1);
}

function goToSlide(carouselId, index) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    const thumbs = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-thumb`);
    items.forEach(item => {
        item.classList.remove('opacity-100');
        item.classList.add('opacity-0');
    });
    items[index].classList.remove('opacity-0');
    items[index].classList.add('opacity-100');
    updateThumbnails(thumbs, index);
    updateCounter(index + 1);
}

function updateThumbnails(thumbs, activeIndex) {
    thumbs.forEach((thumb, index) => {
        if (index === activeIndex) {
            thumb.classList.add('border-white', 'scale-110');
            thumb.classList.remove('border-white/30', 'opacity-60');
        } else {
            thumb.classList.remove('border-white', 'scale-110');
            thumb.classList.add('border-white/30', 'opacity-60');
        }
    });
}

function updateCounter(current) {
    const counter = document.querySelector('.current-slide');
    if (counter) counter.textContent = current;
}

// Track selected time slots for multi-select
let selectedTimeSlots = {}; // { morning: price, afternoon: price, evening: price }

// Pricing Selection Functions
function selectPricingOption(element) {
    const type = element.dataset.type;
    const timeSlotTypes = ['morning', 'afternoon', 'evening'];

    // --- Multi-select logic for time slots ---
    if (timeSlotTypes.includes(type)) {
        const isSelected = element.classList.contains('selected');

        if (isSelected) {
            // Check if deselecting this slot would leave morning+evening combo
            const afterRemoval = Object.keys(selectedTimeSlots).filter(s => s !== type);
            if (afterRemoval.includes('morning') && afterRemoval.includes('evening') && !afterRemoval.includes('afternoon')) {
                alert('Invalid combination. Removing this slot would leave Morning + Evening selected, which is not allowed. Please deselect Morning or Evening first.');
                return;
            }

            // Deselect this slot
            element.classList.remove('selected', 'ring-4', 'ring-blue-300', 'ring-orange-300', 'ring-indigo-300');
            const radio = element.querySelector('.pricing-radio div');
            if (radio) radio.style.opacity = '0';
            delete selectedTimeSlots[type];
        } else {
            // Validate combo before selecting
            const currentSlots = Object.keys(selectedTimeSlots);

            // Block morning+evening combo
            if (type === 'evening' && currentSlots.includes('morning') && !currentSlots.includes('afternoon')) {
                alert('Invalid combination. You cannot select Morning and Evening together. Please select Morning + Afternoon or Afternoon + Evening.');
                return;
            }
            if (type === 'morning' && currentSlots.includes('evening') && !currentSlots.includes('afternoon')) {
                alert('Invalid combination. You cannot select Morning and Evening together. Please select Morning + Afternoon or Afternoon + Evening.');
                return;
            }

            // Deselect full-day and package if switching to time slots
            document.querySelectorAll('.pricing-option').forEach(opt => {
                if (!timeSlotTypes.includes(opt.dataset.type)) {
                    opt.classList.remove('selected', 'ring-4', 'ring-purple-300');
                    const r = opt.querySelector('.pricing-radio div');
                    if (r) r.style.opacity = '0';
                }
            });
            document.getElementById('packageTimeSlotSection').classList.add('hidden');

            // Select this slot
            element.classList.add('selected');
            const radio = element.querySelector('.pricing-radio div');
            if (radio) radio.style.opacity = '1';
            if (type === 'morning') element.classList.add('ring-4', 'ring-blue-300');
            else if (type === 'afternoon') element.classList.add('ring-4', 'ring-orange-300');
            else if (type === 'evening') element.classList.add('ring-4', 'ring-indigo-300');

            selectedTimeSlots[type] = parseFloat(element.dataset.price);
        }

        const slots = Object.keys(selectedTimeSlots);
        const fullDayPrice = {{ $venue->price_per_day ?? 0 }};

        if (slots.length === 0) {
            selectedPricing = null;
            if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'none';
            document.getElementById('priceSummary').style.display = 'none';
            return;
        }

        // 3 slots = full day
        if (slots.length === 3) {
            selectedPricing = {
                type: 'full-day',
                price: fullDayPrice,
                name: 'Full Day (Morning + Afternoon + Evening)',
                packageId: null,
                timeSlot: 'full-day'
            };
        } else {
            const totalPrice = Object.values(selectedTimeSlots).reduce((a, b) => a + b, 0);
            const slotNames = slots.map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(' + ');
            selectedPricing = {
                type: slots.length === 1 ? slots[0] : 'multiple',
                price: totalPrice,
                name: slotNames + ' Slot' + (slots.length > 1 ? 's' : ''),
                packageId: null,
                timeSlot: slots.join(',')
            };
        }

        if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'block';
        updatePriceSummary();
        return;
    }

    // --- Single-select logic for full-day and packages ---
    const isAlreadySelected = element.classList.contains('selected');

    // Deselect all time slots when picking full-day or package
    document.querySelectorAll('.pricing-option').forEach(option => {
        option.classList.remove('selected', 'ring-4', 'ring-purple-300', 'ring-blue-300', 'ring-orange-300', 'ring-indigo-300');
        const radio = option.querySelector('.pricing-radio div');
        if (radio) radio.style.opacity = '0';
    });
    selectedTimeSlots = {};

    if (isAlreadySelected) {
        selectedPricing = null;
        document.getElementById('packageTimeSlotSection').classList.add('hidden');
        if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'none';
        document.getElementById('priceSummary').style.display = 'none';
        return;
    }

    element.classList.add('selected');
    const radio = element.querySelector('.pricing-radio div');
    if (radio) radio.style.opacity = '1';
    element.classList.add('ring-4', 'ring-purple-300');

    if (type === 'package') {
        showPackageTimeSlots(element);
        selectedPricing = null;
        if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'none';
        document.getElementById('priceSummary').style.display = 'none';
    } else {
        // full-day
        document.getElementById('packageTimeSlotSection').classList.add('hidden');
        selectedPricing = {
            type: type,
            price: parseFloat(element.dataset.price),
            name: element.dataset.name,
            packageId: null,
            timeSlot: null
        };
        if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'block';
        updatePriceSummary();
    }
}

function showPackageTimeSlots(packageElement) {
    const timeSlotSection = document.getElementById('packageTimeSlotSection');
    const timeSlotOptions = document.getElementById('packageTimeSlotOptions');
    const packageId = packageElement.dataset.packageId;
    const packageName = packageElement.dataset.name;
    const priceMorning = parseFloat(packageElement.dataset.priceMorning);
    const priceAfternoon = parseFloat(packageElement.dataset.priceAfternoon);
    const priceEvening = parseFloat(packageElement.dataset.priceEvening);
    const priceFullDay = parseFloat(packageElement.dataset.price);

    function slotHtml(slotKey, price, colorFrom, colorTo, borderColor, hoverBorder, ring, icon, iconColor, label, timeLabel) {
        return `<div class="time-slot-option bg-gradient-to-br ${colorFrom} ${colorTo} border-2 ${borderColor} rounded-xl p-4 cursor-pointer hover:scale-105 hover:${hoverBorder} transition-all duration-300 transform" data-time-slot="${slotKey}" data-price="${price}" data-package-id="${packageId}" data-package-name="${packageName}"><div class="flex items-center mb-3"><div class="w-5 h-5 border-2 ${ring} rounded-full mr-3 flex items-center justify-center time-slot-radio"><div class="w-2.5 h-2.5 ${iconColor} rounded-full opacity-0 transition-opacity duration-200"></div></div><i class="fas ${icon} ${iconColor} text-xl mr-2"></i><h5 class="font-semibold text-gray-800">${label}</h5></div><p class="text-sm text-gray-600 mb-2">${timeLabel}</p><div class="text-2xl font-bold ${iconColor}">₱${price.toLocaleString()}</div></div>`;
    }

    let html = '';
    if (priceMorning > 0) {
        html += slotHtml('morning', priceMorning, 'from-blue-50', 'to-cyan-50', 'border-blue-200', 'border-blue-400', 'border-blue-400', 'fa-sun', 'text-blue-600', 'Morning', '8:00 AM - 12:00 PM');
    }
    if (priceAfternoon > 0) {
        html += slotHtml('afternoon', priceAfternoon, 'from-orange-50', 'to-yellow-50', 'border-orange-200', 'border-orange-400', 'border-orange-400', 'fa-cloud-sun', 'text-orange-600', 'Afternoon', '1:00 PM - 5:00 PM');
    }
    if (priceEvening > 0) {
        html += slotHtml('evening', priceEvening, 'from-indigo-50', 'to-purple-50', 'border-indigo-200', 'border-indigo-400', 'border-indigo-400', 'fa-moon', 'text-indigo-600', 'Evening', '6:00 PM - 10:00 PM');
    }

    html += `<div class="time-slot-option bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-4 cursor-pointer hover:border-purple-400 hover:scale-105 transition-all duration-300 transform" data-time-slot="full-day" data-price="${priceFullDay}" data-package-id="${packageId}" data-package-name="${packageName}"><div class="flex items-center mb-3"><div class="w-5 h-5 border-2 border-purple-400 rounded-full mr-3 flex items-center justify-center time-slot-radio"><div class="w-2.5 h-2.5 bg-purple-600 rounded-full opacity-0 transition-opacity duration-200"></div></div><i class="fas fa-calendar-day text-purple-600 text-xl mr-2"></i><h5 class="font-semibold text-gray-800">Full Day</h5></div><p class="text-sm text-gray-600 mb-2">Complete access</p><div class="text-2xl font-bold text-purple-600">₱${priceFullDay.toLocaleString()}</div></div>`;

    timeSlotOptions.innerHTML = html;
    timeSlotSection.classList.remove('hidden');
    timeSlotSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    document.querySelectorAll('.time-slot-option').forEach(option => {
        option.addEventListener('click', function() { selectTimeSlot(this); });
    });
}

function selectTimeSlot(element) {
    document.querySelectorAll('.time-slot-option').forEach(option => {
        option.classList.remove('selected', 'ring-4', 'ring-blue-300', 'ring-orange-300', 'ring-indigo-300', 'ring-purple-300');
        const radio = option.querySelector('.time-slot-radio div');
        if (radio) radio.style.opacity = '0';
    });
    element.classList.add('selected');
    const radio = element.querySelector('.time-slot-radio div');
    if (radio) radio.style.opacity = '1';
    const timeSlot = element.dataset.timeSlot;
    if (timeSlot === 'morning') element.classList.add('ring-4', 'ring-blue-300');
    else if (timeSlot === 'afternoon') element.classList.add('ring-4', 'ring-orange-300');
    else if (timeSlot === 'evening') element.classList.add('ring-4', 'ring-indigo-300');
    else element.classList.add('ring-4', 'ring-purple-300');
    const packageName = element.dataset.packageName;
    const timeSlotLabel = timeSlot.charAt(0).toUpperCase() + timeSlot.slice(1).replace('-', ' ');
    selectedPricing = {
        type: 'package',
        price: parseFloat(element.dataset.price),
        name: `${packageName} (${timeSlotLabel})`,
        packageId: element.dataset.packageId,
        timeSlot: timeSlot
    };
    if (venueType === 'venue') document.getElementById('addonsSection').style.display = 'block';
    updatePriceSummary();
}

function updatePriceSummary() {
    const priceSummary = document.getElementById('priceSummary');
    if (!selectedPricing) { priceSummary.style.display = 'none'; return; }
    priceSummary.style.display = 'block';
    document.getElementById('selectedOption').textContent = selectedPricing.name;
    document.getElementById('basePrice').textContent = '₱' + selectedPricing.price.toLocaleString();
    let addonsTotal = 0;
    let addonsHtml = '';
    Object.values(selectedAddons).forEach(addon => {
        const subtotal = addon.price * addon.quantity;
        addonsTotal += subtotal;
        addonsHtml += `<div class="flex justify-between text-sm"><span>${addon.name} (${addon.quantity}x)</span><span>₱${subtotal.toLocaleString()}</span></div>`;
    });
    const addonsBreakdown = document.getElementById('addonsBreakdown');
    if (addonsTotal > 0) {
        addonsBreakdown.style.display = 'block';
        document.getElementById('addonsList').innerHTML = addonsHtml;
    } else {
        addonsBreakdown.style.display = 'none';
    }
    document.getElementById('totalPrice').textContent = '₱' + (selectedPricing.price + addonsTotal).toLocaleString();
}

function updateContactModalSummary() {
    const summaryContainer = document.getElementById('contactModalSummary');
    if (!summaryContainer || !selectedPricing) return;
    let summaryHtml = `<div class="mb-2"><strong>Selected Option:</strong> ${selectedPricing.name}</div>`;
    summaryHtml += `<div class="mb-2"><strong>Base Price:</strong> ₱${selectedPricing.price.toLocaleString()}</div>`;
    if (Object.keys(selectedAddons).length > 0) {
        summaryHtml += `<div class="mb-2"><strong>Add-ons:</strong></div>`;
        Object.values(selectedAddons).forEach(addon => {
            summaryHtml += `<div class="ml-4 text-xs">• ${addon.name} (${addon.quantity}x): ₱${(addon.price * addon.quantity).toLocaleString()}</div>`;
        });
    }
    const total = selectedPricing.price + Object.values(selectedAddons).reduce((sum, addon) => sum + (addon.price * addon.quantity), 0);
    summaryHtml += `<div class="mt-2 font-semibold text-green-600"><strong>Total: ₱${total.toLocaleString()}</strong></div>`;
    summaryContainer.innerHTML = summaryHtml;
}

// Add-ons Functions
let addonsData = {};

function loadAddons() {
    const container = document.getElementById('addonsContainer');
    const loadBtn = document.getElementById('loadAddons');
    loadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
    loadBtn.disabled = true;
    fetch(`/venue/{{ $venue->id }}/addons-data`)
        .then(response => response.json())
        .then(data => {
            addonsData = data.addons;
            const categorySelector = document.getElementById('addon-category-selector');
            categorySelector.innerHTML = '<option value="">Choose a category...</option>';
            Object.keys(addonsData).forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = `${category.charAt(0).toUpperCase() + category.slice(1)} (${addonsData[category].length} items)`;
                categorySelector.appendChild(option);
            });
            container.style.display = 'block';
            initializeCategorySelector();
            loadBtn.style.display = 'none';
        })
        .catch(error => {
            console.error('Error loading add-ons:', error);
            loadBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error loading add-ons';
        });
}

function initializeCategorySelector() {
    const categorySelector = document.getElementById('addon-category-selector');
    const noMessage = document.getElementById('no-addon-category-message');
    const categoriesContainer = document.getElementById('addons-categories-container');
    categorySelector.addEventListener('change', function() {
        const selectedCategory = this.value;
        categoriesContainer.innerHTML = '';
        if (selectedCategory && addonsData[selectedCategory]) {
            noMessage.style.display = 'none';
            const categoryAddons = addonsData[selectedCategory];
            let html = `<div class="addon-category-section" data-category="${selectedCategory}"><div class="bg-white border border-gray-200 rounded-xl p-6"><h4 class="text-xl font-bold text-gray-800 mb-4 capitalize flex items-center"><i class="fas fa-${getCategoryIcon(selectedCategory)} text-orange-600 mr-2"></i>${selectedCategory}<span class="text-sm text-gray-500 font-normal ml-2">(${categoryAddons.length} items available)</span></h4><div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">`;
            categoryAddons.forEach(addon => {
                html += `<div class="addon-item border border-gray-200 rounded-lg p-4 hover:border-orange-300 transition-all duration-300 cursor-pointer ${addon.isOutOfStock ? 'opacity-60 cursor-not-allowed' : ''}" data-addon-id="${addon.id}"><div class="flex items-start justify-between mb-3"><div class="flex-1"><h5 class="font-semibold text-gray-900 mb-1">${addon.name}</h5><p class="text-sm text-gray-600 mb-2">${addon.description}</p><p class="text-lg font-bold text-orange-600">₱${addon.price.toLocaleString()}</p>${addon.track_stock ? `<div class="mt-2">${addon.isOutOfStock ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Out of Stock</span>' : addon.isLowStock ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i>Only ${addon.stock_quantity} left</span>` : `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>${addon.stock_quantity} available</span>`}</div>` : ''}</div><div class="ml-3">${!addon.isOutOfStock ? `<div class="addon-checkbox-custom w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center transition-all duration-200"><i class="fas fa-check text-white text-xs opacity-0 transition-opacity duration-200"></i></div><input type="checkbox" class="addon-checkbox sr-only" data-addon-id="${addon.id}" data-addon-price="${addon.price}" data-addon-name="${addon.name}" data-track-stock="${addon.track_stock}" data-stock-quantity="${addon.stock_quantity || 999}">` : `<div class="w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center bg-gray-200"><i class="fas fa-ban text-gray-400 text-xs"></i></div>`}</div></div>${!addon.isOutOfStock ? `<div class="addon-quantity border-t border-gray-200"><label class="block text-xs font-medium text-gray-700 mb-2">Quantity:</label><div class="flex items-center space-x-3"><button type="button" class="quantity-btn quantity-minus w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-all duration-200"><i class="fas fa-minus text-xs"></i></button><input type="number" name="addon_quantities[${addon.id}]" class="addon-quantity-input w-14 text-center border border-gray-300 rounded px-2 py-1 text-sm font-medium" value="1" min="1" max="${addon.track_stock ? addon.stock_quantity : 99}"><button type="button" class="quantity-btn quantity-plus w-7 h-7 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-all duration-200"><i class="fas fa-plus text-xs"></i></button></div>${addon.track_stock && addon.stock_quantity <= 5 ? `<p class="text-xs text-orange-600 mt-1"><i class="fas fa-info-circle mr-1"></i>Limited stock: Maximum ${addon.stock_quantity} available</p>` : ''}</div>` : ''}</div>`;
            });
            html += '</div></div></div>';
            categoriesContainer.innerHTML = html;
            initializeAddonEvents();
        } else {
            noMessage.style.display = 'block';
        }
    });
}

function getCategoryIcon(category) {
    const icons = { 'catering': 'utensils', 'decoration': 'palette', 'equipment': 'tools', 'service': 'concierge-bell' };
    return icons[category] || 'plus-circle';
}

function initializeAddonEvents() {
    document.querySelectorAll('.addon-item').forEach(addonCard => {
        addonCard.addEventListener('click', function(e) {
            if (e.target.closest('.quantity-btn') || e.target.closest('.addon-quantity-input')) return;
            if (this.classList.contains('cursor-not-allowed')) return;
            const checkbox = this.querySelector('.addon-checkbox');
            if (checkbox) { checkbox.checked = !checkbox.checked; checkbox.dispatchEvent(new Event('change')); }
        });
    });
    document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const addonId = this.dataset.addonId;
            const addonItem = this.closest('.addon-item');
            const quantitySection = addonItem.querySelector('.addon-quantity');
            const checkboxCustom = addonItem.querySelector('.addon-checkbox-custom');
            if (this.checked) {
                selectedAddons[addonId] = { name: this.dataset.addonName, price: parseFloat(this.dataset.addonPrice), quantity: 1 };
                checkboxCustom.classList.add('bg-orange-600', 'border-orange-600');
                checkboxCustom.querySelector('i').style.opacity = '1';
                quantitySection.classList.add('expanded');
                addonItem.classList.add('ring-2', 'ring-orange-300', 'bg-orange-50');
            } else {
                delete selectedAddons[addonId];
                checkboxCustom.classList.remove('bg-orange-600', 'border-orange-600');
                checkboxCustom.querySelector('i').style.opacity = '0';
                quantitySection.classList.remove('expanded');
                addonItem.classList.remove('ring-2', 'ring-orange-300', 'bg-orange-50');
            }
            updatePriceSummary();
        });
    });
    document.querySelectorAll('.quantity-plus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const input = this.parentElement.querySelector('.addon-quantity-input');
            const maxValue = parseInt(input.getAttribute('max'));
            const newValue = Math.min(parseInt(input.value) + 1, maxValue);
            if (parseInt(input.value) < maxValue) {
                input.value = newValue;
                const addonId = this.closest('.addon-item').querySelector('.addon-checkbox').dataset.addonId;
                if (selectedAddons[addonId]) { selectedAddons[addonId].quantity = newValue; updatePriceSummary(); }
            }
        });
    });
    document.querySelectorAll('.quantity-minus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const input = this.parentElement.querySelector('.addon-quantity-input');
            const newValue = Math.max(parseInt(input.value) - 1, 1);
            if (parseInt(input.value) > 1) {
                input.value = newValue;
                const addonId = this.closest('.addon-item').querySelector('.addon-checkbox').dataset.addonId;
                if (selectedAddons[addonId]) { selectedAddons[addonId].quantity = newValue; updatePriceSummary(); }
            }
        });
    });
    document.querySelectorAll('.addon-quantity-input').forEach(input => {
        input.addEventListener('click', e => e.stopPropagation());
        input.addEventListener('change', function(e) {
            e.stopPropagation();
            const addonId = this.closest('.addon-item').querySelector('.addon-checkbox').dataset.addonId;
            if (selectedAddons[addonId]) { selectedAddons[addonId].quantity = parseInt(this.value); updatePriceSummary(); }
        });
    });
}

// Package Inclusions Modal
function showPackageInclusions(packageId, packageName, inclusions) {
    const modal = document.getElementById('packageInclusionsModal');
    document.getElementById('packageModalTitle').textContent = packageName + ' - Inclusions';
    let html = '';
    if (inclusions && inclusions.length > 0) {
        html = '<div class="grid gap-3">';
        inclusions.forEach((inclusion, index) => {
            html += `<div class="flex items-start p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200"><div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-bold mr-3">${index + 1}</div><div class="flex-1"><p class="text-gray-800 font-medium">${inclusion}</p></div><div class="flex-shrink-0 ml-3"><i class="fas fa-check-circle text-green-500 text-xl"></i></div></div>`;
        });
        html += '</div>';
    } else {
        html = '<div class="text-center py-8"><i class="fas fa-info-circle text-gray-400 text-4xl mb-3"></i><p class="text-gray-600">No inclusions listed for this package.</p></div>';
    }
    document.getElementById('packageInclusionsList').innerHTML = html;
    modal.style.display = 'flex';
}

// Auto-slide
function startAutoSlide(carouselId) {
    const items = document.querySelectorAll(`[data-carousel="${carouselId}"].carousel-item`);
    if (items.length <= 1) return;
    carouselIntervals[carouselId] = setInterval(() => nextSlide(carouselId), 4000);
}

function stopAutoSlide(carouselId) {
    if (carouselIntervals[carouselId]) { clearInterval(carouselIntervals[carouselId]); delete carouselIntervals[carouselId]; }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pricing-option').forEach(option => {
        option.addEventListener('click', function() { selectPricingOption(this); });
    });
    if (venueType === 'suite') {
        const suiteOption = document.querySelector('.pricing-option[data-type="suite"]');
        if (suiteOption) selectPricingOption(suiteOption);
    }
    document.getElementById('loadAddons')?.addEventListener('click', loadAddons);
    document.getElementById('checkAvailability').addEventListener('click', function() {
        if (!selectedPricing) { alert('Please select a booking option first.'); return; }
        document.getElementById('availabilityModal').style.display = 'flex';
        loadCalendarData(currentCalendarYear, currentCalendarMonth);
    });
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('availabilityModal').style.display = 'none';
    });
    document.getElementById('closePackageModal')?.addEventListener('click', function() {
        document.getElementById('packageInclusionsModal').style.display = 'none';
    });
    document.getElementById('packageInclusionsModal')?.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });

    let currentCalendarYear = new Date().getFullYear();
    let currentCalendarMonth = new Date().getMonth() + 1;
    let calendarData = null;

    function loadCalendarData(year, month) {
        const loading = document.getElementById('calendarLoading');
        const grid = document.getElementById('calendarGrid');
        loading.style.display = 'block';
        grid.innerHTML = '';
        fetch(`/venue/{{ $venue->id }}/calendar-data?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(data => {
                calendarData = data;
                renderCalendar(data);
                document.getElementById('calendarTitle').textContent = `${data.calendar.month_name} ${data.calendar.year}`;
                loading.style.display = 'none';
            })
            .catch(error => {
                console.error('Error loading calendar:', error);
                loading.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-red-400 text-3xl mb-3"></i><p class="text-red-600">Error loading calendar</p></div>';
            });
    }

    function renderCalendar(data) {
        const grid = document.getElementById('calendarGrid');
        const days = data.calendar.days;
        const startDayOfWeek = days[0].day_of_week;
        let html = '';
        for (let i = 0; i < startDayOfWeek; i++) html += '<div class="p-3 border-b border-r border-gray-200"></div>';
        days.forEach(day => {
            const isClickable = !day.is_past;
            const hasBookings = day.bookings.length > 0;
            let cellClass = 'p-3 border-b border-r border-gray-200 min-h-[80px] relative transition-colors duration-200';
            let bgClass = '', textClass = 'text-gray-900';
            if (day.is_past) { bgClass = 'bg-gray-100'; textClass = 'text-gray-400'; }
            else if (day.availability.status === 'available') { bgClass = 'bg-green-50 hover:bg-green-100'; cellClass += ' cursor-pointer'; }
            else if (day.availability.status === 'partially-booked') { bgClass = 'bg-yellow-50 hover:bg-yellow-100'; cellClass += ' cursor-pointer'; }
            else if (day.availability.status === 'fully-booked') { bgClass = 'bg-red-50'; textClass = 'text-red-700'; }
            if (day.is_today) cellClass += ' ring-2 ring-blue-500';

            html += `<div class="${cellClass} ${bgClass}" ${isClickable ? `onclick="showDateDetails('${day.date}')"` : ''}><div class="flex justify-between items-start mb-2"><span class="font-semibold ${textClass}">${day.day}</span>${day.is_today ? '<span class="text-xs bg-blue-500 text-white px-1 rounded">Today</span>' : ''}</div>${hasBookings ? `<div class="space-y-1">${day.bookings.slice(0, 2).map(b => `<div class="text-xs p-1 rounded ${getBookingColor(b.time_slot)} truncate">${getTimeSlotLabel(b.time_slot)}</div>`).join('')}${day.bookings.length > 2 ? `<div class="text-xs text-gray-500">+${day.bookings.length - 2} more</div>` : ''}</div>` : ''}${!day.is_past && day.availability.status === 'available' ? '<div class="absolute bottom-1 right-1"><i class="fas fa-check-circle text-green-500 text-sm"></i></div>' : ''}</div>`;
        });
        grid.innerHTML = html;
    }

    function getTimeSlotLabel(timeSlot) {
        const labels = {
            'morning': 'Morning (8AM–12PM)',
            'afternoon': 'Afternoon (1PM–5PM)',
            'evening': 'Evening (6PM–10PM)',
            'full-day': 'Full Day',
            'suite': 'Suite (22 hours)',
            'package': 'Package',
        };
        return labels[timeSlot] || timeSlot;
    }

    function getBookingColor(timeSlot) {
        const colors = { 'morning': 'bg-blue-200 text-blue-800', 'afternoon': 'bg-orange-200 text-orange-800', 'evening': 'bg-purple-200 text-purple-800', 'full-day': 'bg-red-200 text-red-800', 'suite': 'bg-indigo-200 text-indigo-800', 'package': 'bg-pink-200 text-pink-800' };
        return colors[timeSlot] || 'bg-gray-200 text-gray-800';
    }

    function showDateDetails(date) {
        if (!calendarData) return;
        const dayData = calendarData.calendar.days.find(day => day.date === date);
        if (!dayData) return;
        const infoDiv = document.getElementById('selectedDateInfo');
        const contentDiv = document.getElementById('selectedDateContent');
        let html = `<div class="mb-3"><h6 class="font-semibold text-blue-900">${new Date(date + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</h6></div>`;

        if (dayData.bookings.length > 0) {
            html += `<div class="mb-4"><h6 class="font-medium text-blue-800 mb-2">Existing Bookings:</h6><div class="space-y-2">${dayData.bookings.map(booking => `<div class="flex items-center justify-between p-2 bg-white rounded border"><div><span class="font-medium">${getTimeSlotLabel(booking.time_slot)}</span>${booking.package_name ? `<span class="text-sm text-gray-600"> - ${booking.package_name}</span>` : ''}</div><span class="text-xs px-2 py-1 rounded ${booking.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${booking.status}</span></div>`).join('')}</div></div>`;
        }
        if (dayData.availability.available_slots.length > 0) {
            html += `<div class="mb-4"><h6 class="font-medium text-green-800 mb-2">Available Options:</h6><div class="flex flex-wrap gap-2">${dayData.availability.available_slots.map(slot => `<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">${getTimeSlotLabel(slot)}</span>`).join('')}</div></div>`;
        } else if (!dayData.is_past) {
            html += `<div class="mb-4"><div class="p-3 bg-red-100 border border-red-200 rounded"><p class="text-red-800 font-medium">No Slots Available</p><p class="text-red-600 text-sm">All time slots are fully booked for this date.</p></div></div>`;
        }
        if (!dayData.is_past && selectedPricing) {
            const availableSlots = dayData.availability.available_slots;
            const slotType = selectedPricing.type;
            const isTimeSlot = ['morning', 'afternoon', 'evening'].includes(slotType);
            const isMultiple = slotType === 'multiple';

            let canBook = false;
            if (isMultiple && selectedPricing.timeSlot) {
                // All selected slots must be available
                const individualSlots = selectedPricing.timeSlot.split(',');
                canBook = individualSlots.every(s => availableSlots.includes(s));
            } else if (isTimeSlot) {
                canBook = availableSlots.includes(slotType);
            } else {
                // full-day, package, suite
                canBook = availableSlots.includes(slotType) ||
                    (['full-day', 'package', 'suite'].includes(slotType) && dayData.availability.status === 'available');
            }
            html += canBook
                ? `<div class="mt-4 p-3 bg-green-50 border border-green-200 rounded"><p class="text-green-800 font-medium mb-2"><i class="fas fa-check-circle mr-2"></i>${selectedPricing.name} is available!</p><button onclick="proceedWithBooking('${date}')" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors"><i class="fas fa-calendar-check mr-2"></i>Book for this Date</button></div>`
                : `<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded"><p class="text-yellow-800 font-medium"><i class="fas fa-exclamation-triangle mr-2"></i>${selectedPricing.name} is not available for this date</p></div>`;
        }
        contentDiv.innerHTML = html;
        infoDiv.classList.remove('hidden');
    }

    function proceedWithBooking(date) {
        if (!selectedPricing) { alert('Please select a booking option first.'); return; }
        updateContactModalSummary();
        const summaryContainer = document.getElementById('contactModalSummary');
        summaryContainer.innerHTML = `<div class="mb-2"><strong>Selected Date:</strong> ${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</div>${summaryContainer.innerHTML}`;
        document.getElementById('availabilityModal').style.display = 'none';
        document.getElementById('contactModal').style.display = 'flex';
    }

    document.getElementById('prevMonth').addEventListener('click', function() {
        currentCalendarMonth--;
        if (currentCalendarMonth < 1) { currentCalendarMonth = 12; currentCalendarYear--; }
        loadCalendarData(currentCalendarYear, currentCalendarMonth);
        document.getElementById('selectedDateInfo').classList.add('hidden');
    });
    document.getElementById('nextMonth').addEventListener('click', function() {
        currentCalendarMonth++;
        if (currentCalendarMonth > 12) { currentCalendarMonth = 1; currentCalendarYear++; }
        loadCalendarData(currentCalendarYear, currentCalendarMonth);
        document.getElementById('selectedDateInfo').classList.add('hidden');
    });
    document.getElementById('contactForBooking').addEventListener('click', function() {
        if (!selectedPricing) { alert('Please select a booking option first.'); return; }
        updateContactModalSummary();
        document.getElementById('contactModal').style.display = 'flex';
    });
    document.getElementById('closeContactModal').addEventListener('click', function() {
        document.getElementById('contactModal').style.display = 'none';
    });
    document.getElementById('whatsappContact')?.addEventListener('click', function(e) {
        e.preventDefault();
        if (!selectedPricing) { alert('Please select a booking option first.'); return; }
        let message = `Hi! I'm interested in booking ${selectedPricing.name} for {{ $venue->name }}.%0A%0A`;
        message += `Selected Option: ${selectedPricing.name}%0ABase Price: ₱${selectedPricing.price.toLocaleString()}%0A`;
        if (Object.keys(selectedAddons).length > 0) {
            message += `%0ASelected Add-ons:%0A`;
            Object.values(selectedAddons).forEach(addon => { message += `- ${addon.name} (${addon.quantity}x): ₱${(addon.price * addon.quantity).toLocaleString()}%0A`; });
        }
        const total = selectedPricing.price + Object.values(selectedAddons).reduce((sum, addon) => sum + (addon.price * addon.quantity), 0);
        message += `%0ATotal Estimated Cost: ₱${total.toLocaleString()}%0A%0APlease let me know about availability and next steps.`;
        window.open(`https://wa.me/${this.dataset.whatsapp}?text=${message}`, '_blank');
    });

    const carouselId = 'venue-details';
    startAutoSlide(carouselId);
    const carouselContainer = document.querySelector(`[data-carousel="${carouselId}"]`)?.closest('.relative');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', () => stopAutoSlide(carouselId));
        carouselContainer.addEventListener('mouseleave', () => startAutoSlide(carouselId));
    }
});
</script>
