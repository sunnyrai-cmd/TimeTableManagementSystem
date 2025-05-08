<?php
session_start();
include("connection.php");

// Constants
define('MAX_FACULTY_LOAD', 6);
define('MAX_SUBJECT_PER_DAY', 2);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $course = $_POST["course"] ?? '';
    $semester = $_POST["semester"] ?? '';
    $section = $_POST["section"] ?? '';
    $subjectsPerDay = $_POST["subjects"] ?? 0;
    $startTime = $_POST["start-time"] ?? "09:00";
    $selectedDays = $_POST['days'] ?? [];
    $subjectDuration = $_POST["subject-duration"] ?? 60;
    $breakDuration = $_POST["break-duration"] ?? 10;
    $lunchDuration = $_POST["lunch-duration"] ?? 45;
    $lecturesBeforeLunch = $_POST["lectures-before-lunch"] ?? 3;

    // Validate inputs
    if (empty($course) || empty($semester) || empty($section) || empty($selectedDays)) {
        die("Required fields are missing.");
    }

    // Check if timetable already exists
    $checkTimetableQuery = "SELECT id FROM timetable WHERE course = ? AND Semester = ? AND Section = ? LIMIT 1";
    $stmt = $conn->prepare($checkTimetableQuery);
    $stmt->bind_param("sss", $course, $semester, $section);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo '<script>alert("Timetable for this course, semester, and section already exists. Please delete the existing timetable first."); window.location.href = "timetable.php";</script>';
        exit();
    }

    // Check if subjects are assigned
    $checkSubjectsQuery = "SELECT COUNT(*) as subject_count FROM subjectform s 
                           JOIN courseform c ON s.SubjectCode = c.SubjectCode 
                           WHERE c.Course = ? AND c.semester = ?";
    $stmt = $conn->prepare($checkSubjectsQuery);
    $stmt->bind_param("ss", $course, $semester);
    $stmt->execute();
    $subjectCheckResult = $stmt->get_result();
    $subjectCount = $subjectCheckResult->fetch_assoc()['subject_count'];

    if ($subjectCount === 0) {
        echo '<script>alert("No subjects assigned for this course and semester. Please assign subjects first."); window.location.href = "../SubjectPHP/Subject.php";</script>';
        exit();
    }

    // Get subjects
    $subjectQuery = "SELECT s.SubjectCode, s.SubjectName, s.FacultyId, s.FacultyName 
                     FROM subjectform s 
                     JOIN courseform c ON s.SubjectCode = c.SubjectCode 
                     WHERE c.Course = ? AND c.semester = ?";
    $stmt = $conn->prepare($subjectQuery);
    $stmt->bind_param("ss", $course, $semester);
    $stmt->execute();
    $subjectResult = $stmt->get_result();
    $subjects = $subjectResult->fetch_all(MYSQLI_ASSOC);

    if (count($subjects) === 0) {
        header("Location: subject.php?error=no_subjects&course=".urlencode($course)."&semester=".urlencode($semester));
        exit();
    }

    // Get available rooms
    $roomQuery = "SELECT RoomId, RoomName FROM roomform WHERE Availability = 'Available'";
    $roomResult = $conn->query($roomQuery);
    $rooms = $roomResult->fetch_all(MYSQLI_ASSOC);

    // Organize faculty by subject
    $facultyMembers = [];
    foreach ($subjects as $subject) {
        if (!isset($facultyMembers[$subject['SubjectCode']])) {
            $facultyMembers[$subject['SubjectCode']] = [];
        }
        $facultyMembers[$subject['SubjectCode']][] = [
            'id' => $subject['FacultyId'],
            'name' => $subject['FacultyName']
        ];
    }

    // Get existing timetable for conflict checking
    $existingTimetableQuery = "SELECT course, semester, section, day_of_week AS day, start_time AS time, 
                              FacultyId, RoomId, SubjectCode FROM timetable";
    $existingTimetableResult = $conn->query($existingTimetableQuery);
    $existingTimetable = $existingTimetableResult ? $existingTimetableResult->fetch_all(MYSQLI_ASSOC) : [];

    // Get faculty teaching loads
    $facultyLoadQuery = "SELECT FacultyId, COUNT(*) as current_load FROM timetable GROUP BY FacultyId";
    $facultyLoadResult = $conn->query($facultyLoadQuery);
    $facultyLoads = [];
    while ($row = $facultyLoadResult->fetch_assoc()) {
        $facultyLoads[$row['FacultyId']] = $row['current_load'];
    }

    // Calculate time slots
    $timeSlots = [];
    $currentTime = $startTime;
    $lectureCount = 0;
    $lunchAdded = false;

    for ($i = 0; $i < $subjectsPerDay; $i++) {
        $lectureCount++;
        
        // Add subject slot
        $endTime = date('H:i', strtotime("+$subjectDuration minutes", strtotime($currentTime)));
        $timeSlots[] = [
            'start' => $currentTime,
            'end' => $endTime,
            'type' => 'subject'
        ];
        $currentTime = $endTime;
        
        // Add lunch break
        if (!$lunchAdded && $lectureCount >= $lecturesBeforeLunch) {
            $lunchEnd = date('H:i', strtotime("+$lunchDuration minutes", strtotime($currentTime)));
            $timeSlots[] = [
                'start' => $currentTime,
                'end' => $lunchEnd,
                'type' => 'lunch'
            ];
            $currentTime = $lunchEnd;
            $lunchAdded = true;
            continue;
        }
        
        // Add regular break
        if ($i < $subjectsPerDay - 1) {
            $breakEnd = date('H:i', strtotime("+$breakDuration minutes", strtotime($currentTime)));
            $timeSlots[] = [
                'start' => $currentTime,
                'end' => $breakEnd,
                'type' => 'break'
            ];
            $currentTime = $breakEnd;
        }
    }

    // Function to check conflicts
    function checkConflicts($existingTimetable, $course, $semester, $section, $day, $time, $facultyId, $roomId, $subjectCode) {
        $conflicts = [];
        
        // Check faculty conflicts
        foreach ($existingTimetable as $entry) {
            if ($entry['day'] == $day && $entry['time'] == $time && $entry['FacultyId'] == $facultyId) {
                $conflicts[] = [
                    'type' => 'faculty',
                    'message' => "Faculty already assigned at this time to: " .
                                 "Course: {$entry['course']}, " .
                                 "Semester: {$entry['semester']}, " .
                                 "Section: {$entry['section']}"
                ];
            }
            
            // Check room conflicts
            if ($entry['day'] == $day && $entry['time'] == $time && $entry['RoomId'] == $roomId) {
                $conflicts[] = [
                    'type' => 'room',
                    'message' => "Room already in use at this time by: " .
                                 "Course: {$entry['course']}, " .
                                 "Semester: {$entry['semester']}, " .
                                 "Section: {$entry['section']}"
                ];
            }
            
            // Check subject distribution in same day
            if ($entry['day'] == $day && $entry['SubjectCode'] == $subjectCode && 
                $entry['course'] == $course && $entry['semester'] == $semester && $entry['section'] == $section) {
                $conflicts[] = [
                    'type' => 'subject',
                    'message' => "Subject already scheduled on this day"
                ];
            }
        }
        
        return $conflicts;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Timetable</title>
    <link rel="stylesheet" href="../CSS/Generatetimetable.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .conflict-cell {
            background-color: #ffebee !important;
            border: 2px solid #f44336 !important;
            position: relative;
        }
        .conflict-tooltip {
            position: absolute;
            bottom: 100%;
            left: 0;
            background: #f44336;
            color: white;
            padding: 5px;
            border-radius: 4px;
            font-size: 12px;
            width: 300px;
            z-index: 100;
            display: none;
        }
        .conflict-cell:hover .conflict-tooltip {
            display: block;
        }
        .alert {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .lunch-break {
            background-color: #fff3cd;
            text-align: center;
        }
        #saveBtn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        .status-icon {
            margin-left: 5px;
            font-size: 0.9em;
        }
        .fa-check-circle {
            color: green;
        }
        .fa-times-circle {
            color: red;
        }
        .fa-exclamation-circle {
            color: orange;
        }
    </style>
