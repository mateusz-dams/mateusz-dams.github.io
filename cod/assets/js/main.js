/**
 * COD 2026 – skrypty strony.
 * Bez zależności (bez jQuery). Każdy moduł działa tylko, gdy na stronie jest jego element.
 */
(function () {
  'use strict';

  var doc = document.documentElement;
  doc.classList.add('js');

  var CFG = window.COD || { i18n: {}, hours: {} };
  var T = CFG.i18n || {};
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canHover = window.matchMedia('(hover: hover)').matches;

  /* ---------- Pomiar: zdarzenia trafiają do dataLayer (wysyła je GTM po zgodzie) ---------- */
  window.dataLayer = window.dataLayer || [];
  function track(event, params) {
    var o = { event: event };
    if (params) { for (var k in params) { if (Object.prototype.hasOwnProperty.call(params, k)) { o[k] = params[k]; } } }
    window.dataLayer.push(o);
  }
  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('a, button') : null;
    if (!a) { return; }
    var loc = a.getAttribute('data-track-location') || '';
    var href = a.getAttribute('href') || '';
    if (href.indexOf('tel:') === 0) { track('cod_phone_click', { location: loc }); }
    else if (href.indexOf('mailto:') === 0) { track('cod_email_click', { location: loc }); }
    else if (a.getAttribute('data-track')) {
      var p = { location: loc };
      if (a.getAttribute('data-package')) { p.package = a.getAttribute('data-package'); }
      track(a.getAttribute('data-track'), p);
    }
  });

  /* ---------- H1 na pierwszym ekranie: słowa pojawiają się po kolei ---------- */
  var h1 = $('[data-split]');
  if (h1 && !reduce) {
    var words = h1.textContent.trim().split(/\s+/);
    h1.textContent = '';
    words.forEach(function (w, i) {
      var s = document.createElement('span');
      s.className = 'w'; s.textContent = w;
      s.style.animationDelay = (0.18 + i * 0.07) + 's';
      h1.appendChild(s);
      if (i < words.length - 1) { h1.appendChild(document.createTextNode(' ')); }
    });
  }

  /* ---------- Menu pełnoekranowe ---------- */
  var menuBtn = $('.menu-btn'), menu = $('#menu'), header = $('#naglowek');
  function setMenu(open) {
    if (!menu || !menuBtn) { return; }
    menu.classList.toggle('is-open', open);
    doc.classList.toggle('menu-open', open);
    if (header) {
      if (open) { header.classList.add('is-solid'); header.classList.remove('is-hidden'); } else { window.requestAnimationFrame(onScroll); }
    }
    menuBtn.setAttribute('aria-expanded', String(open));
    menuBtn.setAttribute('aria-label', open ? (T.menuClose || 'Zamknij menu') : (T.menuOpen || 'Otwórz menu'));
    document.body.style.overflow = open ? 'hidden' : '';
    [$('#tresc'), $('.footer'), $('#fab'), $('.cta-band')].forEach(function (el) {
      if (el) { if (open) { el.setAttribute('inert', ''); } else { el.removeAttribute('inert'); } }
    });
    if (open) { setTimeout(function () { var a = $('.menu-links a', menu); if (a) { a.focus({ preventScroll: true }); } }, 350); }
  }
  if (menuBtn && menu) {
    menuBtn.addEventListener('click', function () { setMenu(!menu.classList.contains('is-open')); });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) { setMenu(false); } });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) { setMenu(false); menuBtn.focus(); }
    });
    window.addEventListener('resize', function () { if (window.innerWidth > 1279 && menu.classList.contains('is-open')) { setMenu(false); } });
  }

  /* ---------- Przewijanie: pasek postępu, nagłówek, przyciski pływające, oś czasu, paralaksa ---------- */
  var bar = $('.progress'), fab = $('#fab'), par = $('[data-parallax]');
  var contact = $('#kontakt'), footer = $('.footer');
  var tl = $('#tl'), tlFill = tl ? $('.tl-line i', tl) : null, tlItems = tl ? $$('li', tl) : [];
  var band = $('.cta-band');
  var lastY = window.scrollY, ticking = false;
  // Nagłówek leżący na pierwszym ekranie (CSS ustawia position:fixed) – po zjechaniu pod ten ekran dostaje tło.
  var first = $('#tresc') ? $('#tresc').firstElementChild : null;
  if (first && /^(ARTICLE)$/.test(first.tagName) && first.firstElementChild) { first = first.firstElementChild; }
  var overlay = header && first && window.getComputedStyle(header).position === 'fixed';

  function onScroll() {
    var y = window.scrollY, max = doc.scrollHeight - doc.clientHeight, vh = window.innerHeight;
    if (bar) { bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')'; }
    if (header && !(menu && menu.classList.contains('is-open'))) {
      header.classList.toggle('is-scrolled', y > 8);
      header.classList.toggle('is-solid', !overlay || y > 8);
      header.classList.toggle('is-hidden', y > lastY && y > 400);
    }
    lastY = y;
    if (fab) {
      var inContact = contact ? (contact.getBoundingClientRect().top < vh * 0.85 && contact.getBoundingClientRect().bottom > 0) : false;
      var inFooter = footer ? footer.getBoundingClientRect().top < vh : false;
      fab.classList.toggle('show', y > vh * 0.6 && !inContact && !inFooter);
    }
    if (par && !reduce) {
      var r = par.parentElement.getBoundingClientRect();
      var p = Math.max(-1, Math.min(1, (r.top + r.height / 2 - vh / 2) / vh));
      par.style.transform = 'scale(1.08) translateY(' + (p * -18) + 'px)';
    }
    if (tl && tlFill) {
      var tr = tl.getBoundingClientRect(), mid = vh * 0.6;
      tlFill.style.setProperty('--p', Math.max(0, Math.min(1, (mid - tr.top) / tr.height)));
      tlItems.forEach(function (li) { li.classList.toggle('lit', li.getBoundingClientRect().top < mid); });
    }
    if (band && !reduce) {
      var br = band.getBoundingClientRect();
      var bp = Math.max(0, Math.min(1, (vh - br.top) / (vh + br.height)));
      band.style.setProperty('--pk1', ((1 - bp) * 60) + 'px');
      band.style.setProperty('--pk2', ((1 - bp) * 110) + 'px');
    }
    ticking = false;
  }
  window.addEventListener('scroll', function () { if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; } }, { passive: true });
  if (header) { header.addEventListener('focusin', function () { header.classList.remove('is-hidden'); }); }
  onScroll();

  /* ---------- Pojawianie się sekcji i liczniki ---------- */
  function countUp(el) {
    var end = +el.getAttribute('data-count'), suf = el.getAttribute('data-suffix') || '';
    if (reduce || !end) { el.textContent = end + suf; return; }
    var t0 = performance.now(), dur = 1400;
    (function step(t) {
      var k = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - k, 3);
      el.textContent = Math.round(end * e) + suf;
      if (k < 1) { window.requestAnimationFrame(step); }
    })(t0);
  }
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) { return; }
        en.target.classList.add('in');
        io.unobserve(en.target);
        $$('[data-count]', en.target).forEach(countUp);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    $$('.reveal, .reveal-stagger').forEach(function (el) { io.observe(el); });
  } else {
    $$('.reveal, .reveal-stagger').forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Zakładki oferty (strona główna) ---------- */
  var tabs = $$('.tab'), ink = $('.tab-ink');
  function moveInk(t) { if (ink && t) { ink.style.width = t.offsetWidth + 'px'; ink.style.transform = 'translateX(' + t.offsetLeft + 'px)'; } }
  function selectTab(name, focus) {
    tabs.forEach(function (t) {
      var on = t.getAttribute('data-tab') === name;
      t.setAttribute('aria-selected', on ? 'true' : 'false');
      t.tabIndex = on ? 0 : -1;
      var panel = document.getElementById(t.getAttribute('aria-controls'));
      if (panel) { panel.hidden = !on; }
      if (on) { moveInk(t); if (focus) { t.focus(); } }
    });
  }
  if (tabs.length) {
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () { selectTab(t.getAttribute('data-tab')); track('cod_tab_select', { location: t.getAttribute('data-tab') }); });
      t.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : (e.key === 'ArrowLeft' ? -1 : 0);
        if (e.key === 'Home') { e.preventDefault(); selectTab(tabs[0].getAttribute('data-tab'), true); return; }
        if (e.key === 'End') { e.preventDefault(); selectTab(tabs[tabs.length - 1].getAttribute('data-tab'), true); return; }
        if (!d) { return; }
        e.preventDefault();
        selectTab(tabs[(i + d + tabs.length) % tabs.length].getAttribute('data-tab'), true);
      });
    });
    window.addEventListener('resize', function () { moveInk($('.tab[aria-selected="true"]')); });
    window.addEventListener('load', function () { moveInk($('.tab[aria-selected="true"]')); });
    moveInk($('.tab[aria-selected="true"]'));
    // Odnośniki #dzieci / #dorosli / #firmy (np. z menu) otwierają właściwą zakładkę.
    var openFromHash = function () {
      var h = (window.location.hash || '').replace('#', '');
      if (h && $('.tab[data-tab="' + h + '"]')) {
        selectTab(h);
        var sec = document.getElementById('oferta');
        if (sec) { sec.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' }); }
      }
    };
    window.addEventListener('hashchange', openFromHash);
    openFromHash();
  }

  /* ---------- Okno „Szczegóły” (usługi, zespół, produkty) ----------
     Komputer: okno na środku – zdjęcie po lewej, przewijana treść po prawej, przyciski zawsze widoczne.
     Telefon: arkusz wysuwany od dołu (zamykany krzyżykiem, Esc, kliknięciem obok lub przesunięciem w dół). */
  var dlg = $('#svc-dialog'), dBody = $('#d-body'), dForm = $('#d-form');
  var dCta = $('#d-cta'), dPage = $('#d-page'), dKicker = $('#d-kicker');
  var dMedia = $('#d-media'), dOwn = $('#d-own'), dScroll = $('#d-scroll'), dMain = dlg ? $('.m-main', dlg) : null;
  var kickerDefault = dKicker ? dKicker.textContent : '';
  var lastOpener = null;
  function moreHint() {
    if (!dScroll || !dMain) { return; }
    var more = dScroll.scrollHeight - dScroll.clientHeight - dScroll.scrollTop > 8;
    dMain.classList.toggle('has-more', more);
    dMain.classList.toggle('is-scrolled', dScroll.scrollTop > 4);
  }
  function openSvc(key) {
    var card = $('[data-svc="' + key + '"]');
    if (!card || !dlg || typeof dlg.showModal !== 'function') { return false; }
    var tpl = $('template.svc-full', card);
    dBody.innerHTML = '';
    if (dOwn) { dOwn.innerHTML = ''; }
    if (dMedia) { dMedia.innerHTML = ''; dMedia.className = 'm-media'; }
    if (tpl) { dBody.appendChild(tpl.content.cloneNode(true)); }
    var h = $('h3', dBody); if (h) { h.id = 'd-title'; }
    // Zdjęcie do lewej kolumny (na telefonie – pasek u góry).
    var fig = $('.d-media', dBody);
    if (fig && dMedia) {
      dMedia.className = 'm-media ' + fig.className.replace(/\bd-media\b/, '').trim().replace(/d-media--/g, 'm-media--');
      while (fig.firstChild) { dMedia.appendChild(fig.firstChild); }
      fig.parentNode.removeChild(fig);
    }
    if (dMedia) { dMedia.hidden = !dMedia.firstElementChild; }
    dlg.classList.toggle('has-media', !!(dMedia && !dMedia.hidden));
    dlg.setAttribute('data-kind', dMedia && /m-media--product/.test(dMedia.className) ? 'product' : (dMedia && !dMedia.hidden ? 'person' : 'text'));
    // Własne przyciski z szablonu zastępują domyślne.
    var own = $('.d-cta', dBody);
    if (own && dOwn) { dOwn.appendChild(own); }
    if (dCta) { dCta.hidden = !!own; }
    if (dKicker) { dKicker.textContent = card.getAttribute('data-kicker') || kickerDefault; }
    if (dPage) {
      var url = card.getAttribute('data-url');
      dPage.hidden = !url;
      if (url) { dPage.setAttribute('href', url); }
    }
    lastOpener = document.activeElement;
    if (dForm) {
      dForm.setAttribute('data-topic', card.getAttribute('data-topic-value') || '');
      dForm.setAttribute('data-service', card.getAttribute('data-service-name') || '');
    }
    dlg.classList.remove('is-closing');
    dlg.showModal();
    if (dScroll) { dScroll.scrollTop = 0; }
    document.documentElement.classList.add('modal-open');
    window.requestAnimationFrame(moreHint);
    track(card.getAttribute('data-view-event') || 'cod_service_view', { service: card.getAttribute('data-service-name') || key });
    return true;
  }
  function closeSvc() { if (dlg && dlg.open) { dlg.close(); } }
  if (dlg) {
    document.addEventListener('click', function (e) {
      var opener = e.target.closest('[data-svc-open]');
      if (opener && openSvc(opener.getAttribute('data-svc-open'))) { e.preventDefault(); return; }
      var card = e.target.closest('[data-svc]');
      if (card && !e.target.closest('a, button, input, select, textarea, label')) { openSvc(card.getAttribute('data-svc')); }
    });
    dlg.addEventListener('close', function () {
      document.documentElement.classList.remove('modal-open');
      dlg.style.transform = '';
      if (lastOpener && lastOpener.focus) { try { lastOpener.focus({ preventScroll: true }); } catch (err) { /* */ } }
    });
    dlg.addEventListener('click', function (e) {
      if (e.target === dlg || e.target.closest('[data-close]')) { closeSvc(); return; }
      var a = e.target.closest('a[href]');
      if (a && a.getAttribute('href').indexOf('#') !== -1 && a.pathname === window.location.pathname) { closeSvc(); }
    });
    if (dScroll) { dScroll.addEventListener('scroll', moreHint, { passive: true }); }
    window.addEventListener('resize', function () { if (dlg.open) { moreHint(); } });
    if (dForm) { dForm.addEventListener('click', closeSvc); }
    // Przesunięcie w dół zamyka arkusz na telefonie (chwyt za górny pasek).
    var top = $('.m-top', dlg), y0 = null;
    if (top) {
      top.addEventListener('touchstart', function (e) { y0 = e.touches[0].clientY; }, { passive: true });
      top.addEventListener('touchmove', function (e) {
        if (y0 === null) { return; }
        var dy = Math.max(0, e.touches[0].clientY - y0);
        dlg.style.transform = 'translateY(' + dy + 'px)';
      }, { passive: true });
      top.addEventListener('touchend', function (e) {
        if (y0 === null) { return; }
        var dy = e.changedTouches[0].clientY - y0;
        y0 = null;
        if (dy > 90) { closeSvc(); } else { dlg.style.transform = ''; }
      });
    }
  }

  /* ---------- Wstępne wypełnienie formularza (z przycisków i z adresu ?temat=&usluga=) ---------- */
  function prefill(topic, service) {
    if (topic) { $$('input[name="dotyczy"]').forEach(function (r) { if (r.value === topic) { r.checked = true; } }); }
    var s = $('#f-usluga');
    if (s && service) { Array.prototype.forEach.call(s.options, function (o) { if (o.text === service) { s.value = o.value || o.text; } }); }
  }
  document.addEventListener('click', function (e) {
    var a = e.target.closest('[data-topic], [data-service]');
    if (a) { prefill(a.getAttribute('data-topic'), a.getAttribute('data-service')); }
  });
  try {
    var qs = new URLSearchParams(window.location.search);
    if (qs.get('temat') || qs.get('usluga')) { prefill(qs.get('temat'), qs.get('usluga')); }
  } catch (err) { /* stare przeglądarki */ }

  /* ---------- Kreator „Od czego zacząć?” ---------- */
  var wiz = $('#wiz'), wizData = null;
  try { wizData = JSON.parse(($('#cod-wizard') || {}).textContent || 'null'); } catch (err) { wizData = null; }
  if (wiz && wizData) {
    var panels = $$('.wiz-panel', wiz), bars = $$('.wiz-steps span', wiz), who = null, current = null;
    var show = function (step) {
      panels.forEach(function (p) { p.hidden = String(p.getAttribute('data-step')) !== String(step); });
      bars.forEach(function (b, i) { b.classList.toggle('on', i < step); });
      var h = $('.wiz-panel[data-step="' + step + '"] h3', wiz); if (h) { h.focus({ preventScroll: true }); }
    };
    var esc = function (s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; };
    $$('[data-who]', wiz).forEach(function (b) {
      b.addEventListener('click', function () {
        who = b.getAttribute('data-who');
        var opts = wizData[who] || [];
        $('#wiz-opts').innerHTML = opts.map(function (o, i) {
          return '<button class="opt" type="button" data-i="' + i + '"><span class="ico"><svg aria-hidden="true"><use href="#i-spark"/></svg></span><span>' + esc(o.q) + '</span></button>';
        }).join('');
        show(2);
      });
    });
    $('#wiz-opts').addEventListener('click', function (e) {
      var b = e.target.closest('[data-i]'); if (!b) { return; }
      current = (wizData[who] || [])[+b.getAttribute('data-i')]; if (!current) { return; }
      $('#wiz-title').textContent = current.title;
      $('#wiz-desc').textContent = current.a;
      $('#wiz-price').textContent = current.price;
      var f = $('#wiz-form');
      var topicMap = { dzieci: 'Dziecka', dorosli: 'Mnie', firmy: 'Firmy lub zespołu' };
      if (f) { f.setAttribute('data-topic', topicMap[who] || ''); f.setAttribute('data-service', current.title); }
      var more = $('#wiz-more'); if (more) { more.setAttribute('href', current.url); }
      show(3);
      track('cod_finder_result', { service: current.title });
    });
    $$('[data-back]', wiz).forEach(function (b) { b.addEventListener('click', function () { show(+b.getAttribute('data-back')); }); });
    var moreBtn = $('#wiz-more');
    if (moreBtn) { moreBtn.addEventListener('click', function (e) { if (current && openSvc(current.svc)) { e.preventDefault(); } }); }
  }

  /* ---------- Akordeony ---------- */
  $$('.acc-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      var open = b.getAttribute('aria-expanded') !== 'true';
      b.setAttribute('aria-expanded', open ? 'true' : 'false');
      var panel = document.getElementById(b.getAttribute('aria-controls'));
      if (panel) { panel.classList.toggle('open', open); }
      if (open) { track('cod_faq_open', { question: b.textContent.trim().slice(0, 80) }); }
    });
  });

  /* ---------- Mapa po kliknięciu ---------- */
  $$('[data-map-load]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var box = btn.closest('[data-map]');
      var f = document.createElement('iframe');
      f.src = box.getAttribute('data-src') || CFG.mapEmbed;
      f.title = T.mapTitle || 'Mapa';
      f.loading = 'lazy';
      f.referrerPolicy = 'no-referrer-when-downgrade';
      box.innerHTML = '';
      box.appendChild(f);
      box.classList.add('is-loaded');
      track('cod_map_load');
    });
  });

  /* ---------- Karuzela produktów i efekt 3D kart ---------- */
  var rail = $('#rail');
  $$('[data-rail]').forEach(function (b) {
    b.addEventListener('click', function () { if (rail) { rail.scrollBy({ left: +b.getAttribute('data-rail') * (rail.clientWidth * 0.8), behavior: reduce ? 'auto' : 'smooth' }); } });
  });
  if (!reduce && canHover) {
    $$('[data-tilt]').forEach(function (c) {
      c.addEventListener('pointermove', function (e) {
        var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - 0.5, y = (e.clientY - r.top) / r.height - 0.5;
        c.style.transform = 'perspective(900px) rotateY(' + (x * 7) + 'deg) rotateX(' + (-y * 7) + 'deg) translateY(-4px)';
      });
      c.addEventListener('pointerleave', function () { c.style.transform = ''; });
    });
    document.addEventListener('pointermove', function (e) {
      var el = e.target.closest ? e.target.closest('.spot') : null; if (!el) { return; }
      var r = el.getBoundingClientRect();
      el.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      el.style.setProperty('--my', (e.clientY - r.top) + 'px');
    }, { passive: true });
    var hero = $('.hero'), glow = $('.hero .glow');
    if (hero && glow) {
      hero.addEventListener('pointermove', function (e) {
        var r = hero.getBoundingClientRect();
        glow.style.setProperty('--gx', (e.clientX - r.left) + 'px');
        glow.style.setProperty('--gy', (e.clientY - r.top) + 'px');
      }, { passive: true });
    }
  }

  /* ---------- Slajdy w sekcji „hero” stron (gdy jest kilka) ---------- */
  $$('[data-slides]').forEach(function (track_) {
    var sec = track_.closest('.fx-hero');
    $$('[data-slide]', sec).forEach(function (b) {
      b.addEventListener('click', function () { track_.scrollBy({ left: +b.getAttribute('data-slide') * track_.clientWidth, behavior: reduce ? 'auto' : 'smooth' }); });
    });
  });

  /* ---------- Hasło w pasku końcowym ---------- */
  var bandH = $('[data-split-scroll]');
  if (bandH && !reduce) {
    var bw = bandH.textContent.trim().split(/\s+/);
    bandH.textContent = '';
    bw.forEach(function (w, i) {
      var s = document.createElement('span'); s.className = 'w'; s.textContent = w; s.style.transitionDelay = (i * 0.09) + 's';
      bandH.appendChild(s);
      if (i < bw.length - 1) { bandH.appendChild(document.createTextNode(' ')); }
    });
  }

  /* ---------- Status godzin kontaktu (czas w Polsce) ---------- */
  (function () {
    var H = CFG.hours || {};
    var toMin = function (s) { var m = /(\d{1,2})[:.]?(\d{2})?/.exec(s || ''); return m ? (+m[1]) * 60 + (+(m[2] || 0)) : null; };
    var from = toMin(H.from), to = toMin(H.to), days = H.days || [];
    if (from === null || to === null) { return; }
    try {
      var parts = new Intl.DateTimeFormat('en-GB', { timeZone: 'Europe/Warsaw', weekday: 'short', hour: '2-digit', minute: '2-digit', hour12: false }).formatToParts(new Date());
      var get = function (k) { for (var i = 0; i < parts.length; i++) { if (parts[i].type === k) { return parts[i].value; } } return ''; };
      var dayIdx = { Sun: 0, Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6 }[get('weekday')];
      var mins = (+get('hour') % 24) * 60 + (+get('minute'));
      var open = days.indexOf(dayIdx) !== -1 && mins >= from && mins < to;
      $$('[data-status]').forEach(function (el) {
        el.classList.toggle('is-open', open);
        var span = $('span', el);
        if (span) { span.textContent = (open ? (T.open || '%s') : (T.closed || '%s')).replace('%s', H.text || ''); }
      });
    } catch (err) { /* zostaje tekst z serwera */ }
  })();

  /* ---------- Formularz kontaktowy ---------- */
  var form = $('#form-kontakt');
  if (form) {
    var status = $('#f-status'), submit = $('#f-submit');
    var F = { imie: $('#f-imie'), tel: $('#f-tel'), email: $('#f-email'), zgoda: $('#f-zgoda') };
    var setErr = function (input, id, msg) {
      var el = document.getElementById(id); if (el) { el.textContent = msg || ''; }
      if (input) { if (msg) { input.setAttribute('aria-invalid', 'true'); } else { input.removeAttribute('aria-invalid'); } }
    };
    var validate = function () {
      var first = null;
      var imie = F.imie.value.trim(), tel = F.tel.value.trim(), mail = F.email.value.trim();
      setErr(F.imie, 'e-imie', imie ? '' : T.errName); if (!imie) { first = F.imie; }
      var telOk = !tel || tel.replace(/\D/g, '').length >= 9, mailOk = !mail || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail);
      setErr(F.tel, 'e-tel', !telOk ? T.errPhone : (!tel && !mail ? T.errContact : ''));
      setErr(F.email, 'e-email', mailOk ? '' : T.errEmail);
      if (!telOk || (!tel && !mail)) { first = first || F.tel; }
      if (!mailOk) { first = first || F.email; }
      setErr(F.zgoda, 'e-zgoda', F.zgoda.checked ? '' : T.errConsent); if (!F.zgoda.checked) { first = first || F.zgoda; }
      return first;
    };
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      status.textContent = ''; status.classList.remove('is-error');
      var bad = validate();
      if (bad) { bad.focus(); track('cod_form_invalid'); return; }
      var data = new FormData(form);
      var topic = data.get('dotyczy') || '', service = data.get('usluga') || '';
      submit.disabled = true;
      var label = $('span', submit); if (label) { label.textContent = T.sending; }
      (CFG.demo ? Promise.resolve({ json: function () { return Promise.resolve({ success: true, data: { message: T.demo } }); } }) : fetch(CFG.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' }))
        .then(function (r) { return r.json().catch(function () { return { success: false }; }); })
        .then(function (res) {
          if (res && res.success) {
            form.reset();
            status.textContent = (res.data && res.data.message) || T.sent;
            track('cod_form_submit', { topic: topic, service: service });
          } else {
            status.classList.add('is-error');
            var errs = res && res.data && res.data.errors ? res.data.errors : null;
            if (errs) {
              if (errs.imie) { setErr(F.imie, 'e-imie', errs.imie); }
              if (errs.telefon) { setErr(F.tel, 'e-tel', errs.telefon); }
              if (errs.email) { setErr(F.email, 'e-email', errs.email); }
              if (errs.zgoda) { setErr(F.zgoda, 'e-zgoda', errs.zgoda); }
            }
            status.textContent = (res && res.data && res.data.message) || T.error;
            track('cod_form_error');
          }
        })
        .catch(function () { status.classList.add('is-error'); status.textContent = T.error; track('cod_form_error'); })
        .then(function () { submit.disabled = false; if (label) { label.textContent = T.send; } });
    });
  }

  /* ---------- Contact Form 7: pomiar wysłania ---------- */
  document.addEventListener('wpcf7mailsent', function () { track('cod_form_submit', { topic: 'cf7', service: '' }); });

  /* ---------- Slider opinii ---------- */
  $$('[data-qslider]').forEach(function (box) {
    var sec = box.closest('section') || box.parentNode;
    var track = $('.qs-track', box), slides = $$('.qs-slide', track), dots = $$('[data-qs-go]', box);
    var prev = $('[data-qs="-1"]', sec), next = $('[data-qs="1"]', sec), count = $('.qs-count b', sec);
    if (!track || slides.length < 2) { return; }
    var idx = 0;
    function step() { return (slides[1].offsetLeft - slides[0].offsetLeft) || track.clientWidth; }
    function fit() { track.style.height = slides[idx].offsetHeight + 'px'; }
    function paint() {
      fit();
      if (count) { count.textContent = String(idx + 1); }
      if (prev) { prev.disabled = idx === 0; }
      if (next) { next.disabled = idx === slides.length - 1; }
      dots.forEach(function (d, i) { if (i === idx) { d.setAttribute('aria-current', 'true'); } else { d.removeAttribute('aria-current'); } });
    }
    function go(i) {
      idx = Math.max(0, Math.min(slides.length - 1, i));
      track.scrollTo({ left: idx * step(), behavior: reduce ? 'auto' : 'smooth' });
      paint();
    }
    var t = null;
    track.addEventListener('scroll', function () {
      window.clearTimeout(t);
      t = window.setTimeout(function () { idx = Math.max(0, Math.min(slides.length - 1, Math.round(track.scrollLeft / step()))); paint(); }, 80);
    }, { passive: true });
    if (prev) { prev.addEventListener('click', function () { go(idx - 1); }); }
    if (next) { next.addEventListener('click', function () { go(idx + 1); }); }
    dots.forEach(function (d) { d.addEventListener('click', function () { go(+d.getAttribute('data-qs-go')); }); });
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { e.preventDefault(); go(idx + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(idx - 1); }
    });
    paint();
    window.addEventListener('resize', fit);
    window.addEventListener('load', fit);
    $$('img', track).forEach(function (im) { if (!im.complete) { im.addEventListener('load', fit); } });
    if (window.ResizeObserver) { var ro = new ResizeObserver(fit); slides.forEach(function (sl) { ro.observe(sl); }); }
  });

  /* ---------- LearnDash i sklep: zabezpieczenie przycisków ----------
     Wtyczki mają własne style (różne w różnych wersjach). Po załadowaniu sprawdzamy, czy napis przycisku jest
     czytelny i czy nic go nie przycina – jeśli tak, poprawiamy to tylko dla tego przycisku. */
  function fixPluginButtons() {
    var sel = '.learndash-wrapper .learndash_mark_complete_button, .learndash-wrapper #learndash_mark_complete_button, ' +
      '.learndash-wrapper .ld-content-action input[type="submit"], .learndash-wrapper .ld-content-action button, ' +
      '.learndash-wrapper .ld-button, .learndash-wrapper .ld-content-action a, .woocommerce .button, .woocommerce button[type="submit"]';
    $$(sel).forEach(function (b) {
      var cs = window.getComputedStyle(b);
      if (cs.display === 'none') { return; }
      var isInput = b.tagName === 'INPUT';
      // 1. Brak napisu.
      if (isInput && !String(b.value || '').trim() && /mark_complete/.test(b.className + b.id)) { b.value = 'Oznacz jako ukończone'; }
      if (!isInput && !b.textContent.trim() && /mark_complete/.test(b.className + b.id)) { b.appendChild(document.createTextNode('Oznacz jako ukończone')); }
      // 1b. Kolory wpisane przez wtyczkę bezpośrednio w przycisk (np. niebieski z ustawień LearnDash) → kolory strony.
      if (/color|background/i.test(b.getAttribute('style') || '') && !b.hasAttribute('data-cod-fixed')) {
        var ghost = /transparent|reverse/.test(b.className);
        b.style.setProperty('background-color', ghost ? 'transparent' : 'var(--pri-bg-base)', 'important');
        b.style.setProperty('border-color', ghost ? 'var(--ink)' : 'var(--pri-bg-base)', 'important');
        ['color', '-webkit-text-fill-color'].forEach(function (p) { b.style.setProperty(p, ghost ? 'var(--ink)' : 'var(--pri-fg-base)', 'important'); });
        b.setAttribute('data-cod-fixed', '');
        cs = window.getComputedStyle(b);
      }
      // 2. Nieczytelny napis (kolor jak tło, przezroczysty, zerowa wielkość, przesunięty poza przycisk).
      var fg = rgba(cs.color), fill = cs.webkitTextFillColor ? rgba(cs.webkitTextFillColor) : fg, bg = bgOf(b);
      var hidden = fg[3] < 0.5 || fill[3] < 0.5 || parseFloat(cs.fontSize) < 9 || Math.abs(parseFloat(cs.textIndent) || 0) > 50 || ratio(fg, bg) < 3;
      if (hidden) {
        var dark = ratio([14, 42, 48], bg) >= ratio([238, 246, 246], bg) ? '#0e2a30' : '#eef6f6';
        ['color', '-webkit-text-fill-color'].forEach(function (p) { b.style.setProperty(p, dark, 'important'); });
        b.style.setProperty('font-size', '0.98rem', 'important');
        b.style.setProperty('text-indent', '0', 'important');
        b.style.setProperty('opacity', '1', 'important');
        b.style.setProperty('visibility', 'visible', 'important');
        $$('*', b).forEach(function (c) { c.style.setProperty('color', dark, 'important'); c.style.setProperty('-webkit-text-fill-color', dark, 'important'); });
      }
      // 3. Przycinanie przez rodzica.
      var r = b.getBoundingClientRect();
      for (var p = b.parentElement, i = 0; p && i < 6 && p !== document.body; p = p.parentElement, i++) {
        var pc = window.getComputedStyle(p);
        if (pc.overflow === 'visible' && pc.overflowY === 'visible') { continue; }
        if (pc.overflowY === 'auto' || pc.overflowY === 'scroll') { break; }
        var pr = p.getBoundingClientRect();
        if (r.bottom > pr.bottom + 1 || r.top < pr.top - 1 || r.right > pr.right + 1 || r.left < pr.left - 1) {
          p.style.setProperty('overflow', 'visible', 'important');
          p.style.setProperty('height', 'auto', 'important');
          p.style.setProperty('max-height', 'none', 'important');
        }
      }
    });
  }

  /* ---------- LearnDash: własna nawigacja lekcji i tematu ----------
     Pasek LearnDash (Poprzedni / Oznacz jako ukończone / Następny / Wróć) ma w różnych wersjach wtyczki
     własne wysokości i przycinanie. Budujemy z niego pasek z przyciskami motywu: linki i formularz
     „Oznacz jako ukończone” są te same (LearnDash dalej zapisuje postęp), zmienia się tylko wygląd.
     Bez JavaScriptu zostaje oryginalny pasek LearnDash. */
  function sentence(t) {
    t = String(t || '').replace(/\s+/g, ' ').trim();
    return t ? t.charAt(0).toUpperCase() + t.slice(1).toLowerCase() : t;
  }
  function buildLessonNav() {
    if (!document.body.matches('.single-sfwd-lessons, .single-sfwd-topic')) { return; }
    $$('.learndash-wrapper .ld-content-actions').forEach(function (box) {
      if (box.hasAttribute('data-cod-nav')) { return; }
      var acts = $$('.ld-content-action', box);
      var links = $$('a.ld-button, .ld-content-action > a:not(.ld-course-step-back)', box).filter(function (a) { return !a.closest('form'); });
      var prev = null, next = null;
      links.forEach(function (a) {
        var ic = $('.ld-icon', a), cls = (ic ? ic.className : '') + ' ' + a.className;
        var first = acts.length && acts[0].contains(a), last = acts.length && acts[acts.length - 1].contains(a);
        if (/arrow-left|prev/.test(cls) || (first && !/arrow-right/.test(cls))) { prev = prev || a; } else if (/arrow-right|next/.test(cls) || last) { next = next || a; }
      });
      var form = $('form.sfwd-mark-complete, form#sfwd-mark-complete', box);
      var backA = $('.ld-course-step-back', box) || $('.learn-hero__back a');
      var nav = document.createElement('nav');
      nav.className = 'lesson-nav';
      nav.setAttribute('aria-label', 'Nawigacja lekcji');
      function arrowLink(a, dir) {
        var x = document.createElement('a');
        x.className = 'btn btn--ghost lesson-nav__' + dir;
        x.href = a.getAttribute('href');
        var label = sentence(($('.ld-text', a) || a).textContent);
        var ico = '<svg class="ico" aria-hidden="true" focusable="false"><use href="#i-' + (dir === 'prev' ? 'left' : 'arrow') + '"/></svg>';
        x.innerHTML = dir === 'prev' ? ico + '<span></span>' : '<span></span>' + ico;
        $('span', x).textContent = label || (dir === 'prev' ? 'Wstecz' : 'Dalej');
        return x;
      }
      var cPrev = document.createElement('div'); cPrev.className = 'lesson-nav__cell lesson-nav__cell--prev';
      var cMid = document.createElement('div'); cMid.className = 'lesson-nav__cell lesson-nav__cell--mid';
      var cNext = document.createElement('div'); cNext.className = 'lesson-nav__cell lesson-nav__cell--next';
      if (prev) { cPrev.appendChild(arrowLink(prev, 'prev')); }
      if (next) { cNext.appendChild(arrowLink(next, 'next')); }
      if (form) {
        form.classList.add('lesson-nav__form');
        $$('input[type="submit"], button[type="submit"]', form).forEach(function (btn) {
          btn.classList.add('btn', 'btn--primary', 'lesson-nav__done');
          btn.removeAttribute('style');
          if (btn.tagName === 'INPUT' && !String(btn.value || '').trim()) { btn.value = 'Oznacz jako ukończone'; }
        });
        cMid.appendChild(form);
      } else {
        var doneNow = document.querySelector('.learndash-wrapper .ld-topic-status .ld-status-complete, .learndash-wrapper .ld-lesson-status .ld-status-complete, .learndash-wrapper .ld-status.ld-status-complete');
        if (doneNow) {
          var pill = document.createElement('span'); pill.className = 'lesson-nav__pill';
          pill.innerHTML = '<svg class="ico" aria-hidden="true" focusable="false"><use href="#i-check"/></svg>Ukończono';
          cMid.appendChild(pill);
          if (next) { $('a', cNext).classList.replace('btn--ghost', 'btn--primary'); }
        }
      }
      nav.appendChild(cPrev); nav.appendChild(cMid); nav.appendChild(cNext);
      if (backA) {
        var bp = document.createElement('p'); bp.className = 'lesson-nav__back';
        var b = document.createElement('a'); b.className = 'link-arrow'; b.href = backA.getAttribute('href');
        b.innerHTML = '<svg class="ico" aria-hidden="true" focusable="false"><use href="#i-left"/></svg><span>Wróć do szkolenia</span>';
        bp.appendChild(b); nav.appendChild(bp);
      }
      var host = box.closest('.learn-content') || box.parentNode;
      host.parentNode.insertBefore(nav, host.nextSibling);
      box.setAttribute('data-cod-nav', '');
      box.hidden = true;
      box.style.setProperty('display', 'none', 'important');
    });
  }

  /* ---------- LearnDash: strona kursu – własna karta „Brak dostępu” i własne przyciski ----------
     Przyciski LearnDash (Zapisz się, Zaloguj, Wyloguj) bywają przycinane przez style wtyczki. Rysujemy własne
     przyciski motywu POZA kontenerami LearnDash; kliknięcie przekazujemy do oryginału (okno logowania,
     zapis na kurs i przejście do sklepu działają jak dotąd). Bez JavaScriptu zostają oryginały. */
  function ldProxy(orig, cls, label, icon) {
    var isLink = orig.tagName === 'A' && orig.getAttribute('href');
    var el = document.createElement(isLink ? 'a' : 'button');
    el.className = 'btn ' + cls;
    if (isLink) { el.href = orig.getAttribute('href'); } else { el.type = 'button'; }
    el.innerHTML = (icon ? '<svg class="ico" aria-hidden="true" focusable="false"><use href="#i-' + icon + '"/></svg>' : '') + '<span></span>';
    $('span', el).textContent = label;
    el.addEventListener('click', function (e) { e.preventDefault(); orig.click(); });
    orig.style.setProperty('display', 'none', 'important');
    // Puste pojemniki po ukrytym przycisku też chowamy (żeby nie zostawiały pustego miejsca).
    for (var p = orig.parentElement; p && !p.classList.contains('learndash-wrapper'); p = p.parentElement) {
      var visible = Array.prototype.some.call(p.childNodes, function (n) {
        return (n.nodeType === 3 && n.textContent.trim()) || (n.nodeType === 1 && window.getComputedStyle(n).display !== 'none');
      });
      if (visible) { break; }
      p.style.setProperty('display', 'none', 'important');
    }
    return el;
  }
  function textOf(el) { return el ? String(el.tagName === 'INPUT' ? el.value : el.textContent).replace(/\s+/g, ' ').trim() : ''; }
  function buildCourseAccess() {
    if (!document.body.matches('.single-sfwd-courses')) { return; }
    var content = $('.learn-content'); if (!content || content.hasAttribute('data-cod-access')) { return; }
    content.setAttribute('data-cod-access', '');
    var wrap = document.createElement('div'); wrap.className = 'ld-access';
    var box = $('.learndash-wrapper .ld-course-status.ld-course-status-not-enrolled', content);
    var join = box ? $('#btn-join, .btn-join, .ld-course-status-action a, .ld-course-status-action button, .ld-course-status-action input[type="submit"]', box) : null;
    if (box && join) {
      var priceEl = $('.ld-course-status-price', box), price = textOf(priceEl).replace(/\s+/g, ' ');
      var hasPrice = /\d/.test(price);
      var card = document.createElement('div'); card.className = 'ld-access__card';
      card.innerHTML = '<span class="ld-access__tag">Brak dostępu</span><p class="ld-access__text"></p>' + (hasPrice ? '<p class="ld-access__price"></p>' : '');
      $('.ld-access__text', card).textContent = hasPrice ? 'Kup dostęp, aby rozpocząć naukę we własnym tempie.' : 'Dostęp do szkolenia otrzymasz po zakupie pakietu.';
      if (hasPrice) { $('.ld-access__price', card).textContent = price; }
      card.appendChild(ldProxy(join, 'btn--primary ld-access__join', textOf(join) || 'Zapisz się na szkolenie'));
      wrap.appendChild(card);
      box.style.setProperty('display', 'none', 'important');
    }
    var login = $('.learndash-wrapper .ld-login:not(.ld-logout), .learndash-wrapper a.ld-login-button:not(.ld-logout)', content);
    var logout = $('.learndash-wrapper .ld-logout', content);
    if (login) {
      var lp = document.createElement('p'); lp.className = 'ld-access__login';
      lp.appendChild(ldProxy(login, 'btn--ghost', textOf(login) || 'Masz już dostęp? Zaloguj się', 'user'));
      wrap.appendChild(lp);
    } else if (logout) {
      var op = document.createElement('p'); op.className = 'ld-access__logout';
      op.appendChild(ldProxy(logout, 'btn--ghost btn--sm', textOf(logout) || 'Wyloguj'));
      wrap.appendChild(op);
    }
    if (wrap.children.length) { content.parentNode.insertBefore(wrap, content); }
  }

  /* LearnDash: „3 Tematy” → „3 tematy” */
  function ldLowercaseCounts() {
    $$('.learndash-wrapper .ld-item-components .ld-item-component, .learndash-wrapper .ld-item-components > span').forEach(function (el) {
      if (el.children.length) { return; }
      el.textContent = el.textContent.replace(/^(\s*\d+\s+)(\S)/, function (m, a, c) { return a + c.toLowerCase(); });
    });
  }

  /* ---------- reCAPTCHA: plakietka zamiast w rogu – dopisek pod formularzem (dopuszczalne wg zasad Google) ---------- */
  (function recaptchaNote() {
    var tries = 0;
    var timer = window.setInterval(function () {
      tries++;
      if (document.querySelector('.grecaptcha-badge')) {
        window.clearInterval(timer);
        doc.classList.add('has-recaptcha-note');
        $$('form.wpcf7-form, form.form').forEach(function (f) {
          if ($('.recaptcha-note', f)) { return; }
          var p = document.createElement('p');
          p.className = 'fine recaptcha-note';
          p.innerHTML = 'Strona jest chroniona przez reCAPTCHA – obowiązują <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Polityka prywatności</a> i <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Warunki korzystania</a> Google.';
          f.appendChild(p);
        });
      } else if (tries > 20) { window.clearInterval(timer); }
    }, 500);
  })();

  /* ---------- Czytelność kolorów wpisanych w edytorze ----------
     Treść z edytora może mieć kolor tekstu lub tła ustawiony „na sztywno” (np. biały tekst przeniesiony ze starego
     projektu). Gdy na tle sekcji taki tekst byłby nieczytelny, przywracamy kolor sekcji albo dobieramy czytelny. */
  var guardSel = '.prose [style*="color"], .prose [style*="background"], .prose .has-text-color, .prose .has-background, .prose font[color], ' +
    '.learn-content [style*="color"], .woocommerce-Tabs-panel [style*="color"], .woocommerce-product-details__short-description [style*="color"]';
  var guarded = [];
  function rgba(str) {
    var m = String(str).match(/[\d.]+/g) || [0, 0, 0, 0];
    return [+m[0], +m[1], +m[2], m.length > 3 ? +m[3] : 1];
  }
  function lum(c) {
    var a = [c[0], c[1], c[2]].map(function (v) { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); });
    return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
  }
  function ratio(a, b) { var x = lum(a), y = lum(b); return (Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05); }
  function bgOf(el) {
    for (var n = el; n && n.nodeType === 1; n = n.parentElement) {
      var c = rgba(window.getComputedStyle(n).backgroundColor);
      if (c[3] >= 0.5) { return c; }
    }
    return rgba(window.getComputedStyle(document.body).backgroundColor);
  }
  function guardColors() {
    guarded.forEach(function (el) {
      if (el.codColor[0]) { el.style.setProperty('color', el.codColor[0], el.codColor[1]); } else { el.style.removeProperty('color'); }
    });
    guarded = [];
    $$(guardSel).forEach(function (el) {
      if (!el.textContent.trim()) { return; }
      var bg = bgOf(el), fg = rgba(window.getComputedStyle(el).color);
      if (fg[3] === 0 || ratio(fg, bg) >= 3) { return; }
      el.codColor = [el.style.getPropertyValue('color'), el.style.getPropertyPriority('color')];
      el.style.setProperty('color', 'inherit', 'important');
      fg = rgba(window.getComputedStyle(el).color);
      if (ratio(fg, bg) < 3) {
        el.style.setProperty('color', ratio([20, 40, 44], bg) >= ratio([238, 246, 246], bg) ? '#14282c' : '#eef6f6', 'important');
      }
      guarded.push(el);
    });
  }
  guardColors();
  buildLessonNav();
  buildCourseAccess();
  ldLowercaseCounts();
  fixPluginButtons();
  window.addEventListener('load', fixPluginButtons);
  window.setTimeout(fixPluginButtons, 1500);
  if (window.matchMedia) {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    if (mq.addEventListener) { mq.addEventListener('change', function () { guardColors(); fixPluginButtons(); }); } else if (mq.addListener) { mq.addListener(guardColors); }
  }
})();
