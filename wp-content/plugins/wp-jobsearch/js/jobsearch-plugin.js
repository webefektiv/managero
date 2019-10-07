var $ = jQuery;
$(document).ready(function () {

    if (jQuery('.fancybox').length > 0) {
        //*** Function FancyBox
        jQuery(".fancybox").fancybox({
            openEffect: 'elastic',
            closeEffect: 'elastic',
        });
    }

    if (jQuery('.fancybox-video').length > 0) {
        //*** Function FancyBox
        jQuery('.fancybox-video').fancybox({
            maxWidth: 800,
            maxHeight: 600,
            fitToView: false,
            width: '70%',
            height: '70%',
            autoSize: false,
            closeClick: false,
            openEffect: 'none',
            closeEffect: 'none'
        });

    }

    if (jQuery('.jobsearch_progressbar1').length > 0) {
        jQuery('.jobsearch_progressbar1').progressBar({
            percentage: false,
            backgroundColor: "#dbdbdb",
            barColor: jobsearch_plugin_vars.careerfy_theme_color,
            animation: true,
            height: "6",
        });
    }

    // selectize
    jQuery('.selectize-select').selectize({
        //allowEmptyOption: true,
        plugins: ['remove_button'],
    });

    jQuery('.sort-records-per-page').selectize({
        allowEmptyOption: true,
    });

});

(function ($) {
    $.fn.jobsearch_seliz_req_field_loop = function (callback, thisArg) {
        var me = this;
        return this.each(function (index, element) {
            return callback.call(thisArg || element, element, index, me);
        });
    };
})(jQuery);

function jobsearch_validate_seliz_req_form(that) {
    var req_class = 'selectize-req-field',
            _this_form = jQuery(that),
            form_validity = 'valid';
    var errors_counter = 1;
    _this_form.find('select.' + req_class).jobsearch_seliz_req_field_loop(function (element, index, set) {
        var ret_err = '0';
        if (jQuery(element).val() == '') {
            form_validity = 'invalid';
            ret_err = '1';
        } else {
            jQuery(element).parents('.jobsearch-profile-select').css({"border": "none"});
        }

        if (ret_err == '1') {
            jQuery(element).parents('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
            var animate_to = jQuery(element).parents('.jobsearch-profile-select');

            if (errors_counter == 1) {
                jQuery('html, body').animate({scrollTop: animate_to.offset().top - 70}, 1000);
            }

            errors_counter++;
        }
    });
    if (form_validity == 'valid') {
        return true;
    } else {
        return false;
    }
}

function jobsearch_validate_cprofile_req_form(that) {
    var req_class = 'jobsearch-cpreq-field',
            _this_form = jQuery(that),
            form_validity = 'valid';
    var errors_counter = 1;
    _this_form.find('.' + req_class).jobsearch_seliz_req_field_loop(function (element, index, set) {
        var ret_err = '0';
        if (jQuery(element).val() == '') {
            form_validity = 'invalid';
            ret_err = '1';
        } else {
            jQuery(element).css({"border": "1px solid #eceeef"});
        }

        if (ret_err == '1') {
            jQuery(element).css({"border": "1px solid #ff0000"});
            var animate_to = jQuery(element);

            if (errors_counter == 1) {
                jQuery('html, body').animate({scrollTop: animate_to.offset().top - 70}, 1000);
            }

            errors_counter++;
        }
    });
    if (form_validity == 'valid') {
        return true;
    } else {
        return false;
    }
}

var jobsearch_custm_getJSON = function (url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function () {
        var status = xhr.status;
        if (status === 200) {
            callback(null, xhr.response);
        } else {
            callback(status, xhr.response);
        }
    };
    xhr.send();
};

jQuery(document).on('submit', 'form#employer-profilesetings-form', function () {
    var fields_1 = jobsearch_validate_cprofile_req_form(jQuery(this));
    if (!fields_1) {
        return false;
    }
    var fields_2 = jobsearch_validate_seliz_req_form(jQuery(this));
    if (!fields_2) {
        return false;
    }
});

jQuery(document).on('submit', 'form#candidate-profilesetings-form', function () {
    var fields_1 = jobsearch_validate_cprofile_req_form(jQuery(this));
    if (!fields_1) {
        return false;
    }
    var fields_2 = jobsearch_validate_seliz_req_form(jQuery(this));
    if (!fields_2) {
        return false;
    }
});

jQuery(function () {
    jQuery('.jobsearch-tooltipcon').tooltip();
});

jQuery(document).on('click', '.jobsearch-activcode-popupbtn', function () {
    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
    jQuery('body').removeClass('jobsearch-modal-active');
    jobsearch_modal_popup_open('JobSearchModalAccountActivationForm');
});

jQuery(document).on('click', '.user-activeacc-submit-btn', function (e) {
    e.preventDefault();
    var this_form = jQuery('#jobsearch_uaccont_aprov_form');
    var this_loader = this_form.find('.loader-box');
    var this_msg_con = this_form.find('.message-box');

    var activ_code = this_form.find('input[name="activ_code"]');
    var user_email = this_form.find('input[name="user_email"]');

    var error = 0;
    if (activ_code.val() == '') {
        error = 1;
        activ_code.css({"border": "1px solid #ff0000"});
    } else {
        activ_code.css({"border": "1px solid #d3dade"});
    }

    if (error == 0) {

        this_msg_con.hide();
        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                active_code: activ_code.val(),
                user_email: user_email.val(),
                action: 'jobsearch_activememb_accont_by_activation_url',
            },
            dataType: "json"
        });

        request.done(function (response) {
            var msg_before = '';
            var msg_after = '';
            if (typeof response.error !== 'undefined') {
                if (response.error == '1') {
                    msg_before = '<div class="alert alert-danger"><i class="fa fa-times"></i> ';
                    msg_after = '</div>';
                } else if (response.error == '0') {
                    msg_before = '<div class="alert alert-success"><i class="fa fa-check"></i> ';
                    msg_after = '</div>';
                }
            }
            if (typeof response.msg !== 'undefined') {
                this_msg_con.html(msg_before + response.msg + msg_after);
                this_msg_con.slideDown();
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    this_form.find('ul.email-fields-list').slideUp();
                }
            } else {
                this_msg_con.html(jobsearch_plugin_vars.error_msg);
            }
            this_loader.html('');

        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html(jobsearch_plugin_vars.error_msg);
        });
    }
});

