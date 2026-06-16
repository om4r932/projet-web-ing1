// JS pour Carrousel et Auto-complétion
let currentSpriteIndex = 0;
const items = document.querySelectorAll('.carousel-item');

function showSlide(index) {
    items.forEach((item, i) => {
        item.style.display = (i === index) ? 'block' : 'none';
    });
}

function nextSlide() {
    currentSpriteIndex = (currentSpriteIndex + 1) % items.length;
    showSlide(currentSpriteIndex);
}

function prevSlide() {
    currentSpriteIndex = (currentSpriteIndex - 1 + items.length) % items.length;
    showSlide(currentSpriteIndex);
}

// Recherche via Entrée
function handleSearch(event) {
    if (event.key === 'Enter') {
        const input = document.getElementById('search').value;
        if (input) window.location.href = 'index.php?id=' + input;
    }
}

// Auto-complétion
async function autoComplete() {
    const term = document.getElementById('search').value;
    const suggestions = document.getElementById('suggestions');
    if (term.length < 2) {
        suggestions.innerHTML = '';
        return;
    }
    const response = await fetch('index.php?term=' + term);
    const data = await response.json();
    suggestions.innerHTML = data.map(p =>
        `<div onclick="window.location.href='index.php?id=${p.pokedex_id}'">${p.name_fr}</div>`
    ).join('');
}

// Initial
if (items.length > 0) showSlide(0);
document.getElementById('search').addEventListener('keypress', handleSearch);
