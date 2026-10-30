(function () {
  var CFG = window.PMBS || {};
  var FORM_ENDPOINT = CFG.ajax || ""; // admin-ajax.php (akcja pmbs_contact); puste = otwarcie programu pocztowego
  var MAIL_TO = CFG.email || "";
  var PHONE = CFG.phone || "";
  var doc = document.documentElement;
  var loadedAt = Date.now();
  doc.classList.add("js");
  window.pmbsOk = true; // skrypt wystartował (inaczej treść zostanie pokazana awaryjnie)

  // Źródło wejścia (UTM, gclid, fbclid, strona odsyłająca): pierwszy kontakt w tej sesji, dołączany do zgłoszenia
  var ATTR_KEYS = ["utm_source", "utm_medium", "utm_campaign", "utm_content", "utm_term", "gclid", "fbclid"];
  var attr = "";
  try {
    var q = new URLSearchParams(location.search), cur = {}, any = false;
    ATTR_KEYS.forEach(function (k) { var v = q.get(k); if (v) { cur[k] = v.slice(0, 120); any = true; } });
    var stored = JSON.parse(sessionStorage.getItem("pmbs_attr") || "null");
    if (any && !stored) {
      stored = cur; stored.landing = location.pathname; stored.referrer = (document.referrer || "").slice(0, 200);
      sessionStorage.setItem("pmbs_attr", JSON.stringify(stored));
    } else if (!stored && document.referrer && document.referrer.indexOf(location.host) < 0) {
      stored = { referrer: document.referrer.slice(0, 200), landing: location.pathname };
      sessionStorage.setItem("pmbs_attr", JSON.stringify(stored));
    }
    if (stored) attr = Object.keys(stored).map(function (k) { return k + "=" + stored[k]; }).join(", ");
  } catch (e) {}

  // Header state
  var header = document.querySelector(".site-header");
  function onScroll() { if (header) header.classList.toggle("scrolled", window.scrollY > 8); }
  onScroll(); window.addEventListener("scroll", onScroll, { passive: true });

  // Mobile menu
  var burger = document.getElementById("burger");
  var nav = document.getElementById("nav");
  function setMenu(open) {
    if (!nav || !burger) return;
    nav.classList.toggle("open", open);
    burger.setAttribute("aria-expanded", open ? "true" : "false");
    burger.setAttribute("aria-label", open ? "Zamknij menu" : "Otwórz menu");
    document.body.style.overflow = open ? "hidden" : "";
  }
  if (burger && nav) burger.addEventListener("click", function () { setMenu(!nav.classList.contains("open")); });
  if (nav) nav.addEventListener("click", function (e) { if (e.target.closest("a")) setMenu(false); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") setMenu(false); });
  window.addEventListener("resize", function () { if (window.innerWidth > 1240) setMenu(false); });

  // Scroll reveal
  var items = document.querySelectorAll("[data-reveal]");
  if ("IntersectionObserver" in window && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); }
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.05 });
    items.forEach(function (el, i) {
      el.style.transitionDelay = (i % 4) * 70 + "ms";
      io.observe(el);
    });
  } else {
    items.forEach(function (el) { el.classList.add("in"); });
  }

  // Preselect form type from CTA links
  var select = document.getElementById("f-type");
  document.addEventListener("click", function (e) {
    var a = e.target.closest("a[data-form]");
    if (!a) return;
    var v = a.getAttribute("data-form");
    if (v && select) select.value = v;
  });

  // Program cards: aktywna karta + strzałki
  var prog = document.getElementById("program");
  if (prog) {
    var cards = prog.querySelectorAll(".pcard");
    function activate(c) { cards.forEach(function (x) { x.classList.toggle("active", x === c); }); }
    cards.forEach(function (c) {
      c.addEventListener("mouseenter", function () { activate(c); });
      c.addEventListener("focusin", function () { activate(c); });
      c.addEventListener("click", function () { activate(c); });
    });
    function scrollProg(d) { prog.scrollBy({ left: d * (cards[0].offsetWidth + 16), behavior: "smooth" }); }
    document.getElementById("prog-prev").addEventListener("click", function () { scrollProg(-1); });
    document.getElementById("prog-next").addEventListener("click", function () { scrollProg(1); });
  }

  // Zakładki cennika
  var tabs = Array.prototype.slice.call(document.querySelectorAll('.tabs [role="tab"]'));
  function selectTab(t, focus) {
    tabs.forEach(function (x) {
      var on = x === t;
      x.setAttribute("aria-selected", on ? "true" : "false");
      x.tabIndex = on ? 0 : -1;
      document.getElementById(x.getAttribute("aria-controls")).hidden = !on;
    });
    if (focus) t.focus();
  }
  tabs.forEach(function (t, i) {
    t.addEventListener("click", function () { selectTab(t); });
    t.addEventListener("keydown", function (e) {
      if (e.key === "ArrowRight") { e.preventDefault(); selectTab(tabs[(i + 1) % tabs.length], true); }
      if (e.key === "ArrowLeft") { e.preventDefault(); selectTab(tabs[(i - 1 + tabs.length) % tabs.length], true); }
    });
  });
  // CTA z formą: przełącz cennik na odpowiednią zakładkę nie jest potrzebne

  // Opinie: slider
  var slides = document.querySelectorAll(".slide"), dots = document.querySelectorAll(".dot"), cur = 0;
  function showSlide(n) {
    cur = (n + slides.length) % slides.length;
    slides.forEach(function (s, i) { s.hidden = i !== cur; });
    dots.forEach(function (d, i) { d.classList.toggle("on", i === cur); });
  }
  dots.forEach(function (d, i) { d.addEventListener("click", function () { showSlide(i); }); });
  var slNext = document.getElementById("sl-next");
  if (slNext) slNext.addEventListener("click", function () { showSlide(cur + 1); });

  // Hero: delikatny parallax
  var hv = document.getElementById("hv");
  if (hv && window.matchMedia("(hover: hover)").matches && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    (hv.closest(".hero") || document).addEventListener("pointermove", function (e) {
      var r = hv.getBoundingClientRect();
      hv.style.setProperty("--mx", ((e.clientX - (r.left + r.width / 2)) / r.width).toFixed(3));
      hv.style.setProperty("--my", ((e.clientY - (r.top + r.height / 2)) / r.height).toFixed(3));
    });
  }

  // Mapa (ładowana dopiero po kliknięciu)
  var mapBtn = document.getElementById("map-load"), mapFrame = document.getElementById("map-frame");
  if (mapBtn && mapFrame) {
    mapBtn.addEventListener("click", function () {
      if (mapFrame.hidden) {
        if (!mapFrame.firstChild) {
          var f = document.createElement("iframe");
          f.src = mapFrame.getAttribute("data-src"); f.title = mapFrame.getAttribute("data-title") || "Mapa";
          f.loading = "lazy"; f.referrerPolicy = "no-referrer-when-downgrade"; mapFrame.appendChild(f);
        }
        mapFrame.hidden = false; mapBtn.textContent = "Ukryj mapę";
      } else { mapFrame.hidden = true; mapBtn.textContent = "Pokaż mapę"; }
    });
  }

  // Dokumenty prawne w oknie dialogowym (wersja podglądowa)
  document.addEventListener("click", function (e) {
    var a = e.target.closest("a[data-legal]");
    if (a) {
      var d = document.getElementById("legal-" + a.getAttribute("data-legal"));
      if (d && d.showModal) { e.preventDefault(); d.showModal(); }
      return;
    }
    if (e.target.closest(".legal-close")) { e.target.closest("dialog").close(); return; }
    if (e.target.tagName === "DIALOG") e.target.close();
  });

  // Form
  var form = document.getElementById("lead");
  var thanks = document.getElementById("thanks");
  var thanksText = document.getElementById("thanks-text");
  function setErr(id, msg) {
    var el = document.getElementById(id);
    el.textContent = msg || "";
    el.closest(".field").classList.toggle("invalid", !!msg);
  }
  function validate() {
    var ok = true, first = null;
    var name = form.imie.value.trim();
    var phone = form.telefon.value.replace(/[\s\-().]/g, "");
    if (name.length < 2) { setErr("e-name", "Podaj imię."); ok = false; first = first || form.imie; } else setErr("e-name");
    if (!/^\+?\d{9,13}$/.test(phone)) { setErr("e-phone", "Podaj poprawny numer telefonu, np. 534 581 536."); ok = false; first = first || form.telefon; } else setErr("e-phone");
    var mail = form.email.value.trim();
    if (mail && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(mail)) { setErr("e-email", "Podaj poprawny adres e-mail albo zostaw pole puste."); ok = false; first = first || form.email; } else setErr("e-email");
    if (!form.forma.value) { setErr("e-type", "Wybierz formę treningu."); ok = false; first = first || form.forma; } else setErr("e-type");
    if (!form.zgoda.checked) { setErr("e-consent", "Zgoda jest wymagana, abyśmy mogli się z Tobą skontaktować."); ok = false; first = first || form.zgoda; } else setErr("e-consent");
    if (first) first.focus();
    return ok;
  }
  function done(text) {
    if (text) thanksText.textContent = text;
    form.hidden = true; thanks.hidden = false;
    thanks.scrollIntoView({ behavior: "smooth", block: "center" });
  }
  // Dane do ulepszonych konwersji Google Ads (przekazywane tylko po zgodzie marketingowej, hashuje je gtag)
  function userData(d) {
    var u = {}, ph = (d.telefon || "").replace(/[^\d+]/g, "");
    if (d.email) u.email = d.email.toLowerCase();
    if (ph) u.phone_number = ph.charAt(0) === "+" ? ph : "+48" + ph.replace(/^0+/, "");
    return u;
  }
  if (form) form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!validate()) return;
    if (CFG.demo) { done(CFG.demoText); return; }
    var data = {
      imie: form.imie.value.trim(), telefon: form.telefon.value.trim(), email: form.email.value.trim(), kiedy: form.kiedy ? form.kiedy.value : "",
      forma: form.forma.value, wiadomosc: form.wiadomosc.value.trim(), zgoda: true
    };
    if (FORM_ENDPOINT) {
      var btn = form.querySelector("button[type=submit]"), orig = btn.textContent;
      var errBox = document.getElementById("form-error");
      errBox.hidden = true;
      btn.disabled = true; btn.textContent = "Wysyłanie…";
      var body = new URLSearchParams();
      body.set("action", CFG.action || "pmbs_contact"); body.set("imie", data.imie); body.set("telefon", data.telefon); body.set("email", data.email); body.set("kiedy", data.kiedy); body.set("forma", data.forma);
      body.set("wiadomosc", data.wiadomosc); body.set("zgoda", "1");
      body.set("pole_kontrolne", form.pole_kontrolne ? form.pole_kontrolne.value : "");
      body.set("t", String(Math.round((Date.now() - loadedAt) / 1000)));
      var lm = window.pmLeadMeta ? window.pmLeadMeta() : {};
      body.set("eid", lm.eid || ""); body.set("mc", lm.mc || "0"); body.set("fbp", lm.fbp || ""); body.set("fbc", lm.fbc || "");
      body.set("zrodlo", attr);
      body.set("url", location.href.split("#")[0].slice(0, 300));
      fetch(FORM_ENDPOINT, { method: "POST", headers: { "Accept": "application/json" }, body: body })
        .then(function (r) {
          return r.json().then(function (j) { return { ok: r.ok && j && j.ok, j: j }; }, function () { return { ok: false, j: {} }; });
        })
        .then(function (res) {
          if (res.ok) { if (window.pmTrack) window.pmTrack("generate_lead", { form_type: data.forma, call_time: data.kiedy }, "Lead", { content_name: data.forma }, { eid: lm.eid, user: userData(data) }); done(res.j && res.j.confirmation ? "Odezwiemy się na podany numer telefonu. Potwierdzenie wysłaliśmy też na Twój adres e-mail." : ""); }
          else { throw res.j || {}; }
        })
        .catch(function (j) {
          btn.disabled = false; btn.textContent = orig;
          errBox.textContent = (j && j.error) || "Nie udało się wysłać zgłoszenia." + (PHONE ? " Zadzwoń: " + PHONE + "." : "");
          errBox.hidden = false;
        });
    } else {
      // Tryb bez serwera: przygotowana wiadomość e-mail
      var mailBody = "Imię: " + data.imie + "\nTelefon: " + data.telefon + (data.email ? "\nE-mail: " + data.email : "") + (data.kiedy ? "\nPora na telefon: " + data.kiedy : "") + "\nForma treningu: " + data.forma +
        (data.wiadomosc ? "\n\n" + data.wiadomosc : "") + "\n\nZgoda na kontakt i przetwarzanie danych: tak";
      if (window.pmTrack) window.pmTrack("generate_lead", { form_type: data.forma, call_time: data.kiedy }, "Lead", { content_name: data.forma });
      window.location.href = "mailto:" + MAIL_TO + "?subject=" + encodeURIComponent("Zgłoszenie na pierwszy trening") + "&body=" + encodeURIComponent(mailBody);
      done("Przygotowaliśmy wiadomość w Twoim programie pocztowym. Wyślij ją, a odezwiemy się na podany numer telefonu.");
    }
  });
})();