</head>
<body>
    <div class="container">
        <main class="main-content">
            <header>
                <h1>Generated Timetable Preview</h1>
                <p>Course: <?php echo htmlspecialchars($course); ?> | Semester: <?php echo htmlspecialchars($semester); ?> | Section: <?php echo htmlspecialchars($section); ?></p>
            </header>
            
            <div id="conflict-messages" class="conflict-messages"></div>
            
            <div class="generated-timetable-container">
                <form action="SaveTimeTable.php" method="post" id="timetableForm">
                    <input type="hidden" name="course" value="<?php echo htmlspecialchars($course); ?>">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                    <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
                    <input type="hidden" name="subject-duration" value="<?php echo htmlspecialchars($subjectDuration); ?>">
                    <input type="hidden" name="lunch-duration" value="<?php echo htmlspecialchars($lunchDuration); ?>">
                    
                    <?php foreach ($selectedDays as $day): ?>
                        <input type="hidden" name="days[]" value="<?php echo htmlspecialchars($day); ?>">
                    <?php endforeach; ?>
                    
                    <table class="generated-timetable">
                        <thead>
                            <tr>
                                <th>Day/Time</th>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <?php if ($slot['type'] !== 'break'): ?>
                                        <th>
                                            <?php echo $slot['start']; ?> - <?php echo $slot['end']; ?><br>
                                            <?php if ($slot['type'] === 'lunch'): ?>
                                                <small>(Lunch Break)</small>
                                            <?php endif; ?>
                                        </th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($selectedDays as $day): ?>
                                <tr>
                                    <td><strong><?php echo $day; ?></strong></td>
                                    <?php foreach ($timeSlots as $slot): ?>
                                        <?php if ($slot['type'] === 'lunch'): ?>
                                            <td class="lunch-break">Lunch Break</td>
                                        <?php elseif ($slot['type'] === 'break'): ?>
                                            <?php /* Skip break slots in display */ ?>
                                        <?php else: ?>
                                            <td>
                                                <div class="subject-select-container">
                                                    <div class="subject-select-row">
                                                        <select name="subject[<?php echo $day; ?>][<?php echo $slot['start']; ?>]" required 
                                                                onchange="updateFacultyDropdown(this, '<?php echo $day; ?>_<?php echo $slot['start']; ?>_faculty')"
                                                                class="subject-select">
                                                            <option value="">Select Subject</option>
                                                            <?php foreach ($subjects as $subject): ?>
                                                                <option value="<?php echo $subject['SubjectCode']; ?>">
                                                                    <?php echo $subject['SubjectName']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="subject-select-row">
                                                        <select name="faculty[<?php echo $day; ?>][<?php echo $slot['start']; ?>]" 
                                                                id="<?php echo $day; ?>_<?php echo $slot['start']; ?>_faculty" 
                                                                required class="faculty-select">
                                                            <option value="">Select Faculty</option>
                                                        </select>
                                                        <span class="faculty-status"></span>
                                                    </div>
                                                    <div class="subject-select-row">
                                                        <select name="room[<?php echo $day; ?>][<?php echo $slot['start']; ?>]" 
                                                                required class="room-select">
                                                            <option value="">Select Room</option>
                                                            <?php foreach ($rooms as $room): ?>
                                                                <option value="<?php echo $room['RoomId']; ?>">
                                                                    <?php echo $room['RoomName']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <span class="room-status"></span>
                                                    </div>
                                                    <div class="status-icons">
                                                        <span class="faculty-check-icon status-icon"></span>
                                                        <span class="room-check-icon status-icon"></span>
                                                        <span class="load-check-icon status-icon"></span>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <button type="submit" name="save_timetable" class="save-btn" id="saveBtn" disabled>Save Timetable</button>
                </form>
            </div>
        </main>
    </div>

    <script>
    // JavaScript data from PHP
    const facultyData = <?php echo json_encode($facultyMembers); ?>;
    const existingTimetable = <?php echo json_encode($existingTimetable); ?>;
    const facultyLoads = <?php echo json_encode($facultyLoads); ?>;
    const MAX_FACULTY_LOAD = <?php echo MAX_FACULTY_LOAD; ?>;
    const MAX_SUBJECT_PER_DAY = <?php echo MAX_SUBJECT_PER_DAY; ?>;
    
    // Global variables
    let currentSelections = {};
    let allPossibleAssignments = {};
    let allConflicts = [];
    
    // Initialize faculty dropdowns
    function updateFacultyDropdown(subjectSelect, facultyDropdownId) {
        const facultyDropdown = document.getElementById(facultyDropdownId);
        const subjectCode = subjectSelect.value;
        
        // Clear existing options
        facultyDropdown.innerHTML = '<option value="">Select Faculty</option>';
        
        // Add faculty options for selected subject
        if (subjectCode && facultyData[subjectCode]) {
            facultyData[subjectCode].forEach(faculty => {
                const option = document.createElement("option");
                option.value = faculty.id;
                option.textContent = faculty.name;
                facultyDropdown.appendChild(option);
            });
        }
        
        // Trigger conflict check
        checkAllConflicts();
    }
    
    // Main conflict checking function
    function checkAllConflicts() {
        allConflicts = [];
        const conflictMessages = document.getElementById('conflict-messages');
        conflictMessages.innerHTML = '';
        
        // Reset all conflict indicators
        document.querySelectorAll('.conflict-cell').forEach(el => {
            el.classList.remove('conflict-cell');
        });
        document.querySelectorAll('.conflict-tooltip').forEach(el => {
            el.remove();
        });
        
        // Reset status icons
        document.querySelectorAll('.status-icon').forEach(icon => {
            icon.className = 'status-icon';
        });
        
        // Collect current selections
        currentSelections = {};
        const subjectDistribution = {};
        
        document.querySelectorAll('select').forEach(select => {
            const matches = select.name.match(/\[(.*?)\]\[(.*?)\]/);
            if (!matches) return;
            
            const day = matches[1];
            const time = matches[2];
            const type = select.name.split('[')[0];
            
            if (!currentSelections[day]) currentSelections[day] = {};
            if (!currentSelections[day][time]) currentSelections[day][time] = {};
            
            currentSelections[day][time][type] = select.value;
            
            // Track subject distribution
            if (type === 'subject' && select.value) {
                if (!subjectDistribution[day]) subjectDistribution[day] = {};
                subjectDistribution[day][select.value] = (subjectDistribution[day][select.value] || 0) + 1;
            }
        });
        
        // Check for conflicts
        for (const day in currentSelections) {
            for (const time in currentSelections[day]) {
                const slot = currentSelections[day][time];
                const row = document.querySelector(`select[name="subject[${day}][${time}]"]`)?.closest('td');
                
                if (!row || !slot.subject || !slot.faculty || !slot.room) continue;
                
                // Check faculty conflicts
                const facultyConflicts = existingTimetable.filter(entry => 
                    entry.day === day && 
                    entry.time === time && 
                    entry.FacultyId === slot.faculty
                );
                
                if (facultyConflicts.length > 0) {
                    facultyConflicts.forEach(conflict => {
                        allConflicts.push({
                            type: 'faculty',
                            message: `Faculty conflict: ${getFacultyName(slot.faculty)} is already teaching ${conflict.SubjectCode} at this time in ${conflict.course} - Sem ${conflict.semester} - Sec ${conflict.section}`,
                            day: day,
                            time: time,
                            element: `faculty[${day}][${time}]`
                        });
                    });
                }
                
                // Check room conflicts
                const roomConflicts = existingTimetable.filter(entry => 
                    entry.day === day && 
                    entry.time === time && 
                    entry.RoomId === slot.room
                );
                
                if (roomConflicts.length > 0) {
                    roomConflicts.forEach(conflict => {
                        allConflicts.push({
                            type: 'room',
                            message: `Room conflict: ${getRoomName(slot.room)} is already in use by ${conflict.SubjectCode} at this time in ${conflict.course} - Sem ${conflict.semester} - Sec ${conflict.section}`,
                            day: day,
                            time: time,
                            element: `room[${day}][${time}]`
                        });
                    });
                }
                
                // Check subject distribution
                if (subjectDistribution[day][slot.subject] > MAX_SUBJECT_PER_DAY) {
                    allConflicts.push({
                        type: 'subject',
                        message: `Subject distribution: ${getSubjectName(slot.subject)} appears more than ${MAX_SUBJECT_PER_DAY} times on ${day}`,
                        day: day,
                        time: time,
                        element: `subject[${day}][${time}]`
                    });
                }
                
                // Check faculty load
                const currentLoad = facultyLoads[slot.faculty] || 0;
                const newAssignments = Object.values(currentSelections).reduce((count, daySlots) => {
                    return count + Object.values(daySlots).filter(s => s.faculty === slot.faculty).length;
                }, 0);
                
                if ((currentLoad + newAssignments) > MAX_FACULTY_LOAD) {
                    allConflicts.push({
                        type: 'load',
                        message: `Faculty overload: ${getFacultyName(slot.faculty)} will exceed maximum load (${currentLoad + newAssignments}/${MAX_FACULTY_LOAD})`,
                        day: day,
                        time: time,
                        element: `faculty[${day}][${time}]`
                    });
                }
            }
        }
        
        // Display conflicts
        if (allConflicts.length > 0) {
            displayConflicts(allConflicts);
            document.getElementById('saveBtn').disabled = true;
        } else {
            conflictMessages.innerHTML = '<div class="alert alert-success">No conflicts detected. You can save the timetable.</div>';
            document.getElementById('saveBtn').disabled = false;
        }
    }
    
    // Display conflicts function
    function displayConflicts(conflicts) {
        const conflictMessages = document.getElementById('conflict-messages');
        conflictMessages.innerHTML = '';
        
        // Group conflicts by type for better display
        const facultyConflicts = conflicts.filter(c => c.type === 'faculty');
        const roomConflicts = conflicts.filter(c => c.type === 'room');
        const subjectConflicts = conflicts.filter(c => c.type === 'subject');
        const loadConflicts = conflicts.filter(c => c.type === 'load');
        
        // Display faculty conflicts
        if (facultyConflicts.length > 0) {
            const facultyDiv = document.createElement('div');
            facultyDiv.className = 'alert alert-danger';
            facultyDiv.innerHTML = '<strong><i class="fas fa-chalkboard-teacher"></i> Faculty Conflicts:</strong>';
            
            const facultyList = document.createElement('ul');
            facultyConflicts.forEach(conflict => {
                const li = document.createElement('li');
                li.textContent = conflict.message;
                facultyList.appendChild(li);
                
                // Highlight conflicting cell
                const input = document.querySelector(`select[name="${conflict.element}"]`);
                if (input) {
                    const cell = input.closest('td');
                    cell.classList.add('conflict-cell');
                    cell.insertAdjacentHTML('beforeend', 
                        `<div class="conflict-tooltip">${conflict.message}</div>`);
                    
                    // Update status icon
                    const icon = cell.querySelector('.faculty-check-icon');
                    if (icon) {
                        icon.className = 'status-icon fas fa-times-circle';
                    }
                }
            });
            
            facultyDiv.appendChild(facultyList);
            conflictMessages.appendChild(facultyDiv);
        }
        
        // Display room conflicts
        if (roomConflicts.length > 0) {
            const roomDiv = document.createElement('div');
            roomDiv.className = 'alert alert-danger';
            roomDiv.innerHTML = '<strong><i class="fas fa-door-open"></i> Room Conflicts:</strong>';
            
            const roomList = document.createElement('ul');
            roomConflicts.forEach(conflict => {
                const li = document.createElement('li');
                li.textContent = conflict.message;
                roomList.appendChild(li);
                
                // Highlight conflicting cell
                const input = document.querySelector(`select[name="${conflict.element}"]`);
                if (input) {
                    const cell = input.closest('td');
                    cell.classList.add('conflict-cell');
                    cell.insertAdjacentHTML('beforeend', 
                        `<div class="conflict-tooltip">${conflict.message}</div>`);
                    
                    // Update status icon
                    const icon = cell.querySelector('.room-check-icon');
                    if (icon) {
                        icon.className = 'status-icon fas fa-times-circle';
                    }
                }
            });
            
            roomDiv.appendChild(roomList);
            conflictMessages.appendChild(roomDiv);
        }
        
        // Display subject distribution conflicts
        if (subjectConflicts.length > 0) {
            const subjectDiv = document.createElement('div');
            subjectDiv.className = 'alert alert-warning';
            subjectDiv.innerHTML = '<strong><i class="fas fa-book"></i> Subject Distribution Issues:</strong>';
            
            const subjectList = document.createElement('ul');
            subjectConflicts.forEach(conflict => {
                const li = document.createElement('li');
                li.textContent = conflict.message;
                subjectList.appendChild(li);
                
                // Highlight conflicting cell
                const input = document.querySelector(`select[name="${conflict.element}"]`);
                if (input) {
                    const cell = input.closest('td');
                    cell.classList.add('conflict-cell');
                    cell.insertAdjacentHTML('beforeend', 
                        `<div class="conflict-tooltip">${conflict.message}</div>`);
                }
            });
            
            subjectDiv.appendChild(subjectList);
            conflictMessages.appendChild(subjectDiv);
        }
        
        // Display faculty load conflicts
        if (loadConflicts.length > 0) {
            const loadDiv = document.createElement('div');
            loadDiv.className = 'alert alert-warning';
            loadDiv.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> Faculty Load Issues:</strong>';
            
            const loadList = document.createElement('ul');
            loadConflicts.forEach(conflict => {
                const li = document.createElement('li');
                li.textContent = conflict.message;
                loadList.appendChild(li);
                
                // Highlight conflicting cell
                const input = document.querySelector(`select[name="${conflict.element}"]`);
                if (input) {
                    const cell = input.closest('td');
                    
                    // Update status icon
                    const icon = cell.querySelector('.load-check-icon');
                    if (icon) {
                        icon.className = 'status-icon fas fa-exclamation-circle';
                    }
                }
            });
            
            loadDiv.appendChild(loadList);
            conflictMessages.appendChild(loadDiv);
        }
    }
    
    // Helper functions
    function getFacultyName(facultyId) {
        for (const subject in facultyData) {
            for (const faculty of facultyData[subject]) {
                if (faculty.id === facultyId) return faculty.name;
            }
        }
        return facultyId;
    }
    
    function getRoomName(roomId) {
        const roomSelects = document.querySelectorAll('.room-select option');
        for (const option of roomSelects) {
            if (option.value === roomId) return option.textContent;
        }
        return roomId;
    }
    
    function getSubjectName(subjectCode) {
        const subjectSelects = document.querySelectorAll('.subject-select option');
        for (const option of subjectSelects) {
            if (option.value === subjectCode) return option.textContent;
        }
        return subjectCode;
    }
    
    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', checkAllConflicts);
        });
        
        // Initialize faculty dropdowns for any pre-selected subjects
        document.querySelectorAll('.subject-select').forEach(select => {
            const idParts = select.name.match(/\[(.*?)\]\[(.*?)\]/);
            if (idParts && select.value) {
                const facultyId = `${idParts[1]}_${idParts[2]}_faculty`;
                updateFacultyDropdown(select, facultyId);
            }
        });
        
        // Initial conflict check
        checkAllConflicts();
    });
    </script>
</body>
</html>
<?php
} else {
    header("Location: timetable.php");
    exit();
}
?>