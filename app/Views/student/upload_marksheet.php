<div class="card" style="max-width: 700px; margin: 0 auto; padding: 2.5rem; border-radius: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Previous Marksheet Upload</h2>
            <p style="color: #64748b; font-size: 0.9rem;">Upload your previous academic certificates for verification.</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button onclick="document.getElementById('history-modal').style.display='flex'" class="btn btn-light" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; color: #475569; border: 1px solid #e2e8f0; cursor: pointer; background: #f8fafc; display: inline-flex; align-items: center; gap: 0.5rem; white-space: nowrap; height: 38px;"><i class="fas fa-history"></i> View History</button>
        </div>
    </div>

    <form id="marksheet-form">
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.75rem;">Step 1: Document Name</label>
            <input type="text" id="doc-name" class="form-control" placeholder="e.g. 10th Standard Marksheet" required style="width: 100%; height: 3.5rem; border-radius: 12px; font-weight: 600; border: 2px solid #e2e8f0;">
            <small id="doc-warning" style="display: none; color: #ef4444; font-weight: 600; margin-top: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Document name locked. Click the red 'X' on your image to clear and rename.</small>
        </div>

        <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 3rem; text-align: center; margin-bottom: 2rem; transition: all 0.3s;" id="drop-zone">
            <i class="fas fa-file-signature" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem; display: block;"></i>
            <h4 style="font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Step 2: Select Certificate Image</h4>
            <input type="file" id="file-input" accept="image/*" style="display: none;">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('file-input').click()" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; background: #64748b; color: white;">Choose File</button>
        </div>

        <!-- Single Preview -->
        <div id="preview-area" style="display: none; margin-bottom: 2rem; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; position: relative;">
            <img id="preview-img" style="width: 100%; display: block;">
            <button type="button" onclick="resetUpload()" style="position: absolute; top: 10px; right: 10px; background: #ef4444; color: white; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #f1f5f9; padding-top: 2rem;">
            <button type="button" onclick="window.location.href='<?= base_url('student/profile') ?>'" class="btn btn-light" style="padding: 0.875rem 2rem; border-radius: 12px; font-weight: 600;">Cancel</button>
            <button id="final-submit" type="submit" class="btn btn-primary" style="padding: 0.875rem 2.5rem; border-radius: 12px; font-weight: 800; background: #10b981;">Submit for Verification</button>
        </div>
    </form>
</div>

