/* ==========================================================================
   Portfolio Mateusz Dams – skrypty strony
   Każda funkcja odpowiada za jedną rzecz i uruchamia się tylko wtedy,
   gdy na stronie jest jej element. Kolejność jak sekcje na stronach.
   ========================================================================== */

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const scrollBehavior = reduceMotion ? 'auto' : 'smooth';

/* --- Pomocnicze --- */

// Wywołuje funkcję przy przewijaniu najwyżej raz na klatkę obrazu
function onScrollFrame(callback) {
    let waiting = false;

    window.addEventListener('scroll', () => {
        if (waiting) return;

        waiting = true;
        requestAnimationFrame(() => {
            callback();
            waiting = false;
        });
    }, { passive: true });

    callback();
}

// Przeciąganie listy myszką (dotyk przewija się sam)
function enableMouseDrag(list, canStart, onEnd) {
    let startX = 0;
    let startScroll = 0;
    let dragging = false;

    list.addEventListener('pointerdown', (event) => {
        if (event.pointerType !== 'mouse' || !canStart()) return;

        dragging = true;
        startX = event.clientX;
        startScroll = list.scrollLeft;
        list.classList.add('is-dragging');
        list.setPointerCapture(event.pointerId);
    });

    list.addEventListener('pointermove', (event) => {
        if (dragging) list.scrollLeft = startScroll - (event.clientX - startX);
    });

    const stop = (event) => {
        if (!dragging) return;

        dragging = false;
        list.classList.remove('is-dragging');
        onEnd(event, Math.abs(event.clientX - startX));
    };

    list.addEventListener('pointerup', stop);
    list.addEventListener('pointercancel', stop);
}

/* --- Nagłówek: tło po przewinięciu strony --- */

function initHeader() {
    const header = document.getElementById('header');

    if (!header) return;

    onScrollFrame(() => header.classList.toggle('is-scrolled', window.scrollY > 10));
}

/* --- Menu: podkreślenie sekcji, w której jest odwiedzający --- */

function initMenuSpy() {
    const links = [...document.querySelectorAll('.header__link[href^="#"]')];
    const sections = links.map((link) => document.querySelector(link.getAttribute('href'))).filter(Boolean);

    if (!sections.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            links.forEach((link) => {
                if (link.getAttribute('href') === `#${entry.target.id}`) link.setAttribute('aria-current', 'true');
                else link.removeAttribute('aria-current');
            });
        });
    }, { rootMargin: '-40% 0px -55% 0px' });

    sections.forEach((section) => observer.observe(section));
}

/* --- Wypływanie elementów przy przewijaniu --- */

