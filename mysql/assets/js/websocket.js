// var wsURL = "wss://spsonline.co.uk/websocket.php";
var wsURL = "ws://192.168.133.138:2003/websocket.php";

var websocket = new WebSocket(wsURL);

websocket.onopen = function(ev) { // connection is open 
    console.log('Socket Connected!'); //notify user
}
// Message received from server
websocket.onmessage = function(ev) {
    var response = JSON.parse(ev.data); //PHP sends Json data
    if(response.type == 'update'){
        var selected = $('#date_picker').val().split("-");
        read_excel_data(selected[2] + "-" + selected[1] + "-" + selected[0]);
        finalData();
    }
};

websocket.onerror = function(ev) {
    console.log('Error Occurred - ' , ev.data);
};
websocket.onclose = function(ev) {
    console.log('Connection Closed');
};