const header = document.querySelector("[data-header]");
const nav = document.querySelector("[data-nav]");
const navToggle = document.querySelector("[data-nav-toggle]");
const parallaxItems = [...document.querySelectorAll("[data-parallax]")];
const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

const setHeaderState = () => {
  header?.classList.toggle("is-scrolled", window.scrollY > 20);
};

const setMenuIcon = (name) => {
  const icon = navToggle?.querySelector("svg");
  if (!icon) return;
  icon.outerHTML = `<i data-lucide="${name}" aria-hidden="true"></i>`;
  window.lucide?.createIcons();
};

setHeaderState();
window.addEventListener("scroll", setHeaderState, { passive: true });

navToggle?.addEventListener("click", () => {
  const isOpen = nav?.classList.toggle("is-open");
  document.body.classList.toggle("nav-open", Boolean(isOpen));
  navToggle.setAttribute("aria-expanded", String(Boolean(isOpen)));
  setMenuIcon(isOpen ? "x" : "menu");
});

nav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    nav.classList.remove("is-open");
    document.body.classList.remove("nav-open");
    navToggle?.setAttribute("aria-expanded", "false");
    setMenuIcon("menu");
  });
});

const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

const updateParallax = () => {
  if (reducedMotion || !parallaxItems.length) return;

  const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
  const viewportCenter = viewportHeight / 2;

  parallaxItems.forEach((item) => {
    const speed = Number.parseFloat(item.dataset.parallax || "0.16");
    const container = item.closest("section") || item.parentElement || item;
    const rect = container.getBoundingClientRect();

    if (rect.bottom < -viewportHeight * 0.25 || rect.top > viewportHeight * 1.25) return;

    const sectionCenter = rect.top + rect.height / 2;
    const distanceFromViewportCenter = viewportCenter - sectionCenter;
    const maxOffset = Math.max(72, Math.min(230, rect.height * 0.22));
    const offset = clamp(distanceFromViewportCenter * speed, -maxOffset, maxOffset);

    item.style.setProperty("--parallax-offset", `${offset.toFixed(2)}px`);
  });
};

let ticking = false;
const requestParallax = () => {
  if (ticking) return;
  ticking = true;
  requestAnimationFrame(() => {
    updateParallax();
    ticking = false;
  });
};

updateParallax();
window.addEventListener("load", requestParallax);
window.addEventListener("scroll", requestParallax, { passive: true });
window.addEventListener("resize", requestParallax);

