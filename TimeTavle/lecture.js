const lectureForm = document.getElementById('lectureForm');
const lecturesTable = document.querySelector('#lecturesTable tbody');
let selectedRow = null;

// Add or Edit Lecture
lectureForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const unitCode = document.getElementById('unitCode').value;
    const unitName = document.getElementById('unitName').value;
    const duration = document.getElementById('duration').value;
    const tutor = document.getElementById('tutor').value;
    const roomCapacity = document.getElementById('roomCapacity').value;

    if (selectedRow == null) {
        // Add Lecture
        const newRow = lecturesTable.insertRow();
        addLectureToTable(newRow, unitCode, unitName, duration, tutor, roomCapacity);
        clearForm();
    } else {
        // Edit Lecture
        selectedRow.cells[1].textContent = unitCode;
        selectedRow.cells[2].textContent = unitName;
        selectedRow.cells[3].textContent = duration;
        selectedRow.cells[4].textContent = tutor;
        selectedRow.cells[5].textContent = roomCapacity;
        clearForm();
        selectedRow = null;
        document.getElementById('addLectureBtn').disabled = false;
        document.getElementById('updateLecture').disabled = true;
        document.getElementById('deleteLecture').disabled = true;
    }
});

// Add lecture to the table
function addLectureToTable(row, unitCode, unitName, duration, tutor, roomCapacity) {
    const cellId = row.insertCell(0);
    const cellUnitCode = row.insertCell(1);
    const cellUnitName = row.insertCell(2);
    const cellDuration = row.insertCell(3);
    const cellTutor = row.insertCell(4);
    const cellRoom = row.insertCell(5);
    const cellActions = row.insertCell(6);

    cellId.textContent = lecturesTable.rows.length; // Serial number based on row count
    cellUnitCode.textContent = unitCode;
    cellUnitName.textContent = unitName;
    cellDuration.textContent = duration;
    cellTutor.textContent = tutor;
    cellRoom.textContent = roomCapacity;

    // Add Edit and Delete buttons
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Edit';
    editBtn.addEventListener('click', function () {
        editLecture(row);
    });
    cellActions.appendChild(editBtn);

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Delete';
    deleteBtn.addEventListener('click', function () {
        deleteLecture(row);
    });
    cellActions.appendChild(deleteBtn);
}

// Edit lecture
function editLecture(row) {
    selectedRow = row;
    document.getElementById('unitCode').value = row.cells[1].textContent;
    document.getElementById('unitName').value = row.cells[2].textContent;
    document.getElementById('duration').value = row.cells[3].textContent;
    document.getElementById('tutor').value = row.cells[4].textContent;
    document.getElementById('roomCapacity').value = row.cells[5].textContent;

    document.getElementById('addLectureBtn').disabled = true;
    document.getElementById('updateLecture').disabled = false;
    document.getElementById('deleteLecture').disabled = false;
}
function clearForm() {
    document.getElementById('unitCode').value = '';
    document.getElementById('unitName').value = '';
    document.getElementById('duration').value = '';
    document.getElementById('tutor').value = '';
    document.getElementById('roomCapacity').value = '';
}

// Delete room
function deleteLecture(row) {
    row.remove();
    clearForm();
    selectedRow = null;
    document.getElementById('addLectureBtn').disabled = false;
    document.getElementById('updateLecture').disabled = true;
    document.getElementById('deleteLecture').disabled = true;
}