function initxhr() {
    // адрес сервера
    var str = 'http://127.0.0.1/test.php';
    var elem = document.getElementById("elem");
    var text = document.getElementById("text");
    var send = document.getElementById("send");

    var _xhr = new XMLHttpRequest();
    send.onclick = function () {
        if (text.value !== 'undefined' && text.value !== '') {
            _xhr.open('POST', str, true);
            _xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		    _xhr.onreadystatechange = function () {
                if (_xhr.readyState === 4) {
                    if (_xhr.status === 200) {
                        var json = JSON.parse(decodeURIComponent(_xhr.responseText));

                        elem.value = '';
                        if (json.error !== undefined)
                            console.log('Ошибка: ' + json.error);
                        else {
                            console.log('данные успешно получены');
                            for(var param in json) {
                                elem.value += json[param] + '\r\n'; 
                            }
                        }
                    }
                }
            };
            _xhr.send('value=' + encodeURIComponent(text.value));
        }
    };
}
