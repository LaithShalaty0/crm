"use strict";

var current_fs, next_fs, previous_fs;
var left, opacity, scale;
var animating;
$(".steps").validate({
    errorClass: 'invalid',
    errorElement: 'p',
    errorPlacement: function(error, element) {
        error.insertAfter(element.next('p').children());
    },
    highlight: function(element) {
        $(element).next('p').show();
    },
    unhighlight: function(element) {
        $(element).next('p').hide();
    }
});
$(".next").click(function() {
    $(".steps").validate({
        errorClass: 'invalid',
        errorElement: 'p',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next('p').children());
        },
        highlight: function(element) {
            $(element).next('p').show();
        },
        unhighlight: function(element) {
            $(element).next('p').hide();
        }
    });

    if ((!$('.steps').valid())) {
        return true;
    }
    if (animating) return false;
    animating = true;
    current_fs = $(this).parent().parent().parent();
    next_fs = $(this).parent().parent().parent().next();
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
    $(".wizard li").eq($("fieldset").index(next_fs)).addClass("completed");
    next_fs.attr("disabled", false);
    next_fs.show();
    current_fs.animate({
        opacity: 0
    }, {
        step: function(now, mx) {
            scale = 1 - (1 - now) * 0.2;
            left = (now * 50) + "%";
            opacity = 1 - now;
            current_fs.css({
                'transform': 'scale(' + scale + ')'
            });
            next_fs.css({
                'left': left,
                'opacity': opacity
            });
        },
        duration: 800,
        complete: function() {
            current_fs.hide();
            animating = false;
        },
        easing: 'easeInOutExpo'
    });
});
$(".submit").click(function() {
    $(".steps").validate({
        errorClass: 'invalid',
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.insertAfter(element.next('span').children());
        },
        highlight: function(element) {
            $(element).next('span').show();
        },
        unhighlight: function(element) {
            $(element).next('span').hide();
        }
    });
    if ((!$('.steps').valid())) {
        return false;
    }
    if (animating) return false;
    animating = true;
    current_fs = $(this).parent().parent().parent();
    next_fs = $(this).parent().parent().parent().next();
    $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
    $(".wizard li").eq($("fieldset").index(next_fs)).addClass("completed");
    next_fs.show();
    current_fs.animate({
        opacity: 0
    }, {
        step: function(now, mx) {
            scale = 1 - (1 - now) * 0.2;
            left = (now * 50) + "%";
            opacity = 1 - now;
            current_fs.css({
                'transform': 'scale(' + scale + ')'
            });
            next_fs.css({
                'left': left,
                'opacity': opacity
            });
        },
        duration: 800,
        complete: function() {
            current_fs.hide();
            animating = false;
        },
        easing: 'easeInOutExpo'
    });
});
$(".previous").click(function() {
    if (animating) return false;
    animating = true;
    current_fs = $(this).parent().parent().parent();
    previous_fs = $(this).parent().parent().parent().prev();
    $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");
    $(".wizard li").eq($("fieldset").index(current_fs)).removeClass("completed");
    current_fs.attr("disabled", true);
    previous_fs.show();
    current_fs.animate({
        opacity: 0
    }, {
        step: function(now, mx) {
            scale = 0.8 + (1 - now) * 0.2;
            left = ((1 - now) * 50) + "%";
            opacity = 1 - now;
            current_fs.css({
                'left': left
            });
            previous_fs.css({
                'transform': 'scale(' + scale + ')',
                'opacity': opacity
            });
        },
        duration: 800,
        complete: function() {
            current_fs.hide();
            animating = false;
        },
        easing: 'easeInOutExpo'
    });
});