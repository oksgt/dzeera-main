const header = document.querySelector("#nav1");
const header2 = document.querySelector("#nav2");
const header3 = document.querySelector("#nav3");
let myList = document.querySelector(".mobile-navbar-nav");

if (!myList) {
    myList = document.querySelector(".mobile-navbar-nav-other");
}

// if(myList){
const items = myList.children;
// }

function modal_test() {
    $("#exampleModalCenter").modal("show");
}

window.addEventListener("scroll", () => {
    if (window.scrollY > 100) {
        header.classList.add("is-scrolled");
        header2.classList.add("is-scrolled");
        header3.classList.add("is-scrolled");

        // if (myList) {
        //     for (let i = 0; i < items.length; i++) {
        //         myList.children[i].firstElementChild.classList.add(
        //             "is-scrolled"
        //         );
        //     }
        // }

        if (myList) {
            for (let i = 0; i < items.length; i++) {
                if (myList.children[i] && myList.children[i].firstElementChild) {
                    myList.children[i].firstElementChild.classList.add("is-scrolled");
                } else {
                    // console.error("Child element not found at index " + i);
                }
            }
        }

        var ulElement = document.getElementById('list-cat');
        var aElements = ulElement.getElementsByTagName('a');
        for (var i = 0; i < aElements.length; i++) {
            aElements[i].classList.add('is-scrolled');
        }

    } else {
        header.classList.remove("is-scrolled");
        header2.classList.remove("is-scrolled");
        header3.classList.remove("is-scrolled");

        // if (myList) {
        //     for (let i = 0; i < items.length; i++) {
        //         myList.children[i].firstElementChild.classList.remove(
        //             "is-scrolled"
        //         );
        //     }
        // }

        if (myList) {
            for (let i = 0; i < items.length; i++) {
                if (myList.children[i] && myList.children[i].firstElementChild) {
                    myList.children[i].firstElementChild.classList.remove("is-scrolled");
                } else {
                    // console.error("Child element not found at index " + i);
                }
            }
        }

        var ulElement = document.getElementById('list-cat');
        var aElements = ulElement.getElementsByTagName('a');
        for (var i = 0; i < aElements.length; i++) {
            aElements[i].classList.remove('is-scrolled');
        }
    }
});

var isMobile = window.innerWidth <= 768;
var slide_new_arrivals_item = 6;
var slide_category_1 = 6;
var slide_category_2 = 6;
var slide_category_3 = 6;

var slide_category_item = 10;
if (isMobile) {
    slide_new_arrivals_item = 2;
    slide_category_1 = 2;
    slide_category_2 = 2;
    slide_category_3 = 2;
    slide_category_item = 6;
}

if (document.querySelector("#slide_new_arrivals")) {
    var splide = new Splide("#slide_new_arrivals", {
        type: "slide",
        perPage: slide_new_arrivals_item,
        rewind: true,
    });

    splide.mount();

    var splide = new Splide("#slide_category", {
        perPage: slide_category_item,
        rewind: true,
    });

    splide.mount();

    var splide = new Splide("#slide_category_1", {
        type: "slide",
        perPage: slide_category_1,
        rewind: true,
    });

    splide.mount();

    var splide = new Splide("#slide_category_2", {
        type: "slide",
        perPage: slide_category_2,
        rewind: true,
    });

    splide.mount();

    var splide = new Splide("#slide_category_3", {
        type: "slide",
        perPage: slide_category_3,
        rewind: true,
    });

    splide.mount();
}
