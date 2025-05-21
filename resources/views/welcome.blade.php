<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - Your Bakery Management Solution</title>
        
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="msapplication-TileColor" content="#8B4513">
        <meta name="theme-color" content="#8B4513">
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Styles -->
        @vite(['resources/css/welcome.css'])
    </head>
    <body>
        <header>
            <div class="logo">
                <x-application-logo class="w-8 h-8" />
                {{ config('app.name') }}<br>Management System
            </div>
            <nav class="nav-buttons">
                <a class="nav-link">Features</a>
                <a class="nav-link">Pricing</a>
                <a class="nav-link">About</a>
                <a href="{{ route('login') }}">
                    <button class="login-btn">Login</button>
                </a>
                <a href="{{ route('register') }}">
                </a>
            </nav>
        </header>

        <main class="content">
            <div class="left-side">
                <div class="graphics">
                    <h1>{{ config('app.name') }} Management System</h1>
                    <h2>Streamline your bakery operations with our comprehensive management solution. Perfect for bakeries of all sizes.</h2>
                </div>
            </div>

        <section class="subscription-section">
            <div class="subscription-title">
                    <h2>Choose Your Plan</h2>
                <p style="color: #DEB887;">Select the perfect plan for your bakery management needs</p>
            </div>

            <div class="subscription-cards">
                <!-- Free Plan -->
                <div class="subscription-card">
                    <div class="card-header">
                            <h3>Free Plan</h3>
                            <div class="card-price">$0<span>/month</span></div>
                            <p>Perfect for small bakeries</p>
                    </div>
                    <div class="card-features">
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Up to 3 products</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Generate PDF reports</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Basic inventory management</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Up to 3 photos per product</span>
                        </div>
                    </div>
                    <button class="select-plan-btn free-plan-btn" onclick="openModal('free')">Start Free Plan</button>
                </div>

                <!-- Pro Plan -->
                <div class="subscription-card" style="border: 1px solid #00bfff;">
                    <div class="card-header">
                            <h3>Pro Plan</h3>
                            <div class="card-price">$29<span>/month</span></div>
                            <p>For growing bakeries</p>
                    </div>
                    <div class="card-features">
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Unlimited products</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Generate PDF reports</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Advanced inventory management</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Unlimited photos per product</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Sales analytics & statistics</span>
                        </div>
                        <div class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Low stock alerts</span>
                        </div>
                    </div>
                    <button class="select-plan-btn pro-plan-btn" onclick="openModal('pro')">Start Pro Plan</button>
                </div>
            </div>
        </section>
        </main>

        <!-- Registration Modal -->
        <div id="registrationModal" class="modal">
            <div class="modal-content enhanced-modal scrollable-modal">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <div id="alertContainer" class="alert-container" style="display: none; margin-bottom: 20px;">
                </div>
                <h2 class="modal-title">Complete Your Registration</h2>
                <form id="tenantRegistrationForm" action="{{ route('tenant.register') }}" method="POST">
                    @csrf
                    <input type="hidden" id="selectedPlan" name="plan">

                    <div class="form-section">
                        <h4 class="section-title">Selected Plan</h4>
                        <div class="selected-plan-info">
                            <div id="planInfo" class="alert alert-info">
                                <!-- Plan info will be dynamically updated -->
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="section-title">Personal Information</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required placeholder="Enter your full name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required placeholder="Enter your email">
                            </div>
                            <div class="form-group">
                                <label for="contactNumber">Contact Number</label>
                                <input type="tel" id="contactNumber" name="contact_number" required placeholder="Enter contact number">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4 class="section-title">Bakery Information</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="bakeryName">Bakery Name</label>
                                <input type="text" id="bakeryName" name="bakery_name" required placeholder="Enter bakery name">
                            </div>
                            <div class="form-group">
                                <label for="domainName">Domain Name</label>
                                <input type="text" id="domainName" name="domain_name" required placeholder="Enter domain name">
                                <small style="color: #888;">Example: mybakery (will become mybakery.localhost:8000)</small>
                            </div>
                        </div>
                    </div>

                   

                    <!-- Hidden fields for backend validation -->
                    <input type="hidden" name="address" value="Not Specified">
                    <input type="hidden" name="city" value="Not Specified">
                    <input type="hidden" name="state" value="Not Specified">
                    <input type="hidden" name="postal_code" value="00000">
                    <input type="hidden" name="latitude" value="0">
                    <input type="hidden" name="longitude" value="0">
                    <input type="hidden" name="location_notes" value="">

                    <button type="submit" class="submit-btn enhanced-btn">Complete Registration</button>
                </form>
            </div>
        </div>

        <script>
            function showAlert(message, type = 'success') {
                const alertContainer = document.getElementById('alertContainer');
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                
                alertContainer.innerHTML = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                alertContainer.style.display = 'block';
                
                // Auto hide after 5 seconds
                setTimeout(() => {
                    const alertElement = alertContainer.querySelector('.alert');
                    if (alertElement) {
                        alertElement.classList.remove('show');
                        setTimeout(() => {
                            alertContainer.style.display = 'none';
                        }, 150);
                    }
                }, 5000);
            }

            function openModal(plan) {
                document.getElementById('registrationModal').style.display = 'block';
                document.getElementById('selectedPlan').value = plan;
                document.getElementById('alertContainer').style.display = 'none'; // Hide any existing alerts
                
                // Update plan info with styled content
                const planInfo = document.getElementById('planInfo');
                if (plan === 'free') {
                    planInfo.innerHTML = `
                        <h4>Free Plan Selected</h4>
                        <ul>
                            <li>Up to 3 products</li>
                            <li>Generate PDF reports</li>
                            <li>Basic inventory management</li>
                            <li>Up to 3 photos per product</li>
                        </ul>
                    `;
                } else {
                    planInfo.innerHTML = `
                        <h4>Pro Plan Selected ($29/month)</h4>
                        <ul>
                            <li>Unlimited products</li>
                            <li>Generate PDF reports</li>
                            <li>Advanced inventory management</li>
                            <li>Unlimited photos per product</li>
                            <li>Sales analytics & statistics</li>
                            <li>Low stock alerts</li>
                        </ul>
                    `;
                }
            }

            function closeModal() {
                document.getElementById('registrationModal').style.display = 'none';
                document.getElementById('alertContainer').style.display = 'none'; // Hide any existing alerts
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target == document.getElementById('registrationModal')) {
                    closeModal();
                }
            }

            // Handle form submission
            document.getElementById('tenantRegistrationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert('Registration successful! Your application is pending approval.');
                        setTimeout(() => {
                        closeModal();
                        this.reset();
                        }, 2000);
                    } else {
                        showAlert(data.message || 'An error occurred. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error: ' + error.message, 'error');
                });
            });
        </script>
    </body>
</html>

