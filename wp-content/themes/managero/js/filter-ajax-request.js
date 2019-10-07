jQuery(document).ready(function ($) {


    var selectedCountry = [];

    jQuery('.input-fake').toggle(
        function () {
            var target = jQuery(this).attr('data-target')

            console.log('mancare si jumari');
            if(!jQuery('#' + target).is(':hidden')){
                jQuery('#' + target).hide();

            }else{
                jQuery('#' + target).show();
            }
            //jQuery('#' + target).show();

            jQuery(".judet").click(function () {

                var niceSelectedCountry = [];
                var n = jQuery(".judet:checked").length;
                if (n > 0) {
                    jQuery(".judet:checked").each(function () {

                        var value = $(this).val()
                        var niceValue = '<div class="filter-value"><span class="filter-remove" choice="' + value + '"><i class="far fa-times-circle"></i></span>' + value + '</div>'

                        selectedCountry.push(value);
                        niceSelectedCountry.push(niceValue);
                    });
                }
                //  alert(selectedCountry);
                jQuery('.choice-locatie .rezultat').html(niceSelectedCountry);
                // console.log(jQuery('.choice-locatie .rezultat').html(niceSelectedCountry));


                jQuery('.filter-remove').click(function () {

                    var choice = jQuery(this).attr('choice');

                    jQuery('.judet[value="' + choice + '"]').prop("checked", false);

                    jQuery(this).parent().remove();

                    showJobs();

                    //   console.log('judete');
                    if ((jQuery('.judet:checked').length > 0)) {
                        jQuery('.choice-locatie').css('display', 'block');
                    }
                    else {
                        jQuery('.choice-locatie').css('display', 'none');
                    }

                    if(jQuery('.choice-locatie .rezultat').children('div').length > 0  === false && jQuery('.choice-nivel .rezultat').children('div').length > 0  === false && jQuery('.choice-domeniu .rezultat').children('div').length > 0  === false && jQuery('.choice-salariu .rezultat').children('div').length > 0  === false){
                        jQuery('.filter-wrapper-box').css('display', 'none');
                    }
                });

                setTimeout(function () {
                    //  console.log('test');
                    if(jQuery('.choice-locatie .rezultat').children('div').length > 0  === false && jQuery('.choice-nivel .rezultat').children('div').length > 0  === false && jQuery('.choice-domeniu .rezultat').children('div').length > 0  === false && jQuery('.choice-salariu .rezultat').children('div').length > 0  === false){
                        jQuery('.filter-wrapper-box').css('display', 'none');
                        //      console.log('here j');
                    }
                }, 2000);

            });


            jQuery(".domeniu").click(function () {

                var selectedDomeniu = [];
                var niceSelectedDomeniu = [];

                var n = jQuery(".domeniu:checked").length;
                if (n > 0) {
                    jQuery(".domeniu:checked").each(function () {


                        var value = $(this).val();
                        var niceValue = '<div class="filter-value"><span class="filter-remove" choice="' + value + '"><i class="far fa-times-circle"></i></span>' + value + '</div>'

                        selectedDomeniu.push(value);
                        niceSelectedDomeniu.push(niceValue);
                    });
                }
                //  alert(selectedCountry);
                jQuery('.choice-domeniu .rezultat').html(niceSelectedDomeniu);


                jQuery('.filter-remove').click(function () {

                    var choice = jQuery(this).attr('choice');

                    jQuery('.domeniu[value="' + choice + '"]').prop("checked", false);

                    jQuery(this).parent().remove();

                    showJobs();
                    //     console.log('domenii');
                    if ((jQuery('.domeniu:checked').length > 0)) {
                        jQuery('.choice-domeniu').css('display', 'block');
                    }
                    else {
                        jQuery('.choice-domeniu').css('display', 'none');
                    }
                    if(jQuery('.choice-locatie .rezultat').children('div').length > 0  === false && jQuery('.choice-nivel .rezultat').children('div').length > 0  === false && jQuery('.choice-domeniu .rezultat').children('div').length > 0  === false && jQuery('.choice-salariu .rezultat').children('div').length > 0  === false){
                        jQuery('.filter-wrapper-box').css('display', 'none');
                    }

                });

            });


            jQuery(".nivel").click(function () {

                var selectedNivel = [];
                var niceSelectedNivel = [];

                var n = jQuery(".nivel:checked").length;
                if (n > 0) {
                    jQuery(".nivel:checked").each(function () {

                        var value = $(this).val();
                        var niceValue = '<div class="filter-value"><span class="filter-remove" choice="' + value + '"><i class="far fa-times-circle"></i></span>' + value + '</div>'
                        //    console.log(value);

                        selectedNivel.push(value);
                        niceSelectedNivel.push(niceValue);
                    });
                }
                //  alert(selectedCountry);
                jQuery('.choice-nivel .rezultat').html(niceSelectedNivel);


                jQuery('.filter-remove').click(function () {

                    var choice = jQuery(this).attr('choice');

                    jQuery('.nivel[value="' + choice + '"]').prop("checked", false);

                    jQuery(this).parent().remove();

                    showJobs();
                    if ((jQuery('.nivel:checked').length > 0)) {
                        jQuery('.choice-nivel').css('display', 'block');
                    }
                    else {
                        jQuery('.choice-nivel').css('display', 'none');
                    }
                    if(jQuery('.choice-locatie .rezultat').children('div').length > 0  === false && jQuery('.choice-nivel .rezultat').children('div').length > 0  === false && jQuery('.choice-domeniu .rezultat').children('div').length > 0  === false && jQuery('.choice-salariu .rezultat').children('div').length > 0  === false){
                        jQuery('.filter-wrapper-box').css('display', 'none');
                    }


                });

            });

        },
        function () {
            var target = jQuery(this).attr('data-target')
            //  jQuery('#' + target).hide();
            if(!jQuery('#' + target).is(':hidden')){
                jQuery('#' + target).hide();

            }else{
                jQuery('#' + target).show();
            }
        }
    );


    $('.salariu').on("input", function () {
        var salariu = this.value;
        var inputSalariu = '<div class="filter-value" style="min-width: 100px"><span class="filter-clear" ><i class="far fa-times-circle"></i></span>>' + salariu + '&euro;</div>'
        jQuery('.choice-salariu .rezultat').html(inputSalariu);

        jQuery('.filter-clear').click(function () {

            jQuery('.salariu').val('');

            jQuery(this).parent().remove();

            showJobs();
            //  console.log('salariu');
            if ((jQuery('.salariu:checked').length > 0)) {
                jQuery('.choice-salariu').css('display', 'block');
            }
            else {
                jQuery('.choice-salariu').css('display', 'none');
            }
            if(jQuery('.choice-locatie .rezultat').children('div').length > 0  === false && jQuery('.choice-nivel .rezultat').children('div').length > 0  === false && jQuery('.choice-domeniu .rezultat').children('div').length > 0  === false && jQuery('.choice-salariu .rezultat').children('div').length > 0  === false){
                jQuery('.filter-wrapper-box').css('display', 'none');
            }


        });

    });


    jQuery('.close').click(function () {

        jQuery(this).parent().hide();


    });


    // functie afisare joburi

    function showJobs() {
        //  console.log('Trigger AFISARE JOBURI');
        var judete = [];
        var domenii = [];
        var niveluri = [];
        var salariu = '';

        // if ((jQuery('.domeniu:checked').length > 0) == false && (jQuery('.judet:checked').length > 0) == false && (jQuery('salariu').not(":empty") == false && (jQuery('.nivel:checked').length > 0)) == false) {
        //     jQuery('.filter-wrapper-box').css('display', 'none');
        // }

        //domenii
        var n = jQuery(".domeniu:checked").length;
        if (n > 0) {
            jQuery(".domeniu:checked").each(function () {

                var value = $(this).val();
                domenii.push(value);
            });
        }
        // nivel
        var b = jQuery(".nivel:checked").length;
        if (b > 0) {
            jQuery(".nivel:checked").each(function () {

                var value = $(this).val();
                niveluri.push(value);
            });
        }
        // judete
        var c = jQuery(".judet:checked").length;
        if (c > 0) {
            jQuery(".judet:checked").each(function () {

                var value = $(this).val()
                judete.push(value);
            });
        }


        salariu = jQuery(".salariu").val();

        //  alert(selectedCountry);

        var filtre = {
            'judete': judete,
            'niveluri': niveluri,
            'domenii': domenii,
            'salariu': salariu
        };
        var urlLoadGif = 'http://managero.ro/wp-content/themes/managero/js/Spinner-2.2s-200px.gif';
        var image = '<img src="' + urlLoadGif + '" class="loading"  />';

        $('#filter-results').html(image);
        // This does the ajax request
        $.ajax({
            type: "post",
            url: filter_ajax_obj.ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            //   dataType : "json",
            data: {
                'action': 'filtre_jobs',
                'filtre': filtre
            },
            success: function (data) {
                // This outputs the result of the ajax request\
                //  console.log(data);
                $('#filter-results').html(data);

            },
            error: function (errorThrown) {
                //     console.log(errorThrown);
            }
        });

    }

    // end functie afisare joburi


    // apply filters si call afisare

    jQuery('.filter-apply').click(function () {
        showJobs();
    });
// end filtre


    jQuery('#filter-form').submit(function (event) {
        event.preventDefault();
        showJobs();
    });

});


