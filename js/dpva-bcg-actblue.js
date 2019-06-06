/*
	Blue Commonwealth Gala Donation Display Screen
	Democratic Party of Virginia
	Coded by Ricardo Alfaro
	May 2019	
*/

// Get hostname
var hostname = document.location.hostname;
var listenerUrl = 'https://'+hostname+'/wp-json/bcg/v1/endpoint/';

// Refresh interval of 5 seconds
var interval = 5000;
var contributionForm = '';
var contributionForm = '';
var alternateContributionForm = '';

initialize();

function initialize() {
    var payload = {
        functionname: 'getStatus'
    };
    jsonProcessor(getStatus, payload);
}

function getStatus(jsonPayload) {
    $.ajax({
        type: "POST",
        url: listenerUrl,
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        success: function (data) {
            console.log(data);
            var payload = {
                functionname: 'getSettings'
            };
            jsonProcessor(getSettings, payload);
            if (data[0].active_fl == '1') {
                var payload = {
                    functionname: 'getDonors',
                    contributionForm: contributionForm,
                    alternateContributionForm: alternateContributionForm
                };
                jsonProcessor(getDonors, payload);
            } else {
                $("#displayLatestDonors").html('');
                $("#displayTotal").text('$0.00');
                $(".progress-bar").css('width', 0 + '%');
            }
        },
        complete: function () {
            // Trigger function again per each interval
            setTimeout(function () { getStatus(jsonPayload); }, interval);
        }
    });
}

function getSettings(jsonPayload) {
    $.ajax({
        type: "POST",
        url: listenerUrl,
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        success: function (data) {
            if (data.length === 0) {
                $("#displayTitle").text("You need to setup the ActBlue parameters before continuing");
                $(".progress").hide();
            } else {
                $("#displayTitle").text(data[0].title.replace(/\\"/g, '"'));
                contributionForm = data[0].actblue_contribution_form;
                alternateContributionForm = data[0].alternate_actblue_contribution_form;
                contributionGoal = data[0].goal;
                $("#displayGoal").text('$' + commaSeparateNumber(data[0].goal));
                $("#displayDisclaimer").text(data[0].disclaimer.replace(/\\"/g, '"'));
            }            
        }
    });
}

function getDonors(jsonPayload) {
    $.ajax({
        type: "POST",
        url: listenerUrl,
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        beforeSend: function () {

        },
        success: function (data) {
            processDonors(data);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}

function processDonors(data) {
    var donorHtml = '';
    $.each(data, function (index, value) {
        if (value.contribution_form === alternateContributionForm) {
            donorHtml = donorHtml +
                '<div class="col-md-4">' +
                '<h2>Anonymous</h2>' +
                '</div>';
        } else {
            donorHtml = donorHtml +
                '<div class="col-md-4">' +
                '<h2>' + value.firstname + ' ' + value.lastname + '</h2>' +
                '</div>';
        }
    });
    $("#displayLatestDonors").html(donorHtml);
    // get total calculation
    var payload = {
        functionname: 'getTotal',
        contributionForm: contributionForm,
        alternateContributionForm: alternateContributionForm
    };
    jsonProcessor(getTotal, payload);
}

function getTotal(jsonPayload) {
    $.ajax({
        type: "POST",
        url: listenerUrl,
        dataType: 'json',
        data: jsonPayload,
        contentType: 'application/json',
        success: function (data) {
            processTotal(data);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
            console.log(errorThrown);
        }
    });
}

function processTotal(data) {
    var progress = '';
    if (data.total_amount === null) {
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