const setupLightbox = () => {
  const sourceItems = [
    ...document.querySelectorAll(".gallery-item:not(.swiper-slide-duplicate), [data-lightbox-src]")
  ];
  const lightbox = document.querySelector("[data-lightbox]");
  const image = document.querySelector("[data-lightbox-image]");
  const title = document.querySelector("[data-lightbox-title]");
  const counter = document.querySelector("[data-lightbox-counter]");
  const closeButton = document.querySelector("[data-lightbox-close]");
  const prevButton = document.querySelector("[data-lightbox-prev]");
  const nextButton = document.querySelector("[data-lightbox-next]");
  const stage = document.querySelector("[data-lightbox-stage]");
  if (!sourceItems.length || !lightbox || !image || !title || !counter) return;

  const getItemGroup = (item) => item.dataset.lightboxGroup || "ambientes";
  const getItemSrc = (item) => item.dataset.lightboxSrc || item.dataset.gallerySrc;
  const getItemTitle = (item) => item.dataset.lightboxCaption || item.dataset.galleryTitle;
  const galleries = new Map();

  sourceItems.forEach((item) => {
    const src = getItemSrc(item);
    const itemTitle = getItemTitle(item);
    if (!src || !itemTitle) return;

    const group = getItemGroup(item);
    const gallery = galleries.get(group) || [];
    gallery.push({
      src,
      title: itemTitle,
      alt: item.querySelector("img")?.alt || itemTitle
    });
    galleries.set(group, gallery);
  });

  let currentIndex = 0;
  let currentGallery = [];
  let lastFocusedElement = null;
  let touchStartX = 0;
  let touchStartY = 0;

  const render = () => {
    const item = currentGallery[currentIndex];
    if (!item) return;

    image.src = item.src;
    image.alt = item.alt;
    title.textContent = item.title;
    counter.textContent = `${currentIndex + 1} / ${currentGallery.length}`;
  };

  const getTriggerIndex = (item, gallery) => {
    if (item.dataset.galleryIndex) return Number(item.dataset.galleryIndex);

    const src = getItemSrc(item);
    const itemTitle = getItemTitle(item);
    return Math.max(0, gallery.findIndex((entry) => entry.src === src && entry.title === itemTitle));
  };

  const open = (group, index) => {
    currentGallery = galleries.get(group) || [];
    if (!currentGallery.length) return;

    currentIndex = clamp(index, 0, currentGallery.length - 1);
    lastFocusedElement = document.activeElement;
    render();
    lightbox.hidden = false;
    document.body.classList.add("lightbox-open");
    closeButton?.focus();
  };

  const close = () => {
    lightbox.hidden = true;
    document.body.classList.remove("lightbox-open");
    image.removeAttribute("src");
    lastFocusedElement?.focus?.();
  };

  const move = (direction) => {
    if (!currentGallery.length) return;

    currentIndex = (currentIndex + direction + currentGallery.length) % currentGallery.length;
    render();
  };

  document.querySelectorAll(".gallery-item, [data-lightbox-src]").forEach((item) => {
    item.addEventListener("click", () => {
      const group = getItemGroup(item);
      const gallery = galleries.get(group) || [];
      open(group, getTriggerIndex(item, gallery));
    });
  });

  closeButton?.addEventListener("click", close);
  prevButton?.addEventListener("click", () => move(-1));
  nextButton?.addEventListener("click", () => move(1));

  lightbox.addEventListener("click", (event) => {
    if (event.target === lightbox) close();
  });

  stage?.addEventListener("touchstart", (event) => {
    touchStartX = event.changedTouches[0].clientX;
    touchStartY = event.changedTouches[0].clientY;
  }, { passive: true });

  stage?.addEventListener("touchend", (event) => {
    const touch = event.changedTouches[0];
    const deltaX = touch.clientX - touchStartX;
    const deltaY = touch.clientY - touchStartY;
    if (Math.abs(deltaX) < 45 || Math.abs(deltaX) < Math.abs(deltaY)) return;
    move(deltaX < 0 ? 1 : -1);
  }, { passive: true });

  document.addEventListener("keydown", (event) => {
    if (lightbox.hidden) return;
    if (event.key === "Escape") close();
    if (event.key === "ArrowLeft") move(-1);
    if (event.key === "ArrowRight") move(1);
  });
};

const cookieConsentKey = "amorFraternoCookieConsent";
const defaultCookieConsent = {
  necessary: true,
  analytics: false,
  marketing: false
};

const getStoredCookieConsent = () => {
  try {
    const stored = JSON.parse(localStorage.getItem(cookieConsentKey));
    if (!stored || typeof stored !== "object") return null;

    return {
      ...defaultCookieConsent,
      analytics: Boolean(stored.analytics),
      marketing: Boolean(stored.marketing)
    };
  } catch (error) {
    return null;
  }
};

const updateGoogleConsent = (consent) => {
  if (typeof window.gtag !== "function") return;

  window.gtag("consent", "update", {
    ad_storage: consent.marketing ? "granted" : "denied",
    analytics_storage: consent.analytics ? "granted" : "denied",
    ad_user_data: consent.marketing ? "granted" : "denied",
    ad_personalization: consent.marketing ? "granted" : "denied"
  });
};

const saveCookieConsent = (consent) => {
  const normalizedConsent = {
    ...defaultCookieConsent,
    analytics: Boolean(consent.analytics),
    marketing: Boolean(consent.marketing),
    updatedAt: new Date().toISOString()
  };

  try {
    localStorage.setItem(cookieConsentKey, JSON.stringify(normalizedConsent));
  } catch (error) {
    // Consent still applies for the current page view when storage is unavailable.
  }

  updateGoogleConsent(normalizedConsent);
  return normalizedConsent;
};

