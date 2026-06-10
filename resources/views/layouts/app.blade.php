<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Icon Venue & Suites')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
    --gold: #c9a84c;
    --gold-dark: #8b6914;
    --gold-bright: #f0d080;
    --bg-dark: #1a0e05;
    --bg-card: #2d1a08;
    --bg-card-alt: rgba(45,26,8,0.95);
    --text-gold: #c9a84c;
    --text-light: #f5e6c8;
    --text-muted: #8b6914;
    --border-gold: rgba(201,168,76,0.3);
    --border-gold-strong: rgba(201,168,76,0.6);
}

* {
    font-family: 'Inter', sans-serif;
}

body {
    background: linear-gradient(135deg, #1a0e05 0%, #2d1a08 40%, #1a0e05 100%);
    min-height: 100vh;
    color: var(--text-light);
}

.gradient-bg {
    background: linear-gradient(135deg, #2d1a08 0%, #1a0e05 100%);
    border-bottom: 1px solid var(--border-gold);
}

.gradient-bg-alt {
    background: linear-gradient(135deg, #c9a84c 0%, #8b6914 100%);
}

.card-hover {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(201,168,76,0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #8b6914 0%, #c9a84c 50%, #8b6914 100%);
    color: #1a0e05 !important;
    font-weight: 700;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #c9a84c 0%, #f0d080 50%, #c9a84c 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(201,168,76,0.4);
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.slide-in {
    animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
    from { transform: translateX(-100%); }
    to   { transform: translateX(0); }
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.glass-effect {
    background: rgba(45,26,8,0.6);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-gold);
}

.shadow-glow {
    box-shadow: 0 0 20px rgba(201,168,76,0.3);
}

/* Gold card style — use on any card/panel */
.gold-card {
    background: var(--bg-card-alt);
    border: 1px solid var(--border-gold);
    border-radius: 0.75rem;
    position: relative;
    overflow: hidden;
}

.gold-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, #8b6914, #c9a84c, #f0d080, #c9a84c, #8b6914);
}

/* Override Tailwind white backgrounds for admin content */
.bg-white {
    background: rgba(45,26,8,0.9) !important;
    border: 1px solid rgba(201,168,76,0.2) !important;
    color: #f5e6c8 !important;
}

.bg-gray-50 { background: rgba(26,14,5,0.5) !important; }
.bg-gray-100 { background: rgba(26,14,5,0.7) !important; }
.bg-gray-200 { background: rgba(45,26,8,0.7) !important; }

/* Text overrides */
.text-gray-900 { color: #f5e6c8 !important; }
.text-gray-800 { color: #e8d5a8 !important; }
.text-gray-700 { color: #d4b87a !important; }
.text-gray-600 { color: #b8954a !important; }
.text-gray-500 { color: #8b6914 !important; }
.text-gray-400 { color: #6b4f0f !important; }

/* Border overrides */
.border-gray-200 { border-color: rgba(201,168,76,0.2) !important; }
.border-gray-100 { border-color: rgba(201,168,76,0.1) !important; }
.divide-gray-200 > * + * { border-color: rgba(201,168,76,0.2) !important; }

/* Table rows */
tbody tr:hover { background: rgba(201,168,76,0.05) !important; }
thead.bg-gray-50 { background: rgba(45,26,8,0.95) !important; }

/* Form inputs */
input[type="text"],
input[type="email"],
input[type="number"],
input[type="password"],
input[type="date"],
input[type="tel"],
textarea,
select {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(201,168,76,0.3) !important;
    color: #f5e6c8 !important;
    border-radius: 0.5rem;
}

input::placeholder, textarea::placeholder { color: #6b4f0f !important; }
select option { background: #2d1a08; color: #f5e6c8; }

input:focus, textarea:focus, select:focus {
    border-color: #c9a84c !important;
    box-shadow: 0 0 0 2px rgba(201,168,76,0.2) !important;
    outline: none;
}

/* Buttons */
button[type="submit"],
a.bg-purple-600, .bg-purple-600,
a.bg-blue-600,  .bg-blue-600 {
    background: linear-gradient(135deg, #8b6914, #c9a84c, #8b6914) !important;
    color: #1a0e05 !important;
    border: none !important;
}

button[type="submit"]:hover,
a.bg-purple-600:hover, .bg-purple-600:hover,
a.bg-blue-600:hover,   .bg-blue-600:hover {
    background: linear-gradient(135deg, #c9a84c, #f0d080, #c9a84c) !important;
    box-shadow: 0 4px 15px rgba(201,168,76,0.4) !important;
}

/* Cancel/secondary buttons */
.bg-gray-300, .bg-gray-400 {
    background: rgba(45,26,8,0.8) !important;
    color: #c9a84c !important;
    border: 1px solid rgba(201,168,76,0.3) !important;
}

/* Status badges — keep readable but gold-tinted */
.bg-green-100  { background: rgba(16,185,129,0.15) !important; }
.text-green-800 { color: #6ee7b7 !important; }
.bg-yellow-100 { background: rgba(251,191,36,0.15) !important; }
.text-yellow-800 { color: #fde68a !important; }
.bg-red-100    { background: rgba(239,68,68,0.15) !important; }
.text-red-800  { color: #fca5a5 !important; }
.bg-blue-100   { background: rgba(59,130,246,0.15) !important; }
.text-blue-800 { color: #93c5fd !important; }
.bg-purple-100 { background: rgba(139,92,246,0.15) !important; }
.text-purple-800 { color: #d8b4fe !important; }

/* Pagination */
.bg-purple-600 { background: linear-gradient(135deg, #8b6914, #c9a84c) !important; }
.text-purple-600 { color: #c9a84c !important; }
.text-blue-600  { color: #c9a84c !important; }
.text-purple-700 { color: #c9a84c !important; }

/* Links */
a { color: #c9a84c; }
a:hover { color: #f0d080; }

/* Smooth expand/collapse for add-on quantity section */
.addon-quantity {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding-top 0.3s ease, opacity 0.3s ease;
    padding-top: 0;
    opacity: 0;
}
.addon-quantity.expanded {
    max-height: 200px;
    padding-top: 0.75rem;
    opacity: 1;
}

/* Scrollbar — gold */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: #1a0e05; }
::-webkit-scrollbar-thumb { background: #8b6914; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #c9a84c; }

/* Loading spinner */
.loading {
    display: inline-block;
    width: 20px; height: 20px;
    border: 3px solid rgba(201,168,76,0.3);
    border-radius: 50%;
    border-top-color: #c9a84c;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    @yield('content')
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
