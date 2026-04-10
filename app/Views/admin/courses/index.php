<div class="glass-card" style="padding: 1.25rem; margin-top: 0.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 5px; height: 1.6rem; background: #4f46e5; border-radius: 4px;"></div>
            <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: #1e293b; letter-spacing: -0.02em;">Course Catalogue</h2>
            <select id="customDeptFilter" style="margin-left: 0.5rem; padding: 0.35rem 0.8rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.8rem; outline: none; background: #f8fafc; color: #475569; font-weight: 600; cursor: pointer;">
                <option value="">All Departments</option>
            </select>
        </div>
        <button onclick="openAddCourseModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.4rem; border: none; cursor: pointer; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-plus"></i> Add New Course
        </button>
    </div>

    <div class="table-responsive">
        <table id="coursesTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Course Name</th>
                    <th>Department</th>
                    <th>Sem</th>
                    <th>Credits</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr data-id="<?= $course['id'] ?>">
                    <td style="font-weight: 600; color: var(--primary);"><?= $course['course_code'] ?></td>
                    <td><?= $course['course_name'] ?></td>
                    <td><?= $course['department'] ?></td>
                    <td><span class="badge" style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.5rem; font-size: 0.7rem;">Sem <?= $course['semester'] ?></span></td>
                    <td><?= $course['credits'] ?></td>
                    <td>
                        <span class="badge" style="background: <?= $course['status'] === 'Active' ? '#dcfce7' : '#fef3c7' ?>; color: <?= $course['status'] === 'Active' ? '#166534' : '#92400e' ?>; padding: 0.25rem 0.5rem; font-size: 0.7rem;">
                            <?= $course['status'] ?>
                        </span>
                    </td>
                    <td style="font-size: 0.7rem; color: #94a3b8;"><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-start;">
                            <button onclick='openViewCourseModal(<?= json_encode($course) ?>)' class="action-btn view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick='openEditCourseModal(<?= json_encode($course) ?>)' class="action-btn edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete(<?= $course['id'] ?>, '<?= $course['course_code'] ?>')" class="action-btn delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Course Modal -->