function initReveal() {
    const items = document.querySelectorAll('.reveal');

    if (reduceMotion || !('IntersectionObserver' in window)) {
        items.forEach((item) => item.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -10% 0px' });

    items.forEach((item) => observer.observe(item));
}

/* --- Pasek technologii (technologies): druga kopia listy dla płynnej pętli --- */

function initTechnologies() {
    const list = document.getElementById('technologies-list');

    if (!list || reduceMotion) return;

    [...list.children].forEach((item) => {
        const copy = item.cloneNode(true);
        copy.setAttribute('aria-hidden', 'true');
        list.append(copy);
    });
}

/* --- Strony treningowe (practice-sites): podgląd strony przy kursorze --- */

function initPracticeSitesPreview() {
    const preview = document.getElementById('practice-sites-preview');
    const canHover = window.matchMedia('(hover: hover) and (min-width: 769px)').matches;

    if (!preview || !canHover) return;

    const image = preview.querySelector('img');

    document.querySelectorAll('.practice-sites__link').forEach((link) => {
        link.addEventListener('mouseenter', () => {
            image.src = link.dataset.preview;
            preview.classList.add('is-visible');
        });

        link.addEventListener('mouseleave', () => preview.classList.remove('is-visible'));

        // Podgląd stoi w wolnym miejscu między nazwą a opisem i jedzie za kursorem w pionie
        link.addEventListener('mousemove', (event) => {
            const box = link.getBoundingClientRect();
            preview.style.left = `${box.left + box.width * .58}px`;
            preview.style.top = `${event.clientY}px`;
        });
    });
}

/* --- Strony treningowe na telefonie: slider ze strzałkami, licznikiem,
   paskiem postępu, przeciąganiem myszką i powolnym samym przewijaniem --- */

function initPracticeSitesSlider() {
    const list = document.querySelector('[data-slider]');

    if (!list) return;

    const section = list.closest('section');
    const items = [...list.children];
    const prev = section.querySelector('[data-slider-prev]');
    const next = section.querySelector('[data-slider-next]');
    const current = section.querySelector('[data-slider-current]');
    const progress = section.querySelector('[data-slider-progress]');
    const mobile = window.matchMedia('(max-width: 768px)');

    section.querySelector('[data-slider-total]').textContent = items.length;

    const max = () => list.scrollWidth - list.clientWidth;
    const step = () => items[1].offsetLeft - items[0].offsetLeft;
    const index = () => (list.scrollLeft >= max() - 2 ? items.length - 1 : Math.round(list.scrollLeft / step()));

    const goTo = (i) => {
        const target = Math.max(0, Math.min(items.length - 1, i));
        list.scrollTo({ left: items[target].offsetLeft - items[0].offsetLeft, behavior: scrollBehavior });
    };

    const update = () => {
        if (!mobile.matches) return;

        const i = index();
        current.textContent = i + 1;
        progress.style.width = `${((i + 1) / items.length) * 100}%`;
        prev.disabled = list.scrollLeft <= 2;
        next.disabled = list.scrollLeft >= max() - 2;
    };

    let timer = null;

    const stopAuto = () => {
        clearInterval(timer);
        timer = null;
    };

    prev.addEventListener('click', () => {
        stopAuto();
        goTo(index() - 1);
    });

    next.addEventListener('click', () => {
        stopAuto();
        goTo(index() + 1);
    });

    list.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();

    // Samo przewijanie co 4 sekundy, dopóki odwiedzający nie dotknie slidera
    if (!reduceMotion && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting && mobile.matches && timer === null) {
                timer = setInterval(() => goTo(index() >= items.length - 1 ? 0 : index() + 1), 4000);
            } else if (!entry.isIntersecting) {
                stopAuto();
            }
        }, { threshold: .6 });

        observer.observe(list);

        ['pointerdown', 'touchstart', 'wheel', 'mouseenter'].forEach((type) => {
            list.addEventListener(type, () => {
                if (!mobile.matches) return;

                stopAuto();
                observer.disconnect();
            }, { passive: true });
        });
    }

    enableMouseDrag(list, () => mobile.matches, (event, moved) => {
        goTo(Math.round(list.scrollLeft / step()));

        // Krótkie kliknięcie bez przesunięcia otwiera stronę
        if (moved < 5) {
            const link = document.elementFromPoint(event.clientX, event.clientY)?.closest('a');
            if (link) link.click();
        }
    });
}

/* --- Kontakt (contact): kopiowanie e-maila --- */

function initCopy() {
    document.querySelectorAll('[data-copy]').forEach((button) => {
        const label = button.textContent;

        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                button.textContent = 'Skopiowano';
            } catch {
                button.textContent = 'Zaznacz i skopiuj';
            }

            setTimeout(() => {
                button.textContent = label;
            }, 2000);
        });
    });
}

/* --- Dolny pasek na telefonie (tabbar): podświetla sekcję, w której jest odwiedzający --- */

function initTabbar() {
    const links = [...document.querySelectorAll('.tabbar__link')];

    if (!links.length) return;

    const sections = links.map((link) => (link.dataset.section === 'top' ? document.body : document.querySelector(link.getAttribute('href'))));

    onScrollFrame(() => {
        const line = window.innerHeight * .35;
        let current = 0;

        sections.forEach((section, i) => {
            if (i > 0 && section && section.getBoundingClientRect().top < line) current = i;
        });

        // Na samym dole strony zawsze podświetla się ostatnia zakładka
        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4) current = links.length - 1;

        links.forEach((link, i) => {
            if (i === current) link.setAttribute('aria-current', 'true');
            else link.removeAttribute('aria-current');
        });
    });
}

/* --- Galeria na podstronach projektów (gallery): slider ze strzałkami,
   licznikiem, paskiem postępu i przeciąganiem myszką --- */

