// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });

    // Pet details modal functionality
    const petModal = document.getElementById('petModal');
    if (petModal) {
        petModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const petName = button.getAttribute('data-pet-name');
            const petImg = button.getAttribute('data-pet-img');
            const petDesc = button.getAttribute('data-pet-desc');
            
            // Update the modal's content
            const modalTitle = petModal.querySelector('#modalPetName');
            const modalImg = petModal.querySelector('#modalPetImg');
            const modalDesc = petModal.querySelector('#modalPetDesc');
            
            modalTitle.textContent = petName;
            modalImg.src = petImg;
            modalDesc.textContent = petDesc;
        });
    }

    // Pet filtering functionality
    const typeFilter = document.getElementById('petTypeFilter');
    const ageFilter = document.getElementById('ageFilter');
    const genderFilter = document.getElementById('genderFilter');
    const petCards = document.querySelectorAll('.pet-card');

    function applyFilters() {
        const typeValue = typeFilter.value;
        const ageValue = ageFilter.value;
        const genderValue = genderFilter.value;

        petCards.forEach(card => {
            const cardType = card.getAttribute('data-type');
            const cardAge = card.getAttribute('data-age');
            const cardGender = card.getAttribute('data-gender');

            const typeMatch = typeValue === 'all' || cardType === typeValue;
            const ageMatch = ageValue === 'all' || cardAge === ageValue;
            const genderMatch = genderValue === 'all' || cardGender === genderValue;

            if (typeMatch && ageMatch && genderMatch) {
                card.style.display = '';
                // Add a subtle animation when cards appear
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add event listeners to filters
    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (ageFilter) ageFilter.addEventListener('change', applyFilters);
    if (genderFilter) genderFilter.addEventListener('change', applyFilters);

    // Form submission handlers
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            // Get form ID to handle different forms
            const formId = form.id;
            let message = '';
            
            switch(formId) {
                case 'loginForm':
                    message = 'Login successful! Redirecting to your account...';
                    // Here you would typically handle login authentication
                    // For demo purposes, just show success message
                    showFormSuccess(form, message);
                    break;
                    
                case 'signupForm':
                    message = 'Account created successfully! Welcome to Pawfect Match!';
                    showFormSuccess(form, message);
                    break;
                    
                case 'adoptionInterestForm':
                    message = 'Thank you for your interest! We\'ll contact you soon about the adoption process.';
                    showFormSuccess(form, message);
                    setTimeout(() => {
                        // Close the modal after submission
                        const modal = bootstrap.Modal.getInstance(document.getElementById('petModal'));
                        modal.hide();
                    }, 3000);
                    break;
                    
                case 'subscribeForm':
                    message = 'You\'ve been subscribed to our updates!';
                    showFormSuccess(form, message);
                    break;
                
                default:
                    message = 'Form submitted successfully!';
                    showFormSuccess(form, message);
            }
            
            // Reset form after submission
            setTimeout(() => {
                form.reset();
                form.classList.remove('was-validated');
                // Remove success message if it exists
                const successMessage = form.querySelector('.alert-success');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
        });
    });
    
    // Function to show success message after form submission
    function showFormSuccess(form, message) {
        // Create success alert
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success mt-3';
        successDiv.textContent = message;
        
        // Add to form
        form.appendChild(successDiv);
    }
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Adopt Now button scroll to pets section
    const adoptNowBtn = document.getElementById('adoptNowBtn');
    if (adoptNowBtn) {
        adoptNowBtn.addEventListener('click', () => {
            const petsSection = document.querySelector('.pet-cards').closest('.section');
            petsSection.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    // Add animation to feature cards on scroll
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.feature-card, .pet-card');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.2;
            
            if (elementPosition < screenPosition) {
                element.classList.add('visible');
            }
        });
    };
    
    // Execute on scroll
    window.addEventListener('scroll', animateOnScroll);
    
    // Execute once on page load
    setTimeout(animateOnScroll, 500);
    
    // Image hover effect for pet cards
    const petImages = document.querySelectorAll('.pet-cards img');
    petImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Mobile menu toggle effect
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }
    
    
    
    
    
    // Simple theme toggler (bonus feature)
    const addThemeToggler = () => {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;
        
        const themeBtn = document.createElement('button');
        themeBtn.className = 'btn theme-toggle ms-2';
        themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
        themeBtn.title = 'Toggle Dark/Light Mode';
        themeBtn.setAttribute('data-bs-toggle', 'tooltip');
        themeBtn.setAttribute('data-bs-placement', 'bottom');
        
        const authButtons = document.querySelector('.auth-buttons');
        authButtons.prepend(themeBtn);
        
        // Initialize tooltip
        new bootstrap.Tooltip(themeBtn);
        
        let darkMode = false;
        themeBtn.addEventListener('click', () => {
            darkMode = !darkMode;
            if (darkMode) {
                document.body.classList.add('dark-theme');
                themeBtn.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                document.body.classList.remove('dark-theme');
                themeBtn.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
        
        // Add CSS for dark theme
        const style = document.createElement('style');
        style.textContent = `
            .dark-theme {
                background-color: #121212;
                color: #e0e0e0;
            }
            .dark-theme header, .dark-theme .card, .dark-theme footer {
                background-color: #1e1e1e;
                color: #e0e0e0;
            }
            .dark-theme .navbar-light .navbar-nav .nav-link,
            .dark-theme h2, .dark-theme h3, .dark-theme h4 {
                color: #e0e0e0;
            }
            .dark-theme .bg-light {
                background-color: #1a1a1a !important;
            }
        `;
        document.head.appendChild(style);
    };
    
    // Initialize theme toggler
    addThemeToggler();
});

// Pet Data
const pets = [
    {
        name: "Milo",
        description: "Milo is vaccinated, and bursting with love. He's eager to fill your days with happy tail wags and cuddles.",
        weight: "8",
        age: "2",
        bio: "Milo is a sweet pup searching for a forever home where he can share his boundless affection."
    },
    {
        name: "Luna",
        description: "Luna is a gentle soul who loves quiet evenings and belly rubs.",
        weight: "5",
        age: "3",
        bio: "This calm companion is perfect for apartment living."
    },
    {
        name: "Rocky",
        description: "Full of energy! Rocky loves playing fetch and going on long hikes.",
        weight: "12",
        age: "1",
        bio: "Ideal for active owners who enjoy outdoor adventures."
    }
];

// DOM Elements
const cardsContainer = document.querySelector('.pet-cards-container');
let currentIndex = 0;

// Create Pet Cards
function createPetCards() {
    pets.forEach((pet, index) => {
        const card = document.createElement('div');
        card.className = 'pet-card';
        card.style.transform = `translateX(${index * 100}%)`;
        card.innerHTML = `
            <h2>${pet.name}</h2>
            <p>${pet.description}</p>
            <div class="pet-stats">
                <div class="stat">
                    <div class="stat-value">${pet.weight}</div>
                    <div class="stat-label">kg</div>
                </div>
                <div class="stat">
                    <div class="stat-value">${pet.age}</div>
                    <div class="stat-label">y/o</div>
                </div>
            </div>
            <button class="adopt-btn">ADOPT ME</button>
            <p class="pet-bio">${pet.bio}</p>
        `;
        cardsContainer.appendChild(card);
    });
}

// Update Carousel Position
function updateCarousel() {
    const cards = document.querySelectorAll('.pet-card');
    cards.forEach((card, index) => {
        card.style.transform = `translateX(${100 * (index - currentIndex)}%)`;
        card.style.opacity = index === currentIndex ? '1' : '0.5';
    });
}

// Event Listeners for Arrows
document.querySelector('.left-arrow').addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + pets.length) % pets.length;
    updateCarousel();
});

document.querySelector('.right-arrow').addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % pets.length;
    updateCarousel();
});

// Initialize
createPetCards();
updateCarousel();