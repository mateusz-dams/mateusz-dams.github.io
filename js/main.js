/* ==========================================================================
   Portfolio Mateusz Dams – skrypty strony
   Autor: Mateusz Dams
   Każda funkcja odpowiada za jedną rzecz i uruchamia się tylko wtedy,
   gdy na stronie jest jej element. Kolejność jak sekcje na stronach.
   ==========================================================================
*/

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

/* --- Logo w stylu terminala (.terminal) ---
   Przy każdym wejściu na stronę ścieżka wpisuje się od nowa, cała, jak w terminalu.
   Na stronie głównej ścieżka podąża za przewijaniem (~/mateusz-dams/projekty).
   Kliknięcie w logo wpisuje „$ cd ~”: na podstronie przechodzi na start,
   a na stronie głównej (poniżej pierwszej sekcji) wraca na górę strony. --- */

function initTerminal() {
    const logo = document.querySelector('.terminal');
    const path = logo?.querySelector('.terminal__path');

    if (!path) return;

    const original = path.innerHTML;
    const deep = logo.classList.contains('terminal--deep');
    const mobile = window.matchMedia('(max-width: 768px)');
    const show = () => logo.classList.add('is-ready');
    const toText = (parts) => parts.map((part) => part.text).join('');

    // Widoczne części ścieżki tej strony (na telefonie i tablecie część jest ukryta)
    const pageParts = () => [...path.querySelectorAll(':scope > span')]
        .filter((part) => part.getClientRects().length)
        .map((part) => ({ text: part.textContent, className: part.className }));

    const base = pageParts();
    let shown = base;
    let timer = null;
    let current = '';
    let jumping = false;
    let pathTyping = false;

    // Rysuje pierwsze „count” znaków ścieżki, z kolorami jej części
    const render = (parts, count) => {
        path.textContent = '';
        let left = count;

        parts.forEach((part) => {
            if (left <= 0) return;

            const span = document.createElement('span');
            span.className = part.className;
            span.textContent = part.text.slice(0, left);
            span.style.display = 'inline';
            path.append(span);
            left -= part.text.length;
        });
    };

    // Zmienia ścieżkę z „from” na „to”: kasuje różnicę i dopisuje nową część.
    // „fresh” – wpisuje całą ścieżkę od zera (wejście na stronę).
    const change = (from, to, { fresh = false, done } = {}) => {
        clearTimeout(timer);

        // Zmiana ścieżki przerywa wpisaną po najechaniu komendę
        clearTimeout(cmdTimer);
        cmdCount = 0;

        const oldText = toText(from);
        const text = toText(to);
        const home = text.startsWith('~/') && oldText.startsWith('~/') ? 2 : 0;

        let keep = 0;
        while (keep < text.length && keep < oldText.length && text[keep] === oldText[keep]) keep += 1;
        keep = Math.max(home, Math.min(keep, oldText.length));
        if (fresh) keep = 0;

        const start = fresh ? 0 : oldText.length;
        const steps = [];
        for (let i = start - 1; i >= keep; i -= 1) steps.push([from, i, 26]);
        const erased = steps.length;
        for (let i = keep + 1; i <= text.length; i += 1) steps.push([to, i, fresh ? 45 : 50]);

        shown = to;

        if (reduceMotion || !steps.length) {
            if (to === base) path.innerHTML = original;
            else render(to, text.length);
            show();
            done?.();
            return;
        }

        logo.classList.add('is-typing');
        pathTyping = true;
        render(from, start);
        show();

        let index = 0;
        const next = () => {
            if (index >= steps.length) {
                if (to === base) path.innerHTML = original;
                logo.classList.remove('is-typing');
                pathTyping = false;
                done?.();
                return;
            }

            const [source, count, delay] = steps[index];
            render(source, count);
            index += 1;
            timer = setTimeout(next, erased && index === erased ? 160 : delay);
        };

        next();
    };

    // Komenda „ $ cd ~” za ścieżką. Na komputerze wpisuje się płynnie po najechaniu myszką
    // i kasuje po zjechaniu; kliknięcie używa już wpisanej komendy (nie pisze jej drugi raz).
    const CMD = { text: ' $ cd ~', className: 'terminal__cmd' };
    const hoverable = window.matchMedia('(hover: hover) and (min-width: 1201px)');
    let cmdCount = 0;
    let cmdTimer = null;
    let going = false;

    const animateCmd = (target, then) => {
        clearTimeout(cmdTimer);
        logo.classList.add('is-typing');

        const step = () => {
            if (cmdCount === target) {
                logo.classList.remove('is-typing');
                then?.();
                return;
            }

            const typing = cmdCount < target;
            cmdCount += typing ? 1 : -1;
            render([...shown, CMD], toText(shown).length + cmdCount);
            cmdTimer = setTimeout(step, typing ? 45 : 22);
        };

        step();
    };

    const canHover = () => hoverable.matches && !pathTyping && !going
        && (deep || logo.classList.contains('is-away'));

    logo.addEventListener('mouseenter', () => {
        if (canHover()) animateCmd(CMD.text.length);
    });

    logo.addEventListener('mouseleave', () => {
        if (!going && cmdCount > 0) animateCmd(0);
    });

    // Po powrocie przyciskiem „wstecz” przeglądarka pokazuje zapamiętaną stronę – logo wraca do stanu wyjściowego
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        clearTimeout(cmdTimer);
        going = false;
        cmdCount = 0;
        shown = base;
        path.innerHTML = original;
        logo.classList.remove('is-typing');
    });

    // Wejście na stronę: cała ścieżka wpisuje się od nowa
    const begin = () => change(base, base, { fresh: true, done: watchSections });

    if (reduceMotion) begin();
    else document.fonts.ready.then(() => setTimeout(begin, 200));

    // Strona główna: ścieżka podąża za sekcją, którą ogląda odwiedzający
    function watchSections() {
        const sections = [...document.querySelectorAll('main > section[id]')];

        if (!sections.length || deep || watchSections.on) return;
        watchSections.on = true;

        let waiting = null;

        // Na telefonie i tablecie krótsze nazwy, żeby ścieżka zmieściła się w nagłówku
        const short = { doswiadczenie: 'praca', 'w-czym-pracuje': 'narzedzia' };

        // Tablet i mały laptop: bez „mateusz-dams/”, żeby menu obok logo się nie łamało
        const compact = window.matchMedia('(max-width: 1200px)');
        const narrow = window.matchMedia('(max-width: 900px)');

        const partsFor = (id) => {
            if (!id) return base;
            if (narrow.matches) return [base[0], { text: short[id] || id, className: 'terminal__page' }];
            if (compact.matches) return [base[0], { text: id, className: 'terminal__page' }];
            return [...base, { text: `/${id}`, className: 'terminal__dir' }];
        };

        const update = () => {
            // W czasie powrotu na górę logo nie pisze mijanych sekcji
            if (jumping) {
                if (window.scrollY > 2) return;
                jumping = false;
                going = false;
                current = '';
                logo.classList.remove('is-away');
                change(shown, base);
                return;
            }

            const line = window.innerHeight * .4;
            let id = '';

            sections.forEach((section) => {
                if (section.getBoundingClientRect().top < line) id = section.id;
            });

            if (id === current) return;
            current = id;
            logo.classList.toggle('is-away', id !== '');

            // Krótka zwłoka, żeby przy szybkim przewijaniu logo nie pisało każdej mijanej sekcji
            clearTimeout(waiting);
            waiting = setTimeout(() => change(shown, partsFor(current)), 180);
        };

        watchSections.update = update;
        onScrollFrame(update);
    }

    logo.addEventListener('click', (event) => {
        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        const full = CMD.text.length;

        // Podstrona: dopisuje brakującą część „$ cd ~” (albo nic, gdy już wpisana) i przechodzi na start
        if (deep) {
            if (reduceMotion) return;
            event.preventDefault();
            if (going) return;
            going = true;

            const href = logo.href;
            const leave = () => setTimeout(() => { window.location.href = href; }, cmdCount === full ? 120 : 180);
            if (cmdCount === full) leave();
            else animateCmd(full, leave);
            return;
        }

        // Strona główna: w pierwszej sekcji zwykły powrót na górę,
        // niżej „$ cd ~” i powrót na górę z kasowaniem ścieżki
        event.preventDefault();
        const behavior = reduceMotion ? 'auto' : 'smooth';

        if (!logo.classList.contains('is-away')) {
            window.scrollTo({ top: 0, behavior });
            return;
        }

        if (going) return;
        going = true;

        // Od kliknięcia logo nie reaguje na przewijanie, dopóki strona nie wróci na górę
        jumping = true;

        const goUp = () => {
            // Komenda staje się częścią ścieżki – po dotarciu na górę skasuje się razem z nią
            if (cmdCount === full) {
                shown = [...shown, CMD];
                cmdCount = 0;
            }

            window.scrollTo({ top: 0, behavior });

            // Gdy ktoś przerwie przewijanie, logo po chwili znowu śledzi sekcje
            setTimeout(() => {
                if (!jumping) return;
                jumping = false;
                going = false;
                current = null;
                watchSections.update?.();
            }, 2500);
        };

        // Na telefonie bez dopisywania komendy, żeby logo nie najechało na plakietkę
        if (reduceMotion || mobile.matches) goUp();
        else if (cmdCount === full) goUp();
        else animateCmd(full, goUp);
    });
}

