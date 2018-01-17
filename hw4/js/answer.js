var check_clearMap = -1;
var trainstationSourcef = null;
var buffersize = $('#Buffersize').val();
var markerSource = new ol.source.Vector({});
var trainstationSource = null;
var AllMap = iniMap();

showClickPoint();


function iniMap() {

    var view = new ol.View({
        center: ol.proj.transform([121.0001, 23.5], 'EPSG:4326', 'EPSG:3857'),
        zoom: 7
    });

    var source = new ol.source.Vector();
    var baseLayer = new ol.layer.Tile({
        source: new ol.source.OSM()
    });
    var layer = new ol.layer.Vector({
        source: source
    });
    var map = new ol.Map({
        target: 'mapa',
        controls: ol.control.defaults().extend([
            new ol.control.ScaleLine(),
            new ol.control.ZoomSlider()
        ]),
        renderer: 'canvas',
        layers: [baseLayer],
        view: view
    });

    map.addLayer(layer);

    return map;

}


function showClickPoint() {


    AllMap.on('click', function(evt) {
        if(check_clearMap >= 0){
        	markerSource.clear();

        }

        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
        var lng = lonlat[0];
        var lat = lonlat[1];

        var iconFeature = new ol.Feature({
            name: "icon",
            geometry: new ol.geom.Point(ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857')),
        });


        var iconStyle = new ol.style.Style({
            image: new ol.style.Icon(({
                scale: 0.4,
                anchor: [0.5, 1],
                anchorXUnits: 'fraction',
                anchorYUnits: 'fraction',
                src: 'icon/allow.png'
            })),
        });

        markerSource.addFeature(iconFeature);


         markerLayer = new ol.layer.Vector({
            source: markerSource,
            style: iconStyle
        });


        /*You should check the query ragne size before you query the train station*/
        if(!buffersize){
            $('.item').remove();
            var tr = $("<tr class=\"item\"></tr>");
            var td1 = $("<td colspan=\"3\"></td>").text("請先輸入搜尋範圍");
            tr.append(td1);
            $('.station').append(tr);
        }else{
            AllMap.addLayer(markerLayer);
            queryTrainsation(lat,lng,buffersize); // you should send lng,lat and query ragne size to this function
        }

    });

}

function showTrainstationPoint(trainstation) {


    trainstation_geojson = (new ol.format.GeoJSON()).readFeatures(trainstation, { dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857' })

    trainstationSourcef = new ol.source.Vector({
        features: trainstation_geojson
    });


     trainstationSource = new ol.layer.Vector({


        source: trainstationSourcef,

    })



    AllMap.addLayer(trainstationSource);

}

/*Write your code */
//function query() {

function queryTrainsation(lat, lng, buffersize){
	  // Clean previous search results of station points
    if (check_clearMap > 0) {
         trainstationSourcef.clear();
         AllMap.removeLayer(trainstationSource);

      }

    //Please write your ajax code here,use POST or Get type

    xmlhttp=new XMLHttpRequest();

    xmlhttp.onreadystatechange=function(){
      if (xmlhttp.status!==200){
          check_clearMap = 0;
      }else if (xmlhttp.readyState===4 && xmlhttp.status===200){
          var datas = JSON.parse(xmlhttp.responseText);
          showTrainstationPoint(datas.landmarkna);
          showtable(datas) ;
          check_clearMap = 1;
      }
    }
    xmlhttp.open("POST","result.php");
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("lat="+lat+"&lng="+lng+"&Buffersize="+buffersize);

}


function showtable(datas) {
  $('.item').remove();
  if(datas.length > 0){
      $.map(datas.landmarkna.features, function(value, index) {
          var tr = $("<tr class=\"item\"></tr>");
          var td1 = $("<td></td>").text(value.properties.landmarkna);
          var td2 = $("<td></td>").text(value.properties.address);
          var td3 = $("<td></td>").text(value.properties.distance);
          tr.append(td1,td2,td3);
          $('.station').append(tr);
      });
    }else{
      var tr = $("<tr class=\"item\"></tr>");
      var td1 = $("<td colspan=\"3\"></td>").text("搜尋範圍內無車站");
      tr.append(td1);
      $('.station').append(tr);
    }
}
