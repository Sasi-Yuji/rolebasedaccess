<div class="card" style="max-width: 900px; margin: 0 auto; padding: 2.5rem; border-radius: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Answer Sheet Batch Upload</h2>
            <p style="color: #64748b; font-size: 0.9rem;">Upload handwritten booklets (up to 40 pages). System will auto-format to A4.</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button onclick="document.getElementById('history-modal').style.display='flex'" class="btn btn-light" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; color: #475569; border: 1px solid #e2e8f0; cursor: pointer; background: #f8fafc; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; height: 38px;"><i class="fas fa-history"></i> View History</button>
            <span class="badge badge-student" style="background: #eef2ff; color: #4f46e5; padding: 0.5rem 1rem; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; white-space: nowrap; height: 38px;">A4 SCAN MODE</span>
        </div>
    </div>

    <div class="form-group" style="margin-bottom: 2rem;">
        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.75rem;">Step 1: Select Subject First</label>
        <select id="subject-id" class="form-control" style="width: 100%; height: 3.5rem; border-radius: 12px; font-weight: 600; font-size: 1rem; border: 2px solid #e2e8f0;">
            <option value="">-- Choose Subject --</option>
            <?php foreach($subjects as $s): ?>
                <option value="<?= $s['id'] ?>"><?= $s['subject_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <small id="subject-warning" style="display: none; color: #ef4444; font-weight: 600; margin-top: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Subject is locked. Click Cancel below to clear images and change subject.</small>
    </div>

    <!-- Load SortableJS for Drag-and-Drop Reordering -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 3rem; text-align: center; margin-bottom: 2rem; transition: all 0.3s;" id="drop-zone">
        <i class="fas fa-images" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem; display: block;"></i>
        <h4 style="font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Step 2: Drag & Drop Exam Pages</h4>
        <p style="color: #64748b; font-size: 0.8rem; margin-bottom: 1.5rem;">Or click to browse files from your device</p>
        <input type="file" id="file-input" multiple accept="image/*" style="display: none;">
        <button class="btn btn-primary" onclick="document.getElementById('file-input').click()" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700;">Browse Images</button>
    </div>

    <!-- Preview Grid -->
    <div id="preview-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 2rem;"></div>

    <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 2rem;">
        <button onclick="window.location.reload()" class="btn btn-light" style="padding: 0.875rem 2rem; border-radius: 12px; font-weight: 600;">Cancel</button>
        <button id="final-submit" class="btn btn-primary" style="padding: 0.875rem 2.5rem; border-radius: 12px; font-weight: 800; display: none;">Submit Final Booklet</button>
    </div>
</div>

<!-- History Modal -->
<div id="history-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 95%; max-width: 1100px; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 1.5rem 2.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Submission History</h3>
            <button onclick="document.getElementById('history-modal').style.display='none'" style="width: 40px; height: 40px; border-radius: 12px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding: 2.5rem; background: #f8fafc;">
            <table id="submissions-table" class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Submitted Date</th>
                        <th>Pages Delivered</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): foreach ($submissions as $sub): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= $sub['subject_name'] ?></div>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: #64748b; font-weight: 500; white-space: nowrap;">
                                <i class="far fa-calendar-alt"></i> <?= date('d M Y, h:i A', strtotime($sub['created_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: #eef2ff; color: #4f46e5; border-radius: 8px; font-weight: 700;"><i class="far fa-file-image"></i> <?= $sub['page_count'] ?> Pages</span>
                        </td>
                        <td>
                            <span class="badge" style="background: #ecfdf5; color: #10b981; border-radius: 8px; font-weight: 700;"><i class="fas fa-check-circle"></i> <?= $sub['status'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Cropping Modal -->
<div id="crop-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 95%; max-width: 800px; border-radius: 24px; overflow: hidden; height: 90vh; display: flex; flex-direction: column;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-weight: 800; color: #1e293b;">Perfect A4 Crop <span id="current-page-label" style="color: #4f46e5; margin-left: 10px;">Page 1</span></h3>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="rotateCrop(-90)" class="btn-action" title="Rotate Left" style="background: #f1f5f9; padding: 0.5rem; border-radius: 8px; border: none; cursor: pointer;"><i class="fas fa-rotate-left"></i></button>
                <button onclick="rotateCrop(90)" class="btn-action" title="Rotate Right" style="background: #f1f5f9; padding: 0.5rem; border-radius: 8px; border: none; cursor: pointer;"><i class="fas fa-rotate-right"></i></button>
                <div style="width: 1px; background: #e2e8f0; margin: 0 0.5rem;"></div>
                <button onclick="zoomCrop(0.1)" class="btn-action" title="Zoom In" style="background: #f1f5f9; padding: 0.5rem; border-radius: 8px; border: none; cursor: pointer;"><i class="fas fa-search-plus"></i></button>
                <button onclick="zoomCrop(-0.1)" class="btn-action" title="Zoom Out" style="background: #f1f5f9; padding: 0.5rem; border-radius: 8px; border: none; cursor: pointer;"><i class="fas fa-search-minus"></i></button>
            </div>
        </div>
        <div style="flex: 1; padding: 2rem; background: #2d3748; position: relative; overflow: hidden;">
            <div style="width: 100%; height: 100%;">
                <img id="crop-image" style="display: block; max-width: 100%; max-height: 100%; margin: 0 auto;">
            </div>
        </div>
        <div style="padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: #64748b; font-size: 0.8rem; margin: 0;"><i class="fas fa-info-circle"></i> Use the grid to align your paper edges.</p>
            <div style="display: flex; gap: 1rem;">
                <button onclick="skipCrop()" class="btn btn-light" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 600;">Default Crop</button>
                <button onclick="saveCrop()" class="btn btn-primary" style="padding: 0.75rem 2.5rem; border-radius: 12px; font-weight: 800; background: #4f46e5;">Save & Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cropper;
    let pendingFiles = [];
    let currentIdx = 0;
    let croppedResults = []; // Just array again

    const fileInput = document.getElementById('file-input');
    const previewGrid = document.getElementById('preview-grid');
    const finalSubmit = document.getElementById('final-submit');
    const cropModal = document.getElementById('crop-modal');
    const cropImg = document.getElementById('crop-image');

    cropModal.style.display = 'none';

    fileInput.addEventListener('change', function(e) {
        if (document.getElementById('subject-id').value === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Subject Required',
                text: 'Please select a subject first before uploading images.',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Got it'
            });
            fileInput.value = '';
            return;
        }
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        pendingFiles = Array.from(files);
        if (pendingFiles.length === 0) return;
        
        currentIdx = 0;
        croppedResults = [];
        previewGrid.innerHTML = '';
        startCropping();
    }

    function startCropping() {
        if (currentIdx >= pendingFiles.length) {
            cropModal.style.display = 'none';
            finalSubmit.style.display = 'flex';
            renderPreviews();
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            cropModal.style.display = 'flex';
            document.getElementById('current-page-label').textContent = `Page ${currentIdx + 1}/${pendingFiles.length}`;
            
            cropImg.onload = function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropImg, {
                    aspectRatio: 1 / 1.414,
                    viewMode: 1,
                    autoCropArea: 0.95,
                    dragMode: 'move',
                    guides: true,
                    center: true,
                    highlight: false,
                    background: false
                });
                cropImg.onload = null;
            };
            cropImg.src = e.target.result;
        };
        reader.readAsDataURL(pendingFiles[currentIdx]);
    }

    function saveCrop() {
        const base64 = cropper.getCroppedCanvas({
            width: 1000,
            imageSmoothingQuality: 'high'
        }).toDataURL('image/jpeg', 0.8);
        
        croppedResults.push(base64);
        currentIdx++;
        startCropping();
    }

    function skipCrop() {
        saveCrop();
    }

    function rotateCrop(deg) {
        if (cropper) cropper.rotate(deg);
    }

    function zoomCrop(ratio) {
        if (cropper) cropper.zoom(ratio);
    }

    function renderPreviews() {
        previewGrid.innerHTML = '';
        
        croppedResults.forEach((base64, idx) => {
            const div = document.createElement('div');
            // We store the base64 in a data attribute to retrieve its visual order later
            div.dataset.base64 = base64; 
            div.style.cssText = 'position: relative; aspect-ratio: 1/1.414; border-radius: 8px; overflow: hidden; border: 2px solid #eef2ff; cursor: grab; transition: transform 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);';
            div.innerHTML = `<img src="${base64}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;" draggable="false">
                             <div class="page-badge" style="position: absolute; top: 4px; left: 4px; background: #4f46e5; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.65rem; font-weight: 800; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">PAGE ${idx + 1}</div>
                             <div style="position: absolute; bottom: 0; left: 0; width: 100%; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); padding: 0.5rem; text-align: center; color: white; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-arrows-alt"></i> Drag to Reorder</div>
                             <div onclick="this.parentElement.remove(); updateNumbers();" style="position: absolute; top: 4px; right: 4px; background: #ef4444; color: white; width: 22px; height: 22px; border-radius: 50%; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;"><i class="fas fa-times"></i></div>`;
            previewGrid.appendChild(div);
        });

        // Initialize Sortable
        new Sortable(previewGrid, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                updateNumbers();
            }
        });
    }

    function updateNumbers() {
        // When dragged or deleted, visually update the "PAGE X" badges based on their new DOM sequence
        const items = previewGrid.children;
        for (let i = 0; i < items.length; i++) {
            items[i].querySelector('.page-badge').textContent = `PAGE ${i + 1}`;
        }
        
        // Locking Subject Selection Logic
        const subjectSelect = document.getElementById('subject-id');
        const subjectWarning = document.getElementById('subject-warning');
        if (items.length === 0) {
            finalSubmit.style.display = 'none';
            subjectSelect.disabled = false;
            subjectWarning.style.display = 'none';
        } else {
            subjectSelect.disabled = true;
            subjectWarning.style.display = 'block';
        }
    }

    finalSubmit.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        
        // Read the visual order directly from the DOM!
        let finalImagesToSubmit = [];
        const items = previewGrid.children;
        for (let i = 0; i < items.length; i++) {
            finalImagesToSubmit.push(items[i].dataset.base64); // Extract base64 dynamically in the exact order they sit on screen
        }

        if (finalImagesToSubmit.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Images Uploaded',
                text: 'Please upload at least one page before submitting.',
                confirmButtonColor: '#4f46e5'
            });
            this.disabled = false;
            this.innerHTML = 'Submit Final Booklet';
            return;
        }

        const payload = {
            subject_id: document.getElementById('subject-id').value, // Use the correct ID without worrying about disabled, since we read it from DOM manually
            cropped_images: finalImagesToSubmit
        };

        $.ajax({
            url: '<?= base_url('student/upload/answers/store') ?>',
            method: 'POST',
            data: payload,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                    
                    Toast.fire({
                        icon: 'success',
                        title: 'Submitted successfully'
                    });

                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 4000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: res.message,
                        confirmButtonColor: '#ef4444'
                    });
                    finalSubmit.disabled = false;
                    finalSubmit.textContent = 'Submit Final Booklet';
                }
            }
        });
    });
