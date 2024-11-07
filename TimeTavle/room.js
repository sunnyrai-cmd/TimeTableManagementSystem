const lectureForm = document.getElementById('RoomForm');
const lecturesTable = document.querySelector('#lecturesTable tbody');
let selectedRow = null;

// Add or Edit Room
lectureForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const RoomNumber = document.getElementById('RoomNumber').value;
    const RoomName = document.getElementById('RoomName').value;
    const RoomCapacity = document.getElementById('RoomCapacity').value;
    const RoomLocation = document.getElementById('RoomLocation').value;
    const RoomAvalability = document.getElementById('RoomAvalability').value;

    if (selectedRow == null) {
        // Add Room
        const newRow = RoomTable.insertRow();
        addRoomToTable(newRow, RoomNumber, RoomName, RoomCapacity, RoomLocation, RoomAvalability);
        clearForm();
    } else {
        // Edit Room
        selectedRow.cells[1].textContent = RoomNumber;
        selectedRow.cells[2].textContent = RoomName;
        selectedRow.cells[3].textContent = RoomCapacity;
        selectedRow.cells[4].textContent = RoomLocation;
        selectedRow.cells[5].textContent = RoomAvalability;
        clearForm();
        selectedRow = null;
        document.getElementById('addLectureBtn').disabled = false;
        document.getElementById('updateLecture').disabled = true;
        document.getElementById('deleteLecture').disabled = true;
    }
});

// Add room to the table
function addRoomToTable(row, RoomNumber, RoomName, RoomCapacity, RoomLocation, RoomAvalability) {
    const cellId = row.insertCell(0);
    const cellRoomNumber = row.insertCell(1);
    const cellRoomName = row.insertCell(2);
    const cellRoomCapacity = row.insertCell(3);
    const cellRoomLocation = row.insertCell(4);
    const cellRoomAvalability = row.insertCell(5);
    const cellActions = row.insertCell(6);

    cellId.textContent = RoomTable.rows.length; // Serial number based on row count
    cellRoomNumber.textContent = RoomNumber;
    cellRoomName.textContent = RoomName;
    cellRoomCapacity.textContent = RoomCapacity;
    cellRoomLocation.textContent = RoomLocation;
    cellRoomAvalability.textContent = RoomAvalability;

    // Add Edit and Delete buttons
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Edit';
    editBtn.addEventListener('click', function () {
        editRoom(row);
    });
    cellActions.appendChild(editBtn);

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Delete';
    deleteBtn.addEventListener('click', function () {
        deleteRoom(row);
    });
    cellActions.appendChild(deleteBtn);
}

// Edit room
function editRoom(row) {
    selectedRow = row;
    document.getElementById('RoomNumber').value = row.cells[1].textContent;
    document.getElementById('RoomName').value = row.cells[2].textContent;
    document.getElementById('RoomCapacity').value = row.cells[3].textContent;
    document.getElementById('RoomLocation').value = row.cells[4].textContent;
    document.getElementById('RoomAvalability').value = row.cells[5].textContent;

    document.getElementById('addLectureBtn').disabled = true;
    document.getElementById('updateLecture').disabled = false;
    document.getElementById('deleteLecture').disabled = false;
}

// Clear the form
function clearForm() {
    document.getElementById('RoomNumber').value = '';
    document.getElementById('RoomName').value = '';
    document.getElementById('RoomCapacity').value = '';
    document.getElementById('RoomLocation').value = '';
    document.getElementById('RoomAvalability').value = '';
}

// Delete room
function deleteRoom(row) {
    row.remove();
    clearForm();
    selectedRow = null;
    document.getElementById('addLectureBtn').disabled = false;
    document.getElementById('updateLecture').disabled = true;
    document.getElementById('deleteLecture').disabled = true;
}
