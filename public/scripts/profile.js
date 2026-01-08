document.addEventListener('DOMContentLoaded', () => {
    const tabsContainer = document.querySelector('.profile-tabs');
    const profileContainer = document.querySelector('.profile-main-content');

    if (tabsContainer && profileContainer) {
        const userId = profileContainer.getAttribute('data-user-id');
        const tabs = tabsContainer.querySelectorAll('.p-tab');

        tabs.forEach(tab => {
            tab.addEventListener('click', async (e) => {
                e.preventDefault();
                const discipline = tab.getAttribute('data-discipline');
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                await fetchDisciplineData(discipline, userId);
            });
        });

        fetchDisciplineData('MMA', userId);
    }
});

async function fetchDisciplineData(discipline, userId) {
    const header = document.getElementById('stats-header');
    if (header) header.innerText = `STATISTICS (${discipline})`;

    const labels = {
        winMethod: document.getElementById('win-method-label'),
        lossMethod: document.getElementById('loss-method-label'),
        winSub: document.getElementById('win-submission-label'),
        lossSub: document.getElementById('loss-submission-label')
    };

    const methodText = (discipline === 'BJJ') ? 'POINTS' : 'KO/TKO';
    const subText = (discipline === 'BOXING' || discipline === 'KICKBOXING') ? 'MAJORITY DEC.' : 'SUBMISSION';

    if (labels.winMethod) labels.winMethod.innerText = methodText;
    if (labels.lossMethod) labels.lossMethod.innerText = methodText;
    if (labels.winSub) labels.winSub.innerText = subText;
    if (labels.lossSub) labels.lossSub.innerText = subText;

    try {
        const response = await fetch("/filterProfile", {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ discipline: discipline, userId: userId })
        });

        if (!response.ok) throw new Error("JSON error");
        const fights = await response.json();
        updateProfileUI(fights);
    } catch (error) {
        console.error("Error updating profile:", error);
    }
}

function updateProfileUI(fights) {
    let stats = {
        WIN: { total: 0, unanimous: 0, split: 0, submission: 0, method: 0, other: 0 },
        LOSS: { total: 0, unanimous: 0, split: 0, submission: 0, method: 0, other: 0 },
        DRAW: 0
    };

    const tbody = document.querySelector('.fighter-table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = "";

    if (fights.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem;">No history for this discipline.</td></tr>';
        resetCounters();
        return;
    }

    fights.forEach(fight => {
        const res = fight.result.toUpperCase(); 
        const method = fight.method.toUpperCase();

        if (res === 'DRAW') {
            stats.DRAW++;
        } else if (stats[res]) {
            stats[res].total++;
            if (method.includes('UNANIMOUS')) stats[res].unanimous++;
            else if (method.includes('SPLIT')) stats[res].split++;
            else if (method.includes('SUBMISSION') || method.includes('MAJORITY')) stats[res].submission++; 
            else if (method.includes('KO') || method.includes('POINTS')) stats[res].method++;
            else stats[res].other++;
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="result-badge ${res.toLowerCase()}-bg">${res}</span></td>
            <td>${fight.opponent_firstname} ${fight.opponent_lastname}</td>
            <td class="text-gray">${fight.event_name}</td>
            <td class="text-gray">${fight.method}</td>
            <td class="text-gray">${new Date(fight.fight_date).toLocaleDateString('pl-PL')}</td>
        `;
        tbody.appendChild(row);
    });

    const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.innerText = val; };
    
    setEl('total-wins', stats.WIN.total);
    setEl('win-unanimous', stats.WIN.unanimous);
    setEl('win-split', stats.WIN.split);
    setEl('win-submission', stats.WIN.submission);
    setEl('win-method-count', stats.WIN.method);
    setEl('win-other', stats.WIN.other);
    
    setEl('total-losses', stats.LOSS.total);
    setEl('loss-unanimous', stats.LOSS.unanimous);
    setEl('loss-split', stats.LOSS.split);
    setEl('loss-submission', stats.LOSS.submission);
    setEl('loss-method-count', stats.LOSS.method);
    setEl('loss-other', stats.LOSS.other);

    setEl('total-draws', stats.DRAW);
}

function resetCounters() {
    document.querySelectorAll('.stat-number').forEach(el => el.innerText = "0");
}