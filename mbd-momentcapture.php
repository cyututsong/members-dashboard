<?php
// ✅ Shortcode: [moment_capture]
function moment_capture_upload() {

    if (is_admin()) return ''; // Prevent output in dashboard

    ob_start();

    // 🔹 Get event (user ID) and email from URL
    $user_id = isset($_GET['event']) ? intval($_GET['event']) : 0;
    $email   = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

    // 🔹 Validate QR scan
    if ($user_id === 0 || empty($email)) {
        return "<div class='divUploadContainer'>
                    <div class='innerUploadContainer'>
                        <p style='color:red; font-size:18px;'>🙏 Please scan the QR Code again.</p>
                    </div>
                </div>   
        ";
    }

    // 🔹 Verify user exists and email matches
    $user = get_userdata($user_id);
    if (!$user || strtolower($user->user_email) !== strtolower($email)) {
        return "<div class='divUploadContainer'>
                    <div class='innerUploadContainer'>
                        <p style='color:red; font-size:18px;'>🙏 Please scan the QR Code again.</p>
                    </div>
                </div>   
        ";
    }

    $profile_picture = get_user_meta( $user_id, 'profile_picture', true );

    ?>
   <style>
   .progress-container {
       width: 100%;
       background-color: #f0f0f0;
       border-radius: 25px;
       margin: 20px 0;
       overflow: hidden;
       box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
   }
   
   .progress-bar {
       width: 0%;
       height: 30px;
       background: linear-gradient(45deg, #4CAF50, #45a049);
       border-radius: 25px;
       transition: width 0.3s ease-in-out;
       position: relative;
       overflow: hidden;
   }
   
   .progress-bar::after {
       content: '';
       position: absolute;
       top: 0;
       left: 0;
       right: 0;
       bottom: 0;
       background: linear-gradient(
           90deg,
           rgba(255,255,255,0.1) 25%,
           rgba(255,255,255,0.3) 50%,
           rgba(255,255,255,0.1) 75%
       );
       animation: shimmer 2s infinite;
       transform: skewX(-20deg);
   }
   
   @keyframes shimmer {
       0% { transform: translateX(-100%) skewX(-20deg); }
       100% { transform: translateX(200%) skewX(-20deg); }
   }
   
   .progress-text {
       text-align: center;
       margin-top: 5px;
       font-size: 16px;
       color: #333;
       font-weight: bold;
   }
   
   .file-status {
       text-align: center;
       margin-top: 5px;
       font-size: 14px;
       color: #666;
   }
   </style>

   <div class="moment-capture-wrapper">
        <form id="moment-capture-form" method="post" enctype="multipart/form-data">
            <input type="file" name="moment_images[]" id="moment_images" multiple accept="image/*" style="display:none;" />

            <div class="divUploadContainer">
                <div class="innerUploadContainer">
                    <img class="profile" src="<?php echo esc_url( $profile_picture ); ?>" alt="Profile">

                    <p id="moment-upload-message" class="simpleMessage">Please share your captured moments during our wedding. Thank you ❤️</p>

                    <!-- Preview + Message -->
                    <div id="moment-upload-preview"></div>
                    <p id="add-more-image" style="display:none; color:blue; cursor:pointer; text-decoration:underline; margin-top:10px;">➕ Add more image</p>
                    <button type="button" id="moment-upload-btn">Select Images</button>
                    <input type="submit" id="moment-submit-btn" name="submit_moment_images" value="Upload" style="display:none;" />
                    
                    <!-- Progress Bar -->
                    <div id="moment-upload-progress" style="display:none; margin-top:15px; width:100%;">
                        <div class="progress-container">
                            <div class="progress-bar" id="upload-progress-bar"></div>
                        </div>
                        <div class="progress-text" id="upload-progress-text">0%</div>
                        <div class="file-status" id="upload-file-status"></div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function(){
        const fileInput   = document.getElementById("moment_images");
        const selectBtn   = document.getElementById("moment-upload-btn");
        const uploadBtn   = document.getElementById("moment-submit-btn");
        const addMoreLink = document.getElementById("add-more-image");
        const preview     = document.getElementById("moment-upload-preview");
        const form        = document.getElementById("moment-capture-form");
        const messageBox  = document.getElementById("moment-upload-message");
        const progressContainer = document.getElementById("moment-upload-progress");
        const progressBar = document.getElementById("upload-progress-bar");
        const progressText = document.getElementById("upload-progress-text");
        const fileStatus = document.getElementById("upload-file-status");

        let allFiles = [];

        // Open file picker
        selectBtn.addEventListener("click", () => fileInput.click());

        // Add images to preview
        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                Array.from(fileInput.files).forEach(file => {
                    allFiles.push(file);

                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.style.maxWidth = "150px";
                        img.style.margin = "5px";
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });

                // Show Upload + Add more, hide Select
                selectBtn.style.display = "none";
                uploadBtn.style.display = "inline-block";
                addMoreLink.style.display = "block";

                fileInput.value = ""; // reset
            }
        });

        // Add more images (reopen file selector)
        addMoreLink.addEventListener("click", () => {
            fileInput.click();
        });

        // Handle upload
        form.addEventListener("submit", function(e){
            if (allFiles.length > 0) {
                e.preventDefault();

                // 🔹 Show progress bar
                progressContainer.style.display = "block";
                addMoreLink.style.display = "none";
                uploadBtn.style.display = "none";
                messageBox.style.display = "none";
                selectBtn.style.display = "none";
                preview.style.display = "none";
                
                uploadBtn.disabled = true;
                selectBtn.disabled = true;
                addMoreLink.style.pointerEvents = "none";
                
                // Initialize progress
                progressBar.style.width = "0%";
                progressText.textContent = "0%";
                fileStatus.textContent = `Uploading 0 of ${allFiles.length} files...`;

                const formData = new FormData(form);
                allFiles.forEach(file => {
                    formData.append("moment_images[]", file, file.name);
                });
                
                formData.append("submit_moment_images", "1");

                // Create XMLHttpRequest for progress tracking
                const xhr = new XMLHttpRequest();
                
                // Track upload progress
                xhr.upload.addEventListener("progress", function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + "%";
                        progressText.textContent = percentComplete + "%";
                        
                        // Estimate which file is being uploaded
                        const estimatedFileIndex = Math.floor((e.loaded / e.total) * allFiles.length);
                        const currentFile = Math.min(estimatedFileIndex + 1, allFiles.length);
                        fileStatus.textContent = `Uploading ${currentFile} of ${allFiles.length} files...`;
                    }
                });

                // Handle completion
                xhr.addEventListener("load", function() {
                    progressBar.style.width = "100%";
                    progressText.textContent = "100%";
                    fileStatus.textContent = `Upload complete! ${allFiles.length} files uploaded.`;
                    
                    setTimeout(() => {
                        // Hide progress
                        progressContainer.style.display = "none";
                        
                        // Reset and show success message
                        allFiles = [];
                        preview.innerHTML = "";
                        messageBox.innerHTML = "🎉 Thanks for sharing your moments with us! Feel free to upload more anytime.";
                        messageBox.style.color = "green";
                        messageBox.style.display = "block";

                        // Reset buttons
                        selectBtn.style.display = "inline-block";
                        uploadBtn.style.display = "none";
                        addMoreLink.style.display = "none";
                        preview.style.display = "block";

                        uploadBtn.disabled = false;
                        selectBtn.disabled = false;
                        addMoreLink.style.pointerEvents = "auto";
                    }, 1000);
                });

                xhr.addEventListener("error", function() {
                    progressContainer.style.display = "none";
                    messageBox.innerHTML = "❌ Upload failed. Please try again.";
                    messageBox.style.color = "red";
                    messageBox.style.display = "block";
                    
                    // Reset buttons
                    selectBtn.style.display = "inline-block";
                    uploadBtn.style.display = "none";
                    addMoreLink.style.display = "none";
                    
                    uploadBtn.disabled = false;
                    selectBtn.disabled = false;
                    addMoreLink.style.pointerEvents = "auto";
                });

                // Open and send request
                xhr.open("POST", window.location.href, true);
                xhr.send(formData);
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('moment_capture', 'moment_capture_upload');


// ✅ Handle upload and save images
function moment_capture_handle_upload() {
    if (isset($_POST['submit_moment_images']) && !empty($_FILES['moment_images'])) {
        $upload_dir = wp_upload_dir();

        // 🔹 Get event (user ID) from URL
        $event_id = isset($_GET['event']) ? intval($_GET['event']) : 0;
        if ($event_id === 0) {
            echo "<p style='color:red; font-size:18px;'>🙏 Please scan the QR Code again.</p>";
            exit;
        }

        // 🔹 Target path per event
        $custom_subdir = '/moment_capture/moments_' . $event_id;
        $target_dir    = $upload_dir['basedir'] . $custom_subdir;

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $files = $_FILES['moment_images'];
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                $filename    = sanitize_file_name($files['name'][$key]);
                $filename    = wp_unique_filename($target_dir, $filename);
                $tmp_name    = $files['tmp_name'][$key];
                $target_file = $target_dir . '/' . $filename;

                move_uploaded_file($tmp_name, $target_file);
            }
        }

    }
}
add_action('wp', 'moment_capture_handle_upload');


function moments_pagination_script() {
    global $post;

    if ( is_page(1367) ) {
        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'moment-pagination',
            plugin_dir_url( __FILE__ ) . 'assets/js/moments-pagination.js',
            [ 'jquery' ],
            '1.0',
            true
        );

        wp_localize_script('moment-pagination', 'ajaxpagination', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('ajax-pagination-nonce')
        ));
    }


}
add_action('wp_enqueue_scripts', 'moments_pagination_script');