// JavaScript Document
var THIS = this,
    default_trunk_price = 29.99,
    default_amount_trunks = 1,
    format_phone_number = function(phone_number) {
        if(phone_number.substr(0,2) === "+1" && phone_number.length === 12) {
            phone_number = phone_number.replace(/(\+1)(\d{3})(\d{3})(\d{4})/, '$1 ($2) $3-$4');
        }

        return phone_number;
    };


if($('.pay-as-you-go').size() > 0) {
    $('.pay-as-you-go #continue-btn').on('click', function() {
        if(typeof(Storage)!=='undefined') {
            delete sessionStorage['trunking_trunks_data'];

            sessionStorage.setItem('trunking_trunks_data', JSON.stringify({
                price: $('#totalcost-number').data('price') || default_trunk_price,
                money: $('#amount_money').data('amount') || 0,
                dids: $('#amount_numbers').data('number') || 0
            }));
        }
    });
}

if($('.pay-trunks').size() > 0) {
    $('.pay-trunks #continue-btn').on('click', function() {
        if(typeof(Storage)!=='undefined') {
            delete sessionStorage['trunking_trunks_data'];

            sessionStorage.setItem('trunking_trunks_data', JSON.stringify({
                price: $('#totalcost-number').data('price') || default_trunk_price,
                trunks: $('#amount_twoway_trunks').data('number') || default_amount_trunks,
                dids: $('#amount_numbers').data('number') || 0
            }));
        }
    });
}

