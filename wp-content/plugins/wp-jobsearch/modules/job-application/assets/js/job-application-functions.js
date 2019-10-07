/*
 * 
 * Directory Plus Member Added jobapplication function
 */
function jobsearch_member_job_job_application(thisObj, job_id, member_id, jobapplication, jobapplicationd, before_icon, after_icon, strings) {

    "use strict";
    var job_application_icon_class = jQuery(thisObj).find("i").attr('class');

    var loader_class = 'fa fa-spinner fa-spin';
    jQuery(thisObj).find("i").removeClass(job_application_icon_class).addClass(loader_class);
    var dataString = 'job_id=' + job_id + '&member_id=' + member_id + '&action=jobsearch_job_application_submit';
    jQuery.ajax({
        type: "POST",
        url: jobsearch_job_application.admin_url,
        data: dataString,
        dataType: "json",
        success: function (response) {

            if (response.status == true) {
                jQuery(thisObj).removeClass('jobapplication').addClass('jobapplication');
                jQuery(thisObj).find("i").removeClass(loader_class).addClass(after_icon);
                jQuery(thisObj).find(".option-content span").html(jobapplicationd);
                var msg_obj = {msg: strings.added, type: 'success'};

                jobsearch_show_response(msg_obj);
                if (response.job_count !== 'undefined' && response.job_count !== '') {
                    jQuery(thisObj).find(".likes-count span").text(response.job_count);
                }
            } else {

                if (response.current_user == true) {
                    jQuery(thisObj).find("i").removeClass(loader_class).addClass(before_icon);
                    var msg_obj = {msg: response.msg, type: 'success'};
                    jobsearch_show_response(msg_obj);
                } else {
                    jQuery(thisObj).removeClass('jobapplication').addClass('jobapplication');
                    jQuery(thisObj).find("i").removeClass(loader_class).addClass(before_icon);
                    jQuery(thisObj).find(".option-content span").html(jobapplication);
                    var msg_obj = {msg: strings.removed, type: 'success'};
                    jobsearch_show_response(msg_obj);
                    if (response.job_count !== 'undefined' && response.job_count !== '') {
                        jQuery(thisObj).find(".likes-count span").text(response.job_count);
                    }
                }

            }
        }
    });
}

function jobsearch_member_job_application(thisObj, job_id, member_id, jobapplication, jobapplicationd, before_icon, after_icon, strings) {

    "use strict";
    var job_application_icon_class = jQuery(thisObj).find("i").attr('class');

    var loader_class = 'fa fa-spinner fa-spin';
    jQuery(thisObj).find("i").removeClass(job_application_icon_class).addClass(loader_class);
    var dataString = 'job_id=' + job_id + '&member_id=' + member_id + '&action=jobsearch_job_application_submit';

    jQuery.ajax({
        type: "POST",
        url: jobsearch_job_application.admin_url,
        data: dataString,
        dataType: "json",
        success: function (response) {

            console.log(response);

            if (response.status == true) {
                jQuery(thisObj).removeClass('jobapplication').addClass('jobapplication');
                jQuery(thisObj).html(after_icon + jobapplicationd);
                var msg_obj = {msg: strings.added, type: 'success'};

                jobsearch_show_response(msg_obj);
                if (response.job_count !== 'undefined' && response.job_count !== '') {
                    jQuery(thisObj).parent().find(".likes-count span").text(response.job_count);
                }
            } else {
                if (response.current_user == true) {
                    jQuery(thisObj).html(before_icon + jobapplication);
                    var msg_obj = {msg: response.msg, type: 'success'};
                    jobsearch_show_response(msg_obj);
                } else {
                    jQuery(thisObj).removeClass('jobapplication').addClass('jobapplication');
                    jQuery(thisObj).html(before_icon + jobapplication);
                    var msg_obj = {msg: strings.removed, type: 'success'};
                    jobsearch_show_response(msg_obj);
                    if (response.job_count !== 'undefined' && response.job_count !== '') {
                        jQuery(thisObj).parent().find(".likes-count span").text(response.job_count);
                    }
                }
            }
        }
    });
}

jQuery(document).on("click", 'input[type="radio"][name="cv_file_item"]', function () {
    jQuery('.jobsearch-apply-withcvs .user-cvs-list').find('li').removeClass('active');
    jQuery(this).parents('li').addClass('active');
});

/*
 * 
 * Directory Plus Member Removed jobapplication function
 */