jQuery(document).on('click', '.jobsearch-candidatesh-opopupbtn', function () {
    var _this_id = jQuery(this).attr('data-id');
    jobsearch_modal_popup_open('JobSearchModalCandShPopup' + _this_id);
});

jQuery(document).on('click', '.div-to-scroll', function () {
    var trag_todiv = jQuery(this).attr('data-target');
    jQuery('html, body').animate({
        scrollTop: jQuery('#' + trag_todiv).offset().top - 200
    }, 1000);
});

var location_box = jQuery('input.srch_autogeo_location');
function JobsearchGetClientLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(JobsearchShowClientPosition);
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
}
function JobsearchShowClientPosition(position) {
    var lat = position.coords.latitude;
    var lng = position.coords.longitude;

    if (lat != '' && lng != '') {
        var location_ajax_box = jQuery('.jobsearch_searchloc_div .jobsearch_search_location_field');
        var pos = {
            lat: lat,
            lng: lng
        };
        var dataString = "lat=" + pos.lat + "&lng=" + pos.lng + "&action=jobsearch_get_location_with_latlng";
        jQuery.ajax({
            type: "POST",
            url: jobsearch_plugin_vars.ajax_url,
            data: dataString,
            dataType: "json",
            success: function (response) {
                if (location_box.length > 0) {
                    location_box.val(response.address);
                }
                if (location_ajax_box.length > 0) {
                    location_ajax_box.val(response.address);
                }
            }
        });
    }
}
jQuery(document).ready(function () {

    if (location_box.length > 0) {
        JobsearchGetClientLocation();
    }
});

jQuery(document).on('submit', 'form', function (er) {
    var this_form = jQuery(this);
    if (this_form.find('input[type="checkbox"][name="terms_cond_check"]').length > 0) {
        var checkbox = this_form.find('input[type="checkbox"][name="terms_cond_check"]');
        if (!checkbox.is(":checked")) {
            er.preventDefault();
            alert(jobsearch_plugin_vars.accpt_terms_cond);
            return false;
        }
    }
});

function jobsearch_accept_terms_cond_pop(this_form) {
    if (this_form.find('input[type="checkbox"][name="terms_cond_check"]').length > 0) {
        var checkbox = this_form.find('input[type="checkbox"][name="terms_cond_check"]');
        if (!checkbox.is(":checked")) {
            alert(jobsearch_plugin_vars.accpt_terms_cond);
            return 'no';
        }
    }
    return 'yes';
}

