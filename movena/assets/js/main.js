/* Movena Pilates Studio — skrypty strony. Żaden fragment nie zakłada istnienia elementu:
   na landingu nie ma menu, cennika ani filtrów, a na podstronach nie ma nic z tego. */
(function () {
  "use strict";
  var CFG = window.MVN || {};
  var FORM_ENDPOINT = CFG.ajax || "";
  var MAIL_TO = CFG.email || "";
  var PHONE = CFG.phone || "";
  var loadedAt = Date.now();
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  function track(name, params) { if (window.mvTrack) window.mvTrack(name, params || {}, ""); }

  /* ---------- źródło wejścia (UTM, gclid, fbclid, referrer): pierwszy kontakt w sesji ---------- */
  var KEYS = ["utm_source", "utm_medium", "utm_campaign", "utm_content", "utm_term", "gclid", "gbraid", "wbraid", "fbclid"];
  var attr = {};
  try {
    var q = new URLSearchParams(location.search), cur = {}, any = false;
    KEYS.forEach(function (k) { var v = q.get(k); if (v) { cur[k] = v.slice(0, 150); any = true; } });
    var stored = JSON.parse(sessionStorage.getItem("mvn_attr") || "null");
    if (any && !stored) {
      stored = cur; stored.landing = location.pathname; stored.referrer = (document.referrer || "").slice(0, 200);
      sessionStorage.setItem("mvn_attr", JSON.stringify(stored));
    } else if (!stored && document.referrer && document.referrer.indexOf(location.host) < 0) {
      stored = { referrer: document.referrer.slice(0, 200), landing: location.pathname };
      sessionStorage.setItem("mvn_attr", JSON.stringify(stored));
    }
    if (stored) attr = stored;
  } catch (e) {}

  /* ---------- CRM to osobna domena: przekazujemy tam parametry kampanii, żeby nie zgubić źródła ---------- */
  $$('a[href*="crm.movenapilates.pl"]').forEach(function (a) {
    try {
      var u = new URL(a.href), changed = false;
      KEYS.forEach(function (k) { if (attr[k]) { u.searchParams.set(k, attr[k]); changed = true; } });
      if (changed) a.href = u.toString();
    } catch (e) {}
  });

  /* ---------- nagłówek ---------- */
  var header = $(".site-header");
  if (header) {
    var onScroll = function () { header.classList.toggle("is-scrolled", window.scrollY > 8); };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  /* ---------- menu na telefonie (tylko tam, gdzie jest) ---------- */
  var toggle = $(".nav-toggle"), nav = $(".nav-mobile");
  if (toggle && nav) {
    var setMenu = function (open) {
      nav.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Zamknij menu" : "Otwórz menu");
      document.body.style.overflow = open ? "hidden" : "";
    };
    toggle.addEventListener("click", function () { setMenu(!nav.classList.contains("is-open")); });
    nav.addEventListener("click", function (e) { if (e.target.closest("a")) setMenu(false); });
    document.addEventListener("keydown", function (e) { if (e.key === "Escape") setMenu(false); });
    window.addEventListener("resize", function () { if (window.innerWidth >= 900) setMenu(false); });
  }

  /* ---------- filtr zajęć ---------- */
  var filters = $("#class-filters");
  if (filters) {
    filters.addEventListener("click", function (e) {
      var chip = e.target.closest(".filter-chip");
      if (!chip) return;
      $$(".filter-chip", filters).forEach(function (c) { c.classList.toggle("is-active", c === chip); });
      var f = chip.getAttribute("data-filter");
      $$(".class-card").forEach(function (card) {
        card.classList.toggle("is-hidden", !(f === "all" || (f === "recommended" && card.getAttribute("data-recommended") === "true")));
      });
      track("view_classes", { filter: f });
    });
  }

  /* ---------- zakładki cennika ---------- */
  var tabBar = $("#pricing-tabs");
  if (tabBar) {
    var tabs = $$(".pricing-tab", tabBar);
    var pick = function (t, focus) {
      tabs.forEach(function (x) {
        var on = x === t;
        x.classList.toggle("is-active", on);
        x.setAttribute("aria-selected", on ? "true" : "false");
        x.tabIndex = on ? 0 : -1;
        var p = document.getElementById(x.getAttribute("aria-controls"));
        if (p) p.hidden = !on;
      });
      if (focus) t.focus();
    };
    tabs.forEach(function (t, i) {
      t.addEventListener("click", function () { pick(t); track("select_offer_type", { offer_type: t.getAttribute("data-tab") }); });
      t.addEventListener("keydown", function (e) {
        if (e.key === "ArrowRight") { e.preventDefault(); pick(tabs[(i + 1) % tabs.length], true); }
        if (e.key === "ArrowLeft") { e.preventDefault(); pick(tabs[(i - 1 + tabs.length) % tabs.length], true); }
      });
    });
  }

  /* ---------- FAQ ---------- */
  $$(".faq-list").forEach(function (list) {
    list.addEventListener("click", function (e) {
      var btn = e.target.closest(".faq-q");
      if (!btn) return;
      var item = btn.closest(".faq-item"), ans = item.querySelector(".faq-a"), open = item.classList.contains("is-open");
      $$(".faq-item.is-open", list).forEach(function (o) {
        if (o === item) return;
        o.classList.remove("is-open"); o.querySelector(".faq-q").setAttribute("aria-expanded", "false"); o.querySelector(".faq-a").style.maxHeight = "";
      });
      item.classList.toggle("is-open", !open);
      btn.setAttribute("aria-expanded", open ? "false" : "true");
      ans.style.maxHeight = open ? "" : ans.scrollHeight + "px";
      if (!open) track("faq_open", { question: (btn.textContent || "").trim().slice(0, 80) });
    });
  });

  /* ---------- wybór zajęć ---------- */
  document.addEventListener("click", function (e) {
    var a = e.target.closest && e.target.closest('a[data-track="select_class"]');
    if (a) track("select_class", { class_id: a.getAttribute("data-class") || "" });
  });

  /* ---------- mapa (ładowana dopiero po kliknięciu) ---------- */
  var mapBtn = $("#map-load"), mapBox = $("#map-frame");
  if (mapBtn && mapBox) {
    mapBtn.addEventListener("click", function () {
      var ph = $(".map-placeholder", mapBox), fr = $("iframe", mapBox);
      if (!fr) {
        fr = document.createElement("iframe");
        fr.src = mapBox.getAttribute("data-src"); fr.title = mapBox.getAttribute("data-title") || "Mapa";
        fr.loading = "lazy"; fr.referrerPolicy = "no-referrer-when-downgrade";
        mapBox.appendChild(fr);
      }
      if (ph) ph.hidden = true;
    });
  }

  /* ---------- formularz ---------- */
  var form = $("#lead");
  if (!form) return;
  var thanks = $("#thanks"), thanksText = $("#thanks-text"), errBox = $("#form-error");
  function setErr(id, msg) {
    var el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg || "";
    var f = el.closest(".field"); if (f) f.classList.toggle("invalid", !!msg);
  }
  function validate() {
    var ok = true, first = null;
    var phone = form.telefon.value.replace(/[\s\-().]/g, ""), mail = form.email.value.trim();
    if (form.imie.value.trim().length < 2) { setErr("e-name", "Podaj imię."); ok = false; first = first || form.imie; } else setErr("e-name");
    if (!/^\+?\d{9,13}$/.test(phone)) { setErr("e-phone", "Podaj poprawny numer telefonu, np. 508 923 051."); ok = false; first = first || form.telefon; } else setErr("e-phone");
    if (mail && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(mail)) { setErr("e-email", "Podaj poprawny adres e-mail albo zostaw pole puste."); ok = false; first = first || form.email; } else setErr("e-email");
    if (!form.forma.value) { setErr("e-type", "Wybierz, czego dotyczy zgłoszenie."); ok = false; first = first || form.forma; } else setErr("e-type");
    if (!form.zgoda.checked) { setErr("e-consent", "Zgoda jest wymagana, abyśmy mogli się z Tobą skontaktować."); ok = false; first = first || form.zgoda; } else setErr("e-consent");
    if (first) first.focus();
    return ok;
  }
  function done(text) {
    if (text && thanksText) thanksText.textContent = text;
    form.hidden = true;
    if (thanks) { thanks.hidden = false; thanks.scrollIntoView({ behavior: "smooth", block: "center" }); }
  }
  function userData(d) {
    var u = {}, ph = (d.telefon || "").replace(/[^\d+]/g, "");
    if (d.email) u.email = d.email.toLowerCase();
    if (ph) u.phone_number = ph.charAt(0) === "+" ? ph : "+48" + ph.replace(/^0+/, "");
    return u;
  }
  function leadEvent(data, lm) {
    if (window.mvTrack) window.mvTrack("generate_lead", { form_type: data.forma, call_time: data.kiedy }, "Lead", { content_name: data.forma }, { eid: lm && lm.eid, user: userData(data) });
  }
  /* przyciski z data-form ustawiają rodzaj zgłoszenia */
  document.addEventListener("click", function (e) {
    var a = e.target.closest && e.target.closest("a[data-form]");
    if (a && form.forma && a.getAttribute("data-form")) form.forma.value = a.getAttribute("data-form");
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!validate()) return;
    if (CFG.demo) { done(CFG.demoText); return; }
    var data = {
      imie: form.imie.value.trim(), telefon: form.telefon.value.trim(), email: form.email.value.trim(),
      kiedy: form.kiedy ? form.kiedy.value : "", forma: form.forma.value, wiadomosc: form.wiadomosc ? form.wiadomosc.value.trim() : ""
    };
    var lm = window.mvLeadMeta ? window.mvLeadMeta() : {};
    if (FORM_ENDPOINT) {
      var btn = form.querySelector("button[type=submit]"), orig = btn.textContent;
      if (errBox) errBox.hidden = true;
      btn.disabled = true; btn.textContent = "Wysyłanie…";
      var body = new URLSearchParams();
      body.set("action", CFG.action || "mvn_contact");
      ["imie", "telefon", "email", "kiedy", "forma", "wiadomosc"].forEach(function (k) { body.set(k, data[k]); });
      body.set("zgoda", "1");
      body.set("pole_kontrolne", form.pole_kontrolne ? form.pole_kontrolne.value : "");
      body.set("t", String(Math.round((Date.now() - loadedAt) / 1000)));
      body.set("eid", lm.eid || ""); body.set("mc", lm.mc || "0"); body.set("fbp", lm.fbp || ""); body.set("fbc", lm.fbc || "");
      body.set("zrodlo", Object.keys(attr).map(function (k) { return k + "=" + attr[k]; }).join(", "));
      KEYS.slice(0, 7).forEach(function (k) { if (attr[k]) body.set(k, attr[k]); });
      body.set("url", location.href.split("#")[0].slice(0, 300));
      fetch(FORM_ENDPOINT, { method: "POST", headers: { "Accept": "application/json" }, body: body })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok && j && j.ok, j: j }; }, function () { return { ok: false, j: {} }; }); })
        .then(function (res) {
          if (!res.ok) throw res.j || {};
          leadEvent(data, lm);
          done(res.j && res.j.confirmation ? "Odezwiemy się na podany numer telefonu. Potwierdzenie wysłaliśmy też na Twój adres e-mail." : "");
        })
        .catch(function (j) {
          btn.disabled = false; btn.textContent = orig;
          if (errBox) {
            errBox.textContent = (j && j.error) || ("Nie udało się wysłać zgłoszenia." + (PHONE ? " Zadzwoń: " + PHONE + "." : ""));
            errBox.hidden = false;
          }
        });
    } else {
      var mailBody = "Imię: " + data.imie + "\nTelefon: " + data.telefon + (data.email ? "\nE-mail: " + data.email : "") + (data.kiedy ? "\nPora na telefon: " + data.kiedy : "") + "\nTemat: " + data.forma + (data.wiadomosc ? "\n\n" + data.wiadomosc : "") + "\n\nZgoda na kontakt i przetwarzanie danych: tak";
      leadEvent(data, lm);
      window.location.href = "mailto:" + MAIL_TO + "?subject=" + encodeURIComponent("Zgłoszenie ze strony") + "&body=" + encodeURIComponent(mailBody);
      done("Przygotowaliśmy wiadomość w Twoim programie pocztowym. Wyślij ją, a odezwiemy się na podany numer telefonu.");
    }
  });

  /* ---------- Rezerwacja online (2.6): wolne godziny z CRM, płatność Stripe, powrót z potwierdzeniem ---------- */
  var book = document.querySelector("[data-book]");
  if (book) (function () {
    var API = book.getAttribute("data-api"), card = book.querySelector(".book-card");
    var st = { loading: card.querySelector('[data-state="loading"]'), form: card.querySelector('[data-state="form"]'), msg: card.querySelector('[data-state="msg"]') };
    var form = st.form, daysEl = form.querySelector(".bk-days"), timesEl = form.querySelector(".bk-times"), dataEl = form.querySelector(".bk-data");
    var pickEl = form.querySelector(".bk-pick"), payBtn = form.querySelector(".bk-pay"), flash = form.querySelector(".bk-flash");
    var data = null, day = null, at = null, live = false, picked = false;
    var MON = ["sty", "lut", "mar", "kwi", "maj", "cze", "lip", "sie", "wrz", "paź", "lis", "gru"];
    var DAYL = ["niedziela", "poniedziałek", "wtorek", "środa", "czwartek", "piątek", "sobota"];
    var MONG = ["stycznia", "lutego", "marca", "kwietnia", "maja", "czerwca", "lipca", "sierpnia", "września", "października", "listopada", "grudnia"];
    var tel = PHONE ? "tel:+48" + PHONE.replace(/\D/g, "").replace(/^48(?=\d{9}$)/, "") : "";
    function show(name) { Object.keys(st).forEach(function (k) { st[k].hidden = k !== name; }); }
    function el(tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; }
    function dObj(s) { var p = s.split(/[- :]/); return new Date(+p[0], +p[1] - 1, +p[2]); }
    function longDay(s) { var d = dObj(s); var w = DAYL[d.getDay()]; return w.charAt(0).toUpperCase() + w.slice(1) + ", " + d.getDate() + " " + MONG[d.getMonth()]; }
    function price() { return data && data.price_txt ? data.price_txt.replace(/\s/g, " ") : "100 zł"; }
    function message(kind, title, text, actions) {
      st.msg.classList.toggle("ok", kind === "ok");
      st.msg.querySelector(".bk-msg-t").textContent = title;
      st.msg.querySelector(".bk-msg-p").textContent = text;
      var a = st.msg.querySelector(".bk-msg-a"); a.innerHTML = "";
      (actions || []).forEach(function (x) { var b = el("a", "btn " + (x.primary ? "btn-primary" : "btn-outline"), x.label); b.href = x.href; if (x.skip) b.setAttribute("data-book-skip", ""); if (x.form) b.setAttribute("data-form", x.form); if (x.blank) { b.target = "_blank"; b.rel = "noopener"; } a.appendChild(b); });
      show("msg");
    }
    function fallback(title, text) {
      live = false;
      var acts = [{ label: "Zostaw numer — oddzwonimy", href: book.getAttribute("data-contact") || "#kontakt", primary: true, skip: true }];
      if (tel) acts.push({ label: "Zadzwoń: " + PHONE, href: tel });
      message("", title, text, acts);
    }
    function setFlash(t, ok) { flash.textContent = t || ""; flash.hidden = !t; flash.classList.toggle("ok", !!ok); }
    function renderDays() {
      daysEl.innerHTML = "";
      data.days.forEach(function (d, i) {
        var b = el("button", "bk-day"); b.type = "button"; b.setAttribute("role", "radio"); b.setAttribute("aria-checked", d.date === day ? "true" : "false");
        var dt = dObj(d.date);
        b.appendChild(el("small", "", d.rel || ["Nd", "Pn", "Wt", "Śr", "Cz", "Pt", "Sb"][dt.getDay()]));
        b.appendChild(el("b", "", dt.getDate() + " " + MON[dt.getMonth()]));
        b.appendChild(el("i", "", d.slots.length + " " + (d.slots.length === 1 ? "termin" : (d.slots.length % 10 >= 2 && d.slots.length % 10 <= 4 && (d.slots.length % 100 < 12 || d.slots.length % 100 > 14) ? "terminy" : "terminów"))));
        b.setAttribute("aria-label", longDay(d.date) + ", wolne terminy: " + d.slots.length);
        b.addEventListener("click", function () { day = d.date; at = null; renderDays(); renderTimes(); sum(); });
        daysEl.appendChild(b);
      });
    }
    function renderTimes() {
      timesEl.innerHTML = "";
      var d = data.days.filter(function (x) { return x.date === day; })[0];
      (d ? d.slots : []).forEach(function (s) {
        var b = el("button", "bk-time"); b.appendChild(el("b", "", s.hm)); if (s.free != null) b.appendChild(el("small", "", "wolne: " + s.free)); b.type = "button"; b.setAttribute("role", "radio"); b.setAttribute("aria-checked", s.at === at ? "true" : "false");
        b.addEventListener("click", function () {
          at = s.at; renderTimes(); sum();
          if (!picked && window.mvTrack) { picked = true; window.mvTrack("add_to_cart", { value: data.price / 100, currency: "PLN", items: [{ item_name: "Poznaj REFORMER" }] }, "AddToCart", { value: data.price / 100, currency: "PLN", content_name: "Poznaj REFORMER — wybrany termin" }); }
          if (dataEl.hidden) { dataEl.hidden = false; var n = form.querySelector("#bk-name"); if (n && window.matchMedia("(min-width: 721px)").matches) n.focus({ preventScroll: true }); }
        });
        timesEl.appendChild(b);
      });
    }
    function sum() {
      if (at) { pickEl.innerHTML = ""; pickEl.appendChild(document.createTextNode("Termin: ")); pickEl.appendChild(el("strong", "", longDay(at) + ", " + at.slice(11))); }
      else pickEl.textContent = "Wybierz termin, żeby przejść dalej.";
      payBtn.disabled = !at;
      payBtn.querySelector("span").textContent = "Przejdź do płatności · " + price();
    }
    function load(keep) {
      return fetch(API + "/terminy", { headers: { Accept: "application/json" }, cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (!j || !j.ok || !j.ready) { fallback("Rezerwacja online jest chwilowo niedostępna", "Zostaw numer, a oddzwonimy i razem wybierzemy termin."); return; }
          data = j;
          if (!data.days.length) { fallback("Najbliższe dni są już zajęte", "Zostaw numer — znajdziemy dla Ciebie termin."); return; }
          if (!keep || !data.days.some(function (d) { return d.date === day; })) day = data.days[0].date;
          if (at && !data.days.some(function (d) { return d.slots.some(function (s) { return s.at === at; }); })) at = null;
          live = true;
          renderDays(); renderTimes(); sum(); show("form");
        })
        .catch(function () { fallback("Nie udało się wczytać kalendarza", "Zostaw numer, a oddzwonimy i razem wybierzemy termin."); });
    }
    form.addEventListener("input", function (e) { var f = e.target.closest && e.target.closest(".field.invalid"); if (f) { f.classList.remove("invalid"); var x = f.querySelector(".err"); if (x) x.textContent = ""; } });
    form.addEventListener("change", function (e) { if (e.target.id === "bk-zgoda" && e.target.checked) err(e.target); });
    function err(input, t) { var f = input.closest(".field"); f.classList.toggle("invalid", !!t); var e = f.querySelector(".err"); if (e) e.textContent = t || ""; }
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (!at) return;
      var fN = form.querySelector("#bk-name"), fP = form.querySelector("#bk-phone"), fE = form.querySelector("#bk-email"), fZ = form.querySelector("#bk-zgoda");
      var n = fN.value.trim(), ph = fP.value.replace(/[\s\-().]/g, ""), m = fE.value.trim(), ok = true, first = null;
      if (n.length < 2) { err(fN, "Podaj imię i nazwisko."); ok = false; first = first || fN; } else err(fN);
      if (!/^\+?\d{9,13}$/.test(ph)) { err(fP, "Podaj numer telefonu, np. 508 923 051."); ok = false; first = first || fP; } else err(fP);
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(m)) { err(fE, "Podaj adres e-mail — wyślemy na niego potwierdzenie płatności."); ok = false; first = first || fE; } else err(fE);
      if (!fZ.checked) { err(fZ, "Zaznacz akceptację regulaminu."); ok = false; first = first || fZ; } else err(fZ);
      if (!ok) { if (dataEl.hidden) dataEl.hidden = false; first.focus(); return; }
      setFlash("");
      var lm = window.mvLeadMeta ? window.mvLeadMeta() : {};
      var body = { name: n, phone: fP.value.trim(), email: m, at: at, zgoda: "1", website: (form.querySelector("[name=website]") || {}).value || "", mc: lm.mc || "0", fbp: lm.fbp || "", fbc: lm.fbc || "",
                   "return": location.origin + location.pathname };
      KEYS.forEach(function (k) { if (attr[k]) body[k] = String(attr[k]).slice(0, 200); });
      var label = payBtn.querySelector("span"), orig = label.textContent;
      payBtn.disabled = true; label.textContent = "Łączę z płatnością…";
      if (window.mvTrack) window.mvTrack("begin_checkout", { value: data ? data.price / 100 : 69, currency: "PLN", items: [{ item_name: "Poznaj REFORMER" }] }, "InitiateCheckout", { value: data ? data.price / 100 : 69, currency: "PLN", content_name: "Poznaj REFORMER" });
      fetch(API, { method: "POST", headers: { "Content-Type": "application/json", Accept: "application/json" }, body: JSON.stringify(body) })
        .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
        .then(function (res) {
          if (res.j && res.j.ok && res.j.url) { try { sessionStorage.setItem("mvn_book", JSON.stringify({ t: res.j.token, u: fP.value.trim(), e: m })); } catch (x) {} location.href = res.j.url; return; }
          payBtn.disabled = false; label.textContent = orig;
          setFlash((res.j && res.j.error) || "Nie udało się przejść do płatności. Spróbuj jeszcze raz.");
          if (res.s === 409) { at = null; load(true).then(function () { setFlash("Ten termin właśnie się zajął — wybierz inną."); }); }
          flash.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "center" });
        })
        .catch(function () { payBtn.disabled = false; label.textContent = orig; setFlash("Brak połączenia. Sprawdź internet i spróbuj jeszcze raz" + (PHONE ? " albo zadzwoń: " + PHONE : "") + "."); });
    });
    // powrót ze Stripe
    var q = new URLSearchParams(location.search), tok = q.get("rezerwacja");
    function clearQ() { try { history.replaceState(null, "", location.pathname); } catch (x) {} }
    function poll(n) {
      fetch(API + "/" + encodeURIComponent(tok), { headers: { Accept: "application/json" }, cache: "no-store" })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (j && j.status === "paid") {
            clearQ();
            var acts = [];
            if (j.change_url) acts.push({ label: "Zmień termin", href: j.change_url, blank: true });
            acts.push({ label: "Wyznacz trasę", href: "https://www.google.com/maps/search/?api=1&query=Movena+Pilates+Studio+Czarnieckiego+36+Radom", blank: true, primary: true });
            message("ok", "Gotowe" + (j.name ? ", " + j.name : "") + "! Do zobaczenia", (j.when ? j.when + ". " : "") + "Movena Pilates Studio, ul. Czarnieckiego 36, Radom. Zabierz strój sportowy i skarpetki antypoślizgowe, jeśli je masz. Potwierdzenie wysłaliśmy SMS-em, a potwierdzenie płatności — e-mailem.", acts);
            var done = "mvn_purchase_" + j.order, seen = false;
            try { seen = !!localStorage.getItem(done); localStorage.setItem(done, "1"); } catch (x) {}
            if (!seen && window.mvTrack) {
              var u = {}; try { var sv = JSON.parse(sessionStorage.getItem("mvn_book") || "{}"); if (sv.e) u.email = sv.e.toLowerCase(); if (sv.u) { var pn = sv.u.replace(/[^\d+]/g, ""); u.phone_number = pn.charAt(0) === "+" ? pn : "+48" + pn; } } catch (x) {}
              window.mvTrack("purchase", { transaction_id: j.order, value: j.value, currency: "PLN", items: [{ item_name: "Poznaj REFORMER" }] }, "Purchase", { value: j.value, currency: "PLN", content_name: "Poznaj REFORMER" }, { eid: j.order, user: u });
            }
          } else if (j && j.status === "pending" && n < 20) {
            message("", "Sprawdzamy płatność…", "To zwykle trwa kilka sekund. Jeśli płatność została pobrana, termin jest Twój — nie płać ponownie.", []);
            setTimeout(function () { poll(n + 1); }, 3000);
          } else if (j && j.status === "pending") {
            clearQ();
            fallback("Płatność jeszcze się przetwarza", "Potwierdzenie przyjdzie SMS-em, gdy bank je zaksięguje. Gdyby nie dotarło w ciągu kilku minut, zadzwoń.");
          } else { clearQ(); setFlash("Płatność nie została dokończona — nic nie pobraliśmy, a miejsce wróciło do wolnych."); load(); }
        })
        .catch(function () { if (n < 5) setTimeout(function () { poll(n + 1); }, 3000); else { clearQ(); load(); } });
    }
    if (tok === "anulowana") { clearQ(); load().then(function () { if (live) setFlash("Płatność przerwana — nic nie pobraliśmy, a miejsce wróciło do wolnych. Wybierz termin jeszcze raz.", true); }); }
    else if (tok) { poll(0); }
    else load();
    if (tok) setTimeout(function () { book.scrollIntoView({ behavior: "auto", block: "start" }); }, 60);
    // przyciski prowadzące do pierwszych zajęć otwierają kalendarz, gdy działa
    var forms = CFG.bookForms || [];
    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    document.addEventListener("click", function (e) {
      var a = e.target.closest && e.target.closest("a[data-form]");
      if (!a || a.hasAttribute("data-book-skip") || !live || a.closest("[data-book]")) return;
      if (forms.indexOf(a.getAttribute("data-form") || "") < 0) return;
      e.preventDefault(); e.stopPropagation();
      book.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
      if (window.mvTrack) window.mvTrack("cta_click", { cta_text: (a.textContent || "").replace(/\s+/g, " ").trim().slice(0, 60), form_type: "rezerwacja" });
    }, true);
  })();
})();