jQuery(document).on("click", ".jobsearch-apply-btn", function () {

    // get template data
    var thisObj = jQuery(this);
    var job_id = thisObj.data('jobid');
    var btn_before_label = thisObj.data('btnbeforelabel');
    var btn_after_label = thisObj.data('btnafterlabel');

    console.log(job_id);

 //   var profil_candidat = jQuery('.a-profil-candidat-'+job_id+'.activ').val();
    var profil_candidat =  jQuery('.activlink').attr('data-nume');

    console.log(profil_candidat);

    if (typeof jQuery('input[type="radio"][name="cv_file_item"]:checked').val() !== 'undefined') {
        var cv_attach = jQuery('input[type="radio"][name="cv_file_item"]:checked').val();
        var dataString = 'job_id=' + job_id + '&attach_cv=' + cv_attach;
    } else {
        var dataString = 'job_id=' + job_id;
    }


    var text_atasat = jQuery('.text-aplicare').html();

   // var text_atasat = tinyMCE.get('zonaText_' + job_id).getContent();


    dataString = dataString + '';
    dataString = dataString + '&text_atasat=' + text_atasat;
    dataString = dataString + '&profil_atasat=' + profil_candidat;


    var fisiere_atasate = {};
    var keys = 0;

    jQuery('.fisier-' + job_id).each(function () {

        keys++;
        var input = $(this);
        var nume_fisier = input.attr('data-nume');
        var link_fisier = input.attr('data-link');
        var id_fisier = input.attr('data-id');

        fisiere_atasate[keys] = {
            'nume': nume_fisier,
            'link': link_fisier,
            'id': id_fisier
        };

    });

    var fisiere_json = JSON.stringify(fisiere_atasate);
    var dataString = dataString + '&fisiere_atasate=' + fisiere_json;

    thisObj.html('<i class="fa fa-spinner fa-spin"></i>');
    thisObj.next('.apply-bmsg').attr('class', 'apply-bmsg');
    thisObj.next('.apply-bmsg').html('');

    // if (jQuery('.fileinput-' + job_id).val() !== '') {
    //     var candidate_id = jQuery('.candidat-' + job_id).val();
    //
    //     var ajaxurl = jobsearch_job_application.admin_url;
    //     var formData = new FormData();
    //     formData.append('updoc', jQuery('.fileinput-' + job_id)[0].files[0]);
    //     formData.append('action', 'questiondatahtml');
    //     formData.append('id', jQuery('.post_id_' + job_id).val());
    //     $.ajax({
    //         url: ajaxurl,
    //         type: 'POST',
    //         data: formData, cache: false,
    //         processData: false,
    //         contentType: false,
    //         success: function (data) {
    //           var fisier  = data;
    //             addAppUpload(fisier);
    //
    //
    //         }
    //
    //     });
    //
    // } else {
    //     addAppUpload();
    // }


    addAppUpload();

    function addAppUpload(fisier) {

        if(fisier !== ''){
            dataString = dataString + '&fisierenou=' + fisier;

        }
        console.log(dataString);

        jQuery.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: jobsearch_job_application.admin_url,
            data: 'action=jobsearch_job_application_submit&' + dataString,

            success: function (response) {

                if (response.status == true) {

                    var apply_msg = thisObj.next('.apply-bmsg');
                    thisObj.html(btn_after_label);

                    thisObj.removeClass('jobsearch-apply-btn');
                    thisObj.addClass('jobsearch-applied-job-btn');
                    thisObj.removeAttr('href');
                    console.log(response);
                    var jobCurent = jQuery('.jobslist-job[data-job=' + response.job + ']');
                    var jobTemp = jobCurent.html();

                    jobCurent.remove();

                    if (typeof response.succmsg !== 'undefined' && response.succmsg != '') {
                        apply_msg.html(response.succmsg);
                    }
                    if (typeof response.redrct_uri !== 'undefined' && response.redrct_uri != '') {
                          window.location.href = response.redrct_uri;
                    }
                } else {
                    thisObj.html(btn_before_label);
                    var apply_msg = thisObj.next('.apply-bmsg');
                    apply_msg.html(response.msg);
                    apply_msg.addClass('alert-msg alert-danger');
                }
            }
        });
    }


    return false;
});

