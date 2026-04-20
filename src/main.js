import './style.css';

// Hero background images – imported so Vite rewrites paths with the correct base
import hero1 from '../public/hero1.jpg';
import hero2 from '../public/hero2.jpg';
import hero3 from '../public/hero3.jpg';

// Apply backgrounds immediately (before DOMContentLoaded delay is fine here)
const bg1 = document.getElementById('slideBg1');
const bg2 = document.getElementById('slideBg2');
const bg3 = document.getElementById('slideBg3');
if (bg1) bg1.style.backgroundImage = `url('${hero1}')`;
if (bg2) bg2.style.backgroundImage = `url('${hero2}')`;
if (bg3) bg3.style.backgroundImage = `url('${hero3}')`;

/* ============================================================
   SURYA DENTAL – MAIN JS
   ============================================================ */

// ── Navbar scroll effect ───────────────────────────────────────
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
  updateActiveNavLink();
});

// ── Hamburger menu ─────────────────────────────────────────────
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('open');
  const spans = hamburger.querySelectorAll('span');
  if (navLinks.classList.contains('open')) {
    spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
    spans[1].style.opacity = '0';
    spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
  } else {
    spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
  }
});

// Close nav on link click
document.querySelectorAll('.nav-link').forEach(link => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('open');
    hamburger.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
  });
});

// ── Active nav link on scroll ──────────────────────────────────
function updateActiveNavLink() {
  const sections = document.querySelectorAll('section[id]');
  const scrollY = window.scrollY + 100;
  sections.forEach(sec => {
    const top    = sec.offsetTop;
    const height = sec.offsetHeight;
    const id     = sec.getAttribute('id');
    const link   = document.querySelector(`.nav-link[href="#${id}"]`);
    if (link) {
      link.classList.toggle('active', scrollY >= top && scrollY < top + height);
    }
  });
}

// ── Hero Slider ────────────────────────────────────────────────
const slides     = document.querySelectorAll('.slide');
const dots       = document.querySelectorAll('.dot');
const prevBtn    = document.getElementById('sliderPrev');
const nextBtn    = document.getElementById('sliderNext');
let currentSlide = 0;
let slideTimer   = null;

function goToSlide(n) {
  slides[currentSlide].classList.remove('active');
  dots[currentSlide].classList.remove('active');
  currentSlide = (n + slides.length) % slides.length;
  slides[currentSlide].classList.add('active');
  dots[currentSlide].classList.add('active');
}

function startAutoSlide() {
  clearInterval(slideTimer);
  slideTimer = setInterval(() => goToSlide(currentSlide + 1), 5000);
}

prevBtn.addEventListener('click', () => { goToSlide(currentSlide - 1); startAutoSlide(); });
nextBtn.addEventListener('click', () => { goToSlide(currentSlide + 1); startAutoSlide(); });

dots.forEach((dot, i) => {
  dot.addEventListener('click', () => { goToSlide(i); startAutoSlide(); });
});

startAutoSlide();

// Touch/swipe support
let touchStartX = 0;
document.getElementById('heroSlider').addEventListener('touchstart', e => {
  touchStartX = e.touches[0].clientX;
}, { passive: true });

document.getElementById('heroSlider').addEventListener('touchend', e => {
  const dx = e.changedTouches[0].clientX - touchStartX;
  if (Math.abs(dx) > 50) {
    goToSlide(currentSlide + (dx < 0 ? 1 : -1));
    startAutoSlide();
  }
}, { passive: true });

// ── Scroll Reveal ──────────────────────────────────────────────
const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

revealElements.forEach(el => revealObserver.observe(el));

// ── Testimonials infinite scroll clone ────────────────────────
const track = document.getElementById('testimonialsTrack');
if (track) {
  const cards = Array.from(track.children);
  cards.forEach(card => {
    const clone = card.cloneNode(true);
    track.appendChild(clone);
  });
}

// ── Gallery Lightbox ───────────────────────────────────────────
const galleryItems  = document.querySelectorAll('.gallery-item');
const lightbox      = document.getElementById('lightbox');
const lightboxImg   = document.getElementById('lightboxImg');
const lightboxClose = document.getElementById('lightboxClose');
const lightboxPrev  = document.getElementById('lightboxPrev');
const lightboxNext  = document.getElementById('lightboxNext');
let currentGallery  = 0;
const gallerySrcs   = Array.from(galleryItems).map(item => item.dataset.src);

function openLightbox(index) {
  currentGallery = index;
  lightboxImg.src = gallerySrcs[currentGallery];
  lightboxImg.alt = galleryItems[currentGallery].querySelector('img').alt;
  lightbox.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  lightbox.classList.remove('open');
  document.body.style.overflow = '';
}

galleryItems.forEach((item, i) => {
  item.addEventListener('click', () => openLightbox(i));
});

