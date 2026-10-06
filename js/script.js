const response = await fetch('unload.php');
export const daten = await response.json();

//Animation Titel

// Das <header>-Element im HTML suchen und in einer Variable merken.
const header = document.querySelector('header');

// Diese Funktion prüft, ob die Seite ganz oben ist und schaltet danach die Klasse "oben" an oder aus.
function updateTitle() {

    // window.scrollY = wie viele Pixel die Seite nach unten gescrollt ist.
    // 0 heisst ganz oben. Hier 20 damit der Titel sich nicht sofort bewegt.
    const istGanzOben = window.scrollY <= 20;

    // classList.toggle(klasse, bedingung):
    //   bedingung wahr  -> Klasse wird hinzugefügt  (Titel Mitte)
    //   bedingung falsch -> Klasse wird entfernt    (Titel Links)
    // Das CSS (.oben ...) und die transition erledigen den Rest.
    header.classList.toggle('oben', istGanzOben);
}

// Bei jedem Scrollen die Funktion ausführen.
// { passive: true } sagt dem Browser, dass wir das Scrollen nicht blockieren.
// Das hält die Seite flüssig.
window.addEventListener('scroll', updateTitle, { passive: true });

// Einmal beim Laden ausführen, damit der richtige Zustand
// von Anfang an stimmt (auch wenn die Seite schon etwas gescrollt ist).
updateTitle();