function jobsearch_apply_job_cv_upload_url(input) {

    if (input.files && input.files[0]) {

        var loader_con = jQuery('#jobsearch-upload-cv-main').find('.fileUpLoader');

        var cv_file = input.files[0];
        var file_size = cv_file.size;
        var file_type = cv_file.type;
        var file_name = cv_file.name;
        jQuery('#jobsearch-uploadfile').attr('placeholder', file_name);
        jQuery('#jobsearch-uploadfile').val(file_name);

        var allowed_types = jobsearch_job_application.cvdoc_file_types;
        var filesize_allow = jobsearch_job_application.cvfile_size_allow;
        filesize_allow = parseInt(filesize_allow);

        file_size = parseFloat(file_size / 1024).toFixed(2);

        if (file_size <= filesize_allow) {
            if (allowed_types.indexOf(file_type) >= 0) {
                loader_con.html('<i class="fa fa-refresh fa-spin"></i>');
                var formData = new FormData();
                formData.append('on_apply_cv_file', cv_file);
                formData.append('action', 'jobsearch_apply_job_with_cv_file');

                var request = $.ajax({
                    url: jobsearch_job_application.admin_url,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json"
                });
                request.done(function (response) {
                    if (typeof response.err_msg !== 'undefined' && response.err_msg != '') {
                        loader_con.html(response.err_msg);
                        return false;
                    }
                    if (typeof response.filehtml !== 'undefined' && response.filehtml != '') {
                        jQuery('.jobsearch-apply-withcvs .user-cvs-list').append(response.filehtml);
                        jQuery('.jobsearch-apply-withcvs .user-cvs-list li:last-child').find('input').trigger('click');
                    }
                    loader_con.html('');
                });

                request.fail(function (jqXHR, textStatus) {
                    loader_con.html(jobsearch_job_application.error_msg);
                    loader_con.html('');
                });
            } else {
                alert(jobsearch_job_application.cv_file_types);
            }

        } else {
            alert(jobsearch_job_application.cvfile_size_err);
        }
    }
}

jQuery(document).on('change', 'input[name="on_apply_cv_file"]', function () {
    jobsearch_apply_job_cv_upload_url(this);
});

//for non-register user popup
jQuery(document).on('click', '.jobsearch-nonuser-apply-btn', function () {
    jobsearch_modal_popup_open('JobSearchNonuserApplyModal');
});

jQuery(document).on('click', '.jobsearch-applyin-withemail', function (e) {
    e.preventDefault();
    var _this = $(this);

    var rand_id = _this.attr('data-randid');
    var this_con = jQuery('#apply-withemail-' + rand_id);

    var get_terr_val = jobsearch_accept_terms_cond_pop(this_con);
    if (get_terr_val != 'yes') {
        return false;
    }

    var ajax_url = jobsearch_job_application.admin_url;
    var msg_con = this_con.find('.apply-job-form-msg');
    var msg_loader = this_con.find('.apply-job-loader');

    var msg_name = this_con.find('input[name="user_fullname"]');
    var msg_email = this_con.find('input[name="user_email"]');

    var cv_file = this_con.find('input[name="cuser_cv_file"]');

    var error = 0;
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

    if (cv_file.length > 0 && cv_file.val() != '') {
        cv_file = cv_file.prop('files')[0];
        var file_size = cv_file.size;
        var file_type = cv_file.type;

        var allowed_types = jobsearch_job_application.cvdoc_file_types;
        var filesize_allow = jobsearch_job_application.cvfile_size_allow;
        filesize_allow = parseInt(filesize_allow);
        file_size = parseFloat(file_size / 1024).toFixed(2);
        if (file_size > filesize_allow) {
            alert(jobsearch_job_application.cvfile_size_err);
            error = 1;
        }
        if (allowed_types.indexOf(file_type) < 0) {
            alert('file type not allowed.');
            error = 1;
        }
    }

    if (error == 0) {
        msg_loader.html('<i class="fa fa-refresh fa-spin"></i>');

        var form_data = new FormData(this_con[0]);
        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            data: form_data,
            processData: false,
            contentType: false,
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
                msg_con.html(msg_before + response.msg + msg_after);
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    msg_name.val('');
                    msg_email.val('');
                    this_con.find('ul.apply-fields-list').slideUp();
                }
            } else {
                msg_con.html(jobsearch_job_application.error_msg);
            }
            msg_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html(jobsearch_job_application.error_msg);
            msg_loader.html('');
        });
    }

    return false;

});

