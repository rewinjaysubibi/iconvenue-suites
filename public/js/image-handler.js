/**
 * Image Handler JavaScript
 * Provides functionality for image uploads, previews, and carousels
 */

// Image Preview Functions
function previewImage(event, previewId = 'imagePreview', imgId = 'preview') {
    const file = event.target.files[0];
    const previewContainer = document.getElementById(previewId);
    const previewImg = document.getElementById(imgId);
    
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file');
            event.target.value = '';
            if (previewContainer) previewContainer.classList.add('hidden');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImg) {
                previewImg.src = e.target.result;
            }
            if (previewContainer) {
                previewContainer.classList.remove('hidden');
            }
        }
        reader.readAsDataURL(file);
    } else {
        if (previewContainer) {
            previewContainer.classList.add('hidden');
        }
    }
}

function previewMultipleImages(event, containerId = 'imagePreviewContainer', maxSize = 10) {
    const files = event.target.files;
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    // Clear previous previews
    container.innerHTML = '';
    
    if (files.length > 0) {
        container.parentElement.classList.remove('hidden');
        
        Array.from(files).forEach((file, index) => {
            // Validate file size (maxSize in MB)
            if (file.size > maxSize * 1024 * 1024) {
                alert(`File "${file.name}" is too large. Maximum size is ${maxSize}MB.`);
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert(`File "${file.name}" is not a valid image.`);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group';
                imageDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}" 
                         class="w-full h-32 object-cover rounded-lg shadow border">
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                        ${file.name}
                    </div>
                    <div class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity"
                         onclick="this.parentElement.remove()">
                        ×
                    </div>
                `;
                container.appendChild(imageDiv);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.parentElement.classList.add('hidden');
    }
}

// Image Carousel Functions
function initCarousel(containerId, options = {}) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const slides = container.querySelectorAll('.carousel-slide');
    const indicators = container.querySelectorAll('.carousel-indicator');
    const prevBtn = container.querySelector('.carousel-prev');
    const nextBtn = container.querySelector('.carousel-next');
    
    let currentSlide = 0;
    let autoSlideInterval;
    
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => slide.style.display = 'none');
        indicators.forEach(indicator => indicator.classList.remove('bg-purple-600'));
        indicators.forEach(indicator => indicator.classList.add('bg-gray-300'));
        
        // Show current slide
        if (slides[index]) {
            slides[index].style.display = 'block';
        }
        if (indicators[index]) {
            indicators[index].classList.remove('bg-gray-300');
            indicators[index].classList.add('bg-purple-600');
        }
        
        currentSlide = index;
    }
    
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    function prevSlide() {
        const prev = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prev);
    }
    
    // Event listeners
    if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
    }
    
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showSlide(index));
    });
    
    // Auto slide
    if (options.auto_slide && slides.length > 1) {
        autoSlideInterval = setInterval(nextSlide, options.slide_interval || 5000);
        
        // Pause on hover
        container.addEventListener('mouseenter', () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        });
        
        container.addEventListener('mouseleave', () => {
            if (options.auto_slide) {
                autoSlideInterval = setInterval(nextSlide, options.slide_interval || 5000);
            }
        });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });
}

// Image Upload with Progress
function uploadImageWithProgress(file, url, progressCallback, successCallback, errorCallback) {
    const formData = new FormData();
    formData.append('image', file);
    
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            if (progressCallback) progressCallback(percentComplete);
        }
    });
    
    xhr.addEventListener('load', () => {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (successCallback) successCallback(response);
            } catch (e) {
                if (errorCallback) errorCallback('Invalid response format');
            }
        } else {
            if (errorCallback) errorCallback('Upload failed');
        }
    });
    
    xhr.addEventListener('error', () => {
        if (errorCallback) errorCallback('Network error');
    });
    
    xhr.open('POST', url);
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.send(formData);
}

// Image Validation
function validateImage(file, options = {}) {
    const defaults = {
        maxSize: 10 * 1024 * 1024, // 10MB
        allowedTypes: ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'],
        minWidth: null,
        minHeight: null
    };
    
    const config = { ...defaults, ...options };
    const errors = [];
    
    // Check file size
    if (file.size > config.maxSize) {
        errors.push(`File size must not exceed ${config.maxSize / (1024 * 1024)}MB`);
    }
    
    // Check file type
    if (!config.allowedTypes.includes(file.type)) {
        errors.push('Invalid file type. Allowed types: ' + config.allowedTypes.join(', '));
    }
    
    return errors;
}

// Drag and Drop functionality
function initDragAndDrop(dropZoneId, inputId, previewId) {
    const dropZone = document.getElementById(dropZoneId);
    const fileInput = document.getElementById(inputId);
    
    if (!dropZone || !fileInput) return;
    
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-purple-500', 'bg-purple-50');
    });
    
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-purple-500', 'bg-purple-50');
    });
    
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-purple-500', 'bg-purple-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            
            // Trigger change event for preview
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
    
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });
}

// Image Lazy Loading
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();
});