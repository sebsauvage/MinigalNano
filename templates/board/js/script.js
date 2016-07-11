/**
 * LazyLoading
 */
$(document).ready(function(){
    // var bLazy = new Blazy;

    $('.grid__element__a__pic').click(function(e){
        e.preventDefault();
        var img_url = $(this).attr('href');

    });
});

function toggle_light_box() {

}

function load_light_box(pic_url) {
    var img = $('#js_lightbox__body__img');
    
}