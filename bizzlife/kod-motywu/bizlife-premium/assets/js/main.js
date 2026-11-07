/**
 * Bizzlife Premium – interakcje (vanilla JS, bez zależności).
 */
(function () {
	'use strict';

	var data = window.bzlData || { i18n: {} };
	var t = function (key, fallback) { return (data.i18n && data.i18n[key]) || fallback || ''; };
	var root = document.documentElement;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Nagłówek: tło po przewinięciu ------------------------------------- */
	var header = document.querySelector('[data-header]');
	if (header) {
		var ticking = false;
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 16);
			ticking = false;
		};
		window.addEventListener('scroll', function () {
			if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
		}, { passive: true });
		onScroll();
	}

	/* Menu mobilne -------------------------------------------------------- */
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav = document.querySelector('[data-nav]');
	var mqDesktop = window.matchMedia('(min-width: 1180px)');

	function focusables(el) {
		return Array.prototype.filter.call(
			el.querySelectorAll('a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'),
			function (n) { return n.offsetParent !== null; }
		);
	}
	function setNav(open) {
		if (!toggle || !nav) { return; }
		root.classList.toggle('nav-open', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		var label = toggle.querySelector('[data-nav-toggle-label]');
		if (label) { label.textContent = open ? t('menuClose', 'Zamknij menu') : t('menuOpen', 'Otwórz menu'); }
		if (open) {
			var first = focusables(nav)[0];
			if (first) { first.focus(); }
		}
	}
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			setNav(toggle.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (!root.classList.contains('nav-open')) { return; }
			if (e.key === 'Escape') { setNav(false); toggle.focus(); return; }
			if (e.key === 'Tab') {
				var items = [toggle].concat(focusables(nav));
				var idx = items.indexOf(document.activeElement);
				if (e.shiftKey && idx <= 0) { e.preventDefault(); items[items.length - 1].focus(); }
				else if (!e.shiftKey && idx === items.length - 1) { e.preventDefault(); items[0].focus(); }
			}
		});
		nav.addEventListener('click', function (e) {
			var link = e.target.closest('a[href]');
			if (link && root.classList.contains('nav-open') && link.getAttribute('href').indexOf('#') !== -1) { setNav(false); }
		});
		var onMq = function () { if (mqDesktop.matches) { setNav(false); } };
		if (mqDesktop.addEventListener) { mqDesktop.addEventListener('change', onMq); } else if (mqDesktop.addListener) { mqDesktop.addListener(onMq); }
	}

	/* Podmenu ----------------------------------------------------------- */
	var subToggles = document.querySelectorAll('.submenu-toggle');
	function closeSubs(except) {
		Array.prototype.forEach.call(subToggles, function (b) {
			if (b === except) { return; }
			b.setAttribute('aria-expanded', 'false');
			b.parentElement.classList.remove('is-open');
		});
	}
	Array.prototype.forEach.call(subToggles, function (btn) {
		btn.addEventListener('click', function (e) {
			e.stopPropagation();
			var open = btn.getAttribute('aria-expanded') !== 'true';
			if (mqDesktop.matches) { closeSubs(btn); }
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			btn.parentElement.classList.toggle('is-open', open);
		});
		btn.parentElement.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && btn.getAttribute('aria-expanded') === 'true') {
				e.stopPropagation();
				btn.setAttribute('aria-expanded', 'false');
				btn.parentElement.classList.remove('is-open');
				btn.focus();
			}
		});
		btn.parentElement.addEventListener('focusout', function (e) {
			if (mqDesktop.matches && !btn.parentElement.contains(e.relatedTarget)) {
				btn.setAttribute('aria-expanded', 'false');
				btn.parentElement.classList.remove('is-open');
			}
		});
	});
	document.addEventListener('click', function () { if (mqDesktop.matches) { closeSubs(null); } });

	/* Akordeon FAQ -------------------------------------------------------- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-accordion]'), function (acc) {
		var triggers = acc.querySelectorAll('.accordion__trigger');
		Array.prototype.forEach.call(triggers, function (btn, i) {
			btn.addEventListener('click', function () {
				var open = btn.getAttribute('aria-expanded') !== 'true';
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				btn.closest('.accordion__item').classList.toggle('is-open', open);
			});
			btn.addEventListener('keydown', function (e) {
				var next = null;
				if (e.key === 'ArrowDown') { next = triggers[(i + 1) % triggers.length]; }
				else if (e.key === 'ArrowUp') { next = triggers[(i - 1 + triggers.length) % triggers.length]; }
				else if (e.key === 'Home') { next = triggers[0]; }
				else if (e.key === 'End') { next = triggers[triggers.length - 1]; }
				if (next) { e.preventDefault(); next.focus(); }
			});
		});
		// Otwórz pytanie wskazane w adresie (#id).
		if (location.hash) {
			var target = acc.querySelector(location.hash.replace(/[^#\w-]/g, ''));
			if (target && target.classList.contains('accordion__trigger')) { target.click(); }
		}
	});

	/* Animacje pojawiania się -------------------------------------------- */
	var reveals = document.querySelectorAll('[data-reveal]');
	if (document.body.classList.contains('no-animations') || reduceMotion || !('IntersectionObserver' in window)) {
		root.classList.add('no-io');
	} else if (reveals.length) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		Array.prototype.forEach.call(reveals, function (el) { io.observe(el); });
		// Zabezpieczenie: elementy już widoczne przy wczytaniu (np. po odświeżeniu w połowie strony).
		window.addEventListener('load', function () {
			Array.prototype.forEach.call(reveals, function (el) {
				var r = el.getBoundingClientRect();
				if (r.top < window.innerHeight && r.bottom > 0) { el.classList.add('is-visible'); }
			});
		});
	}

	/* Mapa po kliknięciu ------------------------------------------------- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-map-load]'), function (btn) {
		btn.addEventListener('click', function () {
			var box = document.getElementById(btn.getAttribute('aria-controls'));
			if (!box || box.classList.contains('is-loaded')) { return; }
			var src = box.getAttribute('data-map-src');
			if (!src) { return; }
			var frame = document.createElement('iframe');
			frame.src = src;
			frame.title = box.getAttribute('data-map-title') || 'Mapa';
			frame.loading = 'lazy';
			frame.referrerPolicy = 'no-referrer-when-downgrade';
			frame.setAttribute('allowfullscreen', '');
			box.appendChild(frame);
			box.classList.add('is-loaded');
			btn.setAttribute('aria-expanded', 'true');
			btn.disabled = true;
		});
	});

	/* Kopiowanie linku -------------------------------------------------- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-copy-link]'), function (btn) {
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-copy-link');
			var done = function () {
				btn.classList.add('is-copied');
				var label = btn.querySelector('[data-copy-label]');
				var prev = label ? label.textContent : '';
				if (label) { label.textContent = t('copied', 'Link skopiowany'); }
				setTimeout(function () { btn.classList.remove('is-copied'); if (label) { label.textContent = prev; } }, 2000);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done, function () {});
			}
		});
	});

	/* Formularz kontaktowy ---------------------------------------------- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-contact-form]'), function (form) {
		var status = form.querySelector('[data-form-status]');
		var submit = form.querySelector('[data-submit]');
		var label = submit ? submit.querySelector('[data-label]') : null;
		var labelText = label ? label.textContent : '';
		var tokenFetched = false;

		// Świeży nonce (strony z cache mogą mieć nieaktualny token).
		form.addEventListener('focusin', function () {
			if (tokenFetched || !data.ajaxUrl || !window.fetch) { return; }
			tokenFetched = true;
			fetch(data.ajaxUrl + '?action=bzl_form_token', { credentials: 'same-origin' })
				.then(function (r) { return r.ok ? r.json() : null; })
				.then(function (json) {
					if (json && json.success && json.data.nonce) { form.querySelector('[name="bzl_nonce"]').value = json.data.nonce; }
				})
				.catch(function () {});
		});

		var fields = {
			name: form.querySelector('[name="bzl_name"]'),
			phone: form.querySelector('[name="bzl_phone"]'),
			email: form.querySelector('[name="bzl_email"]'),
			message: form.querySelector('[name="bzl_message"]'),
			consent: form.querySelector('[name="bzl_consent"]')
		};

		function setError(key, msg) {
			var input = fields[key];
			var err = form.querySelector('#bzl-' + key + '-error');
			if (!input) { return; }
			if (msg) { input.setAttribute('aria-invalid', 'true'); } else { input.removeAttribute('aria-invalid'); }
			if (err) { err.textContent = msg || ''; err.hidden = !msg; }
		}
		function showStatus(type, msg) {
			if (!status) { return; }
			status.innerHTML = '';
			if (!msg) { return; }
			var p = document.createElement('p');
			p.className = 'notice notice--' + type;
			p.textContent = msg;
			status.appendChild(p);
		}
		function validate() {
			var errors = {};
			var phone = fields.phone ? fields.phone.value.trim() : '';
			var email = fields.email ? fields.email.value.trim() : '';
			if (fields.name && !fields.name.value.trim()) { errors.name = t('required', 'To pole jest wymagane.'); }
			if (phone && (phone.replace(/\D/g, '').length < 9 || !/^[0-9+()\s-]+$/.test(phone))) { errors.phone = t('phone'); }
			if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) { errors.email = t('email'); }
			if (!phone && !email) { errors.phone = t('contact', 'Podaj numer telefonu lub adres e-mail.'); }
			if (fields.consent && !fields.consent.checked) { errors.consent = t('consent'); }
			['name', 'phone', 'email', 'message', 'consent'].forEach(function (k) { setError(k, errors[k]); });
			return errors;
		}

		// Walidacja „na żywo” po pierwszym błędzie.
		var touched = false;
		form.addEventListener('input', function () { if (touched) { validate(); } });
		form.addEventListener('change', function () { if (touched) { validate(); } });

		form.addEventListener('submit', function (e) {
			touched = true;
			var errors = validate();
			var keys = Object.keys(errors);
			if (keys.length) {
				e.preventDefault();
				showStatus('error', t('fixErrors', 'Popraw zaznaczone pola formularza.'));
				if (fields[keys[0]]) { fields[keys[0]].focus(); }
				return;
			}
			if (!window.fetch || !window.FormData || !data.ajaxUrl) { return; } // wysyłka klasyczna

			e.preventDefault();
			submit.classList.add('is-loading');
			submit.setAttribute('aria-busy', 'true');
			if (label) { label.textContent = t('sending', 'Wysyłanie…'); }
			showStatus('info', '');

			fetch(data.ajaxUrl, {
				method: 'POST',
				body: new FormData(form),
				credentials: 'same-origin',
				headers: { 'Accept': 'application/json' }
			})
				.then(function (r) { return r.json().catch(function () { return { success: false, data: {} }; }); })
				.then(function (json) {
					if (json && json.success) {
						form.reset();
						form.classList.add('is-sent');
						showStatus('success', json.data.message);
						if (status) { status.setAttribute('tabindex', '-1'); status.focus(); }
						return;
					}
					var d = (json && json.data) || {};
					if (d.errors) { Object.keys(d.errors).forEach(function (k) { setError(k, d.errors[k]); }); }
					showStatus('error', d.message || t('error'));
				})
				.catch(function () { showStatus('error', t('error')); })
				.then(function () {
					submit.classList.remove('is-loading');
					submit.removeAttribute('aria-busy');
					if (label) { label.textContent = labelText; }
				});
		});
	});

	/* Spis tematów FAQ: podświetlenie aktywnej grupy -------------------- */
	var faqLinks = document.querySelectorAll('.faq-nav a');
	if (faqLinks.length && 'IntersectionObserver' in window) {
		var map = {};
		Array.prototype.forEach.call(faqLinks, function (a) { map[a.getAttribute('href').slice(1)] = a; });
		var fio = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting && map[en.target.id]) {
					Array.prototype.forEach.call(faqLinks, function (a) { a.classList.remove('is-active'); a.removeAttribute('aria-current'); });
					map[en.target.id].classList.add('is-active');
					map[en.target.id].setAttribute('aria-current', 'true');
				}
			});
		}, { rootMargin: '-30% 0px -60% 0px' });
		Object.keys(map).forEach(function (id) { var el = document.getElementById(id); if (el) { fio.observe(el); } });
	}
})();
