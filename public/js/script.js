// Wialon site dns
var dns = "https://hosting.wialon.com";

// Main function
function getToken() {
    // construct login page URL
    var url = dns + "/login.html"; // your site DNS + "/login.html"
    url += "?client_id=" + "App";	// your application name
    url += "&access_type=" + 0x100;	// access level, 0x100 = "Online tracking only"
    url += "&activation_time=" + 0;	// activation time, 0 = immediately; you can pass any UNIX time value
    url += "&duration=" + 604800;	// duration, 604800 = one week in seconds
    url += "&flags=" + 0x1;			// options, 0x1 = add username in response

    url += "&redirect_uri=" + dns + "/post_token.html"; // if login succeed - redirect to this page

    // listen message with token from login page window
    window.addEventListener("message", tokenRecieved);

    // finally, open login page in new window
    window.open(url, "_blank", "width=760, height=500, top=300, left=500");
}

// Help function
function tokenRecieved(e) {
    // get message from login window
    var msg = e.data;
    if (typeof msg == "string" && msg.indexOf("access_token=") >= 0) {
        // get token
        var token = msg.replace("access_token=", "");
        localStorage.setItem("token", token);
        // now we can use token, e.g show it on page
        document.getElementById("token").innerHTML = token;
        document.getElementById("login").setAttribute("disabled", "");

        // or login to wialon using our token
        wialon.core.Session.getInstance().initSession("https://hst-api.wialon.com");

        wialon.core.Session.getInstance().loginToken(token, "", function(code) {
            if (code)
                return;
            var user = wialon.core.Session.getInstance().getCurrUser().getName();
            alert("Authorized as " + user);
        });

        // remove "message" event listener
        window.removeEventListener("message", tokenRecieved);

        location.reload();
    }
}

function logout() {
    var sess = wialon.core.Session.getInstance();
    if (sess && sess.getId()) {
        sess.logout(function() {
            document.getElementById("logout").setAttribute("disabled", "");
            document.getElementById("login").removeAttribute("disabled");
        });
    }
}

function checkToken() {
    var storedValue = localStorage.getItem("token");
    alert(storedValue);
}

// Print message to log
function msg(text) { $("#log").prepend(text + "<br/>"); }

function init() { // Execute after login succeed
    var sess = wialon.core.Session.getInstance(); // get instance of current Session
    // flags to specify what kind of data should be returned
    var flags = wialon.item.Item.dataFlag.base | wialon.item.Unit.dataFlag.lastMessage,
        res_flags =  wialon.util.Number.or(wialon.item.Item.dataFlag.base, wialon.item.Resource.dataFlag.drivers, wialon.item.Resource.dataFlag.zones);

    // load Geofences Library
    sess.loadLibrary("resourceDrivers");
    sess.loadLibrary("itemIcon");
    sess.loadLibrary("resourceZones");

    sess.updateDataFlags( // load items to current session
        [{type: "type", data: "avl_unit", flags: flags, mode: 0},
            {type: "type", data: "avl_resource", flags: res_flags, mode: 0}], // Items specification
        function (code) { // updateDataFlags callback
            if (code) { msg(wialon.core.Errors.getErrorText(code)); return; } // exit if error code

            // get loaded 'avl_unit's items
            var units = sess.getItems("avl_unit");
            console.log(units);
            if (!units || !units.length){ msg("Units not found"); return; } // check if units found

            var list_unit = new Array();

            var countSpan = 0;

            for (var i = 0; i< units.length; i++){ // construct Select object using found units
                list_unit[i] = new Object();

                var unit = units[i]; // current unit in cycle

                list_unit[i].id = unit.getId();
                list_unit[i].name = unit.getName();

                var pos = unit.getPosition();

                if(pos){ // check if position data exists
                    var time = wialon.util.DateTime.formatTime(pos.t);

                    list_unit[i].time = time;
                    list_unit[i].pos_x = pos.x;
                    list_unit[i].pos_y = pos.y;
                    list_unit[i].position = 'unknown';

                    wialon.util.Gis.getLocations([{lon:pos.x, lat:pos.y}], function(code, address){
                        if (code) { msg(wialon.core.Errors.getErrorText(code)); return; } // exit if error code

                        countSpan++;
                        console.log(countSpan);

                        if (countSpan > 70){
                            document.getElementById("create-list").removeAttribute("disabled");
                        }
                        $("#location").append("<span>"+ address+"</span>");
                    });
                }
            }


            // get loaded 'avl_resource's items
            var ress = sess.getItems("avl_resource"); // get loaded 'avl_resource's items
            if (!ress || !ress.length){ msg("Resours not found"); return; }

            var all_drivers = {};

            var drivers = ress[0].getDrivers();

            var res = wialon.core.Session.getInstance().getItem(ress[0].getId());

            console.log(res);

            var zones = res.getZones();

            console.log(zones);

            for (var drv in drivers) {
                driver = drivers[drv]; // iterate all drivers

                d_obj = {
                    id: driver.id,
                    name: driver.n,
                    icon: ress[0].getDriverImageUrl(driver, 32)
                };

                if (!all_drivers[driver.bu])
                    all_drivers[driver.bu] = [d_obj];
                else
                    all_drivers[driver.bu].push(d_obj);
            }
            console.log(all_drivers);


            for (var i = 0; i < list_unit.length; i++){
                var u_id = list_unit[i].id;


                if (typeof all_drivers[u_id] != "undefined") { // check, bind driver to unit or no
                    array = all_drivers[u_id]; // read regular array of drivers

                    d_obj = array[0];

                    list_unit[i].driver = d_obj.name;
                }else {
                    list_unit[i].driver = '';
                }
            }




            console.log(list_unit);

            localStorage.setItem("zones", JSON.stringify(zones));

            localStorage.setItem("list_units", JSON.stringify(list_unit));
        }
    );
}