/* --- Strona 404 (not-found): terminal wpisuje zły adres, pokazuje błąd i listę miejsc,
   a potem przyjmuje komendy. Wszystko, co przychodzi z adresu albo od odwiedzającego,
   trafia na stronę tylko jako zwykły tekst. --- */

function initNotFound() {
    const box = document.querySelector('[data-not-found]');

    if (!box) return;

    const screen = box.querySelector('[data-not-found-screen]');
    const output = box.querySelector('[data-not-found-output]');
    const form = box.querySelector('[data-not-found-form]');
    const input = form.querySelector('input');

    const sections = ['o-mnie', 'projekty', 'cwiczenia', 'doswiadczenie', 'w-czym-pracuje', 'kontakt'];
    const projects = [['pm-boxing', 'PM Boxing Studio'], ['movena', 'Movena Pilates Studio'], ['cod', 'Centrum Odkrywania Doskonałości'], ['bizzlife', 'Bizzlife']];
    const drills = [['topblind', 'Topblind'], ['austrowaren', 'Austrowaren'], ['silvermedica', 'Silver Medica'], ['azs', 'Portal leczenia AZS'], ['leadtrust', 'Leadtrust Group'], ['staxx', 'Staxx Polska']];
    const commands = ['help', 'ls', 'cd', 'whoami', 'projekty', 'cv', 'kontakt', 'clear'];
    const cvUrl = '/cv/CV-Mateusz-Dams.pdf';

    // Zły adres, pod który ktoś próbował wejść
    let wrong = '';
    try {
        wrong = decodeURIComponent(window.location.pathname);
    } catch {
        wrong = window.location.pathname;
    }
    wrong = wrong.replace(/^\/+|\/+$/g, '').replace(/\.html$/, '');
    if (!wrong || wrong === '404') wrong = 'zly-adres';
    if (wrong.length > 80) wrong = `${wrong.slice(0, 80)}…`;

    // Pomocnicze: tworzenie elementów tylko z tekstem
    const el = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };

    const link = (href, text, newTab) => {
        const a = el('a', '', text);
        a.href = href;
        if (newTab) {
            a.target = '_blank';
            a.rel = 'noopener';
        }
        return a;
    };

    const line = (...parts) => {
        const row = el('div');
        parts.forEach((part) => row.append(typeof part === 'string' ? document.createTextNode(part) : part));
        output.append(row);
        screen.scrollTop = screen.scrollHeight;
        return row;
    };

    const prompt = () => [el('span', 'not-found__home', '~/mateusz-dams'), ' ', el('span', 'not-found__dollar', '$'), ' '];

    // Wpisuje komendę literka po literce (albo od razu, gdy system prosi o ograniczenie ruchu)
    const typeCommand = (text) => new Promise((resolve) => {
        const typed = el('span');
        line(...prompt(), typed);

        if (reduceMotion) {
            typed.textContent = text;
            resolve();
            return;
        }

        const delay = Math.max(18, Math.min(55, 1400 / text.length));
        let i = 0;
        const step = () => {
            i += 1;
            typed.textContent = text.slice(0, i);
            if (i < text.length) setTimeout(step, delay);
            else setTimeout(resolve, 280);
        };
        setTimeout(step, 250);
    });

    const pause = (ms) => new Promise((resolve) => setTimeout(resolve, reduceMotion ? 0 : ms));

    // --- Odpowiedzi komend ---

    const placesList = () => {
        const row = el('div', 'not-found__list');
        sections.forEach((id) => row.append(link(`/#${id}`, `${id}/`)));
        row.append(link(cvUrl, 'cv.pdf', true));
        output.append(row);
    };

    const listOf = (items, folder) => {
        const row = el('div', 'not-found__list');
        items.forEach(([file]) => row.append(link(`/${folder}/${file}.html`, `${folder}/${file}`)));
        output.append(row);
    };

    const help = () => {
        const list = el('dl', 'not-found__help');
        [
            ['help', 'ta lista komend'],
            ['ls', 'co jest na stronie (klikalne miejsca)'],
            ['cd projekty', 'przejście do sekcji, np. cd kontakt; cd ~ to strona główna'],
            ['cd projekty/movena', 'otwiera projekt albo ćwiczenie'],
            ['whoami', 'kilka słów o mnie'],
            ['projekty', 'wszystkie projekty i ćwiczenia z linkami'],
            ['cv', 'otwiera CV (PDF)'],
            ['kontakt', 'e-mail i telefon'],
            ['clear', 'czyści ekran'],
        ].forEach(([name, desc]) => list.append(el('dt', '', name), el('dd', '', desc)));
        output.append(list);
        line(el('span', 'not-found__muted', 'Tab dokańcza komendę, strzałki ↑ ↓ przywołują poprzednie.'));
    };

    const resolveTarget = (raw) => {
        const target = raw.replace(/^~\/|^\.\/|^\/+|\/+$/g, '').replace(/^mateusz-dams\/?/, '').replace(/\.html$/, '').toLowerCase();

        if (['', '~', '..', '.'].includes(target)) return '/';
        if (sections.includes(target)) return `/#${target}`;
        if (['cv', 'cv.pdf'].includes(target)) return cvUrl;

        const [folder, file] = target.split('/');
        const inFolder = (items, name) => items.some(([id]) => id === name);

        if (folder === 'projekty' && inFolder(projects, file)) return `/projekty/${file}.html`;
        if (folder === 'cwiczenia' && inFolder(drills, file)) return `/cwiczenia/${file}.html`;
        if (!file && inFolder(projects, folder)) return `/projekty/${folder}.html`;
        if (!file && inFolder(drills, folder)) return `/cwiczenia/${folder}.html`;
        return null;
    };

    // Odległość między słowami (do podpowiedzi przy literówce)
    const distance = (a, b) => {
        const row = Array.from({ length: b.length + 1 }, (_, i) => i);
        for (let i = 1; i <= a.length; i += 1) {
            let prev = row[0];
            row[0] = i;
            for (let j = 1; j <= b.length; j += 1) {
                const keep = row[j];
                row[j] = Math.min(row[j] + 1, row[j - 1] + 1, prev + (a[i - 1] === b[j - 1] ? 0 : 1));
                prev = keep;
            }
        }
        return row[b.length];
    };

    const run = (raw) => {
        const text = raw.trim();
        line(...prompt(), text);

        if (!text) return;

        const [name, ...rest] = text.split(/\s+/);
        const arg = rest.join(' ');
        const command = name.toLowerCase();

        if (command === 'help') help();
        else if (command === 'ls') {
            if (/^projekty\/?$/.test(arg)) listOf(projects, 'projekty');
            else if (/^cwiczenia\/?$/.test(arg)) listOf(drills, 'cwiczenia');
            else placesList();
        } else if (command === 'cd') {
            const href = resolveTarget(arg);
            if (!href) {
                line(el('span', 'not-found__error', `bash: cd: ${arg}: Nie ma takiego pliku ani katalogu`));
                return;
            }
            line(el('span', 'not-found__muted', `przechodzę do ${href === '/' ? 'strony głównej' : href}…`));
            setTimeout(() => {
                if (href === cvUrl) window.open(href, '_blank', 'noopener');
                else window.location.href = href;
            }, reduceMotion ? 0 : 400);
        } else if (command === 'whoami') {
            line('Mateusz Dams, technik informatyk z Radomia.');
            line('Strony WWW, serwis komputerowy i praca z klientem. Szukam stażu, pracy lub zleceń.');
        } else if (command === 'projekty') {
            line(el('span', 'not-found__muted', '# strony dla firm'));
            listOf(projects, 'projekty');
            line(el('span', 'not-found__muted', '# strony treningowe'));
            listOf(drills, 'cwiczenia');
        } else if (command === 'cv') {
            line(el('span', 'not-found__ok', 'otwieram cv.pdf…'));
            window.open(cvUrl, '_blank', 'noopener');
        } else if (command === 'kontakt') {
            line('e-mail: ', link('mailto:dams.mateusz@gmail.com', 'dams.mateusz@gmail.com'));
            line('telefon: ', link('tel:+48505309362', '505 309 362'));
            line('github: ', link('https://github.com/mateusz-dams', 'github.com/mateusz-dams', true));
        } else if (command === 'clear') {
            output.textContent = '';
        } else if (command === 'sudo') {
            line(el('span', 'not-found__error', 'sudo: nie tym razem 😉 Tutaj wszystko zrobisz bez uprawnień administratora.'));
        } else if (command === 'rm' || command === 'mkfs' || command === 'shutdown' || command === 'reboot' || text.startsWith(':(){')) {
            line(el('span', 'not-found__error', `${command}: nie tym razem, to portfolio jest mi jeszcze potrzebne 😉`));
        } else {
            const close = commands.find((known) => distance(command, known) <= 2 && command.length > 1);
            line(el('span', 'not-found__error', `bash: ${name}: nie znaleziono polecenia.`), close ? ` Czy chodziło Ci o: ${close}?` : ' Wpisz help, żeby zobaczyć listę.');
        }
    };

    // --- Pisanie komend: Enter, Tab (dokańczanie) i strzałki (historia) ---

    const history = [];
    let back = 0;

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const value = input.value;
        if (value.trim()) history.push(value);
        back = history.length;
        input.value = '';
        run(value);
        screen.scrollTop = screen.scrollHeight;
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowUp' && history.length) {
            event.preventDefault();
            back = Math.max(0, back - 1);
            input.value = history[back];
        } else if (event.key === 'ArrowDown' && history.length) {
            event.preventDefault();
            back = Math.min(history.length, back + 1);
            input.value = history[back] || '';
        } else if (event.key === 'Tab') {
            const value = input.value;
            const options = value.startsWith('cd ')
                ? ['~', ...sections, ...projects.map(([id]) => `projekty/${id}`), ...drills.map(([id]) => `cwiczenia/${id}`)].map((item) => `cd ${item}`)
                : commands;
            const match = options.filter((option) => option.startsWith(value));

            if (value && match.length) {
                event.preventDefault();

                if (match.length === 1) {
                    // Jedna pasująca: wpisuje ją całą (po „cd” zostawia spację na nazwę miejsca)
                    input.value = match[0] === 'cd' ? 'cd ' : match[0];
                } else {
                    // Kilka pasujących: dopisuje ich wspólny początek
                    input.value = match.reduce((a, b) => {
                        let i = 0;
                        while (i < a.length && a[i] === b[i]) i += 1;
                        return a.slice(0, i);
                    });
                }
            }
        }
    });

    // Kliknięcie w terminal ustawia kursor w polu do pisania (bez zaznaczania tekstu)
    screen.addEventListener('click', (event) => {
        if (!form.hidden && !event.target.closest('a') && !window.getSelection().toString()) input.focus();
    });

    // --- Start: wpisanie złego adresu, błąd, lista miejsc i podpowiedź ---

    (async () => {
        await typeCommand(`cd ${wrong}`);
        line(el('span', 'not-found__error', `bash: cd: ${wrong}: Nie ma takiego pliku ani katalogu`));
        await pause(450);
        await typeCommand('ls');
        placesList();
        await pause(350);
        line(el('span', 'not-found__muted', '# wpisz help i naciśnij Enter, żeby zobaczyć, co tu można zrobić'));
        form.hidden = false;
        if (window.matchMedia('(pointer: fine)').matches) input.focus({ preventScroll: true });
    })();
}

