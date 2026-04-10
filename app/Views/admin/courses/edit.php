<div class="glass-card" style="padding: 1.5rem; margin-top: 1rem; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600;">Edit Course: <?= esc($course['course_code']) ?></h2>
        <a href="<?= base_url('admin/courses') ?>" class="btn" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; background: #f1f5f9; color: #475569;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="<?= base_url('admin/courses/update/' . $course['id']) ?>" method="POST" id="editCourseForm">
        <?= csrf_field() ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Department -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="department" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Department <span style="color: #ef4444;">*</span></label>
                <select name="department" id="department" required onchange="populateCourses()"
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem;" 
                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Department</option>
                    <option value="Computer Science" <?= old('department', $course['department']) == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                    <option value="Information Technology" <?= old('department', $course['department']) == 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                    <option value="Mechanical Engineering" <?= old('department', $course['department']) == 'Mechanical Engineering' ? 'selected' : '' ?>>Mechanical Engineering</option>
                    <option value="Electronics Engineering" <?= old('department', $course['department']) == 'Electronics Engineering' ? 'selected' : '' ?>>Electronics Engineering</option>
                    <option value="Civil Engineering" <?= old('department', $course['department']) == 'Civil Engineering' ? 'selected' : '' ?>>Civil Engineering</option>
                    <option value="Business Administration" <?= old('department', $course['department']) == 'Business Administration' ? 'selected' : '' ?>>Business Administration</option>
                </select>
            </div>

            <!-- Course Name -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="course_name" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Course Name <span style="color: #ef4444;">*</span></label>
                <select name="course_name" id="course_name" required onchange="populateCourseCode()"
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem;" 
                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Select Department First</option>
                </select>
                <input type="hidden" id="old_course_name" value="<?= esc(old('course_name', $course['course_name'])) ?>">
            </div>

            <!-- Course Code -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="course_code" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Course Code <span style="color: #ef4444;">*</span></label>
                <input type="text" name="course_code" id="course_code" value="<?= esc(old('course_code', $course['course_code'])) ?>" readonly 
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: #f8fafc; color: #64748b;" 
                    class="cursor-not-allowed">
            </div>

            <!-- Semester -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="semester" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Semester <span style="color: #ef4444;">*</span></label>
                <select name="semester" id="semester" required 
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem;" 
                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <?php for($i=1; $i<=8; $i++): ?>
                        <option value="<?= $i ?>" <?= old('semester', $course['semester']) == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Credits -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="credits" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Credits <span style="color: #ef4444;">*</span></label>
                <select name="credits" id="credits" required 
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem;" 
                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= old('credits', $course['credits']) == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Status -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="status" style="font-weight: 500; font-size: 0.875rem; color: #475569;">Status <span style="color: #ef4444;">*</span></label>
                <select name="status" id="status" required 
                    style="padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem;" 
                    class="focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="Active" <?= old('status', $course['status']) === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= old('status', $course['status']) === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600;">
                <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Update Course
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const annaUniversityCourses = {
    "Computer Science": [
        {"name": "Data Structures", "code": "CS3301"},
        {"name": "Operating Systems", "code": "CS3401"},
        {"name": "Database Management Systems", "code": "CS3492"},
        {"name": "Computer Networks", "code": "CS3591"},
        {"name": "Design and Analysis of Algorithms", "code": "CS3501"},
        {"name": "Object Oriented Programming", "code": "CS3391"}
    ],
    "Information Technology": [
        {"name": "Web Technology", "code": "IT3301"},
        {"name": "Internet of Things", "code": "IT3401"},
        {"name": "Cloud Computing", "code": "IT3501"},
        {"name": "Information Security", "code": "IT3601"}
    ],
    "Mechanical Engineering": [
        {"name": "Engineering Thermodynamics", "code": "ME3301"},
        {"name": "Fluid Mechanics and Machinery", "code": "ME3401"},
        {"name": "Manufacturing Technology", "code": "ME3501"}
    ],
    "Electronics Engineering": [
        {"name": "Electronic Circuits", "code": "EC3301"},
        {"name": "Digital Electronics", "code": "EC3401"},
        {"name": "Signals and Systems", "code": "EC3501"},
        {"name": "VLSI Design", "code": "EC3601"}
    ],
    "Civil Engineering": [
        {"name": "Strength of Materials", "code": "CE3301"},
        {"name": "Fluid Mechanics", "code": "CE3401"},
        {"name": "Environmental Engineering", "code": "CE3501"}
    ],
    "Business Administration": [
        {"name": "Principles of Management", "code": "BA3301"},
        {"name": "Financial Management", "code": "BA3401"},
        {"name": "Marketing Management", "code": "BA3501"}
    ]
};

function populateCourses(selectedValue = '') {
    const dept = document.getElementById('department').value;
    const courseSelect = document.getElementById('course_name');
    const codeInput = document.getElementById('course_code');
    
    courseSelect.innerHTML = '<option value="">Select Course</option>';
    
    if (dept && annaUniversityCourses[dept]) {
        courseSelect.disabled = false;
        annaUniversityCourses[dept].forEach(course => {
            const option = document.createElement('option');
            option.value = course.name;
            option.text = course.name;
            option.dataset.code = course.code;
            if (selectedValue === course.name) option.selected = true;
            courseSelect.appendChild(option);
        });
        if (!selectedValue) codeInput.value = '';
    } else {
        courseSelect.disabled = true;
        courseSelect.innerHTML = '<option value="">Select Department First</option>';
        codeInput.value = '';
    }
}

function populateCourseCode() {
    const courseSelect = document.getElementById('course_name');
    const codeInput = document.getElementById('course_code');
    
    if (courseSelect.selectedIndex > 0) {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        codeInput.value = selectedOption.dataset.code || '';
    } else {
        codeInput.value = '';
    }
}

$(document).ready(function() {
    const oldDept = $('#department').val();
    const oldCourse = $('#old_course_name').val();
    if (oldDept) {
        populateCourses(oldCourse);
    }
});
</script>
