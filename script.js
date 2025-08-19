// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
const body = document.body;
const icon = themeToggle.querySelector('i');

const savedTheme = localStorage.getItem('theme') ||
    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

if (savedTheme === 'dark') {
    body.classList.add('dark-mode');
    icon.classList.replace('fa-moon', 'fa-sun');
}

themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    if (body.classList.contains('dark-mode')) {
        icon.classList.replace('fa-moon', 'fa-sun');
        localStorage.setItem('theme', 'dark');
    } else {
        icon.classList.replace('fa-sun', 'fa-moon');
        localStorage.setItem('theme', 'light');
    }
});

// Mobile Navigation
const hamburger = document.getElementById('hamburger');
const navLinks = document.querySelector('.nav-links');

hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    hamburger.innerHTML = navLinks.classList.contains('active') ?
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
});

document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        hamburger.innerHTML = '<i class="fas fa-bars"></i>';
    });
});

// Project Filtering
const filterButtons = document.querySelectorAll('.filter-btn');
const projectCards = document.querySelectorAll('.project-card');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        const filter = button.dataset.filter;
        projectCards.forEach(card => {
            card.style.display = filter === 'all' || card.dataset.category === filter ? 'block' : 'none';
        });
    });
});

// AJAX form submission with reCAPTCHA
const contactForm = document.getElementById('contactForm');
const formStatus = document.getElementById('formStatus');

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const recaptchaResponse = grecaptcha.getResponse();
    if (!recaptchaResponse) {
        formStatus.textContent = 'Please verify that you are not a robot.';
        formStatus.className = 'form-status error';
        return;
    }
    formData.append('g-recaptcha-response', recaptchaResponse);

    fetch(this.action, { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } })
    .then(response => response.json())
    .then(data => {
        formStatus.textContent = data.message;
        formStatus.className = 'form-status ' + (data.success ? 'success' : 'error');
        if (data.success) {
            contactForm.reset();
            grecaptcha.reset();
            setTimeout(() => { formStatus.style.display = 'none'; }, 5000);
        }
    })
    .catch(error => {
        formStatus.textContent = 'Oops! There was a problem submitting your form.';
        formStatus.className = 'form-status error';
    });
});

// Footer Year
document.getElementById('year').textContent = new Date().getFullYear();

// Scroll animations
const fadeElements = document.querySelectorAll('.fade-in');
const appearOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
const appearOnScroll = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('appear');
        observer.unobserve(entry.target);
    });
}, appearOptions);

fadeElements.forEach(el => appearOnScroll.observe(el));

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({ top: targetElement.offsetTop - 80, behavior: 'smooth' });
        }
    });
});
