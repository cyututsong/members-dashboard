jQuery(function($){
    function allImagesLoaded(container){
        const imgs = container.querySelectorAll('img');
        const promises = [];
        imgs.forEach(img => {
            if (img.complete) return;
            promises.push(new Promise(res => {
                img.onload = img.onerror = res;
            }));
        });
        return Promise.all(promises);
    }

    $('#downloadInviteBtn').on('click', function(e){
        e.preventDefault();

        var node = document.getElementById('inviteCard');
        if (!node) {
            console.error('inviteCard element not found');
            alert('Invitation element not found on this page.');
            return;
        }

        if (typeof html2canvas === 'undefined') {
            console.error('html2canvas not loaded');
            alert('Screenshot tool is not loaded. Try again later.');
            return;
        }

        // Wait for inner images to load (important for correct capture)
        allImagesLoaded(node).then(function(){
            // optional small delay to ensure fonts/rendering
            setTimeout(function(){
                html2canvas(node, {
                    scale: 2,         // higher scale = sharper image
                    useCORS: true,    // allow cross-origin images if CORS headers present
                    allowTaint: false,
                    backgroundColor: null
                }).then(function(canvas){
                    // use blob for better memory handling
                    canvas.toBlob(function(blob){
                        var link = document.createElement('a');
                        link.download = 'invitation.png';
                        link.href = URL.createObjectURL(blob);
                        document.body.appendChild(link);
                        link.click();
                        // cleanup
                        setTimeout(function(){
                            URL.revokeObjectURL(link.href);
                            link.remove();
                        }, 1000);
                    }, 'image/png');
                }).catch(function(err){
                    console.error('html2canvas error', err);
                    alert('Failed to generate image. See console for details.');
                });
            }, 100); // tweak if needed
        });
    });


    $('#downloadCapturedBtn').on('click', function(e){
        e.preventDefault();

        var node = document.getElementById('inviteMomentCapturedCard');
        if (!node) {
            console.error('inviteCard element not found');
            alert('Invitation element not found on this page.');
            return;
        }

        if (typeof html2canvas === 'undefined') {
            console.error('html2canvas not loaded');
            alert('Screenshot tool is not loaded. Try again later.');
            return;
        }

        // Wait for inner images to load (important for correct capture)
        allImagesLoaded(node).then(function(){
            // optional small delay to ensure fonts/rendering
            setTimeout(function(){
                html2canvas(node, {
                    scale: 2,         // higher scale = sharper image
                    useCORS: true,    // allow cross-origin images if CORS headers present
                    allowTaint: false,
                    backgroundColor: null
                }).then(function(canvas){
                    // use blob for better memory handling
                    canvas.toBlob(function(blob){
                        var link = document.createElement('a');
                        link.download = 'capturemoments.png';
                        link.href = URL.createObjectURL(blob);
                        document.body.appendChild(link);
                        link.click();
                        // cleanup
                        setTimeout(function(){
                            URL.revokeObjectURL(link.href);
                            link.remove();
                        }, 1000);
                    }, 'image/png');
                }).catch(function(err){
                    console.error('html2canvas error', err);
                    alert('Failed to generate image. See console for details.');
                });
            }, 100); // tweak if needed
        });
    });


});