function initGallery() {
    const gallery = document.getElementById('gallery');

    if (!gallery) return;

    const items = [...gallery.children];
    const prev = document.querySelector('[data-gallery="prev"]');
    const next = document.querySelector('[data-gallery="next"]');
    const current = document.getElementById('gallery-current');
    const progress = document.getElementById('gallery-progress');

    document.getElementById('gallery-total').textContent = items.length;

    const max = () => gallery.scrollWidth - gallery.clientWidth;
    const step = () => items[0].offsetWidth + parseFloat(getComputedStyle(gallery).columnGap || 20);

    const goTo = (index) => {
        const target = Math.max(0, Math.min(items.length - 1, index));
        gallery.scrollTo({ left: items[target].offsetLeft - items[0].offsetLeft, behavior: scrollBehavior });
    };

    // Który slajd jest teraz na początku listy
    const activeIndex = () => (gallery.scrollLeft >= max() - 2 ? items.length - 1 : Math.round(gallery.scrollLeft / step()));

    const update = () => {
        const index = activeIndex();

        current.textContent = index + 1;
        progress.style.width = `${((index + 1) / items.length) * 100}%`;
        prev.disabled = gallery.scrollLeft <= 2;
        next.disabled = gallery.scrollLeft >= max() - 2;
        items.forEach((item, i) => item.classList.toggle('is-active', i === index));
    };

    prev.addEventListener('click', () => goTo(activeIndex() - 1));
    next.addEventListener('click', () => goTo(activeIndex() + 1));

    gallery.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') goTo(activeIndex() + 1);
        if (event.key === 'ArrowLeft') goTo(activeIndex() - 1);
    });

    gallery.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();

    enableMouseDrag(gallery, () => true, () => goTo(Math.round(gallery.scrollLeft / step())));
}

/* --- Podgląd ćwiczenia (live-preview): strona w ramce, skalowana do szerokości sceny --- */

function initLivePreview() {
    const stage = document.querySelector('[data-preview-stage]');

    if (!stage) return;

    const frame = stage.querySelector('iframe');
    const options = document.querySelectorAll('.live-preview__option');
    const size = document.querySelector('.live-preview__size');

    // Na telefonie podgląd startuje od widoku telefonu
    let width = window.matchMedia('(max-width: 768px)').matches ? 402 : 1440;

    const fit = () => {
        const full = width === 1440;
        const scale = full ? stage.clientWidth / 1440 : Math.min(1, (stage.clientWidth - 32) / width);

        stage.classList.toggle('is-narrow', !full);
        frame.style.width = `${width}px`;
        frame.style.height = `${Math.round((stage.clientHeight - (full ? 0 : 24)) / scale)}px`;
        frame.style.transform = `translateX(-50%) scale(${scale})`;
        size.textContent = `${width} px`;

        options.forEach((option) => {
            const active = Number(option.dataset.width) === width;
            option.classList.toggle('is-active', active);
            option.setAttribute('aria-pressed', active);
        });
    };

    options.forEach((option) => {
        option.addEventListener('click', () => {
            width = Number(option.dataset.width);
            fit();
        });
    });

    window.addEventListener('resize', fit);
    fit();
}

/* --- Mapa podstron ćwiczenia (site-map): kliknięta podstrona trafia do podglądu --- */

function initSiteMap() {
    const pages = document.querySelectorAll('.site-map__page');
    const stage = document.querySelector('[data-preview-stage]');

    if (!pages.length || !stage) return;

    const frame = stage.querySelector('iframe');
    const path = document.querySelector('.live-preview__path');
    const folder = frame.getAttribute('src').replace(/[^/]*$/, '');

    pages.forEach((page) => {
        page.addEventListener('click', () => {
            const file = page.dataset.page;

            frame.src = folder + file;
            path.textContent = `mateusz-dams.github.io/${stage.dataset.site}/${file === 'index.html' ? '' : file}`;

            pages.forEach((item) => {
                item.classList.toggle('is-active', item === page);

                if (item === page) item.setAttribute('aria-current', 'true');
                else item.removeAttribute('aria-current');
            });

            document.getElementById('podglad').scrollIntoView({ behavior: scrollBehavior });
        });
    });
}

/* --- Start --- */

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMenuSpy();
    initReveal();
    initTechnologies();
    initPracticeSitesPreview();
    initPracticeSitesSlider();
    initCopy();
    initTabbar();
    initGallery();
    initLivePreview();
    initSiteMap();
});