var mouse_is_inside = false;

(function ($) {

    $(document).ready(function () {

        $('.triger-div').hover(function () {
            mouse_is_inside = true;
            // console.log(mouse_is_inside);
        }, function () {
            mouse_is_inside = false;
            //   console.log(mouse_is_inside);
        });


        $("body").click(function () {
            if (!mouse_is_inside) {
                $('.triger-div').hide();
            }
        });

    });

})(jQuery);


///locatie
jQuery(document).on('change', '.judet', function () {


    if ((jQuery('.judet:checked').length > 0)) {
        jQuery('.choice-locatie').css('display', 'block');
        jQuery('.filter-wrapper-box').css('display', 'block');
    }
    else {
        jQuery('.choice-locatie').css('display', 'none');
    }

});
///domeniu
jQuery(document).on('change', '.domeniu', function () {


    if ((jQuery('.domeniu:checked').length > 0)) {
        jQuery('.choice-domeniu').css('display', 'block');
        jQuery('.filter-wrapper-box').css('display', 'block');
    }
    else {
        jQuery('.choice-domeniu').css('display', 'none');

    }


});
//nivel ierarhic
jQuery(document).on('change', '.nivel', function () {


    if ((jQuery('.nivel:checked').length > 0)) {
        jQuery('.choice-nivel').css('display', 'block');
        jQuery('.filter-wrapper-box').css('display', 'block');
    }
    else if ((jQuery('.nivel:checked').length > 0) === false) {
        jQuery('.choice-nivel').css('display', 'none');
    }
});
//salariu
jQuery(document).on('change', '.salariu', function () {

    if ((jQuery('.salariu').not(":empty"))) {
        jQuery('.choice-salariu').css('display', 'block');
        jQuery('.filter-wrapper-box').css('display', 'block');
    }
    else {
        jQuery('.choice-nivel').css('display', 'none');

    }


});

