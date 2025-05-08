// Logout button functionality
document.getElementById('logout-btn').addEventListener('click', () => {
    // Redirect to login page
    window.location.href = 'login.html';
});
const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

let currentDate = new Date();

function renderCalendar() {
    const monthYearElement = document.getElementById('month-year');
    const calendarTableBody = document.querySelector('#calendar-table tbody');
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();
    
    monthYearElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;

    // Get the first day of the month
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    
    // Get the last date of the month
    const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();

    // Clear previous calendar
    calendarTableBody.innerHTML = '';

    // Create rows for the calendar
    let day = 1;

    for (let i = 0; i < 6; i++) { // 6 rows max for calendar
        const row = document.createElement('tr');
        
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            
            if (i === 0 && j < firstDay) {
                cell.classList.add('disabled');
            } else if (day <= lastDate) {
                cell.textContent = day;
                day++;
            } else {
                cell.classList.add('disabled');
            }

            row.appendChild(cell);
        }

        calendarTableBody.appendChild(row);
    }
}

document.getElementById('prev-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});

document.getElementById('next-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

// Initial render
renderCalendar();