jQuery(document).on('click', '.jobsearch-apply-woutreg-btn', function (e) {
    e.preventDefault();
    var this_id = $(this).data('id'),
        msg_form = $('#apply-form-' + this_id),
        ajax_url = jobsearch_job_application.admin_url,
        msg_con = msg_form.find('.apply-job-form-msg'),
        msg_loader = msg_form.find('.form-loader'),
        msg_email = msg_form.find('input[name="user_email"]'),
        cv_file = msg_form.find('input[name="candidate_cv_file"]'),
        error = 0;

    var get_terr_val = jobsearch_accept_terms_cond_pop(msg_form);
    if (get_terr_val != 'yes') {
        return false;
    }

    var email_pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,20}$/i);

    if (msg_email.length > 0) {
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
    }

    msg_form.find(".required-apply-field").each(function () {
        var _this_reqf = jQuery(this);
        if (_this_reqf.val() == '') {
            error = 1;
            if (_this_reqf.parent('.jobsearch-profile-select').length > 0) {
                _this_reqf.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
            } else {
                _this_reqf.css({"border": "1px solid #ff0000"});
            }
        } else {
            if (_this_reqf.parent('.jobsearch-profile-select').length > 0) {
                _this_reqf.parent('.jobsearch-profile-select').css({"border": "none"});
            } else {
                _this_reqf.css({"border": "1px solid #efefef"});
            }
        }
    });

    msg_form.find("select.required-cussel-field").each(function () {
        var _this_reqf = jQuery(this);
        if (_this_reqf.val() == '') {
            error = 1;
            if (_this_reqf.parent('.jobsearch-profile-select').length > 0) {
                _this_reqf.parent('.jobsearch-profile-select').css({"border": "1px solid #ff0000"});
            } else {
                _this_reqf.css({"border": "1px solid #ff0000"});
            }
        } else {
            if (_this_reqf.parent('.jobsearch-profile-select').length > 0) {
                _this_reqf.parent('.jobsearch-profile-select').css({"border": "none"});
            } else {
                _this_reqf.css({"border": "1px solid #efefef"});
            }
        }
    });

    var phone_pattern = new RegExp(/^[0-9\-\(\)\/\+\s]*$/);
    var num_pattern = new RegExp('^[0-9]+$');

    var phone_number = msg_form.find('input[name="user_phone"]');
    var curr_salary = msg_form.find('input[name="user_salary"]');

//    if (phone_number.length > 0) {
//        if (phone_number.val() != '' && !phone_pattern.test(phone_number.val())) {
//            error = 1;
//            phone_number.css({"border": "1px solid #ff0000"});
//        } else {
//            phone_number.css({"border": "1px solid #efefef"});
//        }
//    }
//
//    if (curr_salary.length > 0) {
//        if (curr_salary.val() != '' && !num_pattern.test(curr_salary.val())) {
//            error = 1;
//            curr_salary.css({"border": "1px solid #ff0000"});
//        } else {
//            curr_salary.css({"border": "1px solid #efefef"});
//        }
//    }

    if (cv_file.val() == '' && cv_file.hasClass('cv_is_req')) {
        error = 1;
        jQuery('#jobsearch-upload-cv-main').css({"border": "1px solid #ff0000"});
    } else {
        jQuery('#jobsearch-upload-cv-main').css({"border": "none"});
    }
    if (cv_file.val() != '') {
        cv_file = cv_file.prop('files')[0];
        var file_size = cv_file.size;
        var file_type = cv_file.type;

        var allowed_types = jobsearch_job_application.cvdoc_file_types;
        var filesize_allow = jobsearch_job_application.cvfile_size_allow;
        filesize_allow = parseInt(filesize_allow);
        file_size = parseFloat(file_size / 1024).toFixed(2);
        if (file_size > filesize_allow) {
            alert(jobsearch_job_application.cvfile_size_err);
            error = 1;
        }
        if (allowed_types.indexOf(file_type) < 0) {
            alert('file type not allowed.');
            error = 1;
        }
    }

    if (error == 0) {

        msg_form.find("input[type='file']").each(function () {
            if ($(this).get(0).files.length === 0) {
                $(this).remove();
            }
        });

        msg_loader.html('<i class="fa fa-refresh fa-spin"></i>');

        var form_data = new FormData(msg_form[0]);
        var request = $.ajax({
            url: ajax_url,
            method: "POST",
            data: form_data,
            processData: false,
            contentType: false,
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
                msg_con.html(msg_before + response.msg + msg_after);
                if (typeof response.error !== 'undefined' && response.error == '0') {
                    msg_email.val('');
                    msg_form.find('ul.apply-fields-list').slideUp();
                }
                if (typeof response.redrct_uri !== 'undefined' && response.redrct_uri != '') {
                    window.location.href = response.redrct_uri;
                }
            } else {
                msg_con.html(jobsearch_job_application.error_msg);
            }
            msg_loader.html('');
        });

        request.fail(function (jqXHR, textStatus) {
            msg_con.html(jobsearch_job_application.error_msg);
            msg_loader.html('');
        });
    }

    return false;
});

jQuery(document).on('change', 'input[name="candidate_cv_file"]', function () {
    var filename = jQuery(this)[0].files.length ? jQuery(this)[0].files[0].name : "";
    jQuery('#jobsearch-uploadfile').attr('placeholder', filename);
    jQuery('#jobsearch-uploadfile').val(filename);
});

jQuery(document).on('change', 'input[name="cuser_cv_file"]', function () {
    var this_id = jQuery(this).attr('data-randid');
    var filename = jQuery(this)[0].files.length ? jQuery(this)[0].files[0].name : "";
    jQuery('#jobsearch-uploadfile-' + this_id).attr('placeholder', filename);
    jQuery('#jobsearch-uploadfile-' + this_id).val(filename);
});
