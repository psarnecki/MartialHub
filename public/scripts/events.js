async function filterEvents(status, event) {
    event.preventDefault();

    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
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
        const grid = document.getElementById('event-grid');
        grid.innerHTML = ""; 

        if (events.length === 0) {
            grid.innerHTML = "<p>No events found for this category.</p>";
            return;
        }

        events.forEach(event => {
            const card = document.createElement('a');
            card.href = `/eventDetails/${event.id}`;
            card.className = 'event-card';
            card.innerHTML = `
                <div class="img-placeholder">
                    <img src="${event.imageUrl}" alt="">
                </div>
                <h3>${event.title}</h3>
                <p>${event.date}, ${event.location}</p>
            `;
            grid.appendChild(card);
        });
    } catch (error) {
        console.error("Fetch error:", error);
    }
}