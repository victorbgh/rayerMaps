<?php
    session_start();
    if(!isset($_SESSION['loggedIN'])){
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Rayer Maps - Mapa</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700?v=0.6" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i" rel="stylesheet">
    <link href="css/third/bootstrap.css" rel="stylesheet">
    <link href="css/third/fontawesome-all.css" rel="stylesheet">
    <link href="css/third/swiper.css" rel="stylesheet">
    <link rel="stylesheet" href="css/third/owl.carousel.min.css">
    <link rel="stylesheet" href="css/third/owl.theme.default.min.css">
    <link href="css/third/magnific-popup.css" rel="stylesheet">
    <link href="css/ours/styles.css" rel="stylesheet">

    <link rel="icon" href="images/faviconCMA.ico">

  </head>

  <body data-spy="scroll" data-target=".fixed-top">

    <div class="spinner-wrapper">
      <div class="spinner">
          <div class="bounce1"></div>
          <div class="bounce2"></div>
          <div class="bounce3"></div>
      </div>
    </div>

    <div id="header" class="navtop"></div>

    <nav class="navbar navbar-expand-md navbar-dark navbar-custom fixed-top ">
    <!-- <a class="navbar-brand logo-image" href="index.php"><img src="images/logo-transparente.png"
            alt="alternative" height="auto" width="180"></a> -->
            <a class="navbar-brand logo-image" href="index.php" style="color: #000 !important; text-decoration: none;">Sis<span style="color:green;">Coleta</span></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#cma-navbars"
          aria-controls="cma-navbars" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-awesome fas fa-bars"></span>
          <span class="navbar-toggler-awesome fas fa-times"></span>
      </button>
      <div class="collapse navbar-collapse" id="cma-navbars">
          <ul class="navbar-nav ml-auto">
              <li class="nav-item">
                  <a class="nav-link page-scroll" href="index.php">Inicio </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link page-scroll" href="cad-coleta.php">Cadastrar local de coleta </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link page-scroll" href="maps.php">Mapa <span class="sr-only">(current)</span></a>
              </li>
          </ul>
          <ul class="navbar-nav ml-auto">
              <li class="nav-item dropdown">
                  <a style="color: #000 !important;" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-user"></i> Victor</a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      <a class="dropdown-item" href="#"><i class="fa fa-cog"></i> Conta</a>
                      <a class="dropdown-item" data-toggle="modal" data-target="#exampleModalLong" href="" onclick="confirmeSair()"><i class="fa fa-sign-out-alt"></i> Sair</a>
                  </div>
              </li>
          </ul>
        </div>
    </nav>

    <div id="map"></div>



    <div class="copyright">
      <div class="container">
          <div class="row">
              <div class="col-lg-12">
                  <p class="p-small">© Copyright SisColeta 2019 Todos os direitos reservados</p>
              </div>
          </div>
      </div>
    </div>

    <script>
      var customLabel = {
        restaurant: {
          label: 'R'
        },
        bar: {
          label: 'B'
        },
        // Lixo Eletrônico: {
        //   label: 'LE'
        // },
        // Lixo Hospitalar: {
        //   label: 'LH'
        // },
        // Lixo Reciclável: {
        //   label: 'LR'
        // },
        // Lixo Industrial: {
        //   label: 'LI'
        // },

      };

      var map, infoWindow;
      var posicaoUsuario;
      var inicio = new google.maps.LatLng(-15.81443, -47.88816130000001);
      var fim = new google.maps.LatLng(-15.814179, -47.903618);
      // NOTE: Calcula a distancia
      function haversine_distance(mk1, mk2) {
        var R = 3958.8; // Radius of the Earth in miles
        var rlat1 = mk1.position.lat() * (Math.PI/180); // Convert degrees to radians
        var rlat2 = mk2.position.lat() * (Math.PI/180); // Convert degrees to radians
        var difflat = rlat2-rlat1; // Radian difference (latitudes)
        var difflon = (mk2.position.lng()-mk1.position.lng()) * (Math.PI/180); // Radian difference (longitudes)

        var d = 2 * R * Math.asin(Math.sqrt(Math.sin(difflat/2)*Math.sin(difflat/2)+Math.cos(rlat1)*Math.cos(rlat2)*Math.sin(difflon/2)*Math.sin(difflon/2)));
        return d;
      }
      // NOTE: EXEMPLO DE USO
      // const dakota = {lat: 40.7767644, lng: -73.9761399};
      // const frick = {lat: 40.771209, lng: -73.9673991};
      // The markers for The Dakota and The Frick Collection
      // var mk1 = new google.maps.Marker({position: dakota, map: map});
      // var mk2 = new google.maps.Marker({position: frick, map: map});
      // var line = new google.maps.Polyline({path: [dakota, frick], map: map});
      // var distance = haversine_distance(mk1, mk2);


      function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var directionsRenderer = new google.maps.DirectionsRenderer;
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -15.814432199999997, lng: -47.888157299999996},
          zoom: 13
      });
      infoWindow = new google.maps.InfoWindow;

        // Try HTML5 geolocation.
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };
            posicaoUsuario = pos;
            // infoWindow.setPosition(pos);
            var image = 'http://i.stack.imgur.com/orZ4x.png';
            var marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    icon: image
                });
                directionsRenderer.setMap(map);
            var infowincontent = document.createElement('div');
            var strong = document.createElement('strong');
            strong.textContent = 'Voce está aqui'
            infowincontent.appendChild(strong);
            infowincontent.appendChild(document.createElement('br'));
            marker.addListener('click', function() {

              infoWindow.setContent(infowincontent);
              infoWindow.open(map, marker);
            });
            infoWindow.open(map);
            map.setCenter(pos);
          }, function() {
            handleLocationError(true, infoWindow, map.getCenter());
          });
        } else {
          // Browser doesn't support Geolocation
          handleLocationError(false, infoWindow, map.getCenter());
        }
      


      function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'Error: The Geolocation service failed.' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
      }

        downloadUrl('php/marcadores.php', function(data) {
          var xml = data.responseXML;
          var markers = xml.documentElement.getElementsByTagName('marker');
          Array.prototype.forEach.call(markers, function(markerElem) {
            var id = markerElem.getAttribute('id');
            var name = markerElem.getAttribute('name');
            var address = markerElem.getAttribute('address');
            var type = markerElem.getAttribute('type');
            var point = new google.maps.LatLng(
                parseFloat(markerElem.getAttribute('lat')),
                parseFloat(markerElem.getAttribute('lng')));

            var infowincontent = document.createElement('div');
            var strong = document.createElement('h5');
            strong.textContent = name
            infowincontent.appendChild(strong);
            infowincontent.appendChild(document.createElement('br'));

            var text = document.createElement('text');
            text.textContent = address
            infowincontent.appendChild(text);
            infowincontent.appendChild(document.createElement('br'));
            var rotas = document.createElement('a');
            rotas.setAttribute('class', 'aClass');

            rotas.setAttribute('tittle', 'Rotas para o local');
            rotas.textContent = 'Rotas';
            infowincontent.appendChild(rotas);
            infowincontent.appendChild(document.createElement('br'));
            var compartilharTexto = document.createElement('a');
            compartilharTexto.setAttribute('href', 'whatsapp://send?text=TITULO &ndash; LINK');
            compartilharTexto.setAttribute('tittle', 'Acesse de seu smartphone para enviar por WhatsApp');
            compartilharTexto.textContent = 'Compartilhar';
            infowincontent.appendChild(compartilharTexto);
            var icon = customLabel[type] || {};
            var marker = new google.maps.Marker({
              map: map,
              position: point,
              label: icon.label
            });
            marker.addListener('click', function() {
              infoWindow.setContent(infowincontent);
              infoWindow.open(map, marker);
            });
          });
        });

      }

      function calculateAndDisplayRoute(directionsService, directionsRenderer) {
        var waypts = [];
        var checkboxArray = document.getElementById('waypoints');
        for (var i = 0; i < checkboxArray.length; i++) {
          if (checkboxArray.options[i].selected) {
            waypts.push({
              location: checkboxArray[i].value,
              stopover: true
            });
          }
        }

        directionsService.route({
          origin: document.getElementById('start').value,
          destination: document.getElementById('end').value,
          waypoints: waypts,
          optimizeWaypoints: true,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsRenderer.setDirections(response);
            var route = response.routes[0];
            var summaryPanel = document.getElementById('directions-panel');
            summaryPanel.innerHTML = '';
            // For each route, display summary information.
            for (var i = 0; i < route.legs.length; i++) {
              var routeSegment = i + 1;
              summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                  '</b><br>';
              summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
              summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
              summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
            }
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

        
      function downloadUrl(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function() {
          if (request.readyState == 4) {
            request.onreadystatechange = doNothing;
            callback(request, request.status);
          }
        };

        request.open('GET', url, true);
        request.send(null);
      }

      function doNothing() {}
      
      

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjchTw42oDieCVSdDFoKWw6Ua69xAf9AQ&callback=initMap"
    async defer></script>
    <script src="js/third/jquery.min.js"></script>
    <script src="js/third/jquery.mask.min.js"></script>
    <script src="js/third/popper.min.js"></script>
    <script src="js/third/bootstrap.min.js"></script>
    <script src="js/third/jquery.easing.min.js"></script>
    <script src="js/third/swiper.min.js"></script>
    <script src="js/third/jquery.magnific-popup.js"></script>
    <script src="js/third/morphext.min.js"></script>
    <script src="js/third/validator.min.js"></script>
    <script src="js/third/owl.carousel.min.js"></script>
    <script src="js/ours/general.js"></script>
    <script src="js/ours/login.js"></script>
    <script src="js/ours/coleta.js"></script>
  </body>
</html>