<!-- History Modal -->
<div id="history-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 95%; max-width: 1100px; border-radius: 24px; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 1.5rem 2.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
            <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Documents History</h3>
            <button onclick="document.getElementById('history-modal').style.display='none'" style="width: 40px; height: 40px; border-radius: 12px; border: none; background: #f1f5f9; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding: 2.5rem; background: #f8fafc;">
            <table id="documents-table" class="data-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Uploaded Date</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): foreach ($documents as $doc): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= $doc['doc_name'] ?></div>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; color: #64748b; font-weight: 500; white-space: nowrap;">
                                <i class="far fa-calendar-alt"></i> <?= date('d M Y, h:i A', strtotime($doc['created_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($doc['status'] == 'Approved'): ?>
                                <span class="badge" style="background: #ecfdf5; color: #10b981; border-radius: 8px; font-weight: 700;"><i class="fas fa-check-circle"></i> Approved</span>
                            <?php elseif ($doc['status'] == 'Pending'): ?>
                                <span class="badge" style="background: #fffbeb; color: #f59e0b; border-radius: 8px; font-weight: 700;"><i class="fas fa-clock"></i> Pending Validation</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fef2f2; color: #ef4444; border-radius: 8px; font-weight: 700;"><i class="fas fa-times-circle"></i> <?= $doc['status'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url($doc['image_path']) ?>" target="_blank" class="btn btn-light" style="padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600;"><i class="fas fa-eye"></i> View</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Cropping Modal -->
<div id="crop-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: white; width: 95%; max-width: 800px; border-radius: 24px; overflow: hidden; height: 90vh; display: flex; flex-direction: column;">
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-weight: 800; color: #1e293b; margin: 0;">Free Size Crop</h3>
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
        <div style="padding: 1.5rem 2rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 1rem;">
            <button onclick="document.getElementById('crop-modal').style.display='none'" class="btn btn-light" style="padding: 0.75rem 1.5rem; border-radius: 12px;">Cancel</button>
            <button onclick="saveCrop()" class="btn btn-primary" style="padding: 0.75rem 2.5rem; border-radius: 12px; font-weight: 800; background: #10b981;">Apply Crop</button>
        </div>
    </div>
</div>

<script>
    let cropper;
    const fileInput = document.getElementById('file-input');
    const cropModal = document.getElementById('crop-modal');
    const cropImg = document.getElementById('crop-image');
    const previewArea = document.getElementById('preview-area');
    const previewImg = document.getElementById('preview-img');
    const dropZone = document.getElementById('drop-zone');
    let croppedResult = null;

    cropModal.style.display = 'none';

    fileInput.addEventListener('change', function(e) {
        if (document.getElementById('doc-name').value.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Name Required',
                text: 'Please enter a "Document Name" first before uploading the image.',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Got it'
            });
            fileInput.value = '';
            return;
        }

        if (e.target.files && e.target.files[0]) {
            openCropper(e.target.files[0]);
        }
    });

    function openCropper(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            // First show modal so DOM layout resolves the dimensions
            cropModal.style.display = 'flex';
            
            // Wait for image to load to initialize cropper with exact bounds
            cropImg.onload = function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(cropImg, {
                    viewMode: 1, // Keep crop box bounded to the image dimensions
                    autoCropArea: 0.95,
                    dragMode: 'move', // Allow panning the image
                    guides: true,
                    center: true,
                    highlight: false,
                    background: false
                });
                cropImg.onload = null;
            };
            cropImg.src = event.target.result;
        };
        reader.readAsDataURL(file);
    }

    function rotateCrop(deg) {
        if (cropper) cropper.rotate(deg);
    }

    function zoomCrop(ratio) {
        if (cropper) cropper.zoom(ratio);
    }

    function saveCrop() {
        croppedResult = cropper.getCroppedCanvas({
            maxWidth: 2000,
            imageSmoothingQuality: 'high'
        }).toDataURL('image/jpeg', 0.9);
        
        previewImg.src = croppedResult;
        previewArea.style.display = 'block';
        dropZone.style.display = 'none';
        cropModal.style.display = 'none';

        document.getElementById('doc-name').readOnly = true;
        document.getElementById('doc-warning').style.display = 'block';
    }

    function resetUpload() {
        croppedResult = null;
        previewArea.style.display = 'none';
        dropZone.style.display = 'block';
        fileInput.value = '';

        document.getElementById('doc-name').readOnly = false;
        document.getElementById('doc-warning').style.display = 'none';
    }

    document.getElementById('marksheet-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (document.getElementById('doc-name').value.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Name Required',
                text: 'Please enter a "Document Name".',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        if (!croppedResult) {
            Swal.fire({
                icon: 'error',
                title: 'No Image Uploaded',
                text: 'Please select and crop your marksheet image first.',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        const submitBtn = document.getElementById('final-submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

        $.ajax({
            url: '<?= base_url('student/upload/marksheet/store') ?>',
            method: 'POST',
            data: {
                doc_name: document.getElementById('doc-name').value,
                cropped_image: croppedResult
            },
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
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit for Verification';
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
    
    /* SweetAlert2 Background Blur */
    .swal2-container.swal2-backdrop-show {
        backdrop-filter: blur(5px) !important;
        background: rgba(0, 0, 0, 0.6) !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#documents-table').DataTable({
        pageLength: 5,
        lengthChange: false,
        ordering: false,
        language: {
            search: "",
            searchPlaceholder: "Search documents..."
        }
    });
});
</script>
