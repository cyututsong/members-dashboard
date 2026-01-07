jQuery(document).ready(function ($) {
    $(document).on('click', '.pagination a.prev-page, .pagination a.next-page', function (e) {
        e.preventDefault();

        var button = $(this);
        var page = button.data('page');
        var folder = button.data('folder');
        var imagesPerPage = button.data('images-per-page');
        var wrapper = button.closest('.gallery-wrapper');

        //console.log("Clicked page:", page, "folder:", folder, "images:", imagesPerPage);

        wrapper.find('.moments-gallery').html('<div class="gf-loading"><span class="spinner"></span> Loading...</div>');

        $.ajax({
            url: ajaxpagination.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'load_images_page',
                page: page,
                folder: folder,
                images_per_page: imagesPerPage,
                nonce: ajaxpagination.nonce,
            },
            success: function (response) {
                console.log(response.data);
                if (response.success) {
                    wrapper.replaceWith(response.data.html); // ✅ replace wrapper content
                } else {
                    wrapper.find('.moments-gallery').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function () {
                wrapper.find('.moments-gallery').html('<p>AJAX error loading images.</p>');
            },
        });
    });


});


document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImg");
  const closeBtn = modal.querySelector(".close");
  const prevBtn = modal.querySelector(".prev");
  const nextBtn = modal.querySelector(".next");

  let currentIndex = 0;
  let isTransitioning = false;

  // Function to get latest images (in case pagination reloads)
  function getImages() {
    return Array.from(document.querySelectorAll(".moments-gallery-item"));
  }

  function openModal(index) {
    const images = getImages();
    currentIndex = index;
    modal.classList.add("active");
    modalImg.src = images[index].src;
  }

  function closeModal() {
    modal.classList.remove("active");
  }

  function changeImage(newIndex) {
    const images = getImages();
    if (isTransitioning || images.length === 0) return;

    isTransitioning = true;
    modalImg.classList.add("fade-out");

    modalImg.addEventListener(
      "transitionend",
      () => {
        currentIndex = (newIndex + images.length) % images.length;
        modalImg.src = images[currentIndex].src;
        modalImg.classList.remove("fade-out");
        isTransitioning = false;
      },
      { once: true }
    );
  }

  // ✅ Event Delegation: Works even after AJAX pagination
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("moments-gallery-item")) {
      const images = getImages();
      const index = images.indexOf(e.target);
      if (index !== -1) openModal(index);
    }
  });

  closeBtn.addEventListener("click", closeModal);
  prevBtn.addEventListener("click", () => changeImage(currentIndex - 1));
  nextBtn.addEventListener("click", () => changeImage(currentIndex + 1));

  // Close modal by clicking outside
  window.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  // Keyboard controls
  document.addEventListener("keydown", (e) => {
    if (!modal.classList.contains("active")) return;
    if (e.key === "ArrowLeft") changeImage(currentIndex - 1);
    if (e.key === "ArrowRight") changeImage(currentIndex + 1);
    if (e.key === "Escape") closeModal();
  });
});
