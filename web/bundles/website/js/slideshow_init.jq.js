$(window).load(function() {
    initSlideshowsAfterPrepare(prepareSlideshows());
});

function prepareSlideshows(){
    $('ul.carousel').wrap('<div class="carousel">').parents('div.carousel').prepend('<a class="prev"></a>').append('<a class="next"></a>');
}

function initSlideshowsAfterPrepare(callback){
    $('ul.carousel').anoSlide(
        {
            items: 1,
            speed: 250,
            prev: 'a.prev',
            next: 'a.next',
            lazy: false,
            auto: 4000,
        });
}