jQuery('#user-sector').find('option').first().val('');
jQuery('#user-sector').attr('placeholder', jobsearch_plugin_vars.select_sector);
jQuery('#job-sector').attr('placeholder', jobsearch_plugin_vars.select_sector);

$(window).on('load', function () {

});

jQuery(document).on('click', '.show-toggle-filter-list', function () {
    var _this = jQuery(this);
    var more_txt = jobsearch_plugin_vars.see_more_txt;
    var less_txt = jobsearch_plugin_vars.see_less_txt;

    if (_this.hasClass('jobsearch-loadmore-locations')) {

        var this_loader = _this.find('.loc-filter-loder');
        var this_appender = _this.parent('.jobsearch-checkbox-toggle').find('>ul');
        var this_pnm = parseInt(_this.attr('data-pnum'));
        var this_tpgs = parseInt(_this.attr('data-tpgs'));
        
        var this_ptye = _this.attr('data-ptype');

        var this_rid = _this.attr('data-rid');
        var this_cousw = _this.attr('data-cousw');

        var q_args_json = jQuery('input[name="loc_count_qargs_' + this_rid + '"]').val();
        
        var to_action = 'jobsearch_load_more_filter_locs_to_list';
        if (typeof this_ptye !== 'undefined' && this_ptye == 'employer') {
            to_action = 'jobsearch_load_more_filter_emp_locs_to_list';
        }

        this_loader.html('<i class="fa fa-refresh fa-spin"></i>');

        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: {
                page_num: this_pnm,
                t_pgs: this_tpgs,
                param_rid: this_rid,
                q_agrs: q_args_json,
                param_cousw: this_cousw,
                action: to_action,
            },
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.list !== 'undefined' && response.list != '') {
                //
                this_appender.append(response.list);
                if (this_pnm < this_tpgs) {
                    _this.attr('data-pnum', (this_pnm + 1));
                } else {
                    _this.remove();
                }
            }
            this_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            this_loader.html('');
        });
        return false;
    }

    var etarget = _this.prev('ul').find('li.filter-more-fields');
    if (etarget.hasClass('f_showing')) {
        etarget.hide();
        _this.html(more_txt);
        etarget.removeClass('f_showing');
    } else {
        etarget.show();
        _this.html(less_txt);
        etarget.addClass('f_showing');
    }
});

function jobsearch_modal_popup_open(target) {
    jQuery('#' + target).removeClass('fade').addClass('fade-in');
    jQuery('body').addClass('jobsearch-modal-active');
}

jQuery(document).on('click', '.jobsearch-modal .modal-close', function () {
    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
    jQuery('body').removeClass('jobsearch-modal-active');
});

jQuery('.modal-content-area').on('click', function (e) {
    //
    if (e.target !== e.currentTarget)
        return;

    jQuery('.jobsearch-modal').removeClass('fade-in').addClass('fade');
    jQuery('body').removeClass('jobsearch-modal-active');
});

//for login popup
jQuery(document).on('click', '.jobsearch-open-signin-tab', function () {
    var _this = jQuery(this);
    jobsearch_modal_popup_open('JobSearchModalLogin');
    jQuery('.reg-tologin-btn').trigger('click');

    // for redirect url
    var login_form = jQuery('#JobSearchModalLogin').find('form[id^="login-form-"]');
    if (_this.hasClass('jobsearch-wredirct-url')) {
        var wredirct_url = _this.attr('data-wredircto');
        var redrct_hiden_field = login_form.find('input[name="jobsearch_wredirct_url"]');
        if (redrct_hiden_field.length > 0) {
            redrct_hiden_field.remove();
        }
        login_form.append('<input type="hidden" name="jobsearch_wredirct_url" value="' + wredirct_url + '">');
    } else {
        var redrct_hiden_field = login_form.find('input[name="jobsearch_wredirct_url"]');
        if (redrct_hiden_field.length > 0) {
            redrct_hiden_field.remove();
        }
    }

    // for packages
    if (_this.hasClass('jobsearch-pkg-bouybtn')) {
        var extra_login_info = [];
        var this_pkg_id = _this.attr('data-id');

        extra_login_info.push('buying_pkg');
        extra_login_info.push(this_pkg_id);
        if (typeof _this.attr('data-pinfo') !== 'undefined' && _this.attr('data-pinfo') != '') {
            extra_login_info.push(_this.attr('data-pinfo'));
        }

        extra_login_info = extra_login_info.join('|');
        var pkginfo_hiden_field = login_form.find('input[name="extra_login_params"]');
        if (pkginfo_hiden_field.length > 0) {
            pkginfo_hiden_field.remove();
        }
        login_form.append('<input type="hidden" name="extra_login_params" value="' + extra_login_info + '">');
    } else {
        var pkginfo_hiden_field = login_form.find('input[name="extra_login_params"]');
        if (pkginfo_hiden_field.length > 0) {
            pkginfo_hiden_field.remove();
        }
    }
});

