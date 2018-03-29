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
    padData = pad.innerHTML;

pad.addEventListener('keyup', function() {

    console.log('change');

    clearTimeout(userWriting);

    // start timeOut that waits 5 seconds before saving if there are changes
    userWriting = setTimeout(function() {

        console.log('done writing attempting to save');

        let curData = pad.innerHTML;
        if (padData != curData) {
            // save
            postAjax('/webpad/php/update-entry.php', 'pad_name=' + padName + '&entry='+curData, function(data) {
                
                console.log('save attempted');

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