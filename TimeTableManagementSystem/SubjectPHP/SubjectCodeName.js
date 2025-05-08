$(document).ready(function () {
    // Check if we're in add mode (i.e., subjectCode dropdown exists)
    const isAddMode = $('#subjectCode').length > 0;

    if (isAddMode) {
        // Fetch subject codes when course or semester changes
        $('#course, #semester').change(function () {
            let course = $('#course').val();
            let semester = $('#semester').val();

            if (course && semester) {
                $.ajax({
                    url: 'get_subject_codes.php',
                    type: 'POST',
                    data: { course: course, semester: semester },
                    success: function (data) {
                        $('#subjectCode').html(data);
                        $('#subjectName').val(''); // Clear subject name
                        $('#facultyid').val('');    // Clear faculty ID
                        $('#tutor').val('');        // Clear faculty name
                    }
                });
            }
        });

        // When Subject Code is selected
        $('#subjectCode').change(function () {
            let subjectCode = $(this).val();

            if (subjectCode) {
                // Fetch Subject Name
                $.ajax({
                    url: 'get_subject_name.php',
                    type: 'POST',
                    data: { subjectCode: subjectCode },
                    success: function (data) {
                        $('#subjectName').val(data);
                    }
                });

                // Fetch Faculty Details
                $.ajax({
                    url: 'get_faculty_details.php',
                    type: 'POST',
                    data: { subjectCode: subjectCode },
                    dataType: 'json',
                    success: function (data) {
                        if (data.facultyId && data.facultyName) {
                            $('#facultyid').val(data.facultyId);
                            $('#tutor').val(data.facultyName);
                        } else {
                            alert('First assign faculty to this subject!');
                            window.location.href = '../FacultyPHP/faculty.php'; // Redirect if no faculty assigned
                        }
                    }
                });
            } else {
                $('#subjectName').val('');
                $('#facultyid').val('');
                $('#tutor').val('');
            }
        });
    }
});