/* Wizard */
if($('#onboarding_wrapper').size() > 0) {
    if(typeof(Storage)!=='undefined') {
        var data_trunks = JSON.parse(sessionStorage.getItem('trunking_trunks_data')) || { price: default_trunk_price, trunks: default_amount_trunks, dids: 0 };

        if('money' in data_trunks) {
            $('#amount_money').html('$' + data_trunks.money);
            $('li #amount_twoway_trunks').parent().hide();
        }
        else {
            $('#amount_twoway_trunks').html(data_trunks.trunks);
            $('#amount_money').parent().hide();
        }

        $('#totalcost-number').html('$' + data_trunks.price);
        $('#amount_numbers').html(data_trunks.dids);
    }

    window.alert = function(message) {
        $('#override_javascript_alert').text(message).dialog({
            modal:true,
            title:'Validation Error',
            resizable: false,
            buttons: {
                'Ok':function(){
                    $(this).dialog('close');
                }
            }
        });
    };

    var initialize_wizard = function(parent, callback_submit) {
        var THIS = this,
            max_step = parseInt($('.wizard-top-bar', parent).attr('data-max_step'));

        $('.wizard-content-step', parent).hide();
        $('.wizard-content-step[data-step="1"]', parent).show();

        $('.wizard-top-bar', parent).attr('data-active_step', '1');

        if(max_step !== 1) {
            $('.submit-btn', parent).hide();
        }
        else {
            $('.next-step', parent).hide();
        }

        $('.prev-step', parent).hide();

        $('.step', parent).on('click', function() {
            var step = $(this).data('step');
            if($(this).hasClass('completed')) {
                var active_step = parseInt($('.wizard-top-bar', parent).attr('data-active_step'));

                if(active_step > step) {
                    THIS.change_step(step, max_step, parent);
                }
                else {
                    THIS.validate_step($('.wizard-top-bar', parent).attr('data-active_step'), parent, function() {
                        THIS.change_step(step, max_step, parent);
                    });
                }

                $('.next-step', parent).removeClass('summary');
            }
        });

        $('.next-step', parent).on('click', function(ev) {
            ev.preventDefault();
            var go_summary = $(this).hasClass('summary');

            current_step = parseInt($('.wizard-top-bar', parent).attr('data-active_step'));
            THIS.validate_step(current_step, parent, function() {
                if(go_summary) {
                    THIS.change_step(max_step, max_step, parent);
                }
                else {
                    THIS.change_step(++current_step, max_step, parent);
                }
            });
            $(this).removeClass('summary');
        });

        $('.prev-step', parent).on('click', function(ev) {
            ev.preventDefault();

            current_step = parseInt($('.wizard-top-bar', parent).attr('data-active_step'));
            THIS.change_step(--current_step, max_step, parent);
        });

        $('.submit-btn', parent).on('click', function(ev) {
            ev.preventDefault();

            if(typeof callback_submit === 'function') {
                callback_submit();
            }
        });
    };

    var change_step = function(step_index, max_step, parent) {
        var THIS = this;

        $('.step', parent).removeClass('active');
        $('.step[data-step="'+step_index+'"]', parent).addClass('active');

        for(var i = step_index; i >= 1; --i) {
            $('.step[data-step="'+i+'"]', parent).addClass('completed');
        }

        $('.wizard-content-step', parent).hide();
        $('.wizard-content-step[data-step="'+ step_index +'"]', parent).show();

        $('.cancel', parent).hide();
        $('.prev-step', parent).show();
        $('.next-step', parent).show();
        $('.submit-btn', parent).hide();

        if(step_index === max_step) {
            $('.next-step', parent).hide();
            $('.submit-btn', parent).show();
        }

        if(step_index === 1) {
            $('.prev-step', parent).hide();
            $('.cancel', parent).show();
        }

        $('.wizard-top-bar', parent).attr('data-active_step', step_index);
    };

    var validate_step = function(step, parent, callback) {
        var validated = true,
            validation_form_error = false,
            step = parseInt(step),
            error_message = 'Please correct the following errors:';

        if(step === 1) {
            $('#recap_username').html($('#acct-username').val());
            $('#recap_account_name').html($('#acct-name').val());
            $('#recap_email').html($('#acct-email').val());
        }
        else if(step === 2) {
            $('#cardtype').html();
            var card_nbr = $('#billing-cardnumber').val();
            $('#last4digits').html(card_nbr.substr(card_nbr.length - 4));
            $('#full_name').html($('#billing-name').val());
            $('#street').html($('#billing-address1').val() + ' - ' + $('#billing-address2').val());
            $('#city_state_zip').html($('#billing-city').val() + ', ' + $('#billing-state').val() + ' ' + $('#billing-zipcode').val());
        }
        else if(step === 3) {
        }

        $('input', '.wizard-content-step[data-step="'+ step +'"]').each(function(k, v) {
            if(v.validity.valid === false) {
                validated = false;
                validation_form_error = true;
            }
        });
        if(validation_form_error) {
            validated = false;
            error_message += '\n\nPlease check that you filled out all fields correctly.';
        }

        if(validated === true) {
            if(typeof callback === 'function') {
                callback();
            }
        }
        else {
            alert(error_message);
        }
    };

    $('.link-step').on('click', function() {
        THIS.change_step($(this).data('step'), 3, $('#onboarding_wrapper'));
        $('.next-step').addClass('summary');
    });

    $('#billing-cardnumber').on('blur', function(){
        var re = new RegExp("^4"),
            number = $(this).val(),
            card_type = '';

        if (number.match(re) != null) {
            $('#cardtype-image').removeClass('discover mastercard amex')
                                .addClass('visa');
            card_type = 'Visa';
        }
        else {
            re = new RegExp("^(34|37)");
            if (number.match(re) != null){
                $('#cardtype-image').removeClass('discover mastercard visa')
                                    .addClass('amex');
                card_type = 'American Express';
            }
            else {
                re = new RegExp("^5[1-5]");
                if (number.match(re) != null){
                    $('#cardtype-image').removeClass('discover visa amex')
                                        .addClass('mastercard');
                    card_type = 'Mastercard';
                }
                else {
                    $('#cardtype-image').removeClass('discover visa amex mastercard')
                }
            }
        }

        $('#cardtype').html(card_type);
    });

    $('#billing-zipcode').blur(function() {
        $.getJSON('http://www.geonames.org/postalCodeLookupJSON?&country=US&callback=?', { postalcode: $(this).val() }, function(response) {
            if (response && response.postalcodes.length && response.postalcodes[0].placeName) {
                $('#billing-city').val(response.postalcodes[0].placeName);
                $('#billing-state').val(response.postalcodes[0].adminCode1);
            }
        });
    });

    THIS.initialize_wizard($('#onboarding_wrapper'));
}

if($('#testimonial-section').size() > 0) {
    var i = 0;

    var function_rotate = function() {
        $('.testimonial').hide();
        $('#testimonial-' + (((i) % 4) + 1)).slideDown();
        $('#testimonial-' + (((i + 1) % 4) + 1)).slideDown();
        i = i+2;
    };

    setInterval(function(){
        function_rotate();
    }, 10000);
    function_rotate();
}