//for register popup
jQuery(document).on('click', '.jobsearch-open-register-tab', function () {
    var _this = jQuery(this);
    jobsearch_modal_popup_open('JobSearchModalLogin');
    jQuery('.register-form').trigger('click');

    var login_form = jQuery('#JobSearchModalLogin').find('form[id^="login-form-"]');

    // for redirect url
    var redrct_hiden_field = login_form.find('input[name="jobsearch_wredirct_url"]');
    if (redrct_hiden_field.length > 0) {
        redrct_hiden_field.remove();
    }

    // for packages
    var pkginfo_hiden_field = login_form.find('input[name="extra_login_params"]');
    if (pkginfo_hiden_field.length > 0) {
        pkginfo_hiden_field.remove();
    }
});

//for email popup
jQuery(document).on('click', '.jobsearch-send-email-popup-btn', function () {
    jobsearch_modal_popup_open('JobSearchSendEmailModal');
});

jQuery(document).on('click', '.jobsearch-add-resume-to-list', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var download_cv = _this.attr('data-download');
    var this_loader = jQuery(this).next('.resume-loding-msg');

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            candidate_id: this_id,
            download_cv: download_cv,
            action: 'jobsearch_add_employer_resume_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.dbn !== 'undefined' && response.dbn != '') {
            _this.hide();
            _this.parent('.shortlisting-user-btn').hide();
            _this.parent('.shortlisting-user-btn').html(response.dbn).slideDown();
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
            _this.html('<i class="jobsearch-icon jobsearch-add-list"></i> ' + jobsearch_plugin_vars.shortlisted_str);
            _this.removeClass('jobsearch-add-resume-to-list');
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_plugin_vars.error_msg);
    });
});

jQuery(document).on('click', '.jobsearch-svcand-withtyp-tolist', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = jQuery(this).next('.resume-loding-msg');

    var type_selected = _this.parents('#usercand-shrtlistsecs-' + this_id).find('select[name^="shrtlist_type"]').val();

    this_loader.html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            candidate_id: this_id,
            type_selected: type_selected,
            action: 'jobsearch_add_employer_resume_to_list',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if (typeof response.error !== 'undefined' && response.error == '1') {
            //
            this_loader.html(response.msg);
            return false;
        }
        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.html(response.msg);
            _this.html('<i class="jobsearch-icon jobsearch-add-list"></i> ' + jobsearch_plugin_vars.shortlisted_str);
            _this.removeClass('jobsearch-svcand-withtyp-tolist');
            window.location.reload(true);
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.html(jobsearch_plugin_vars.error_msg);
    });
});

