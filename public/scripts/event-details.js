document.addEventListener('DOMContentLoaded', () => {
    const tabsContainer = document.querySelector('.details-tabs');
    const mainColumn = document.querySelector('.details-main');
    const infoHTML = mainColumn.innerHTML;
    const eventId = window.location.pathname.split('/').pop();

    const eventDateRaw = document.getElementById('event-date-val').innerText;
    const isPast = new Date(eventDateRaw) < new Date();

    if (tabsContainer) {
        const tabs = tabsContainer.querySelectorAll('.d-tab');

        tabs.forEach(tab => {
            tab.addEventListener('click', async (e) => {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const tabName = tab.innerText.trim();

                if (tabName === 'INFORMATION') {
                    mainColumn.innerHTML = infoHTML;
                } 
                else if (tabName === 'RESULTS') {
                    mainColumn.innerHTML = `
                        <h2>Tournament Results</h2>
                        <div style="padding: 3rem 0; text-align: center; color: var(--text-gray);">
                            <p style="font-weight: 700; font-size: 1.2rem;">
                                Loading results
                            </p>
                            <p>
                                Please wait while we retrieve the tournament data.
                            </p>
                        </div>
                    `;
                    await fetchResults(eventId, mainColumn, isPast);
                } 
                else {
                    mainColumn.innerHTML = `<h2>${tabName}</h2>` + getPlaceholderMessage(tabName, isPast);
                }
            });
        });
    }

    const regBtn = document.getElementById('register-btn');
    if (regBtn) {
        regBtn.addEventListener('click', (e) => {
            e.preventDefault();
            registerToEvent(eventId); 
        });
    }
});

function getPlaceholderMessage(tabName, isPast) {
    let message = "";
    let submessage = "";

    if (isPast) {
        switch(tabName) {
            case 'START LISTS':
                message = "Archive Data";
                submessage = "Start lists are no longer available for this event.";
                break;
            case 'BRACKETS':
                message = "Brackets unavailable";
                submessage = "Tournament brackets are no longer available. Check the results instead.";
                break;
        }
    } else {
        switch(tabName) {
            case 'START LISTS':
                message = "Fighters are signing up!";
                submessage = "Final start lists have not been published yet.";
                break;
            case 'BRACKETS':
                message = "Brackets pending";
                submessage = "Tournament brackets have not been created yet.";
                break;
        }
    }

    return `
        <div style="padding: 4rem 0; text-align: center; color: var(--text-gray);">
            <p style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--nav-black);">${message}</p>
            <p style="font-size: 0.9rem;">${submessage}</p>
        </div>
    `;
}

async function fetchResults(eventId, container, isPast) {
    try {
        const response = await fetch(`/eventResults/${eventId}`);
        const results = await response.json();

        if (results.length === 0) {
            let title = "";
            let message = "";

            if (isPast) {
                title = "No results available";
                message = "Results for this event are not available. Official results may not have been published.";
            } else {
                title = "Results not available";
                message = "This event has not taken place yet. Results will be available after the tournament concludes.";
            }

            container.innerHTML = `
                <h2>Tournament Results</h2>
                <div style="padding: 4rem 0; text-align: center; color: var(--text-gray); max-width: 480px; margin: 0 auto;">
                    <p style="font-weight: 800; font-size: 1.5rem; color: var(--nav-black); margin-bottom: 0.75rem;">
                        ${title}
                    </p>
                    <p>${message}</p>
                </div>
            `;
            return;
        }

        let html = '<h2>Tournament Results</h2><div class="fighter-table-wrapper"><table class="fighter-table"><thead><tr><th>ATHLETE</th><th>RESULT</th><th>OPPONENT</th><th>METHOD</th></tr></thead><tbody>';
        
        results.forEach(res => {
            const badgeClass = res.result.toLowerCase() + '-bg';
            
            html += `
                <tr>
                    <td><b>${res.fighter_firstname} ${res.fighter_lastname}</b></td>
                    <td><span class="result-badge ${badgeClass}">${res.result}</span></td>
                    <td>${res.opponent_firstname} ${res.opponent_lastname}</td>
                    <td class="text-gray">${res.method}</td>
                </tr>`;
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    } catch (e) {
        container.innerHTML = '<h2>Error</h2><p>Could not load results.</p>';
    }
}

async function registerToEvent(eventId) {
    const btn = document.getElementById('register-btn');
    
    if (confirm("Confirm registration for this event?")) {
        btn.innerText = "PROCESSING...";
        btn.disabled = true;

        setTimeout(() => {
            btn.innerText = "REGISTERED";
            btn.classList.remove('btn-sidebar-red');
            btn.style.backgroundColor = "#F2E8E8";
            alert("Registration successful! Check your email for payment details.");
        }, 1500);
    }
}