/* Twitter plugin */
if($('#tweets-wrapper').size() > 0) {
    $('#tweets-wrapper').tweet({
        username: "2600hertz",
        join_text: "auto",
        avatar_size: 40,
        count: 2,
        auto_join_text_default: "",
        auto_join_text_ed: "",
        auto_join_text_ing: "",
        auto_join_text_reply: "",
        auto_join_text_url: "",
        loading_text: "loading tweets..."
    });
}
/* Tumblr plugin */
if(typeof google !== 'undefined') {
    google.load("feeds", "1");

    function OnLoad() {
        var feedControl = new google.feeds.FeedControl();
        feedControl.setNumEntries(1);
        feedControl.addFeed("http://blog.2600hz.com/rss");
        feedControl.draw(document.getElementById("blog-wrapper"));
    }

    google.setOnLoadCallback(OnLoad);
}

/* Beginning of our JS */
var update_data_trunks = function(trunks_amount) {
    var twoway_price = 6.99,
        total_amount_twoway = (trunks_amount - parseInt($('#amount_twoway_trunks').attr('data-number') || 1)) * twoway_price,
        current_price = parseFloat($('#totalcost-number').attr('data-price')),
        total_price = (current_price + total_amount_twoway).toFixed(2);

    $('#slider-amount').html(trunks_amount);
    $('#amount_twoway_trunks').html(trunks_amount)
                              .attr('data-number', trunks_amount);

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);
};

var update_data_outboundTrunks = function(trunks_amount) {
    var twoway_price = 24.99,
        total_amount_twoway = (trunks_amount - parseInt($('#amount_outbound_trunks').attr('data-number') || 1)) * twoway_price,
        current_price = parseFloat($('#totalcost-number').attr('data-price')),
        total_price = (current_price + total_amount_twoway).toFixed(2);

    $('#ouboundSlider-amount').html(trunks_amount);
    $('#amount_outbound_trunks').html(trunks_amount)
                              .attr('data-number', trunks_amount);

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);
};

var update_data_dids = function() {
    var price_did = 1.00,
        dids_amount = $('.number-line').size();
        current_price = parseFloat($('#totalcost-number').attr('data-price')),
        total_dids = (dids_amount - parseInt($('#amount_numbers').attr('data-number') || 0))* price_did,
        total_price = (total_dids + current_price).toFixed(2),

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);

    $('#number-amount').html(dids_amount);
    $('#amount_numbers').html(dids_amount)
                        .attr('data-number', dids_amount);
};

var update_money = function(money_amount) {
    if(money_amount === '') {
        money_amount = 0;
    }

    var money_amount = parseFloat(money_amount),
        current_price = parseFloat($('#totalcost-number').attr('data-price') || 0),
        total_money = (money_amount - parseInt($('#amount_money').attr('data-amount') || 0)),
        total_price = (total_money + current_price).toFixed(2);

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);

    $('#select-amount').html('$'+money_amount.toFixed(0));
    $('#amount_money').html('$'+money_amount.toFixed(2))
                      .attr('data-amount', money_amount);
};

$('#select_country').on('change', function() {
	$('#as-low-big').html($('option:selected', $(this)).val());
});

$('#trunk-slider').slider({
    min: 1,
    max: 20,
    range: 'min',
    value: 1,
    slide: function( event, ui ) {
        THIS.update_data_trunks(ui.value);
    }
});

$('#outboundTrunk-slider').slider({
    min: 1,
    max: 20,
    range: 'min',
    value: 1,
    slide: function( event, ui ) {
        THIS.update_data_outboundTrunks(ui.value);
    }
});

$('#select_amount').on('change', function() {
    THIS.update_money($(this).val());
});

$('#search-for-did-btn').on('click', function() {
    $('#popup').dialog({
        title: "Select Phone Numbers",
        width: "500px",
        resizable: false,
        modal: true
    });

    $('.number-wrapper input[type="checkbox"]').each(function(){
        $(this).attr('checked', false);
    });

    $('.number-line .remove-did').each(function(k, v) {
        $('.number-wrapper input[type="checkbox"][data-phone_number="'+ $(this).data('phone_number') +'"]').attr('checked', true);
    });
});