$(document).on('click', '.jobsearch-candidate-ct-form', function (e) {
    e.preventDefault();
    var this_id = $(this).data('id'),
            msg_form = $('#ct-form-' + this_id),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            msg_con = msg_form.find('.jobsearch-ct-msg'),
            msg_name = msg_form.find('input[name="u_name"]'),
            msg_email = msg_form.find('input[name="u_email"]'),
            msg_phone = msg_form.find('input[name="u_number"]'),
            msg_txt = msg_form.find('textarea[name="u_msg"]'),
            user_id = msg_form.attr('data-uid'),
            error = 0;

    var cand_ser_form = $('#ct-form-' + this_id)[0];

    var get_terr_val = jobsearch_accept_terms_cond_pop(msg_form);
    if (get_terr_val != 'yes') {
        return false;
    }

    if (msg_form.find('.jobsearch-open-signin-tab').length > 0) {
        msg_form.find('.jobsearch-open-signin-tab').trigger('click');
        return false;
    }

    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);

    if (msg_name.val() == '') {
        error = 1;
        msg_name.css({"border": "1px solid #ff0000"});
    } else {
        msg_name.css({"border": "1px solid #efefef"});
    }

    if (msg_email.val() == '') {
        error = 1;
        msg_email.css({"border": "1px solid #ff0000"});
    } else {
        if (!email_pattern.test(msg_email.val())) {
            error = 1;
            msg_email.css({"border": "1px solid #ff0000"});
        } else {
            msg_email.css({"border": "1px solid #efefef"});
        }
    }

    if (msg_txt.val() == '') {
        error = 1;
        msg_txt.css({"border": "1px solid #ff0000"});
    } else {
        msg_txt.css({"border": "1px solid #efefef"});
    }

    if (error == 0) {
        var formData = new FormData(cand_ser_form);
        
        formData.append("u_candidate_id", user_id);
        formData.append("action", 'jobsearch_candidate_contact_form_submit');
        
        msg_con.html('<em class="fa fa-refresh fa-spin"></em>');
        msg_con.show();

        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            processData: false,
            contentType: false,
            data: formData,
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined') {
                msg_name.val('');
                msg_email.val('');
                msg_phone.val('');
                msg_txt.val('');
                msg_con.html(response.msg);
            } else {
                msg_con.html(jobsearch_plugin_vars.error_msg);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html(jobsearch_plugin_vars.error_msg);
        });
    }

    return false;
});

$(document).on('click', '.jobsearch-employer-ct-form', function (e) {
    e.preventDefault();
    var this_id = $(this).data('id'),
            msg_form = $('#ct-form-' + this_id),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            msg_con = msg_form.find('.jobsearch-ct-msg'),
            msg_name = msg_form.find('input[name="u_name"]'),
            msg_email = msg_form.find('input[name="u_email"]'),
            msg_phone = msg_form.find('input[name="u_number"]'),
            msg_txt = msg_form.find('textarea[name="u_msg"]'),
            user_id = msg_form.attr('data-uid'),
            error = 0;

    var emp_ser_form = $('#ct-form-' + this_id)[0];
    
    var get_terr_val = jobsearch_accept_terms_cond_pop(msg_form);
    if (get_terr_val != 'yes') {
        return false;
    }

    if (msg_form.find('.jobsearch-open-signin-tab').length > 0) {
        msg_form.find('.jobsearch-open-signin-tab').trigger('click');
        return false;
    }

    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);

    if (msg_name.val() == '') {
        error = 1;
        msg_name.css({"border": "1px solid #ff0000"});
    } else {
        msg_name.css({"border": "1px solid #efefef"});
    }

    if (msg_email.val() == '') {
        error = 1;
        msg_email.css({"border": "1px solid #ff0000"});
    } else {
        if (!email_pattern.test(msg_email.val())) {
            error = 1;
            msg_email.css({"border": "1px solid #ff0000"});
        } else {
            msg_email.css({"border": "1px solid #efefef"});
        }
    }

    if (msg_txt.val() == '') {
        error = 1;
        msg_txt.css({"border": "1px solid #ff0000"});
    } else {
        msg_txt.css({"border": "1px solid #efefef"});
    }

    if (error == 0) {
        
        var formData = new FormData(emp_ser_form);
        
        formData.append("u_employer_id", user_id);
        formData.append("action", 'jobsearch_employer_contact_form_submit');
        
        msg_con.html('<em class="fa fa-refresh fa-spin"></em>');
        msg_con.show();

        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            processData: false,
            contentType: false,
            data: formData,
            dataType: "json"
        });

        request.done(function (response) {
            if (typeof response.msg !== 'undefined') {
                msg_name.val('');
                msg_email.val('');
                msg_phone.val('');
                msg_txt.val('');
                msg_con.html(response.msg);
            } else {
                msg_con.html(jobsearch_plugin_vars.error_msg);
            }
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html(jobsearch_plugin_vars.error_msg);
        });
    }

    return false;
});

jQuery(document).on('click', '.send-job-email-btn', function () {
    jQuery('form#jobsearch_send_to_email_form').submit();
});

