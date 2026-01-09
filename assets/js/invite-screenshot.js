jQuery(function ($) {

    // Helper: wait for images inside a container
    function allImagesLoaded(container) {
        const imgs = container.querySelectorAll('img');
        const promises = [];

        imgs.forEach(img => {
            if (img.complete) return;
            promises.push(new Promise(resolve => {
                img.onload = img.onerror = resolve;
            }));
        });

        return Promise.all(promises);
    }

    // Helper: wait for stylesheets to load
    function allStylesheetsLoaded() {
        const links = document.querySelectorAll('link[rel="stylesheet"]');
        const promises = [];

        links.forEach(link => {
            if (link.sheet) return; // Already loaded
            promises.push(new Promise((resolve) => {
                link.onload = link.onerror = resolve;
            }));
        });

        return Promise.all(promises);
    }

    // Helper: copy computed styles to cloned element
    function copyComputedStyles(original, cloned) {
        const originalElements = original.querySelectorAll('*');
        const clonedElements = cloned.querySelectorAll('*');

        clonedElements.forEach((element, index) => {
            if (originalElements[index]) {
                const styles = window.getComputedStyle(originalElements[index]);
                for (let i = 0; i < styles.length; i++) {
                    const property = styles[i];
                    element.style[property] = styles.getPropertyValue(property);
                }
            }
        });

        // Also apply styles to the root cloned element
        const rootStyles = window.getComputedStyle(original);
        for (let i = 0; i < rootStyles.length; i++) {
            const property = rootStyles[i];
            cloned.style[property] = rootStyles.getPropertyValue(property);
        }
    }

    // Main capture function (works for any element)
    function captureElement(node, resultImg) {
        if (!node) {
            console.error('Invitation card element not found');
            return;
        }

        if (!resultImg) {
            console.error('Result image element not found');
            return;
        }

        if (typeof html2canvas === 'undefined') {
            console.error('html2canvas not loaded');
            return;
        }

        // Clone the hidden tab content
        const clone = node.cloneNode(true);

        clone.style.display = 'block';
        clone.style.position = 'absolute';
        clone.style.left = '-9999px';
        clone.style.top = '0';
        clone.style.visibility = 'visible';
        clone.style.opacity = '1';
        clone.style.transform = 'none';

        document.body.appendChild(clone);

        // Wait for stylesheets, images + fonts
        Promise.all([
            allStylesheetsLoaded(),
            allImagesLoaded(clone),
            document.fonts.ready,
            new Promise(r => setTimeout(r, 500))
        ]).then(() => {

            // Copy all computed styles from original to cloned element
            copyComputedStyles(node, clone);

            html2canvas(clone, {
                scale: 1,
                useCORS: false,
                allowTaint: true,
                backgroundColor: '#fff',
                logging: true,
                imageTimeout: 0
            })
            .then(canvas => {

                resultImg.src = canvas.toDataURL('image/png');
                resultImg.style.display = 'block';
                resultImg.classList.add('is-loaded');

                console.log('Image captured successfully');

                clone.remove();
                node.style.display = 'none';
            })
            .catch(err => {
                console.error('html2canvas error:', err);
                console.log('Attempting to capture with fallback settings...');
                
                // Fallback: try again with different settings
                html2canvas(clone, {
                    scale: 1,
                    allowTaint: true,
                    backgroundColor: '#fff',
                    useCORS: false,
                    proxy: null
                }).then(canvas => {
                    resultImg.src = canvas.toDataURL('image/png');
                    resultImg.style.display = 'block';
                    resultImg.classList.add('is-loaded');
                    console.log('Image captured with fallback');
                    clone.remove();
                    node.style.display = 'none';
                }).catch(fallbackErr => {
                    console.error('Fallback html2canvas error:', fallbackErr);
                    clone.remove();
                });
            });
        });
    }

    // Capture both elements on page load
    function captureFromHiddenTab() {
        const node = document.getElementById('inviteMomentCapturedCard');
        const node2 = document.getElementById('inviteCard');
        const resultImg = document.querySelector('.inviteMomentCapturedCardResult');
        const resultImg2 = document.querySelector('.invitationContainerResult');

        if (node && resultImg) {
            captureElement(node, resultImg);
        }

        if (node2 && resultImg2) {
            captureElement(node2, resultImg2);
        }
    }

    // Run automatically on page load
    captureFromHiddenTab();

    // Handle download button click - Wedding Invitation
    const downloadInviteBtn = document.querySelector('#downloadInviteBtn');
    if (downloadInviteBtn) {
        downloadInviteBtn.addEventListener('click', function() {
            const resultImg = document.querySelector('.invitationContainerResult');
            
            if (!resultImg || !resultImg.src) {
                console.error('No captured image available to download');
                return;
            }
            
            const link = document.createElement('a');
            link.href = resultImg.src;
            link.download = 'wedding-invitation.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);     
            console.log('Wedding invitation downloaded successfully');
        });
    }

    // Handle download button clicks - Captured Moments
    const downloadBtn = document.querySelector('#downloadCapturedBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const resultImg = document.querySelector('.inviteMomentCapturedCardResult');    
            
            if (!resultImg || !resultImg.src) {
                console.error('No captured image available to download');
                return;
            }    

            const link = document.createElement('a');
            link.href = resultImg.src;
            link.download = 'captured-moments.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);     
            console.log('Captured moments downloaded successfully');
        });
    }

});