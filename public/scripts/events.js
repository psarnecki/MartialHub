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

    const clickedTab = event ? event.currentTarget : [...document.querySelectorAll('.tab')].find(t => t.innerText === status);
    if (clickedTab) clickedTab.classList.add('active');

    await applyFilters();
}

async function applySidebarFilters(event) {
    if (event) event.preventDefault();
    await applyFilters();
}

async function applyFilters() {
    const data = {
        status: currentStatus,
        search: document.getElementById('filter-search').value,
        discipline: document.getElementById('filter-discipline').value,
        location: document.getElementById('filter-location').value,
        date: document.getElementById('filter-date').value
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
        const countHeader = document.getElementById('results-count');

        if (countHeader) {
            countHeader.innerText = `FOUND ${events.length} EVENTS`;
        }
        
        const grid = document.getElementById('event-grid');
        
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