/* --- Stopka: panel dostępności (.footer__live) ---
   Godzina w Radomiu i stan liczone na żywo w czasie polskim, więc działa tak samo z każdego kraju.
   Pon–pt 16:00–22:00: zielona kropka „Dostępny teraz”, poza tym szara i informacja, kiedy będę dostępny. --- */

function initAvailability() {
    const panel = document.querySelector('[data-available]');
    const text = document.querySelector('[data-available-text]');
    const clock = document.querySelector('[data-radom-time]');

    if (!panel || !text || !window.Intl) return;

    const format = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/Warsaw', weekday: 'short', hour: '2-digit', minute: '2-digit', hourCycle: 'h23',
    });

    const tick = () => {
        const parts = Object.fromEntries(format.formatToParts(new Date()).map((part) => [part.type, part.value]));
        const hour = Number(parts.hour);
        const day = parts.weekday;
        const workday = !['Sat', 'Sun'].includes(day);
        const now = workday && hour >= 16 && hour < 22;

        let label = 'Dostępny teraz';
        if (!now) {
            if (workday && hour < 16) label = 'Dostępny dziś od 16:00';
            else if (['Mon', 'Tue', 'Wed', 'Thu'].includes(day)) label = 'Dostępny jutro od 16:00';
            else label = 'Dostępny w pon. od 16:00';
        }

        panel.classList.toggle('is-now', now);
        text.textContent = label;
        if (clock) clock.textContent = `Radom · ${parts.hour}:${parts.minute}`;
    };

    tick();
    setInterval(tick, 30 * 1000);
}

