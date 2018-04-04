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

pad.addEventListener('keyup', function() {

    clearTimeout(userWriting);

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

    }, 3000);

});

// quick command to get a date time, contributed by /u/cutety from Reddit
pad.addEventListener("keyup", function(evnt) {
	keyupInput.value = this.value.replace(/!dt/g, "04-03-2018 10:48 PM");
}, false);
