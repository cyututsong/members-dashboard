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

    // Main capture function (works even if inside inactive tab)
    function captureFromHiddenTab() {

        const node = document.getElementById('inviteMomentCapturedCard');
        const resultImg = document.querySelector('.inviteMomentCapturedCardResult');

        if (!node) {
            console.error('inviteMomentCapturedCard element not found');
            return;
        }

        if (!resultImg) {
            console.error('Result image (.inviteMomentCapturedCardResult) not found');
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

        // Wait for images + fonts
        Promise.all([
            allImagesLoaded(clone),
            document.fonts.ready,
            new Promise(r => setTimeout(r, 300))
        ]).then(() => {

            html2canvas(clone, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#fff'
            })
            .then(canvas => {

                resultImg.src = canvas.toDataURL('image/png');
                resultImg.style.display = 'block';
                resultImg.classList.add('is-loaded');

                console.log('Image captured successfully');

                clone.remove();
            })
            .catch(err => {
                console.error('html2canvas error:', err);
                clone.remove();
            });

        });
    }

    // Run automatically on page load
    captureFromHiddenTab();

});