jQuery('form#jobsearch_send_to_email_form').on('submit', function (e) {
    e.preventDefault();
    var _form = jQuery(this);
    var submit_btn = _form.find('.send-job-email-btn');
    var msg_con = _form.find('.send-email-msg-box');
    var loader_con = _form.find('.send-email-loader-box');
    var uemail = _form.find('input[name="send_email_to"]');
    var usubject = _form.find('input[name="send_email_subject"]');
    var msg = _form.find('textarea[name="send_email_content"]');
    var form_data = _form.serialize();

    var get_terr_val = jobsearch_accept_terms_cond_pop(_form);
    if (get_terr_val != 'yes') {
        return false;
    }

    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);

    var e_error = 0;
    if (msg.val() == '') {
        msg.css({"border": "1px solid #ff0000"});
        e_error = 1;
    }
    if (uemail.val() == '' || !email_pattern.test(uemail.val())) {
        uemail.css({"border": "1px solid #ff0000"});
        e_error = 1;
    }
    if (usubject.val() == '') {
        usubject.css({"border": "1px solid #ff0000"});
        e_error = 1;
    }

    if (e_error == 1) {
        return false;
    }

    if (!submit_btn.hasClass('jobsearch-loading')) {
        msg.css({"border": "1px solid #eceeef"});
        uemail.css({"border": "1px solid #eceeef"});
        usubject.css({"border": "1px solid #eceeef"});
        //
        submit_btn.addClass('jobsearch-loading');
        msg_con.hide();
        loader_con.show();
        loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
        var request = jQuery.ajax({
            url: jobsearch_plugin_vars.ajax_url,
            method: "POST",
            data: form_data,
            dataType: "json"
        });

        request.done(function (response) {
            if ('undefined' !== typeof response.msg && response.msg != '') {
                msg_con.html(response.msg);
                msg_con.slideDown();
            }
            if ('undefined' !== typeof response.error && response.error == '1') {
                msg_con.removeClass('alert-success').addClass('alert-danger');
            } else {
                msg_con.removeClass('alert-danger').addClass('alert-success');
            }
            submit_btn.removeClass('jobsearch-loading');
            loader_con.hide();
            loader_con.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            submit_btn.removeClass('jobsearch-loading');
            loader_con.hide();
            loader_con.html('');
        });
    }

    return false;
});

function jobsearchReplaceAll(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
}

jQuery(document).on('click', '.jobsearch-applyjob-fb-btn', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = _this.find('i');

    var this_msg_con = _this.parents('ul').next('.apply-msg');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            action: 'jobsearch_applying_job_with_facebook',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.attr('class', 'jobsearch-icon jobsearch-facebook-logo-1');
            this_msg_con.html(response.msg);
            this_msg_con.show();
            return false;
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            var red_url = jobsearchReplaceAll(response.redirect_url, '#038;', '');
            window.location.href = red_url;
        } else {
            this_loader.attr('class', 'jobsearch-icon jobsearch-facebook-logo-1');
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', 'jobsearch-icon jobsearch-facebook-logo-1');
    });
});

jQuery(document).on('click', '.jobsearch-applyjob-linkedin-btn', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = _this.find('i');

    var this_msg_con = _this.parents('ul').next('.apply-msg');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            action: 'jobsearch_applying_job_with_linkedin',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.attr('class', 'jobsearch-icon jobsearch-linkedin-logo');
            this_msg_con.html(response.msg);
            this_msg_con.show();
            return false;
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            var red_url = jobsearchReplaceAll(response.redirect_url, '#038;', '');
            window.location.href = red_url;
        } else {
            this_loader.attr('class', 'jobsearch-icon jobsearch-linkedin-logo');
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', 'jobsearch-icon jobsearch-linkedin-logo');
    });
});

jQuery(document).on('click', '.jobsearch-applyjob-google-btn', function () {
    var _this = jQuery(this);
    var this_id = _this.attr('data-id');
    var this_loader = _this.find('i');

    var this_msg_con = _this.parents('ul').next('.apply-msg');

    this_loader.attr('class', 'fa fa-refresh fa-spin');
    var request = jQuery.ajax({
        url: jobsearch_plugin_vars.ajax_url,
        method: "POST",
        data: {
            job_id: this_id,
            action: 'jobsearch_applying_job_with_google',
        },
        dataType: "json"
    });

    request.done(function (response) {

        if (typeof response.msg !== 'undefined' && response.msg != '') {
            this_loader.attr('class', 'fa fa-google-plus');
            this_msg_con.html(response.msg);
            this_msg_con.show();
            return false;
        }
        if (typeof response.redirect_url !== 'undefined' && response.redirect_url != '') {
            var red_url = jobsearchReplaceAll(response.redirect_url, '#038;', '');
            window.location.href = red_url;
        } else {
            this_loader.attr('class', 'fa fa-google-plus');
        }
    });

    request.fail(function (jqXHR, textStatus) {
        this_loader.attr('class', 'fa fa-google-plus');
    });
});

