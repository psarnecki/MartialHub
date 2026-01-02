async function filterEvents(status, event) {
    event.preventDefault();

    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(t => t.classList.remove('active'));
    event.currentTarget.classList.add('active');

    try {
        const response = await fetch("/filterEvents", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
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
            grid.innerHTML = "<p>No events found for this category.</p>";
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
                    <p>${eventData.fullDate}, ${eventData.location}</p>
                `;
            }
            grid.appendChild(card);
        });
    } catch (error) {
        console.error("Error filtering events:", error);
    }
}