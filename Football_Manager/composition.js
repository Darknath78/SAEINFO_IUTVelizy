document.addEventListener('DOMContentLoaded', () => {

    const pitch = document.getElementById('pitch');
    const saveForm = document.getElementById('save-composition-form');

    let draggedPlayer = null;

    document.querySelectorAll('.player-token').forEach(player => {
        player.addEventListener('dragstart', (e) => {
            draggedPlayer = e.target;
            setTimeout(() => e.target.style.display = 'none', 0); // Cache l'original
        });

        player.addEventListener('dragend', (e) => {
            setTimeout(() => {
                draggedPlayer.style.display = 'flex';
                draggedPlayer = null;
            }, 0);
        });
    });

    document.querySelectorAll('.drop-zone, #pitch').forEach(zone => {
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', (e) => {
            zone.classList.remove('drag-over');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            if (draggedPlayer) {
                if (zone.id === 'pitch') {
                    placePlayerOnPitch(draggedPlayer, e.clientX, e.clientY);
                } else {
                    zone.appendChild(draggedPlayer);
                    draggedPlayer.classList.remove('on-pitch');
                    draggedPlayer.style.position = 'static';
                    if (zone.id === 'bench') {
                        draggedPlayer.dataset.position = 'Remplaçant';
                    } else {
                        draggedPlayer.dataset.position = 'Non-Placé';
                    }
                }
            }
        });
    });

    function placePlayerOnPitch(playerElement, clientX, clientY) {
        pitch.appendChild(playerElement);
        const pitchRect = pitch.getBoundingClientRect();
        let top = ((clientY - pitchRect.top) / pitchRect.height) * 100;
        let left = ((clientX - pitchRect.left) / pitchRect.width) * 100;

        top = Math.max(0, Math.min(100, top));
        left = Math.max(0, Math.min(100, left));

        playerElement.classList.add('on-pitch');
        playerElement.style.position = 'absolute';
        playerElement.style.top = `${top}%`;
        playerElement.style.left = `${left}%`;
        playerElement.style.transform = `translate(-50%, -50%)`;

        playerElement.dataset.position = `${left.toFixed(2)}%|${top.toFixed(2)}%`;
    }

    /**
     * Traduit les coordonnées en % en un nom de poste détaillé
     * @param {number} top - Position verticale en %
     * @param {number} left - Position horizontale en %
     * @returns {string} - Le nom du poste
     */
    function getZoneFromPosition(top, left) {
        if (top > 85) return 'Gardien (G)';

        if (top > 65) {
            if (left < 20) return 'Arrière Gauche (AG)';
            if (left < 45) return 'Défenseur Central Gauche (DCG)';
            if (left < 60) return 'Défenseur Central Droit (DCD)';
            if (left > 80) return 'Arrière Droit (AD)';
            return 'Défenseur';
        }

        if (top > 45) {
            if (left < 25) return 'Milieu Latéral Gauche (MLG)';
            if (left < 75) return 'Milieu Défensif Central (MDC)';
            if (left > 75) return 'Milieu Latéral Droit (MLD)';
            return 'Milieu Défensif';
        }

        if (top > 25) {
            if (left < 30) return 'Milieu Gauche (MG)';
            if (left < 50) return 'Milieu Central Gauche (MCG)';
            if (left < 70) return 'Milieu Central Droit (MCD)';
            if (left > 70) return 'Milieu Droit (MD)';
            return 'Milieu Offensif';
        }

        if (top <= 25) {
            if (left < 35) return 'Ailier Gauche (AiG)';
            if (left > 65) return 'Ailier Droit (AiD)';
            return 'Avant-Centre (BU)';
        }

        return 'Inconnu';
    }


    saveForm.addEventListener('submit', (e) => {
        e.preventDefault();

        saveForm.querySelectorAll('input[type="hidden"]').forEach(input => {
            if (input.name !== 'id_match') input.remove();
        });

        const allPlacedPlayers = document.querySelectorAll('#pitch .player-token, #bench .player-token');

        allPlacedPlayers.forEach(player => {
            const playerId = player.id.split('-')[1];
            let raw_position = player.dataset.position;
            let final_position_value;

            if (!raw_position || raw_position === 'Non-Placé') {
                return;
            }

            if (raw_position === 'Remplaçant') {
                final_position_value = 'Remplaçant';
            } else {
                const parts = raw_position.split('|');

                if (isNaN(parseFloat(parts[0]))) {
                    final_position_value = raw_position;
                }
                else {
                    const [left_str, top_str] = parts;
                    const left = parseFloat(left_str);
                    const top = parseFloat(top_str);
                    const zoneName = getZoneFromPosition(top, left);
                    final_position_value = `${zoneName}|${left_str}|${top_str}`;
                }
            }

            const hiddenInputPlayerId = document.createElement('input');
            hiddenInputPlayerId.type = 'hidden';
            hiddenInputPlayerId.name = `joueurs[${playerId}][id]`;
            hiddenInputPlayerId.value = playerId;

            const hiddenInputPosition = document.createElement('input');
            hiddenInputPosition.type = 'hidden';
            hiddenInputPosition.name = `joueurs[${playerId}][position]`;
            hiddenInputPosition.value = final_position_value;

            saveForm.appendChild(hiddenInputPlayerId);
            saveForm.appendChild(hiddenInputPosition);
        });

        saveForm.submit();
    });

    function initializePitch() {
        document.querySelectorAll('.player-token.on-pitch').forEach(player => {
            const position_data = player.dataset.position;

            if (position_data && position_data.includes('|')) {
                const parts = position_data.split('|');
                if (parts.length === 3) {
                    const left = parts[1];
                    const top = parts[2];
                    player.style.left = left;
                    player.style.top = top;
                    player.style.transform = 'translate(-50%, -50%)';
                }
            }
        });
    }

    initializePitch();
});