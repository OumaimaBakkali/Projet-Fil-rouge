(($_POST['type'] ?? '') === 'exercise') ? 'selected' : ''; ?>>Exercise</option>
                        <option value="exam" <?php echo (($_POST['type'] ?? '') === 'exam') ? 'selected' : ''; ?>>Exam</option>
                        <option value="notes" <?php echo (($_POST['type'] ?? '') === 'notes') ? 'selected' : ''; ?>>Notes</option>
                        <option value="summary" <?php echo (($_POST['type'] ?? '') === 'summary') ? 'selected' : ''; ?>>Summary</option>
                    </select>
                </div>

                <!-- Second Row -->
                <div class="form-row">
                    <select name="level_id" id="levelSelect" class="form-select" required>
                        <option value="">Select Level</option>
                        <?php foreach ($levels as $level): ?>
                            <option value="<?php echo $level['level_id']; ?>" 
                                    <?php echo (($_POST['level_id'] ?? '') == $level['level_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($level['level_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="sector_id" id="sectorSelect" class="form-select" required disabled>
                        <option value="">Select Sector</option>
                    </select>
                </div>

                <!-- Third Row -->
                <select name="subject_id" id="subjectSelect" class="form-select" required disabled>
                    <option value="">Select Subject</option>
                </select>

                <!-- Description -->
                <textarea name="description" class="form-textarea" placeholder="Description (optional)" 
                          rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>

                <!-- Upload Area -->
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="document" id="fileInput" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required hidden>
                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17,8 12,3 7,8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <p class="upload-text">Click to upload or drag and drop</p>
                    <p class="upload-subtext">PDF, DOC, DOCX, PPT, PPTX, TXT (Max 10MB)</p>
                </div>

                <div id="filePreview" class="file-preview" style="display: none;">
                    <div class="file-info">
                        <span class="file-name"></span>
                        <span class="file-size"></span>
                        <button type="button" class="remove-file" onclick="removeFile()">×</button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="publish-btn" id="submitBtn">
                    <span class="btn-text">Publish Document</span>
                    <span class="btn-loading" style="display: none;">
                        <svg class="spinner" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                                <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </form>
        </div>
    </main>

    <?php include "footer.php"; ?>

    <script>
        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const filePreview = document.getElementById('filePreview');
        const form = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');

        uploadArea.addEventListener('click', () => fileInput.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            const file = fileInput.files[0];
            if (file) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('File size must be less than 10MB');
                    fileInput.value = '';
                    return;
                }

                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'text/plain'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload PDF, DOC, DOCX, PPT, PPTX, or TXT files.');
                    fileInput.value = '';
                    return;
                }

                uploadArea.style.display = 'none';
                filePreview.style.display = 'block';
                filePreview.querySelector('.file-name').textContent = file.name;
                filePreview.querySelector('.file-size').textContent = formatFileSize(file.size);
            }
        }

        function removeFile() {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            filePreview.style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Dynamic dropdowns
        const levelSelect = document.getElementById('levelSelect');
        const sectorSelect = document.getElementById('sectorSelect');
        const subjectSelect = document.getElementById('subjectSelect');

        levelSelect.addEventListener('change', function() {
            const levelId = this.value;
            sectorSelect.innerHTML = '<option value="">Select Sector</option>';
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            sectorSelect.disabled = true;
            subjectSelect.disabled = true;

            if (levelId) {
                fetch(`upload.php?ajax=sectors&level_id=${levelId}`)
                    .then(response => response.json())
                    .then(sectors => {
                        sectors.forEach(sector => {
                            const option = document.createElement('option');
                            option.value = sector.Sector_id;
                            option.textContent = sector.sector_name;
                            sectorSelect.appendChild(option);
                        });
                        sectorSelect.disabled = false;
                    })
                    .catch(error => console.error('Error loading sectors:', error));
            }
        });

        sectorSelect.addEventListener('change', function() {
            const levelId = levelSelect.value;
            const sectorId = this.value;
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjectSelect.disabled = true;

            if (levelId && sectorId) {
                fetch(`upload.php?ajax=subjects&level_id=${levelId}&sector_id=${sectorId}`)
                    .then(response => response.json())
                    .then(subjects => {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.subject_id;
                            option.textContent = subject.subject_name;
                            subjectSelect.appendChild(option);
                        });
                        subjectSelect.disabled = false;
                    })
                    .catch(error => console.error('Error loading subjects:', error));
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline-flex';
            submitBtn.disabled = true;
        });

        // Auto-hide success messages
        const message = document.querySelector('.message.success');
        if (message) {
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>