jQuery(document).on('click', '.employer-access-btn', function () {
    jQuery('.employer-access-msg').slideDown();
});
//for Download resume shortlist popup
jQuery(document).on('click', '.jobsearch-open-dloadres-popup', function () {
    jobsearch_modal_popup_open('JobSearchDLoadResModal');
});

jQuery(window).on('load', function () {
    if (jQuery('.grid').length > 0 && jQuery('.grid-item').length > 0) {
        //*** Function Masonery
        jQuery('.grid').isotope({
            itemSelector: '.grid-item',
            percentPosition: true,
            masonry: {
                fitWidth: false
            }
        });
    }
});

//
//
jQuery('.location_location1').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
            nextfieldelement = jQuery(this).data('nextfieldelement'),
            nextfieldval = jQuery(this).data('nextfieldval'),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            location_location1 = jQuery('#location_location1_' + this_id),
            location_location2 = jQuery('#location_location2_' + this_id);
    jQuery('.location_location2_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location1.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            location_location2.html(response.html);
            jQuery('.location_location2_' + this_id).html('');
            if (nextfieldval != '') {
                jQuery('.location_location2').trigger('change');
            }
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});

jQuery('.location_location2').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
            nextfieldelement = jQuery(this).data('nextfieldelement'),
            nextfieldval = jQuery(this).data('nextfieldval'),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            location_location2 = jQuery('#location_location2_' + this_id),
            location_location3 = jQuery('#location_location3_' + this_id);
    jQuery('.location_location3_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location2.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            location_location3.html(response.html);
            jQuery('.location_location3_' + this_id).html('');
            if (nextfieldval != '') {
                jQuery('.location_location3').trigger('change');
            }
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});

jQuery('.location_location3').on('change', function (e) {
    e.preventDefault();
    var this_id = jQuery(this).data('randid'),
            nextfieldelement = jQuery(this).data('nextfieldelement'),
            nextfieldval = jQuery(this).data('nextfieldval'),
            ajax_url = jobsearch_plugin_vars.ajax_url,
            location_location3 = jQuery('#location_location3_' + this_id),
            location_location4 = jQuery('#location_location4_' + this_id);
    jQuery('.location_location4_' + this_id).html('<i class="fa fa-refresh fa-spin"></i>');
    var request = jQuery.ajax({
        url: ajax_url,
        method: "POST",
        data: {
            location_location: location_location3.val(),
            nextfieldelement: nextfieldelement,
            nextfieldval: nextfieldval,
            action: 'jobsearch_location_load_location2_data',
        },
        dataType: "json"
    });

    request.done(function (response) {
        if ('undefined' !== typeof response.html) {
            location_location4.html(response.html);
            jQuery('.location_location4_' + this_id).html('');
        }
    });

    request.fail(function (jqXHR, textStatus) {
    });
    return false;

});

if (jQuery('.jobsearch-employer-list .jobsearch-table-layer').length > 0) {
    jQuery(document).on('click', '.jobsearch-employer-list .jobsearch-table-layer', function (event) {
        var _this = jQuery(this);
        var this_target = jQuery(event.target);
        if (this_target.is('a') || this_target.parent('a').length > 0) {
            // do nothing
        } else {
            var dest_go_to = _this.find('h2 > a');
            window.location.replace(dest_go_to.attr('href'));
        }
    });
}

if (jQuery('.careerfy-employer-grid .careerfy-employer-grid-wrap').length > 0) {
    jQuery(document).on('click', '.careerfy-employer-grid .careerfy-employer-grid-wrap', function (event) {
        var _this = jQuery(this);
        var this_target = jQuery(event.target);
        if (this_target.is('a') || this_target.parent('a').length > 0) {
            // do nothing
        } else {
            var dest_go_to = _this.find('h2 > a');
            window.location.replace(dest_go_to.attr('href'));
        }
    });
}

$(document).ready(function () {
    if (window.location.hash !== 'undefined' && window.location.hash == '#_=_') {
        window.location.hash = ''; // for older browsers, leaves a # behind
        history.pushState('', document.title, window.location.pathname); // nice and clean
        e.preventDefault(); // no page reload
    }
});