function refreshPage() {
    window.location.reload();
}

function getList() {
    var unit = JSON.parse(localStorage.getItem("list_units"));

    var zones = JSON.parse(localStorage.getItem("zones"));

    console.log(zones);

    var div = document.getElementById("location");
    var spans = div.getElementsByTagName("span");

    for(i=0;i<spans.length;i++)
    {
        unit[i].position = spans[i].innerHTML
    }

    for (i = 0 ; i < unit.length ; i++){
        unit[i].geofence = '';
        for (var k in zones){
            if (unit[i].position == zones[k].d){
                unit[i].geofence = zones[k].n;
            }
        }
    }

    for (i = 0 ; i < unit.length ; i++){
        row = "";

        row += "<tr>";
        row += "<td>" + (i+1) + "</td>";
        row += "<td>" + unit[i].time + "</td>";
        row += "<td>" + unit[i].name + "</td>";

        if (unit[i].geofence != ''){
            row += "<td>" + unit[i].geofence + "</td>";
        }else {
            row += "<td>-</td>";
        }

        /*row += "<td>SOON</td>";*/

        row += "<td>" + unit[i].position + "</td>";


        if (unit[i].geofence != ''){
            if (unit[i].geofence == 'Indorama Polypet'){
                row += "<td>PTIP</td>";
            }else if (unit[i].geofence == 'IRS' || unit[i].geofence == 'IVI' || unit[i].geofence == 'IPCI'){
                row += "<td>CUST</td>";
            }else {
                row += "<td><input id='status"+i+"' type='text' class='form-control status-field'><span id='status-display"+i+"' class='hidden'></span></td>";
            }
        }else {
            row += "<td><input id='status"+i+"' type='text' class='form-control status-field'><span id='status-display"+i+"' class='hidden'></span></td>";
        }

        row += "<td><input id='muat"+i+"' type='text' class='form-control muat-field'><span id='muat-display"+i+"' class='hidden'></span></td>";

        row += "</tr>";

        $("#unit-list").append(row); // append formating row
    }

    console.log(unit);
}

// execute when DOM ready
$(document).ready(function () {
    var sess = wialon.core.Session.getInstance().initSession("https://hst-api.wialon.com"); // init session
    // For more info about how to generate token check
    // http://sdk.wialon.com/playground/demo/app_auth_token
    console.log(sess);

    var storedToken = '';

    storedToken = localStorage.getItem("token");

    console.log(storedToken);
    wialon.core.Session.getInstance().loginToken(storedToken, "", // try to login
        function (code) { // login callback
            // if error code - print error message
            if (code){
                msg(wialon.core.Errors.getErrorText(code));
                return;
            }

            msg("Logged successfully"); init(); // when login suceed then run init() function
        });

    document.getElementById("token").innerHTML = storedToken;



    if (storedToken != null){
        $('.unauthorized').hide();
        $('.authorized').show();
    }

    function exportTableToCSV($table, filename) {

        var $rows = $table.find('tr:has(td),tr:has(th)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',

            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                    var $row = $(row), $cols = $row.find('td,th');

                    return $cols.map(function (j, col) {
                        var $col = $(col), text = $col.text();

                        return text.replace(/"/g, '""'); // escape double quotes

                    }).get().join(tmpColDelim);

                }).get().join(tmpRowDelim)
                    .split(tmpRowDelim).join(rowDelim)
                    .split(tmpColDelim).join(colDelim) + '"',



            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        console.log(csv);

        if (window.navigator.msSaveBlob) { // IE 10+
            //alert('IE' + csv);
            window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
        }
        else {
            $(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' });
        }
    }

    function genereateSpanFromInput() {
        var unit = JSON.parse(localStorage.getItem("list_units"));

        for (i = 0 ; i < unit.length ; i++){
            var display_id = "#status-display"+i;
            var input_id = "#status"+i;
            var input_value = $(input_id).val();

            if (input_value == undefined){
                input_value = '';
            }

            $(display_id).text(input_value);


            var display_id_2 = "#muat-display"+i;
            var input_id_2 = "#muat"+i;
            var input_value_2 = $(input_id_2).val();

            if (input_value_2 == undefined){
                input_value_2 = '';
            }

            $(display_id_2).text(input_value_2);
        }

        exportTableToCSV.apply(this, [$('#data-table'), 'export.csv']);
    }

    // This must be a hyperlink
    $("#download").on('click', function (event) {

        genereateSpanFromInput();

        exportTableToCSV.apply(this, [$('#data-table'), 'export.csv']);
        /*exportTableToCSV.apply(this, [$('#data-table'), 'export.csv']);*/

        // IF CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });

    $('#create-list').on('click', function (event) {
        document.getElementById("create-list").setAttribute("disabled", "");
        document.getElementById("download").removeAttribute("disabled");
    })

    $('#refresh').on('click', function (event) {
        $('#refresh i').addClass('fa-spin');
    })


    /*$("#test-field").on("input", function() {
        var span = "#"+this.id+" #test-display";
        var text = $(this).val()

        $("#test-display").text(text); // <= Change on this line
    });*/

});





