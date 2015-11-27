function getServerAddress() {
    var method = 'GET';
    var url = 'serverInfo.php';

    var xhttp = new XMLHttpRequest();
    xhttp.open(method, url, false);
    xhttp.send();
    return xhttp.responseText;
}