<div id="courseModalOverlay" class="modal-overlay" onmousedown="attemptCloseModal(event)">
    <div class="modal-container" onmousedown="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h2 id="modalTitle" style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">Add New Course</h2>
                    <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 700; border: 1px solid #e2e8f0; padding: 0.15rem 0.4rem; border-radius: 4px; margin-top: 0.2rem; display: inline-block;">Shortcut: <kbd>Esc</kbd> to close</span>
                </div>
            </div>
            <button type="button" onclick="attemptCloseModal(event)" style="background:none; border:none; font-size:1.75rem; color:#94a3b8; cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">&times;</button>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid #fecaca;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li style="font-size: 0.85rem; font-weight: 500;"><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/courses/store') ?>" method="POST" id="courseForm">
            <?= csrf_field() ?>
            <input type="hidden" name="course_id" id="course_id">
            
            <div style="display: flex; gap: 2rem;">
                <!-- Form Steps Left -->
                <div style="flex: 1; overflow: hidden; position: relative; min-height: 450px; padding-bottom: 1rem;">
                    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; position: relative;">
                        <div id="step1-tab" style="font-weight: 700; font-size: 0.9rem; color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 1rem; margin-bottom: -1rem; transition: all 0.3s;"><i class="fas fa-info-circle mr-1"></i> 1. Curriculum Info</div>
                        <div id="step2-tab" style="font-weight: 700; font-size: 0.9rem; color: #94a3b8; border-bottom: 2px solid transparent; padding-bottom: 1rem; margin-bottom: -1rem; transition: all 0.3s;"><i class="fas fa-sliders-h mr-1"></i> 2. Credits & Status</div>
                    </div>

                    <div id="step1-content" style="transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s; transform: translateX(0); opacity: 1; position: absolute; width: 100%;">
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <!-- Dept -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="department" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Department <span style="color: #ef4444;">*</span></label>
                                <select name="department" id="department" required onchange="populateCourses()" style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f8fafc;" class="input-focus">
                                    <option value="">Select Department</option>
                                    <option value="Computer Science" <?= old('department') == 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                                    <option value="Information Technology" <?= old('department') == 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                                    <option value="Mechanical Engineering" <?= old('department') == 'Mechanical Engineering' ? 'selected' : '' ?>>Mechanical Engineering</option>
                                    <option value="Electronics Engineering" <?= old('department') == 'Electronics Engineering' ? 'selected' : '' ?>>Electronics Engineering</option>
                                    <option value="Civil Engineering" <?= old('department') == 'Civil Engineering' ? 'selected' : '' ?>>Civil Engineering</option>
                                    <option value="Business Administration" <?= old('department') == 'Business Administration' ? 'selected' : '' ?>>Business Administration</option>
                                </select>
                            </div>
                            <!-- Name -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="course_name" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Course Name <span style="color: #ef4444;">*</span></label>
                                <select name="course_name" id="course_name" required onchange="populateCourseCode()" style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f8fafc;" class="input-focus" disabled>
                                    <option value="">Select Department First</option>
                                </select>
                                <input type="hidden" id="old_course_name" value="<?= old('course_name') ?>">
                            </div>
                            <!-- Code -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="course_code" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Course Code <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="course_code" id="course_code" value="<?= old('course_code') ?>" placeholder="Auto-filled" required maxlength="20" readonly style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f1f5f9; color: #64748b; font-weight: 700;" class="input-focus cursor-not-allowed">
                            </div>
                            <div style="text-align: right; margin-top: 0.5rem;">
                                <button type="button" onclick="goToStep2()" class="btn btn-primary" style="padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 700; border: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">Next Step <i class="fas fa-arrow-right ml-2"></i></button>
                            </div>
                        </div>
                    </div>

                    <div id="step2-content" style="transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s; transform: translateX(100%); opacity: 0; pointer-events: none; position: absolute; width: 100%;">
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <!-- Sem -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="semester" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Semester <span style="color: #ef4444;">*</span></label>
                                <select name="semester" id="semester" required style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f8fafc;" class="input-focus">
                                    <?php for($i=1; $i<=8; $i++): ?>
                                        <option value="<?= $i ?>" <?= old('semester') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <!-- Credits -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="credits" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Credits <span style="color: #ef4444;">*</span></label>
                                <select name="credits" id="credits" required style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f8fafc;" class="input-focus">
                                    <?php for($i=1; $i<=6; $i++): ?>
                                        <option value="<?= $i ?>" <?= old('credits') == $i ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <!-- Status -->
                            <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                                <label for="status" style="font-weight: 600; font-size: 0.8rem; color: #475569;">Status <span style="color: #ef4444;">*</span></label>
                                <select name="status" id="status" required style="padding: 0.7rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; background: #f8fafc;" class="input-focus">
                                    <option value="Active" <?= old('status') === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= old('status') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                                <button type="button" onclick="goToStep1()" class="btn" style="background: #f1f5f9; border: none; color: #475569; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 700; transition: all 0.2s;"><i class="fas fa-arrow-left mr-2"></i> Back</button>
                                <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 0.6rem 2rem; font-weight: 700; border:none; border-radius:8px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);"><i class="fas fa-save mr-2"></i> Save Course</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card Right -->
                <div style="width: 250px; display: flex; flex-direction: column; gap: 0.75rem border-left: 1px solid #f1f5f9; padding-left: 1.5rem;">
                    <h4 style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;"><i class="fas fa-bolt text-amber-400 mr-1"></i> Live Preview</h4>
                    <div class="glass-card" style="background: linear-gradient(135deg, #4f46e5, #3730a3); color: white; padding: 1.25rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4); border: none;">
                        <p id="preview_live_code" style="font-size: 0.7rem; font-weight: 800; color: #c7d2fe; margin: 0; letter-spacing: 0.05em;">XXXXX</p>
                        <h3 id="preview_live_name" style="font-size: 1.05rem; font-weight: 800; margin: 0.25rem 0 1rem 0; line-height: 1.3;">Course Title</h3>
                        <div style="background: rgba(255,255,255,0.1); padding: 0.6rem; border-radius: 8px; margin-bottom: 0.75rem;">
                            <p style="font-size: 0.55rem; color: #a5b4fc; text-transform: uppercase; margin: 0; font-weight: 700; letter-spacing: 0.05em;">Department</p>
                            <p id="preview_live_dept" style="font-size: 0.75rem; font-weight: 700; margin: 0; margin-top: 0.1rem;">-</p>
                        </div>
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <span id="preview_live_sem" style="background: rgba(255,255,255,0.2); padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;">Sem -</span>
                            <span id="preview_live_credits" style="background: rgba(255,255,255,0.2); padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;">- Credits</span>
                            <span id="preview_live_status" style="background: rgba(16,185,129,0.4); padding: 0.2rem 0.4rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700;">Active</span>
                        </div>
                    </div>
                    <!-- Duplicate Warning -->
                    <div id="duplicateWarning" style="display: none; background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 8px; font-size: 0.7rem; font-weight: 700; align-items: center; gap: 0.5rem; border: 1px solid #fecaca; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1); margin-top: 0.5rem; animation: fadeIn 0.3s ease-out forwards;">
                        <i class="fas fa-exclamation-triangle"></i> Course already exists!
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Course Details Modal -->
<div id="viewCourseModalOverlay" class="modal-overlay" onclick="closeViewCourseModal()">
    <div class="modal-container" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">Course Overview</h2>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0; margin-top: 0.2rem;">Detailed syllabus and configuration</p>
                </div>
            </div>
            <button onclick="closeViewCourseModal()" style="background:none; border:none; font-size:1.75rem; color:#94a3b8; cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">&times;</button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Course Code</p>
                <p id="view_code" style="font-size: 1.15rem; color: #4f46e5; font-weight: 900; margin: 0;">-</p>
            </div>
            
            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Status</p>
                <p id="view_status_container" style="margin: 0; margin-top: 0.25rem;"></p>
            </div>

            <div style="grid-column: span 2; background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0;">
                <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Course Title</p>
                <p id="view_name" style="font-size: 1.1rem; color: #1e293b; font-weight: 800; margin: 0;">-</p>
            </div>

            <div style="grid-column: span 2; background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center;"><i class="fas fa-building"></i></div>
                <div>
                    <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.1rem;">Department</p>
                    <p id="view_dept" style="font-size: 0.95rem; color: #334155; font-weight: 700; margin: 0;">-</p>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center;"><i class="fas fa-layer-group"></i></div>
                <div>
                    <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.1rem;">Semester</p>
                    <p id="view_sem" style="font-size: 0.95rem; color: #334155; font-weight: 700; margin: 0;">-</p>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 1.25rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center;"><i class="fas fa-award"></i></div>
                <div>
                    <p style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.1rem;">Credits</p>
                    <p id="view_credits" style="font-size: 0.95rem; color: #334155; font-weight: 700; margin: 0;">-</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button onclick="closeViewCourseModal()" class="btn" style="background:#f1f5f9; color:#475569; padding: 0.7rem 1.5rem; font-weight: 700; border:none; border-radius:12px; cursor:pointer; width: 100%;">Close Panel</button>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0);
    backdrop-filter: blur(0px);
    z-index: 1040;
    display: flex;
    justify-content: center;
    align-items: center;
    visibility: hidden;
    pointer-events: none;
    transition: background 0.4s ease-out, backdrop-filter 0.4s ease-out, visibility 0.4s;
}
.modal-overlay.active {
    visibility: visible;
    pointer-events: auto;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(8px);
}
.modal-container {
    background: white;
    width: 90%;
    max-width: 800px;
    padding: 2.5rem;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    transform: translateY(120vh);
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-overlay.active .modal-container {
    transform: translateY(0);
}
.input-focus:focus {
    outline: none;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    background: white !important;
}

/* Premium DataTables Styling Override */
table.dataTable.no-footer { border-bottom: none !important; }
table.dataTable { border-collapse: collapse !important; width: 100% !important; margin-top: 0 !important; }
table.dataTable thead th { background: transparent !important; color: #64748b !important; font-size: 0.7rem !important; text-transform: uppercase !important; letter-spacing: 0.05em; font-weight: 700 !important; border-bottom: 2px solid #e2e8f0 !important; padding: 0.5rem 0.75rem !important; }
table.dataTable tbody tr { transition: all 0.2s ease; border-bottom: 1px solid #f1f5f9 !important; }
table.dataTable tbody tr:last-child { border-bottom: none !important; }
table.dataTable tbody tr:hover { background-color: #f8fafc !important; }
table.dataTable tbody td { padding: 0.5rem 0.75rem !important; vertical-align: middle; color: #334155; font-size: 0.8rem; }
.dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 6px !important; border: 1px solid transparent !important; padding: 0.2rem 0.6rem !important; font-weight: 600; font-size: 0.75rem; margin-left: 0.2rem; transition: all 0.2s; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; border-color: #e2e8f0 !important; color: #1e293b !important; box-shadow: none !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { background: white !important; border: 1px solid #e2e8f0 !important; color: #4f46e5 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: #f8fafc !important; }
.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input { border: 1px solid #e2e8f0 !important; border-radius: 6px !important; padding: 0.2rem 0.6rem !important; outline: none; background: #f8fafc; transition: all 0.2s; font-size: 0.75rem; }
.dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1 !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); background: white; }
.dataTables_info { font-size: 0.75rem; color: #94a3b8 !important; padding-top: 0.2rem !important; }
.dataTables_wrapper { padding-top: 0; }

/* Action Buttons Styling */
.action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; font-size: 0.8rem; cursor: pointer; border-radius: 6px; border: none; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
.action-btn.view { background: #eff6ff; color: #3b82f6; }
.action-btn.view:hover { background: #dbeafe; color: #2563eb; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(59, 130, 246, 0.15); }
.action-btn.edit { background: #fffbeb; color: #d97706; }
.action-btn.edit:hover { background: #fef3c7; color: #b45309; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(217, 119, 6, 0.15); }
.action-btn.delete { background: #fff1f2; color: #e11d48; }
.action-btn.delete:hover { background: #ffe4e6; color: #be123c; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(225, 29, 72, 0.15); }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- DataTables Buttons Plugins (v3.1.2 for DT 2.1.x) -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.min.css">
<style>
/* Additional DataTables UI */
.dt-buttons { display: inline-flex; align-items: center; margin-left: 1rem; }
.dt-button { background: white !important; border: 1px solid #e2e8f0 !important; border-radius: 6px !important; padding: 0.35rem 0.8rem !important; font-size: 0.75rem !important; font-weight: 700 !important; color: #475569 !important; transition: all 0.2s !important; margin-left: 0.35rem !important; display: inline-flex; align-items: center; justify-content: center; }
.dt-button:hover { background: #f8fafc !important; color: #1e293b !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important; border-color: #cbd5e1 !important; transform: translateY(-1px); }
table.dataTable tbody tr td.highlight { background-color: #fef08a !important; color: #854d0e !important; border-radius: 4px; padding: 0.1em 0.2em; font-weight: 700; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.dt-length select { margin: 0 0.25rem; }
.dt-layout-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.dt-layout-row:last-child { margin-top: 0.75rem; margin-bottom: 0; padding-top: 0.5rem; border-top: 1px solid #f1f5f9; }
</style>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
<script src="https://bartaz.github.io/sandbox.js/jquery.highlight.js"></script>

<script>
let isDirty = false;
$(document).ready(function() {
    var table = $('#coursesTable').DataTable({
        responsive: true,
        order: [[6, 'desc']], 
        columnDefs: [ { orderable: false, targets: 7 } ],
        layout: {
            topStart: {
                pageLength: {},
                buttons: [
                    { extend: 'csvHtml5', text: '<i class="fas fa-file-csv text-blue-500 mr-1"></i> CSV Export' },
                    { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF Export' },
                    { extend: 'print', text: '<i class="fas fa-print text-slate-600 mr-1"></i> Print', title: 'Course Catalogue' }
                ]
            },
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        initComplete: function () {
            var column = this.api().column(2); // Department is index 2

            // Populate custom select
            column.data().unique().sort().each(function (d, j) {
                var text = $('<div>').html(d).text();
                $('#customDeptFilter').append('<option value="' + text + '">' + text + '</option>');
            });

            // Bind change event
            $('#customDeptFilter').on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
            });

            // Auto-filter based on URL parameter (from Dashboard Chart Clicks)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('department')) {
                const urlDept = urlParams.get('department');
                $('#customDeptFilter').val(urlDept).trigger('change');
            }
        }
    });

    // Manual Custom Search Highlight for robust compatibility
    table.on('draw.dt', function() {
        var body = $(table.table().body());
        body.unhighlight();
        var searchVal = table.search();
        if (searchVal) {
            body.highlight(searchVal);
        }
    });

    <?php if (session()->getFlashdata('errors')): ?>
        openAddCourseModal();
    <?php endif; ?>
});

const baseUrl = '<?= base_url() ?>';

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
    $(codeInput).trigger('change');
}

// Ensure old values persist after validation error
$(document).ready(function() {
    const oldDept = $('#department').val();
    const oldCourse = $('#old_course_name').val();
    if (oldDept) {
        populateCourses(oldCourse);
    }
});

function openAddCourseModal() {
    // Reset Form
    document.getElementById('courseForm').reset();
    document.getElementById('course_id').value = '';
    document.getElementById('course_name').disabled = true;
    document.getElementById('course_name').innerHTML = '<option value="">Select Department First</option>';
    
    document.getElementById('modalTitle').innerText = 'Add New Course';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Course';
    document.getElementById('courseForm').action = baseUrl + '/admin/courses/store';
    
    const overlay = document.getElementById('courseModalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    isDirty = false;
    goToStep1();
    updatePreview();
}

function openEditCourseModal(course) {
    document.getElementById('modalTitle').innerText = 'Edit Course: ' + course.course_code;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-check" style="margin-right: 0.5rem;"></i> Update Course';
    document.getElementById('courseForm').action = baseUrl + '/admin/courses/update/' + course.id;
    
    // Fill Data
    document.getElementById('course_id').value = course.id;
    document.getElementById('department').value = course.department;
    
    // Populate courses based on department, then set course name and code
    populateCourses(course.course_name);
    document.getElementById('course_code').value = course.course_code;
    
    document.getElementById('semester').value = course.semester;
    document.getElementById('credits').value = course.credits;
    document.getElementById('status').value = course.status;
    
    const overlay = document.getElementById('courseModalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    isDirty = false;
    goToStep1();
    updatePreview();
}

function closeAddCourseModal() {
    const overlay = document.getElementById('courseModalOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// New Stepper and Interactive Form Functions
$('#courseForm input, #courseForm select').on('change input', function() {
    isDirty = true;
    updatePreview();
});

function updatePreview() {
    $('#preview_live_dept').text($('#department').val() || '-');
    $('#preview_live_name').text($('#course_name').val() || 'Course Title');
    $('#preview_live_code').text($('#course_code').val() || 'XXXXX');
    $('#preview_live_sem').text('Sem ' + ($('#semester').val() || '-'));
    $('#preview_live_credits').text(($('#credits').val() || '-') + ' Credits');
    $('#preview_live_status').text($('#status').val() || 'Active');
    
    // Smart Duplicate Detector
    const code = $('#course_code').val();
    const currentId = $('#course_id').val();
    if (code) {
        let isDuplicate = false;
        $('#coursesTable tbody tr').each(function() {
            if ($(this).data('id') == currentId) return; // Skip self
            if ($(this).find('td:eq(0)').text().trim() === code) isDuplicate = true;
        });
        if (isDuplicate) $('#duplicateWarning').css('display', 'flex');
        else $('#duplicateWarning').hide();
    } else {
        $('#duplicateWarning').hide();
    }
}

function goToStep1() {
    $('#step1-tab').css({'color':'#4f46e5', 'border-bottom':'2px solid #4f46e5'});
    $('#step2-tab').css({'color':'#94a3b8', 'border-bottom':'2px solid transparent'});
    $('#step1-content').css({'transform':'translateX(0)', 'opacity':'1', 'pointer-events':'auto'});
    $('#step2-content').css({'transform':'translateX(100%)', 'opacity':'0', 'pointer-events':'none'});
}

function goToStep2() {
    if (!$('#department').val() || !$('#course_name').val() || !$('#course_code').val()) {
        Swal.fire({icon: 'warning', title: 'Incomplete', text: 'Please complete the Curriculum details to proceed.', timer: 2000, showConfirmButton: false});
        return;
    }
    $('#step2-tab').css({'color':'#4f46e5', 'border-bottom':'2px solid #4f46e5'});
    $('#step1-tab').css({'color':'#94a3b8', 'border-bottom':'2px solid transparent'});
    $('#step1-content').css({'transform':'translateX(-100%)', 'opacity':'0', 'pointer-events':'none'});
    $('#step2-content').css({'transform':'translateX(0)', 'opacity':'1', 'pointer-events':'auto'});
}

function attemptCloseModal(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    if (isDirty) {
        Swal.fire({
            title: 'Discard unsaved changes?',
            text: "You've mapped out some data. Are you sure you want to discard it?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, discard',
            cancelButtonText: 'No, keep editing',
            allowOutsideClick: false,
            backdrop: `rgba(15, 23, 42, 0.6)`,
            didOpen: () => {
                const container = Swal.getContainer();
                container.style.backdropFilter = 'blur(12px)';
                container.style.webkitBackdropFilter = 'blur(12px)';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                closeAddCourseModal();
            }
        });
    } else {
        closeAddCourseModal();
    }
}

$(document).keydown(function(e) {
    if (e.key === 'Escape') {
        if (document.getElementById('courseModalOverlay').classList.contains('active')) attemptCloseModal();
        else if (document.getElementById('viewCourseModalOverlay').classList.contains('active')) closeViewCourseModal();
    }
    if ((e.key === 'n' || e.key === 'N') && !['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) {
        if (!document.getElementById('courseModalOverlay').classList.contains('active')) openAddCourseModal();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        if (document.getElementById('courseModalOverlay').classList.contains('active')) $('#courseForm').submit();
    }
});

function openViewCourseModal(course) {
    document.getElementById('view_code').innerText = course.course_code;
    document.getElementById('view_name').innerText = course.course_name;
    document.getElementById('view_dept').innerText = course.department;
    document.getElementById('view_sem').innerText = 'Semester ' + course.semester;
    document.getElementById('view_credits').innerText = course.credits + ' Credits earned';
    
    const statusHtml = course.status === 'Active' 
        ? `<span style="background: #dcfce7; color: #166534; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-check-circle" style="margin-right: 0.3rem;"></i> Active</span>`
        : `<span style="background: #fef3c7; color: #92400e; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700;"><i class="fas fa-times-circle" style="margin-right: 0.3rem;"></i> Inactive</span>`;
    
    document.getElementById('view_status_container').innerHTML = statusHtml;

    const overlay = document.getElementById('viewCourseModalOverlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeViewCourseModal() {
    const overlay = document.getElementById('viewCourseModalOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

function confirmDelete(id, code) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to soft delete course " + code + ". You can restore it later if needed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it!',
        backdrop: `rgba(15, 23, 42, 0.4) left top no-repeat`,
        didOpen: () => {
            const container = Swal.getContainer();
            container.style.backdropFilter = 'blur(8px)';
            container.style.webkitBackdropFilter = 'blur(8px)';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = baseUrl + "/admin/courses/delete/" + id;
        }
    })
}
</script>