//
// jQuery(document).on('change','.salariu', function(){
//     if (jQuery('.domeniu:checked').length > 0 === false && jQuery('.judet:checked').length > 0 ===false && jQuery('salariu').not(":empty") === false && jQuery('.nivel:checked').length > 0 === false) {
//         jQuery('.filter-wrapper-box').css('display', 'none');
//     }
// } );
// jQuery(document).on('change','.nivel', function(){
//     if (jQuery('.domeniu:checked').length > 0 === false && jQuery('.judet:checked').length > 0 ===false && jQuery('salariu').not(":empty") === false && jQuery('.nivel:checked').length > 0 === false) {
//         jQuery('.filter-wrapper-box').css('display', 'none');
//     }
// } );
// jQuery(document).on('change','.domeniu', function(){
//     if (jQuery('.domeniu:checked').length > 0 === false && jQuery('.judet:checked').length > 0 ===false && jQuery('salariu').not(":empty") === false && jQuery('.nivel:checked').length > 0 === false) {
//         jQuery('.filter-wrapper-box').css('display', 'none');
//     }
// } );
//
// jQuery(window).change(function(){
//     if (jQuery('.domeniu:checked').length > 0 === false && jQuery('.judet:checked').length > 0 ===false && jQuery('salariu').not(":empty") === false && jQuery('.nivel:checked').length > 0 === false) {
//         jQuery('.filter-wrapper-box').css('display', 'none');
//     }
// });




