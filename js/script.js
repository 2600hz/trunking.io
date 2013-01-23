// JavaScript Document
var THIS = this;

var update_data_trunks = function(trunks_amount) {
    var twoway_price = 29.99,
        total_amount_twoway = (trunks_amount - parseInt($('#amount_twoway_trunks').attr('data-number') || 0)) * twoway_price,
        current_price = parseFloat($('#totalcost-number').attr('data-price')),
        total_price = (current_price + total_amount_twoway).toFixed(2);

    $('#slider-amount').html(trunks_amount);
    $('#amount_twoway_trunks').html(trunks_amount)
                              .attr('data-number', trunks_amount);

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);
};

var update_data_dids = function(trunks_amount) {
    var price_did = 1.00,
        current_price = parseFloat($('#totalcost-number').attr('data-price')),
        total_dids = (trunks_amount - parseInt($('#amount_numbers').attr('data-number') || 0))* price_did,
        total_price = (total_dids + current_price).toFixed(2);

    $('#totalcost-number').html('$'+ total_price)
                          .attr('data-price', total_price);

    $('#number-amount').html(trunks_amount);
    $('#amount_numbers').html(trunks_amount)
                        .attr('data-number', trunks_amount);
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
    value: 0,
    slide: function( event, ui ) {
        THIS.update_data_trunks(ui.value);
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
        console.log(v);
        $('.number-wrapper input[type="checkbox"][data-phone_number="'+ $(this).data('phone_number') +'"]').attr('checked', true);
    });
});

$('#search-numbers').on('click', function() {
    $('#results').slideDown();
});

$('#submit_numbers').on('click', function() {
    var format_phone_number = function(phone_number) {
            if(phone_number.substr(0,2) === "+1" && phone_number.length === 12) {
                phone_number = phone_number.replace(/(\+1)(\d{3})(\d{3})(\d{4})/, '$1 ($2) $3-$4');
            }

            return phone_number;
        },
        list_numbers = [];

    $('.number-wrapper input[type="checkbox"]:checked').each(function() {
        list_numbers.push($(this).data('phone_number'));
    });

    $('#selected-numbers').empty();

    $.each(list_numbers, function(k, v) {
        $('#selected-numbers').append('<li class="number-line">'+format_phone_number(v)+'<a class="remove-did" data-phone_number="'+v+'" href="#">remove</a></li>');
    });

    THIS.update_data_dids(list_numbers.length);

    $('#popup').dialog('close');
});

$('#selected-numbers').on('click', '.remove-did', function() {
    var nb_dids = $('#amount_numbers').attr('data-number') || 0;

    $(this).parents('.number-line').first().remove();

    THIS.update_data_dids(--nb_dids);
});

$('#signup').on('click', function() {
    $('#subscribe-wrapper').show();
});