/* --- Pasek stanu: data ostatniej zmiany strony.
   Najpierw z nagłówka serwera (bez zapytań, więc bez limitu GitHuba),
   a gdy go brak – z API GitHuba z zapamiętaniem wyniku. Bez daty pasek pokazuje się bez niej. --- */

function initStatusBar() {
    const slot = document.querySelector('[data-last-commit]');

    if (!slot || !window.fetch) return;

    const ago = (iso) => {
        const days = Math.floor((Date.now() - new Date(iso).getTime()) / 86400000);

        if (days <= 0) return 'dzisiaj';
        if (days === 1) return 'wczoraj';
        if (days < 7) return `${days} dni temu`;
        if (days < 14) return 'tydzień temu';
        if (days < 30) return `${Math.floor(days / 7)} tygodnie temu`;

        // Starsze zmiany: sama data (krótka, więc pasek zawsze mieści się w jednej linii)
        return new Date(iso).toLocaleDateString('pl-PL', { day: '2-digit', month: '2-digit', year: 'numeric' });
    };

    const show = (iso) => {
        slot.textContent = '';
        const label = document.createElement('span');
        label.className = 'status-bar__long';
        label.textContent = 'ostatnia ';
        slot.append(label, `zmiana: ${ago(iso)}`);
        slot.title = new Date(iso).toLocaleDateString('pl-PL', { day: 'numeric', month: 'long', year: 'numeric' });
        slot.hidden = false;
    };

    // 1. Data publikacji strony z nagłówka serwera (GitHub Pages podaje ją przy każdej stronie).
    //    Nie wymaga żadnego zapytania, więc limit GitHuba (60 zapytań na godzinę) jej nie dotyczy.
    const published = new Date(document.lastModified);
    if (!Number.isNaN(published.getTime()) && Date.now() - published.getTime() > 60 * 1000) {
        show(published.toISOString());
        return;
    }

    // 2. Zapas: API GitHuba. Wynik zapamiętany na 6 godzin, a po odmowie (np. limit)
    //    przez godzinę nie pytamy ponownie – pokazujemy ostatnią znaną datę albo nic.
    const KEY = 'last-commit';
    let saved = null;

    try {
        saved = JSON.parse(localStorage.getItem(KEY));
    } catch {
        saved = null;
    }

    const remember = (data) => {
        try {
            localStorage.setItem(KEY, JSON.stringify(data));
        } catch {
            // Pamięć niedostępna: data i tak już się pokazała
        }
    };

    if (saved?.date) show(saved.date);
    if (saved && Date.now() - saved.checked < (saved.failed ? 60 : 6 * 60) * 60 * 1000) return;

    const controller = new AbortController();
    setTimeout(() => controller.abort(), 4000);

    fetch('https://api.github.com/repos/mateusz-dams/mateusz-dams.github.io/commits?per_page=1', { signal: controller.signal })
        .then((response) => (response.ok ? response.json() : Promise.reject()))
        .then(([last]) => {
            const date = last?.commit?.committer?.date;
            if (!date) return;
            show(date);
            remember({ date, checked: Date.now() });
        })
        .catch(() => remember({ date: saved?.date || null, checked: Date.now(), failed: true }));
}

