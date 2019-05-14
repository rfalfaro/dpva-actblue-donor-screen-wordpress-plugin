/*
	Blue Commonwealth Gala Donation Display Screen
	Democratic Party of Virginia
	Coded by Ricardo Alfaro
	May 2019	
*/

// Refresh interval of 20 seconds
var interval = 20000;
var contributionForm = '';
var contributionGoal = '';

initialize();

function initialize() {
    var payload = {
        functionname: 'getSettings'
    };
    jsonProcessor(getSettings, payload);
}

function getSettings(jsonPayload) {
    $.ajax({
        type: "POST",
        url: 'https://bcgdemo.vayd.org/wp-json/bcg/v1/endpoint/',
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        success: function (data) {
            if (data.length == 0) {
                $("#displayTitle").text("You need to setup the ActBlue parameters before continuing");
                $(".progress").hide();
            } else {
                $("#displayTitle").text(data[0].title);
                contributionForm = data[0].actblue_contribution_form;
                contributionGoal = data[0].goal;
                $("#displayGoal").text('$' + commaSeparateNumber(data[0].goal));
                var payload = {
                    functionname: 'getDonors',
                    contributionForm: contributionForm
                };
                jsonProcessor(getDonors, payload);
            }            
        }
    });
}

function getDonors(jsonPayload) {
    $.ajax({
        type: "POST",
        url: 'https://bcgdemo.vayd.org/wp-json/bcg/v1/endpoint/',
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        beforeSend: function () {

        },
        success: function (data) {
            var donorHtml = '';
            $.each(data, function (index, value) {
                donorHtml = donorHtml +
                    '<div class="col-md-4">' +
                    '<h3>' + value.firstname + ' ' + value.lastname + '</h3>' +
                    '</div>';
            });
            $("#displayLatestDonors").html(donorHtml);
            // get total calculation
            var payload = {
                functionname: 'getTotal',
                contributionForm: contributionForm
            };
            jsonProcessor(getTotal, payload);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        },
        complete: function () {
            // Trigger function again per each interval
            setTimeout(function () { getDonors(jsonPayload); }, interval);
        }
    });
}

function getTotal(jsonPayload) {
    $.ajax({
        type: "POST",
        url: 'https://bcgdemo.vayd.org/wp-json/bcg/v1/endpoint/',
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        success: function (data) {
            var progress = '';
            if (data.total_amount == null) {
                $("#displayTotal").text('$0.00');
                progress = 0;
            } else {
                var current = data.total_amount.replace(/,/g, '');
                $("#displayTotal").text('$' + data.total_amount);
                progress = Math.round(current / contributionGoal * 100);
            }            
            if (progress <= 100) {
                $(".progress-bar").css('width', progress + '%');
            } else {
                $(".progress-bar").css('width', '100%');
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}

function commaSeparateNumber(val) {
    while (/(\d+)(\d{3})/.test(val.toString())) {
        val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
    }
    return val;
}

function jsonProcessor(callback, payload) {
    var jsonPayload = JSON.stringify(payload);
    callback(jsonPayload);
}