$('#search-numbers').on('click', function() {
    $.getJSON('https://api.2600hz.com:8443/v1/phone_numbers', {prefix: parseInt($('.list-numbers #search-input').val()) || 415, quantity: 20}, function(response) {
        $('#results .number-selection').empty();

        if(response.data.length > 0) {
            $('#submit_numbers').show();
            response.data.sort();

            $.each(response.data, function(k , v) {
                if($('.remove-did[data-phone_number="'+ v +'"]').size() === 0) {
                    $('<div class="number-wrapper"><label><input type="checkbox" data-phone_number="'+v+'">'+ THIS.format_phone_number(v) +'</input></label></div>').appendTo($('#results .number-selection'));
                }
                else {
                    $('<div class="number-wrapper"><label><input type="checkbox" data-phone_number="'+v+'" checked>'+ THIS.format_phone_number(v) +'</input></label></div>').appendTo($('#results .number-selection'));
                }
            });
        }
        else {
            $('<div class="number-wrapper"><label>There are no number available...</label></div>').appendTo($('#results .number-selection'));
            $('#submit_numbers').hide();
        }

        $('#results').slideDown();
    });
});

$('#submit_numbers').on('click', function() {
    var list_numbers = [];

    $('.number-wrapper input[type="checkbox"]:checked').each(function() {
        list_numbers.push($(this).data('phone_number'));
    });

    $.each(list_numbers, function(k, v) {
        if($('.remove-did[data-phone_number="'+ v +'"]').size() === 0) {
            $('#selected-numbers').append('<li class="number-line">'+THIS.format_phone_number(v)+'<a class="remove-did" data-phone_number="'+v+'" href="#">remove</a></li>');
        }
    });

    THIS.update_data_dids();

    $('#popup').dialog('close');
});

$('#selected-numbers').on('click', '.remove-did', function() {
    var nb_dids = $('#amount_numbers').attr('data-number') || 0;

    $(this).parents('.number-line').first().remove();

    THIS.update_data_dids();
});

$('.topic-title').on('click', function() {
    $('.topic-wrapper').hide();
    $('.topic-title').removeClass('active');
    $(this).addClass('active');

    $('.topic-wrapper[data-id="'+$(this).data('id')+'"]').show();
});

$('#blog-section').on('click', '.gf-title a', function(e) {
    e.preventDefault();
    window.open($(this).attr('href'));
});

/* home page Make a Test Call interaction flow */

$(document).ready(function() {
    $('#credentials-slide').delay(1500).animate({paddingLeft:'302px'});
    $('#rate-slide').delay(1500).animate({paddingLeft:'320px'});

    //generated credentials on index.html
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "http://10.10.2.218:8888/trunking_io/api/tempaccount/credentials",
        contentType: "application/json",
        crossDomain: true
    }).done(function(msg){
        $('#generated-user').html(msg.data.username);
        $('#generated-password').html(msg.data.password);
        $('#ip-number').html(msg.data.ip);
    });

    //credentials registered
    var thisInterval = setInterval(function(){
        $.ajax({
            type: "GET",
            url: "http://10.10.2.221:8888/trunking_io/api/tempaccount/registered",
            dataType: "json",
            contentType: "application/json"
        }).done(function(msg){
            if(msg.data.registered == '1'){
                $('#step1-credentials').hide();
                $('#testcall-step1').removeClass('step-active');
                $('#testcall-step2').addClass('step-active');
                $('#step2-makeCall').show();

                $('#countdown-slide').delay(2000).animate({paddingLeft:'312px'},400, "swing", function(){
                    $.ajax({
                        type: "GET",
                        url: "http://10.10.2.221:8888/trunking_io/api/tempaccount/remaining",
                        dataType: "json",
                        contentType: "application/json"
                    }).done(function(msg){
                        console.log(msg.data.remaining_seconds);
                    });
                });
                clearInterval(thisInterval);
            }
        });
    },5000);

    //time remaining countdown
});

/*$(document).ready(function() {
    $('#rate').jRating({
        step:true,
        length: 5, // nb of stars
        decimalLength: 0, // number of decimal in the rate
        showRateInfo: false // no numbers shown on hover
    });
});*/


