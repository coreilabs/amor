const header = document.querySelector("[data-header]");
const nav = document.querySelector("[data-nav]");
const navToggle = document.querySelector("[data-nav-toggle]");
const parallaxItems = document.querySelectorAll("[data-parallax]");
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

const updateParallax = () => {
  if (reducedMotion) return;

  parallaxItems.forEach((item) => {
    const speed = Number(item.dataset.parallax || 0.12);
    const container = item.closest("section") || item.parentElement || item;
    const rect = container.getBoundingClientRect();
    const rawOffset = (rect.top - window.innerHeight * 0.5) * speed;
    const offset = Math.max(-180, Math.min(180, rawOffset));
    item.style.setProperty("--parallax-offset", `${offset}px`);
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

const setupPartnerTabs = () => {
  const tabs = [...document.querySelectorAll("[data-partner-tab]")];
  const panels = [...document.querySelectorAll("[data-partner-panel]")];
  if (!tabs.length || !panels.length) return;

  let activeIndex = 0;
  let timer;

  const activate = (index, shouldResetTimer = true) => {
    activeIndex = (index + tabs.length) % tabs.length;
    const key = tabs[activeIndex].dataset.partnerTab;

    tabs.forEach((tab) => {
      const isActive = tab.dataset.partnerTab === key;
      tab.classList.toggle("is-active", isActive);
      tab.setAttribute("aria-selected", String(isActive));
    });

    panels.forEach((panel) => {
      const isActive = panel.dataset.partnerPanel === key;
      panel.classList.toggle("is-active", isActive);
      panel.toggleAttribute("aria-hidden", !isActive);
    });

    if (shouldResetTimer) resetTimer();
  };

  const resetTimer = () => {
    window.clearInterval(timer);
    timer = window.setInterval(() => activate(activeIndex + 1, false), 6200);
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => activate(index));
    tab.addEventListener("keydown", (event) => {
      if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") return;
      event.preventDefault();
      const direction = event.key === "ArrowRight" ? 1 : -1;
      const nextIndex = (index + direction + tabs.length) % tabs.length;
      tabs[nextIndex].focus();
      activate(nextIndex);
    });
  });

  resetTimer();
};

const setupLightbox = () => {
  const items = [...document.querySelectorAll(".gallery-item:not(.swiper-slide-duplicate)")];
  const lightbox = document.querySelector("[data-lightbox]");
  const image = document.querySelector("[data-lightbox-image]");
  const title = document.querySelector("[data-lightbox-title]");
  const counter = document.querySelector("[data-lightbox-counter]");
  const closeButton = document.querySelector("[data-lightbox-close]");
  const prevButton = document.querySelector("[data-lightbox-prev]");
  const nextButton = document.querySelector("[data-lightbox-next]");
  const stage = document.querySelector("[data-lightbox-stage]");
  if (!items.length || !lightbox || !image || !title || !counter) return;

  const gallery = items.map((item) => ({
    src: item.dataset.gallerySrc,
    title: item.dataset.galleryTitle,
    alt: item.querySelector("img")?.alt || item.dataset.galleryTitle
  }));

  let currentIndex = 0;
  let lastFocusedElement = null;
  let touchStartX = 0;
  let touchStartY = 0;

  const render = () => {
    const item = gallery[currentIndex];
    image.src = item.src;
    image.alt = item.alt;
    title.textContent = item.title;
    counter.textContent = `${currentIndex + 1} / ${gallery.length}`;
  };

  const open = (index) => {
    currentIndex = index;
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
    currentIndex = (currentIndex + direction + gallery.length) % gallery.length;
    render();
  };

  document.querySelectorAll(".gallery-item").forEach((item) => {
    item.addEventListener("click", () => {
      open(Number(item.dataset.galleryIndex || 0));
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

  setupPartnerTabs();

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
