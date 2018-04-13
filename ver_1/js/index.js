// postAjax from MDN
function postAjax(url, data, success) {
    var params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
        ).join('&');
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}

const notifications = document.getElementById('notifications'),
      pad = document.getElementById('editable-container'),
      padName = window.location.href.split('webpad/')[1];

function postNotification(notification) {
    // set notifications and update
    notifications.innerText = notification;
    notifications.style.display = 'flex';

    // hide
    setTimeout(function() {
        notifications.innerText = '';
        notifications.style.display = 'none';
    }, 1000);
}

let userWriting,
    padData = pad.value;

function addZero(number) {
    if (number < 10) {
        return '0' + number;
    }
    else {
	return number;   
    }
}

function getFormattedDate() {
    let date = new Date(),
            hour = date.getHours(),
        period = hour < 12 ? 'AM' : 'PM',
        dateStr = (addZero(date.getMonth() + 1)) + "-" + addZero(date.getDate()) + "-" + date.getFullYear() + " " + addZero(date.getHours()) + ":" + addZero(date.getMinutes()) + " " + period + "\r\n\r\n";

    return dateStr;
}

function checkForChange(padContent) {

    clearTimeout(userWriting);
	
    // quick command to get a date time, contributed by /u/cutety from Reddit
    if (padContent.value.indexOf('!dt') !== -1) {
        let curStart = pad.selectionStart; // this is for making sure the cursor goes back to the line it replaced, actually after with "\r\n"
        pad.value = padContent.value.replace(/!dt/g, getFormattedDate());
        pad.selectionEnd = curStart + 18; // 18 is the length of the date time string
    }

    // start timeOut that waits 5 seconds before saving if there are changes
    userWriting = setTimeout(function() {
	    
        let curData = pad.value;
        if (padData != curData) {
            // save
            postAjax('/webpad/php/update-entry.php', 'pad_name=' + padName + '&entry='+curData, function(data) {

                if (data == 'ok') {
                    postNotification('Saved');
                }
                else if (data == 'fail') {
                    postNotification('Failed to save');
                }

                // update padData
                padData = curData;

            });
        }

    }, 1000);
	
}

pad.addEventListener('keyup', function() {
    let padContent = this;
    checkForChange(padContent);
});

pad.addEventListener('paste', function() {
    let padContent = this;
    checkForChange(padContent);
});