/* --- Powitanie w konsoli dla osób, które zaglądają do kodu --- */

function greetDevelopers() {
    console.log(
        '%c~/%cmateusz-dams%c▍\n%cCześć! Widzę, że zaglądasz do kodu 👋\nTo portfolio napisałem w HTML, CSS i JavaScript, bez frameworków.\nKod: https://github.com/mateusz-dams/mateusz-dams.github.io\nCV: https://mateusz-dams.github.io/cv/CV-Mateusz-Dams.pdf · e-mail: dams.mateusz@gmail.com\n%c$ szukam stażu, pracy lub zleceń – odezwij się :)',
        'color:#8f9cff;font:700 20px monospace',
        'color:#121620;background:#fff;font:700 20px monospace',
        'color:#14a466;font:700 20px monospace',
        'font:13px/1.7 monospace',
        'color:#14a466;font:700 13px/1.7 monospace',
    );
}

/* --- Nagłówek: tło po przewinięciu strony --- */

function initHeader() {
    const header = document.getElementById('header');

    if (!header) return;

    onScrollFrame(() => header.classList.toggle('is-scrolled', window.scrollY > 10));
}

/* --- Menu: podkreślenie sekcji, w której jest odwiedzający ---
   Ta sama zasada co w logo: liczy się sekcja, której góra minęła 40% wysokości ekranu.
   Na samej górze (pierwsza sekcja) nic nie jest podkreślone; „Strony treningowe” podkreślają „Projekty”. --- */

