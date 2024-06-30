import { sub_mainFolder, mainFolderlength, second_sub_mainFolder, selectedValue, imagesize } from "./myjava.js";
import PhotoSwipeLightbox from '/sites/kahkeshaneketab/PhotoSwipe/demo-docs-website/static/photoswipe/photoswipe-lightbox.esm.js';

export function yes(i) {
    console.log("Thats it");
    console.log(sub_mainFolder[selectedValue].length);

    const lightbox = new PhotoSwipeLightbox({
        showHideAnimationType: 'none',
        pswpModule: () => import('/sites/kahkeshaneketab/PhotoSwipe/demo-docs-website/static/photoswipe/photoswipe.esm.js'),
        preload: [1, 2],
    });

    lightbox.addFilter('numItems', (numItems) => {
        return second_sub_mainFolder[selectedValue].length - 1; // تعداد کل تصاویر بااصلاحات
    });

    lightbox.addFilter('itemData', (itemData, index) => {
        var adjustedIndex = calculateIndex(index); // محاسبه شاخص با توجه به شاخص فعلی

        var imagePath = "Sort/Library/Majles/" + sub_mainFolder[selectedValue] + '/' + second_sub_mainFolder[selectedValue][adjustedIndex];
        var image = imagesize[selectedValue][adjustedIndex];

        return {
            src: imagePath,
            width: image.width,
            height: image.height
        };
    });

    lightbox.init();
    lightbox.loadAndOpen(i);

    function calculateIndex(index) {
        // محاسبه شاخص با توجه به شاخص فعلی و تعداد کل تصاویر
        var totalItems = second_sub_mainFolder[selectedValue].length;
        var adjustedIndex = index % totalItems;
        if (adjustedIndex < 0) {
            adjustedIndex = totalItems + adjustedIndex; // اگر شاخص منفی باشد، به صورت چرخشی به انتها بازگردد
        }
        return adjustedIndex;
    }
}
