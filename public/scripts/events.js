let currentStatus = 'UPCOMING';
let searchTimeout;

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('filter-search');
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
    }
});

async function filterEvents(status, event) {
    if (event) event.preventDefault();
    currentStatus = status;

    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));

    const clickedTab = event ? event.currentTarget : document.querySelector(`.tab[data-status="${status}"]`);
    if (clickedTab) clickedTab.classList.add('active');

    await applyFilters();
}

async function applySidebarFilters(event) {
    if (event) event.preventDefault();
    await applyFilters();
}

async function applyFilters() {
    const searchEl = document.getElementById('filter-search');
    const disciplineEl = document.getElementById('filter-discipline');
    const locationEl = document.getElementById('filter-location');
    const dateEl = document.getElementById('filter-date');

    const data = {
        status: currentStatus,
        search: searchEl ? searchEl.value : "",
        discipline: disciplineEl ? disciplineEl.value : "ALL DISCIPLINES",
        location: locationEl ? locationEl.value : "ALL LOCATIONS",
        date: dateEl ? dateEl.value : ""
    };

    try {
        const response = await fetch("/filterEvents", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            console.error("Server error:", response.status);
            return;
        }

        const events = await response.json();

        const grid = document.getElementById('event-grid');
        if (!grid) {
            console.error('Event grid element not found.');
            return;
        }

        const countHeader = document.getElementById('results-count');
        if (countHeader) {
            countHeader.innerText = `FOUND ${events.length} EVENTS`;
        }

        const isDense = grid.classList.contains('events-grid-dense');
        grid.innerHTML = ""; 

        if (events.length === 0) {
            grid.innerHTML = '<p class="no-results">No events found matching your criteria for ' + currentStatus.toLowerCase() + ' events.</p>';
            return;
        }

        events.forEach(eventData => {
            const card = document.createElement('a');
            card.href = `/eventDetails/${eventData.id}`;
            
            if (isDense) {
                card.className = 'event-card-small';
                card.innerHTML = `
                    <div class="img-wrap-small">
                        <img src="${eventData.imageUrl}" alt="">
                    </div>
                    <h3>${eventData.title}</h3>
                    <p class="meta">${eventData.day}, ${eventData.location}</p>
                    <p class="category">${eventData.discipline}</p>
                `;
            } else {
                card.className = 'event-card';
                card.innerHTML = `
                    <div class="img-placeholder">
                        <img src="${eventData.imageUrl}" alt="">
                    </div>
                    <h3>${eventData.title}</h3>
                    <p>${eventData.date}, ${eventData.location}</p>
                `;
            }
            grid.appendChild(card);
        });
    } catch (error) {
        console.error("Error filtering events:", error);
    }
}