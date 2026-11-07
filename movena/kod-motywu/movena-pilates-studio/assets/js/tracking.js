/* Zgoda na cookies + analityka i reklamy (Consent Mode v2).
   Tryb GTM: ładuje się kontener, a zdarzenia trafiają do dataLayer.
   Tryb bezpośredni: GA4, Google Ads (gtag), Meta Pixel, Hotjar.
   Wszystko ładuje się dopiero po zgodzie użytkownika; zdarzenia nie zawierają danych osobowych
   (adres e-mail i telefon do ulepszonych konwersji trafiają tylko do Google Ads, po zgodzie marketingowej). */
(function () {
  var CFG = window.MVN_TRACK || {};
  var GTM = CFG.gtm || "", GA = CFG.ga4 || "", ADS = CFG.ads || "", META = CFG.meta || "", HJ = CFG.hotjar || "";
  var KEY = "mvn_consent", VER = 1, MAXAGE = 365 * 24 * 3600 * 1000;
  var banner = document.getElementById("ck-banner");
  if (!banner) return;
  var state = { a: false, m: false }, loaded = {};
  var panel = document.getElementById("ck-panel"), toggle = document.getElementById("ck-toggle");
  var boxA = document.getElementById("ck-a"), boxM = document.getElementById("ck-m");

  window.dataLayer = window.dataLayer || [];
  function gtag() { window.dataLayer.push(arguments); }
  window.gtag = window.gtag || gtag;

  /* Consent Mode v2: domyślnie wszystko odrzucone, zanim cokolwiek się załaduje */
  gtag("consent", "default", { ad_storage: "denied", analytics_storage: "denied", ad_user_data: "denied", ad_personalization: "denied", wait_for_update: 500 });

  function read() {
    try {
      var c = JSON.parse(localStorage.getItem(KEY));
      if (c && c.v === VER && Date.now() - c.t < MAXAGE) return c;
    } catch (e) {}
    return null;
  }
  function save(a, m) {
    try { localStorage.setItem(KEY, JSON.stringify({ v: VER, t: Date.now(), a: !!a, m: !!m })); } catch (e) {}
  }
  function script(src, attrs) {
    var s = document.createElement("script");
    s.async = true; s.src = src;
    for (var k in (attrs || {})) s.setAttribute(k, attrs[k]);
    document.head.appendChild(s);
    return s;
  }

  /* ---------- identyfikator zdarzenia do deduplikacji (Pixel + Conversions API) ---------- */
  function eventId() {
    return "mv" + Date.now().toString(36) + Math.random().toString(36).slice(2, 10);
  }
  function cookie(n) {
    var m = document.cookie.match(new RegExp("(?:^|; )" + n + "=([^;]*)"));
    return m ? decodeURIComponent(m[1]) : "";
  }
  /* Dane do wysłania razem z formularzem (serwer użyje ich tylko przy zgodzie marketingowej) */
  window.mvLeadMeta = function () {
    return { eid: eventId(), mc: state.m ? "1" : "0", fbp: state.m ? cookie("_fbp") : "", fbc: state.m ? cookie("_fbc") : "" };
  };

  /* ---------- ładowanie narzędzi ---------- */
  var gtagReady = false, gaConfigured = false, adsConfigured = false;
  function loadGtagLib() {
    if (gtagReady || !(GA || ADS)) return;
    gtagReady = true;
    gtag("js", new Date());
    script("https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(GA || ADS));
  }
  function configureGoogle() {
    if (GA && state.a && !gaConfigured) { loadGtagLib(); gaConfigured = true; gtag("config", GA); }
    if (ADS && state.m && !adsConfigured) { loadGtagLib(); adsConfigured = true; gtag("config", ADS, { allow_enhanced_conversions: true }); }
  }
  function loadGTM() {
    if (loaded.gtm || !GTM) return;
    loaded.gtm = true;
    window.dataLayer.push({ "gtm.start": Date.now(), event: "gtm.js" });
    script("https://www.googletagmanager.com/gtm.js?id=" + encodeURIComponent(GTM));
  }
  function loadMeta() {
    if (loaded.meta || !META) return;
    loaded.meta = true;
    (function (f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
      if (!f._fbq) f._fbq = n;
      n.push = n; n.loaded = true; n.version = "2.0"; n.queue = [];
      t = b.createElement(e); t.async = true; t.src = v;
      s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
    })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
    window.fbq("init", META);
    window.fbq("track", "PageView");
  }
  function loadHotjar() {
    if (loaded.hj || !HJ) return;
    loaded.hj = true;
    (function (h, o, t, j, a, r) {
      h.hj = h.hj || function () { (h.hj.q = h.hj.q || []).push(arguments); };
      h._hjSettings = { hjid: HJ, hjsv: 6 };
      a = o.getElementsByTagName("head")[0];
      r = o.createElement("script"); r.async = 1; r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
      a.appendChild(r);
    })(window, document, "https://static.hotjar.com/c/hotjar-", ".js?sv=");
  }

  function clearCookies(prefixes) {
    var host = location.hostname, parts = host.split("."), domains = ["", host, "." + host];
    if (parts.length > 2) domains.push("." + parts.slice(-2).join("."));
    document.cookie.split(";").forEach(function (c) {
      var name = c.split("=")[0].replace(/^\s+/, "");
      var hit = prefixes.some(function (p) { return name.indexOf(p) === 0; });
      if (!hit) return;
      domains.forEach(function (d) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/" + (d ? "; domain=" + d : "");
      });
    });
  }

  function apply(a, m) {
    var wasA = state.a, wasM = state.m;
    state.a = !!a && !!CFG.hasA; state.m = !!m && !!CFG.hasM;

    gtag("consent", "update", {
      analytics_storage: state.a ? "granted" : "denied",
      ad_storage: state.m ? "granted" : "denied",
      ad_user_data: state.m ? "granted" : "denied",
      ad_personalization: state.m ? "granted" : "denied"
    });

    if (GTM && (state.a || state.m)) loadGTM();
    if (!GTM) {
      configureGoogle();
      if (state.m) loadMeta();
      if (state.a) loadHotjar();
    }

    var revoke = [];
    if (wasA && !state.a) revoke.push("_ga", "_gid", "_hj", "_gat");
    if (wasM && !state.m) revoke.push("_fb", "_gcl", "IDE", "_gac");
    if (revoke.length) { clearCookies(revoke); location.reload(); }
  }

  function hide() { banner.hidden = true; }
  function showPanel(open) {
    if (!panel) return;
    panel.hidden = !open;
    toggle.textContent = open ? "Zapisz wybór" : "Ustawienia";
    toggle.setAttribute("data-ck", open ? "save" : "settings");
  }
  function openSettings() {
    if (boxA) boxA.checked = state.a;
    if (boxM) boxM.checked = state.m;
    banner.hidden = false;
    showPanel(true);
    toggle.focus();
  }
  function decide(a, m) { save(a, m); apply(a, m); hide(); showPanel(false); }

  banner.addEventListener("click", function (e) {
    var b = e.target.closest("[data-ck]");
    if (!b) return;
    var act = b.getAttribute("data-ck");
    if (act === "accept") decide(true, true);
    else if (act === "reject") decide(false, false);
    else if (act === "settings") { if (boxA) boxA.checked = state.a; if (boxM) boxM.checked = state.m; showPanel(true); }
    else if (act === "save") decide(boxA ? boxA.checked : false, boxM ? boxM.checked : false);
  });

  document.addEventListener("click", function (e) {
    var a = e.target.closest && e.target.closest("a");
    if (!a) return;
    var h = a.getAttribute("href") || "";
    if (a.hasAttribute("data-cookie-settings") || h === "#ustawienia-cookies") {
      if (h === "#ustawienia-cookies") { e.preventDefault(); openSettings(); }
      return;
    }
    if (a.getAttribute("data-track") === "click_fitssey" || /^crm\./i.test(a.hostname || "")) {
      var v = parseFloat(a.getAttribute("data-value") || "");
      var fp = { link_type: a.getAttribute("data-offer-type") || "grafik", link_text: (a.textContent || "").replace(/\s+/g, " ").trim().slice(0, 60) };
      if (a.getAttribute("data-pass-name")) fp.item_name = a.getAttribute("data-pass-name");
      if (v > 0) { fp.value = v; fp.currency = "PLN"; }
      fire("click_fitssey", fp, "ClickFitssey", fp);
    } else if (h.indexOf("tel:") === 0) fire("contact", { method: "phone" }, "Contact", { content_name: "telefon" });
    else if (h.indexOf("wa.me") > -1) fire("contact", { method: "whatsapp" }, "Contact", { content_name: "whatsapp" });
    else if (a.getAttribute("data-track") === "click_message" || /(^|\.)(ig\.me|m\.me)$/i.test(a.hostname || "")) fire("contact", { method: "message" }, "Contact", { content_name: "wiadomosc" });
    else if (h.indexOf("mailto:") === 0) fire("contact", { method: "email" }, "Contact", { content_name: "email" });
    else if (a.hasAttribute("data-form")) fire("cta_click", { cta_text: (a.textContent || "").replace(/\s+/g, " ").trim().slice(0, 60), form_type: a.getAttribute("data-form") || "dowolna" });
  });

  /* ---------- zdarzenia ----------
     fire(nazwaGA, parametryGA, nazwaMeta, parametryMeta, opcje)
     opcje: { eid: identyfikator zdarzenia, user: { email, phone_number } – tylko do Google Ads } */
  function fire(gaName, gaParams, metaName, metaParams, opt) {
    opt = opt || {};
    var eid = opt.eid || eventId();
    if (GTM) {
      if (!gaName) return;
      var p = {}; for (var k in (gaParams || {})) p[k] = gaParams[k];
      p.event = gaName; p.event_id = eid; p.meta_event = metaName || "";
      p.consent_analytics = state.a; p.consent_marketing = state.m;
      window.dataLayer.push(p);
      return;
    }
    if (state.a && gaName && GA && window.gtag) window.gtag("event", gaName, gaParams || {});
    if (state.a && window.hj && gaName) window.hj("event", gaName);
    if (state.m && metaName && META && window.fbq) window.fbq(metaName === "ClickFitssey" ? "trackCustom" : "track", metaName, metaParams || {}, { eventID: eid });
    if (state.m && ADS && window.gtag) {
      var label = gaName === "generate_lead" ? CFG.adsLead : (gaName === "contact" ? CFG.adsContact : (gaName === "click_fitssey" ? CFG.adsFitssey : ""));
      if (label) {
        if (opt.user) window.gtag("set", "user_data", opt.user);
        window.gtag("event", "conversion", { send_to: ADS + "/" + label });
      }
    }
  }
  window.mvTrack = fire;

  var saved = read();
  if (saved) apply(saved.a, saved.m);
  else setTimeout(function () { banner.hidden = false; }, 700);
  if (location.hash === "#ustawienia-cookies") openSettings();
})();