lightboxClose.addEventListener('click', closeLightbox);
lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });

lightboxPrev.addEventListener('click', () => {
  currentGallery = (currentGallery - 1 + gallerySrcs.length) % gallerySrcs.length;
  lightboxImg.src = gallerySrcs[currentGallery];
});

lightboxNext.addEventListener('click', () => {
  currentGallery = (currentGallery + 1) % gallerySrcs.length;
  lightboxImg.src = gallerySrcs[currentGallery];
});

document.addEventListener('keydown', e => {
  if (!lightbox.classList.contains('open')) return;
  if (e.key === 'Escape') closeLightbox();
  if (e.key === 'ArrowLeft')  lightboxPrev.click();
  if (e.key === 'ArrowRight') lightboxNext.click();
});

// ── Appointment Form ───────────────────────────────────────────
const form        = document.getElementById('appointmentForm');
const submitBtn   = document.getElementById('submit-appointment');
const successModal = document.getElementById('successModal');
const modalClose  = document.getElementById('modalClose');

form.addEventListener('submit', (e) => {
  e.preventDefault();

  // Basic validation
  const name  = document.getElementById('patient-name').value.trim();
  const phone = document.getElementById('patient-phone').value.trim();

  if (!name || !phone) {
    shakeElement(submitBtn);
    return;
  }

  // Simulate submission
  submitBtn.textContent = '⏳ Booking...';
  submitBtn.style.opacity = '0.75';
  submitBtn.disabled = true;

  setTimeout(() => {
    submitBtn.textContent = 'Book Your Appointment Now 🦷';
    submitBtn.style.opacity = '1';
    submitBtn.disabled = false;
    form.reset();
    successModal.classList.add('open');

    // WhatsApp fallback - open WA with pre-filled message
    const msg = encodeURIComponent(
      `Hello Surya Dental, I'd like to book an appointment.\n\nName: ${name}\nPhone: ${phone}`
    );
    window.open(`https://wa.me/917207726666?text=${msg}`, '_blank');
  }, 1200);
});

modalClose.addEventListener('click', () => successModal.classList.remove('open'));
successModal.addEventListener('click', e => {
  if (e.target === successModal) successModal.classList.remove('open');
});

function shakeElement(el) {
  el.style.animation = 'none';
  el.style.transition = 'transform 0.1s';
  const keyframes = [
    { transform: 'translateX(0)' },
    { transform: 'translateX(-8px)' },
    { transform: 'translateX(8px)' },
    { transform: 'translateX(-6px)' },
    { transform: 'translateX(6px)' },
    { transform: 'translateX(0)' },
  ];
  el.animate(keyframes, { duration: 400 });
}

// ── Smooth scroll enhancement ──────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', e => {
    const target = document.querySelector(anchor.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// ── Trust Indicators – count-up animation ─────────────────────
const trustStats = document.querySelectorAll('.trust-stat[data-count]');

function animateTrustCounter(el) {
  const target   = parseFloat(el.dataset.count);
  const suffix   = el.dataset.suffix || '';
  const decimals = parseInt(el.dataset.decimals || '0', 10);
  const duration = 1800; // ms
  const start    = performance.now();

  function tick(now) {
    const elapsed  = now - start;
    const progress = Math.min(elapsed / duration, 1);
    // Ease-out cubic
    const eased    = 1 - Math.pow(1 - progress, 3);
    const value    = eased * target;
    el.textContent = value.toFixed(decimals) + suffix;
    if (progress < 1) requestAnimationFrame(tick);
    else el.textContent = target.toFixed(decimals) + suffix;
  }

  requestAnimationFrame(tick);
}

const trustObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.querySelectorAll('.trust-stat[data-count]').forEach(el => {
        animateTrustCounter(el);
      });
      trustObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.4 });

const trustSection = document.getElementById('trust');
if (trustSection) trustObserver.observe(trustSection);

// ── Stats counter animation ────────────────────────────────────
function animateCounter(el, target, suffix = '') {
  let start = 0;
  const step = target / 60;
  const timer = setInterval(() => {
    start += step;
    if (start >= target) { start = target; clearInterval(timer); }
    el.textContent = Math.floor(start) + suffix;
  }, 20);
}

const statsObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const statNums = entry.target.querySelectorAll('.stat-num');
      statNums.forEach(num => {
        const text = num.textContent;
        if (text.includes('130'))  animateCounter(num, 130, '+');
        if (text.includes('5.0'))  { /* keep star rating */ }
        if (text.includes('0%'))   animateCounter(num, 0, '%');
      });
      statsObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

const heroStats = document.querySelector('.hero-stats');
if (heroStats) statsObserver.observe(heroStats);

// ── Page load animation ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.body.style.opacity = '0';
  requestAnimationFrame(() => {
    document.body.style.transition = 'opacity 0.5s ease';
    document.body.style.opacity = '1';
  });
});