const setupCookieConsent = () => {
  const banner = document.querySelector("[data-cookie-banner]");
  const modal = document.querySelector("[data-cookie-modal]");
  const closeButton = document.querySelector("[data-cookie-close]");
  const analyticsToggle = document.querySelector('[data-cookie-toggle="analytics"]');
  const marketingToggle = document.querySelector('[data-cookie-toggle="marketing"]');
  if (!banner || !modal) return;

  let lastFocusedElement = null;

  const setToggleState = (consent) => {
    if (analyticsToggle) analyticsToggle.checked = Boolean(consent.analytics);
    if (marketingToggle) marketingToggle.checked = Boolean(consent.marketing);
  };

  const hideBanner = () => {
    banner.hidden = true;
    document.body.classList.remove("cookie-banner-visible");
  };

  const showBanner = () => {
    banner.hidden = false;
    document.body.classList.add("cookie-banner-visible");
    window.lucide?.createIcons();
  };

  const closePreferences = () => {
    modal.hidden = true;
    document.body.classList.remove("cookie-modal-open");
    lastFocusedElement?.focus?.();
  };

  const openPreferences = () => {
    lastFocusedElement = document.activeElement;
    setToggleState(getStoredCookieConsent() || defaultCookieConsent);
    modal.hidden = false;
    document.body.classList.add("cookie-modal-open");
    window.lucide?.createIcons();
    closeButton?.focus();
  };

  const persistChoice = (consent) => {
    saveCookieConsent(consent);
    hideBanner();
    closePreferences();
  };

  const acceptAll = () => {
    persistChoice({
      analytics: true,
      marketing: true
    });
  };

  const rejectOptional = () => {
    persistChoice({
      analytics: false,
      marketing: false
    });
  };

  const savePreferences = () => {
    persistChoice({
      analytics: Boolean(analyticsToggle?.checked),
      marketing: Boolean(marketingToggle?.checked)
    });
  };

  document.querySelectorAll("[data-cookie-customize]").forEach((button) => {
    button.addEventListener("click", openPreferences);
  });

  document.querySelectorAll("[data-cookie-accept]").forEach((button) => {
    button.addEventListener("click", acceptAll);
  });

  document.querySelectorAll("[data-cookie-reject]").forEach((button) => {
    button.addEventListener("click", rejectOptional);
  });

  document.querySelectorAll("[data-cookie-save]").forEach((button) => {
    button.addEventListener("click", savePreferences);
  });

  closeButton?.addEventListener("click", closePreferences);

  modal.addEventListener("click", (event) => {
    if (event.target === modal) closePreferences();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) closePreferences();
  });

  const storedConsent = getStoredCookieConsent();
  if (storedConsent) {
    updateGoogleConsent(storedConsent);
    hideBanner();
    return;
  }

  setToggleState(defaultCookieConsent);
  showBanner();
};

setupCookieConsent();

window.addEventListener("load", () => {
  window.lucide?.createIcons();

  if (window.AOS) {
    window.AOS.init({
      duration: 720,
      easing: "ease-out-cubic",
      once: true,
      offset: 80,
      disable: reducedMotion
    });
  }

  if (window.Swiper) {
    new window.Swiper(".gallery-swiper", {
      loop: true,
      speed: 700,
      spaceBetween: 18,
      slidesPerView: 1,
      grabCursor: true,
      autoplay: reducedMotion
        ? false
        : {
            delay: 4200,
            disableOnInteraction: false
          },
      pagination: {
        el: ".swiper-pagination",
        clickable: true
      },
      navigation: {
        prevEl: ".swiper-button-prev-custom",
        nextEl: ".swiper-button-next-custom"
      },
      breakpoints: {
        720: {
          slidesPerView: 2
        },
        1040: {
          slidesPerView: 3
        }
      }
    });
  }

  setupLightbox();
});
