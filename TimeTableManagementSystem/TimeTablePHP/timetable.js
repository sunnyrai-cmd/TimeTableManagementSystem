// timetable.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('timetable-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(form);
        
        // Send to server
        fetch('GenerateTimeTable.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector('.table-container').innerHTML = html;
            initializeTimeSlotGeneration();
        })
        .catch(error => console.error('Error:', error));
    });
});

function initializeTimeSlotGeneration() {
    const addSlotBtn = document.getElementById('add-slot-btn');
    if (!addSlotBtn) return;

    let currentTime = document.querySelector('[name="start-time"]').value;
    const breakTime = parseInt(document.querySelector('[name="break-time"]').value) || 10;
    const lunchDuration = parseInt(document.querySelector('[name="lunch-duration"]').value) || 30;
    let timeSlots = [];
    
    addSlotBtn.addEventListener('click', function() {
        const subjectSelects = document.querySelectorAll('.subject-select:last-of-type');
        let duration = 60; // Default duration
        
        // Get duration from selected subject if available
        subjectSelects.forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption && selectedOption.value) {
                duration = parseInt(selectedOption.dataset.duration) || 60;
            }
        });
        
        // Calculate end time
        const startTime = currentTime;
        const endTime = addMinutes(startTime, duration);
        
        // Add to time slots
        timeSlots.push({
            start: startTime,
            end: endTime,
            duration: duration
        });
        
        // Update current time with break
        currentTime = addMinutes(endTime, breakTime);
        
        // Update the UI
        updateTimetableUI(timeSlots);
    });
    
    function addMinutes(time, minutes) {
        const [hours, mins] = time.split(':').map(Number);
        const date = new Date();
        date.setHours(hours, mins + minutes, 0);
        return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
    }
    
    function updateTimetableUI(slots) {
        const headerRow = document.querySelector('#generated-table thead tr');
        headerRow.innerHTML = '<th>Day</th>';
        
        // Update header with time slots
        slots.forEach(slot => {
            const th = document.createElement('th');
            th.textContent = `${slot.start} - ${slot.end}`;
            headerRow.appendChild(th);
        });
        
        // Update each day row
        document.querySelectorAll('.day-row').forEach(row => {
            const day = row.dataset.day;
            row.innerHTML = `<td><strong>${day}</strong></td>`;
            
            slots.forEach(slot => {
                // Subject cell
                const subjectCell = document.createElement('td');
                subjectCell.className = 'subject-cell';
                subjectCell.innerHTML = `
                    <select name="subject[${day}][]" class="subject-select" required>
                        <option value="">Select Subject</option>
                        ${getSubjectOptions()}
                    </select>`;
                row.appendChild(subjectCell);
                
                // Room cell
                const roomCell = document.createElement('td');
                roomCell.className = 'room-cell';
                roomCell.innerHTML = `
                    <select name="room[${day}][]" required>
                        <option value="">Select Room</option>
                        ${getRoomOptions()}
                    </select>`;
                row.appendChild(roomCell);
            });
        });
    }
    
    function getSubjectOptions() {
        // This should be populated from server-side data
        return Array.from(document.querySelectorAll('.subject-select option'))
                   .map(opt => opt.outerHTML)
                   .join('');
    }
    
    function getRoomOptions() {
        // This should be populated from server-side data
        return Array.from(document.querySelectorAll('.room-select option'))
                   .map(opt => opt.outerHTML)
                   .join('');
    }
}