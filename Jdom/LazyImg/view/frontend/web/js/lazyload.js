//(Chrome / Firefox / Opera / Edge)
function newdefer() {

    let lazyloadImages;
    if ("IntersectionObserver" in window) {
        lazyloadImages = document.querySelectorAll("img");
        let imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    let image = entry.target;
                    if (image.dataset.origen) {
                        image.src = image.dataset.origen;
                        imageObserver.unobserve(image);
                    }
                }
            });
        });
        lazyloadImages.forEach(function(image) {
            imageObserver.observe(image);
        });
    } else {
        let lazyloadThrottleTimeout;
        lazyloadImages = document.querySelectorAll("img");
        function lazyload() {
            if (lazyloadThrottleTimeout) {
                clearTimeout(lazyloadThrottleTimeout);
            }
            lazyloadThrottleTimeout = setTimeout(function() {
                let scrollTop = window.pageYOffset;
                lazyloadImages.forEach(function(img) {
                    if (img.offsetTop < (window.innerHeight + scrollTop)) {
                        img.src = img.dataset.origen;
                    }
                });
                if (lazyloadImages.length == 0) {
                    document.removeEventListener("scroll", lazyload);
                    window.removeEventListener("resize", lazyload);
                    window.removeEventListener("orientationChange", lazyload);
                }
            }, 10);
        }
        document.addEventListener("scroll", lazyload);
        window.addEventListener("resize", lazyload);
        window.addEventListener("orientationChange", lazyload);
    }
}
document.addEventListener("DOMContentLoaded", newdefer);
