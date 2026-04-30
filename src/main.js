console.log("🦷 Vijaya Dental: Initializing scripts...");

// Safe Base Path handling (Vite vs Live Server)
const getBase = () => {
  try {
    const vBase = import.meta.env.BASE_URL;
    return vBase && vBase !== '/' ? vBase : './';
  } catch (e) {
    return './';
  }
};
const BASE = getBase();
console.log("🦷 Vijaya Dental: Base path set to", BASE);

const bg1 = document.getElementById('slideBg1');
const bg2 = document.getElementById('slideBg2');
const bg3 = document.getElementById('slideBg3');

// Helper to set background safely
const setBg = (el, img) => {
  if (!el) return;
  const path = BASE.endsWith('/') ? `${BASE}${img}` : `${BASE}/${img}`;
  el.style.backgroundImage = `url('${path}')`;
};

setBg(bg1, 'images/general/dental-treatment-hero.webp');
setBg(bg2, 'images/smile-design/smile-design-treatment.webp');

/* ============================================================
   VIJAYA DENTAL – MAIN JS
   ============================================================ */

// ── Navbar scroll effect ───────────────────────────────────────
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 50);
  updateActiveNavLink();
});

// ── Hamburger menu ─────────────────────────────────────────────
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

if (hamburger && navLinks) {
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
}

// ── Active nav link on scroll ──────────────────────────────────
function updateActiveNavLink() {
  const sections = document.querySelectorAll('section[id]');
  const scrollY = window.scrollY + 100;
  sections.forEach(sec => {
    const top    = sec.offsetTop;
    const height = sec.offsetHeight;
    const id     = sec.getAttribute('id');
    const link   = document.querySelector(`.nav-link[href="#${id}"], .nav-link[href="index.html#${id}"]`);
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
const heroSlider = document.getElementById('heroSlider');
let currentSlide = 0;
let slideTimer   = null;

if (heroSlider && slides.length > 0 && prevBtn && nextBtn) {
  const goToSlide = (n) => {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
  };

  const startAutoSlide = () => {
    clearInterval(slideTimer);
    slideTimer = setInterval(() => goToSlide(currentSlide + 1), 5000);
  };

  prevBtn.addEventListener('click', () => { goToSlide(currentSlide - 1); startAutoSlide(); });
  nextBtn.addEventListener('click', () => { goToSlide(currentSlide + 1); startAutoSlide(); });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { goToSlide(i); startAutoSlide(); });
  });

  startAutoSlide();

  // Touch/swipe support
  let touchStartX = 0;
  heroSlider.addEventListener('touchstart', e => {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });

  heroSlider.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) > 50) {
      goToSlide(currentSlide + (dx < 0 ? 1 : -1));
      startAutoSlide();
    }
  }, { passive: true });
}

// ── Scroll Reveal ──────────────────────────────────────────────
const revealElements = document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right');

if (revealElements.length > 0) {
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.05, rootMargin: '0px 0px 50px 0px' });

  revealElements.forEach(el => revealObserver.observe(el));
}

// ── Video Autoplay Observer ──────────────────────────────────
const galleryVideos = document.querySelectorAll('.gallery-item video');
if (galleryVideos.length > 0) {
  const videoObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.play().catch(() => {}); // Catch block for browsers that block autoplay
      } else {
        entry.target.pause();
      }
    });
  }, { threshold: 0.2 });

  galleryVideos.forEach(vid => videoObserver.observe(vid));
}

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
const lightboxVid   = document.getElementById('lightboxVid');
const lightboxClose = document.getElementById('lightboxClose');
const lightboxPrev  = document.getElementById('lightboxPrev');
const lightboxNext  = document.getElementById('lightboxNext');
let currentGallery  = 0;

if (galleryItems.length > 0 && lightbox && lightboxImg) {
  const gallerySrcs   = Array.from(galleryItems).map(item => item.dataset.src);

  const openLightbox = (index) => {
    currentGallery = index;
    const src = gallerySrcs[currentGallery];
    const itemElement = galleryItems[currentGallery];
    
    let altText = 'Gallery Media';
    const imgEl = itemElement.querySelector('img');
    if (imgEl) altText = imgEl.alt;

    if (src.endsWith('.mp4') || src.endsWith('.webm')) {
      lightboxImg.style.display = 'none';
      if (lightboxVid) {
        lightboxVid.style.display = 'block';
        lightboxVid.src = src;
        lightboxVid.play();
      }
    } else {
      if (lightboxVid) {
        lightboxVid.style.display = 'none';
        lightboxVid.pause();
      }
      lightboxImg.style.display = 'block';
      lightboxImg.src = src;
      lightboxImg.alt = altText;
    }
    lightbox.classList.add('open');
    document.body.style.overflow = 'hidden';
  };

  const closeLightbox = () => {
    lightbox.classList.remove('open');
    document.body.style.overflow = '';
    if (lightboxVid) lightboxVid.pause();
  };

  galleryItems.forEach((item, i) => {
    item.addEventListener('click', () => openLightbox(i));
  });

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', e => { if (e.target === lightbox) closeLightbox(); });

  if (lightboxPrev) {
    lightboxPrev.addEventListener('click', () => {
      openLightbox((currentGallery - 1 + gallerySrcs.length) % gallerySrcs.length);
    });
  }

  if (lightboxNext) {
    lightboxNext.addEventListener('click', () => {
      openLightbox((currentGallery + 1) % gallerySrcs.length);
    });
  }

  document.addEventListener('keydown', e => {
    if (!lightbox.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft' && lightboxPrev)  lightboxPrev.click();
    if (e.key === 'ArrowRight' && lightboxNext) lightboxNext.click();
  });
}

// ── Appointment Form ───────────────────────────────────────────
const form        = document.getElementById('appointmentForm');
const submitBtn   = document.getElementById('submit-appointment');
const successModal = document.getElementById('successModal');
const modalClose  = document.getElementById('modalClose');

if (form && submitBtn) {
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    // Basic validation
    const nameInput = document.getElementById('patient-name');
    const phoneInput = document.getElementById('patient-phone');
    if (!nameInput || !phoneInput) return;

    const name  = nameInput.value.trim();
    const phone = phoneInput.value.trim();

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
      if (successModal) successModal.classList.add('open');

      // WhatsApp fallback - open WA with pre-filled message
      const msg = encodeURIComponent(
        `Hello Vijaya Dental, I'd like to book an appointment.\n\nName: ${name}\nPhone: ${phone}`
      );
      window.open(`https://wa.me/919177617437?text=${msg}`, '_blank');
    }, 1200);
  });
}

if (modalClose) modalClose.addEventListener('click', () => successModal.classList.remove('open'));
if (successModal) {
  successModal.addEventListener('click', e => {
    if (e.target === successModal) successModal.classList.remove('open');
  });
}

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
  if (!el) return;
  const target   = parseFloat(el.dataset.count);
  const suffix   = el.dataset.suffix || '';
  const decimals = parseInt(el.dataset.decimals || '0', 10);
  const duration = 1800; // ms
  const start    = performance.now();

  function tick(now) {
    const elapsed  = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased    = 1 - Math.pow(1 - progress, 3);
    const value    = eased * target;
    el.innerHTML = value.toFixed(decimals) + suffix;
    if (progress < 1) requestAnimationFrame(tick);
    else {
      el.innerHTML = target.toFixed(decimals) + suffix;
      if (window.lucide) window.lucide.createIcons();
    }
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

// ── Page load animation ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.body.style.transition = 'opacity 0.6s ease';
    document.body.style.opacity = '1';
  }, 50); // Small delay to ensure styles are ready
});
