let currentType = 'individual';

document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.r-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentType = tab.dataset.type;
            refreshRanking();
        });
    });

    refreshRanking();
});

async function refreshRanking() {
    const disciplineEl = document.getElementById('rank-discipline');
    const discipline = disciplineEl ? (disciplineEl.value || 'MMA') : 'MMA'; 
    
    try {
        const response = await fetch('/filterRanking', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                type: currentType, 
                discipline: discipline 
            })
        });

        if (!response.ok) return;

        const data = await response.json();
        renderTable(data);
    } catch (error) {
        console.error("Error refreshing ranking:", error);
    }
}

function renderTable(data) {
    const header = document.getElementById('table-header');
    const tbody = document.querySelector('#ranking-table tbody');
    
    if (!header || !tbody) return;

    if (currentType === 'individual') {
        header.innerHTML = '<th>RANK</th><th>ATHLETE</th><th>CLUB</th><th>POINTS</th><th>RECORD (W-L-D)</th>';
        tbody.innerHTML = data.map((row, i) => {
            const rankClass = i === 0 ? "rank-1" : i === 1 ? "rank-2" : i === 2 ? "rank-3" : "";
            const otherClass = rankClass ? "" : "rank-other";
            return `
                <tr class="${rankClass}">
                    <td>${i + 1}</td>
                    <td><b>${row.firstname} ${row.lastname}</b></td>
                    <td class="${otherClass}">${row.club_name || 'Independent'}</td>
                    <td>${row.points}</td>
                    <td class="${otherClass}"><b>${row.wins}-${row.losses}-${row.draws}</b></td>
                </tr>`;
        }).join('');
    } else {
        header.innerHTML = '<th>RANK</th><th>CLUB NAME</th><th>ATHLETES</th><th>TOTAL WINS</th><th>TOTAL POINTS</th>';
        tbody.innerHTML = data.map((row, i) => {
            const rankClass = i === 0 ? "rank-1" : i === 1 ? "rank-2" : i === 2 ? "rank-3" : "";
            const otherClass = rankClass ? "" : "rank-other"; 
            return `
                <tr class="${rankClass}">
                    <td>${i + 1}</td>
                    <td><b>${row.club_name}</b></td>
                    <td class="${otherClass}">${row.athlete_count}</td>
                    <td class="${otherClass}">${row.total_wins}</td>
                    <td class="${otherClass}"><b>${row.total_points}</b></td>
                </tr>`;
        }).join('');
    }
}