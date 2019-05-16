/*
	Blue Commonwealth Gala Donation Display Screen
	Democratic Party of Virginia
	Coded by Ricardo Alfaro
	May 2019	
*/

// Get hostname
var hostname = document.location.hostname;
var listenerUrl = 'https://'+hostname+'/wp-json/bcg/v1/endpoint/';

// Refresh interval of 10 seconds
var interval = 10000;
var contributionForm = '';
var contributionForm = '';
var alternateContributionForm = '';

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
        url: listenerUrl,
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
                alternateContributionForm = data[0].alternate_actblue_contribution_form;
                contributionGoal = data[0].goal;
                $("#displayGoal").text('$' + commaSeparateNumber(data[0].goal));
                var payload = {
                    functionname: 'getDonors',
                    contributionForm: contributionForm,
                    alternateContributionForm: alternateContributionForm
                };
                jsonProcessor(getDonors, payload);
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
            var donorHtml = '';
            $.each(data, function (index, value) {
	            if(value.contribution_form == alternateContributionForm)
	            {
		        	donorHtml = donorHtml +
                    '<div class="col-md-4">' +
                    '<h3>Anonymous</h3>' +
                    '</div>';   
	            } else {
		            donorHtml = donorHtml +
                    '<div class="col-md-4">' +
                    '<h3>' + value.firstname + ' ' + value.lastname + '</h3>' +
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
        url: listenerUrl,
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