function initMenuSpy() {
    const links = [...document.querySelectorAll('.header__link[href^="#"]')];
    const sections = [...document.querySelectorAll('main > section[id]')];

    if (!links.length || !sections.length) return;

    const linkFor = { cwiczenia: 'projekty' };
    let current = null;

    onScrollFrame(() => {
        const line = window.innerHeight * .4;
        let id = '';

        sections.forEach((section) => {
            if (section.getBoundingClientRect().top < line) id = section.id;
        });

        id = linkFor[id] || id;
        if (id === current) return;
        current = id;

        links.forEach((link) => {
            if (link.getAttribute('href') === `#${id}`) link.setAttribute('aria-current', 'true');
            else link.removeAttribute('aria-current');
        });
    });
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

/* --- Kontakt (contact): kopiowanie e-maila z komunikatem w stylu terminala --- */

function initCopy() {
    document.querySelectorAll('[data-copy]').forEach((button) => {
        const toast = document.createElement('span');
        toast.className = 'copy-toast';
        toast.setAttribute('role', 'status');
        button.append(toast);

        let timer = null;

        const say = (ok) => {
            toast.textContent = '';
            const mark = document.createElement('span');
            mark.className = ok ? 'copy-toast__ok' : 'copy-toast__error';
            mark.textContent = ok ? '✓ ' : '✗ ';
            toast.append(mark, ok ? 'skopiowano do schowka' : 'zaznacz adres i skopiuj ręcznie');
            toast.classList.add('is-visible');
            clearTimeout(timer);
            timer = setTimeout(() => toast.classList.remove('is-visible'), 2200);
        };

        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                say(true);
            } catch {
                say(false);
            }
        });
    });
}

/* --- Dolny pasek na telefonie (tabbar): podświetla sekcję, w której jest odwiedzający --- */

function initTabbar() {
    // „Start” na podstronach to link na stronę główną, nie sekcja tej strony
    const links = [...document.querySelectorAll('.tabbar__link:not(.tabbar__link--home)')];

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
    initTerminal();
    initNotFound();
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
    initStatusBar();
    initAvailability();
    greetDevelopers();
});
