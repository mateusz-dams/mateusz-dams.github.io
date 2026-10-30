/* Grafik zajęć i zapis na zajęcia (strona /grafik/). Rozmawia z CRM przez publiczne API (CORS tylko dla tej strony). */
(function () {
  "use strict";
  var root = document.querySelector("[data-grafik]");
  if (!root) { return; }
  var API = root.getAttribute("data-api");
  var PHONE = root.getAttribute("data-phone") || "";
  var TEL = root.getAttribute("data-tel") || "";
  var states = {};
  [].forEach.call(root.querySelectorAll("[data-state]"), function (n) { states[n.getAttribute("data-state")] = n; });
  var daysBox = root.querySelector(".bk-days");
  var list = root.querySelector(".gr-list");
  var DATA = [], cur = 0, openId = null;

  function el(tag, cls, txt) { var n = document.createElement(tag); if (cls) { n.className = cls; } if (txt != null) { n.textContent = txt; } return n; }
  function show(name) { Object.keys(states).forEach(function (k) { states[k].hidden = k !== name; }); }
  function plural(n, a, b, c) { return n === 1 ? a : (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? b : c); }

  function message(title, text, withCall) {
    var m = states.msg;
    m.querySelector(".bk-msg-t").textContent = title;
    m.querySelector(".bk-msg-p").textContent = text;
    var a = m.querySelector(".bk-msg-a");
    a.textContent = "";
    if (withCall) {
      var b = el("a", "btn btn-primary", "Zadzwoń: " + PHONE);
      b.href = "tel:" + TEL;
      a.appendChild(b);
    }
    show("msg");
  }

  function renderDays() {
    daysBox.textContent = "";
    DATA.forEach(function (d, i) {
      var b = el("button", "bk-day");
      b.type = "button";
      b.setAttribute("role", "radio");
      b.setAttribute("aria-checked", i === cur ? "true" : "false");
      b.appendChild(el("small", "", d.wd.charAt(0).toUpperCase() + d.wd.slice(1) + "."));
      b.appendChild(el("b", "", d.label.split(" ")[0]));
      b.appendChild(el("small", "", d.label.split(" ").slice(1).join(" ").slice(0, 3) + "."));
      if (d.rel) { b.appendChild(el("i", "", d.rel)); }
      b.addEventListener("click", function () { cur = i; openId = null; renderDays(); renderList(); });
      daysBox.appendChild(b);
    });
  }

  function formFor(s) {
    var li = el("li", "gr-form");
    var f = el("form", "");
    f.noValidate = true;
    var field = el("div", "field");
    var lab = el("label", "", "Twój e-mail (ten, z którym kupujesz karnet)");
    lab.setAttribute("for", "gr-mail-" + s.id);
    var inp = el("input");
    inp.type = "email"; inp.id = "gr-mail-" + s.id; inp.name = "email"; inp.autocomplete = "email"; inp.maxLength = 120; inp.required = true;
    try { var saved = localStorage.getItem("mvn_gr_mail"); if (saved) { inp.value = saved; } } catch (e) { /* brak pamięci przeglądarki */ }
    var err = el("small", "err"); err.setAttribute("aria-live", "polite");
    field.appendChild(lab); field.appendChild(inp); field.appendChild(err);
    var hp = el("div", "hp"); hp.setAttribute("aria-hidden", "true");
    var hl = el("label", "", "Strona www"); var hi = el("input"); hi.name = "website"; hi.tabIndex = -1; hi.autocomplete = "off"; hl.appendChild(hi); hp.appendChild(hl);
    var btn = el("button", "btn btn-primary", "Wyślij link do zapisu");
    btn.type = "submit";
    f.appendChild(field); f.appendChild(hp); f.appendChild(btn);
    f.addEventListener("submit", function (ev) {
      ev.preventDefault();
      err.textContent = "";
      var mail = inp.value.trim();
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) { err.textContent = "Podaj poprawny adres e-mail."; inp.focus(); return; }
      btn.disabled = true; btn.textContent = "Wysyłam…";
      fetch(API + "/zapis", { method: "POST", headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ sid: s.id, email: mail, website: hi.value }) })
        .then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
        .then(function (j) {
          if (j && j.ok) {
            try { localStorage.setItem("mvn_gr_mail", mail); } catch (e) { /* ignoruj */ }
            li.textContent = "";
            li.appendChild(el("p", "gr-sent", "Sprawdź skrzynkę"));
            li.appendChild(el("p", "gr-fine", j.message));
          } else {
            btn.disabled = false; btn.textContent = "Wyślij link do zapisu";
            err.textContent = (j && j.error) || "Nie udało się wysłać. Spróbuj ponownie albo zadzwoń: " + PHONE + ".";
          }
        })
        .catch(function () {
          btn.disabled = false; btn.textContent = "Wyślij link do zapisu";
          err.textContent = "Brak połączenia. Spróbuj ponownie albo zadzwoń: " + PHONE + ".";
        });
    });
    li.appendChild(f);
    return li;
  }

  function renderList() {
    list.textContent = "";
    var d = DATA[cur];
    if (!d) { return; }
    d.slots.forEach(function (s) {
      var full = s.free <= 0;
      var li = el("li", "gr-row" + (full ? " is-full" : ""));
      var t = el("span", "gr-time"); t.appendChild(el("b", "", s.hm)); t.appendChild(el("small", "", s.end));
      var inf = el("span", "gr-inf"); inf.appendChild(el("b", "", s.title)); inf.appendChild(el("small", "", s.trainer));
      var sp = el("span", "gr-spots " + (full ? "bad" : "ok"), full ? "Komplet" : s.free + " z " + s.cap + " " + plural(s.free, "wolne", "wolne", "wolnych"));
      var act = el("span", "gr-act");
      if (full) {
        var c = el("a", "btn btn-secondary btn-sm", "Zapytaj o listę"); c.href = "tel:" + TEL; act.appendChild(c);
      } else {
        var b = el("button", "btn btn-primary btn-sm", openId === s.id ? "Zamknij" : "Zapisz się");
        b.type = "button"; b.setAttribute("aria-expanded", openId === s.id ? "true" : "false");
        b.addEventListener("click", function () { openId = openId === s.id ? null : s.id; renderList(); });
        act.appendChild(b);
      }
      li.appendChild(t); li.appendChild(inf); li.appendChild(sp); li.appendChild(act);
      list.appendChild(li);
      if (openId === s.id && !full) { var f = formFor(s); list.appendChild(f); var i = f.querySelector("input"); if (i) { i.focus({ preventScroll: false }); } }
    });
  }

  fetch(API + "/grafik", { headers: { Accept: "application/json" }, cache: "no-store" })
    .then(function (r) { return r.json(); })
    .then(function (j) {
      if (!j || !j.ok) { throw new Error("api"); }
      DATA = j.days || [];
      if (!DATA.length) { message("Brak opublikowanych terminów", "Nowe zajęcia pojawią się tu wkrótce. Zadzwoń, a pomożemy wybrać termin.", true); return; }
      if (!j.mail) { message("Zapisy przez telefon", "Zadzwoń, a zapiszemy Cię na wybrane zajęcia od ręki.", true); return; }
      renderDays(); renderList(); show("ready");
    })
    .catch(function () { message("Nie udało się wczytać grafiku", "Odśwież stronę albo zadzwoń — zapiszemy Cię telefonicznie.", true); });
})();
