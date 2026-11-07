/* Wspólne dodatki do projektów treningowych (portfolio Mateusza Damsa).
   - plakietka „Projekt treningowy” z powrotem do portfolio,
   - komunikat przy linkach do podstron, których nie obejmowało ćwiczenie,
   - formularze w trybie pokazowym (nic nie jest wysyłane),
   - menu mobilne tam, gdzie projekt go nie miał, i obsługa przycisku menu klawiaturą. */
(function () {
  'use strict';
  var LINK_MSG = 'Ta podstrona nie była częścią projektu treningowego.';
  var FORM_MSG = 'To widok pokazowy formularza. Dane nie są nigdzie wysyłane.';
  var SEARCH_MSG = 'To interfejs wyszukiwarki. Samo wyszukiwanie nie było częścią ćwiczenia.';

  var toast, timer;
  function say(msg) {
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'trn-toast';
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');
      document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('is-on');
    clearTimeout(timer);
    timer = setTimeout(function () { toast.classList.remove('is-on'); }, 3200);
  }

  // Plakietka powrotu do portfolio
  var badge = document.createElement('a');
  badge.className = 'trn-badge';
  badge.href = '../index.html';
  badge.innerHTML = '<b>Projekt treningowy</b> · wróć do portfolio';
  document.body.appendChild(badge);

  // Linki prowadzące donikąd
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href="#"], a[href=""], a:not([href])');
    if (!a || a.classList.contains('trn-badge')) return;
    if (e.defaultPrevented) return;
    e.preventDefault();
    say(LINK_MSG);
  });

  // Formularze: tryb pokazowy
  document.addEventListener('submit', function (e) {
    var form = e.target;
    var action = form.getAttribute('action');
    if (action && action !== '#') return;
    e.preventDefault();
    var isSearch = form.querySelector('input[type="search"]') || /search|szukaj/i.test(form.className);
    if (!isSearch && form.checkValidity && !form.checkValidity()) { form.reportValidity(); return; }
    say(isSearch ? SEARCH_MSG : FORM_MSG);
    if (!isSearch) form.reset();
  }, true);

  // Przycisk menu: dostępny z klawiatury
  var burger = document.querySelector('.header__hamburger');
  if (burger) {
    burger.setAttribute('role', 'button');
    burger.setAttribute('tabindex', '0');
    burger.setAttribute('aria-label', 'Menu');
    burger.setAttribute('aria-expanded', 'false');
    burger.addEventListener('keydown', function (e) {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      e.preventDefault();
      var visible = [].filter.call(burger.querySelectorAll('img'), function (i) { return i.offsetParent !== null; })[0];
      if (visible) visible.click();
    });
    burger.addEventListener('click', function () {
      setTimeout(function () {
        var closeImg = burger.querySelector('.header__hamburger--close');
        burger.setAttribute('aria-expanded', closeImg && closeImg.offsetParent !== null ? 'true' : 'false');
      }, 0);
    });
  }

  // Menu mobilne dla projektów, które go nie miały
  if (burger && !document.querySelector('.menu-mobile')) {
    var header = document.querySelector('.header');
    var open = burger.querySelector('.header__hamburger--open');
    var close = burger.querySelector('.header__hamburger--close');
    var panel = document.createElement('div');
    panel.className = 'trn-mm';
    panel.id = 'trn-mm';
    var search = header.querySelector('form');
    if (search) panel.appendChild(search.cloneNode(true));
    var nav = header.querySelector('.header__nav > ul');
    if (nav) {
      var navCopy = nav.cloneNode(true);
      [].forEach.call(navCopy.querySelectorAll('a'), function (a) {
        var img = a.querySelector('img');
        if (img && !a.textContent.trim()) a.appendChild(document.createTextNode(img.alt));
      });
      panel.appendChild(navCopy);
    }
    var icons = header.querySelector('.header__icons');
    if (icons) {
      var ul = document.createElement('ul');
      [].forEach.call(icons.querySelectorAll('a'), function (a) {
        var li = document.createElement('li');
        var c = a.cloneNode(true);
        var img = c.querySelector('img');
        if (img) c.appendChild(document.createTextNode(img.alt));
        li.appendChild(c); ul.appendChild(li);
      });
      panel.appendChild(ul);
    }
    document.body.appendChild(panel);
    burger.setAttribute('aria-controls', 'trn-mm');
    function setOpen(on) {
      if (on) panel.style.top = Math.max(0, header.getBoundingClientRect().bottom) + 'px';
      panel.classList.toggle('is-open', on);
      if (open) open.style.display = on ? 'none' : '';
      if (close) close.style.display = on ? 'block' : '';
      burger.setAttribute('aria-expanded', on ? 'true' : 'false');
    }
    if (open) open.addEventListener('click', function () { setOpen(true); });
    if (close) close.addEventListener('click', function () { setOpen(false); });
    panel.addEventListener('click', function (e) { if (e.target.closest('a[href]:not([href="#"])')) setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && panel.classList.contains('is-open')) { setOpen(false); burger.focus(); } });
    window.addEventListener('resize', function () { if (burger.offsetParent === null) setOpen(false); });
  }
})();