</script>

<style>
    .badge { display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap; padding: 0.4rem 0.8rem; }
    .badge-student { border: 1px solid #c7d2fe; letter-spacing: 0.05em; font-size: 0.7rem; }
    .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    #drop-zone:hover { border-color: #4f46e5; background: #eef2ff; }
    
    /* Global Cropper Fix for Handles */
    .cropper-point {
        width: 14px !important;
        height: 14px !important;
        background-color: #3b82f6 !important;
        opacity: 1 !important;
        border: 2px solid white !important;
        border-radius: 4px !important;
    }
    .cropper-point.point-e { top: 50%; margin-top: -7px; right: -7px !important; cursor: ew-resize; }
    .cropper-point.point-w { top: 50%; margin-top: -7px; left: -7px !important; cursor: ew-resize; }
    .cropper-point.point-n { left: 50%; margin-left: -7px; top: -7px !important; cursor: ns-resize; }
    .cropper-point.point-s { left: 50%; margin-left: -7px; bottom: -7px !important; cursor: ns-resize; }
    .cropper-point.point-ne { top: -7px !important; right: -7px !important; cursor: nesw-resize; }
    .cropper-point.point-nw { top: -7px !important; left: -7px !important; cursor: nwse-resize; }
    .cropper-point.point-sw { bottom: -7px !important; left: -7px !important; cursor: nesw-resize; }
    .cropper-point.point-se { bottom: -7px !important; right: -7px !important; cursor: nwse-resize; }
    
    .cropper-view-box { outline: 2px solid #3b82f6; outline-color: rgba(59, 130, 246, 0.75); }
    .cropper-line { background-color: #3b82f6; }

    .data-table th { background: #f8fafc; color: #475569; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem; border-bottom: 2px solid #f1f5f9; text-align: left; }
    .data-table td { border-bottom: 1px solid #f1f5f9; padding: 1rem; }
    
    /* Center Modals: Apply Blur */
    .swal2-container.swal2-center.swal2-backdrop-show {
        backdrop-filter: blur(5px) !important;
        background: rgba(0, 0, 0, 0.6) !important;
    }
    
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#submissions-table').DataTable({
        pageLength: 5,
        lengthChange: false,
        ordering: false,
        language: {
            search: "",
            searchPlaceholder: "Search submissions..."
        }
    });
});
</script>
