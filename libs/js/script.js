


sabbr = "";labbr = "";lng = "";lat = "";cty = "";cty2 = "";poly = ""; n="";s="";e="";w="";cp="";
mark = new L.MarkerClusterGroup();

earthIcon = L.icon({
  iconUrl: 'img/eathquake.svg',
  shadowUrl: 'https://www.timbuktutravel.com/assets/images/marker-shadow.png',
  iconSize:     [30, 30], 
  shadowSize:   [30, 30], 
  shadowAnchor: [10, 15]
});

ctyIcon = L.icon({
  iconUrl: 'https://i.ibb.co/4Kbfds1/marker.png',
  shadowUrl: 'https://www.timbuktutravel.com/assets/images/marker-shadow.png',
  iconSize:     [30, 30], 
  shadowSize:   [30, 30], 
  shadowAnchor: [10, 15]
});


var map = L.map('mapid').setView([0.0, 0.0], 2);

const wait = ms => new Promise(resolve => setTimeout(resolve, ms));
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

function on() {
  document.getElementById("overlay").style.display = "block";
}

function off() {
  document.getElementById("overlay").style.display = "none";
}

function getMap(result) {
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  map.removeLayer(poly);
  map.removeLayer(mark);
  map.removeLayer(cp);
  mark.clearLayers();
  

  poly = L.geoJSON(result['bounds'], {
    style: {
      weight: 1,
      color: "black",
      opacity: 1,
      fillColor: "black",
      fillOpacity: 0.2,
      dashArray: '4'
    }
  }).addTo(map);
  map.fitBounds(poly.getBounds());
  
  
  cp=L.marker([result['cap']['lat'], result['cap']['lng']],{icon:ctyIcon}).addTo(map).bindPopup(
    'Capital:'+result['info']['capital']
    ).addTo(map);

 
  map.setView([lat, lng]);
  if (result['earth']['earthquakes']!=null){
      result['earth']['earthquakes'].forEach(element => {
      L.marker([element['lng'], element['lat']],{icon: earthIcon}  
    ).addTo(mark)
      .bindPopup(

        'Earthquake<br>'+
        'Time:'+element['datetime']+
        '<br>Depth: '+element['depth']+
        '<br>Magnitude: '+element['magnitude']
      )
    });
    mark.addTo(map);
  }
}

function getVars(result) {
  sabbr = result['components']['ISO_3166-1_alpha-2'];
  labbr = result['components']['ISO_3166-1_alpha-3'];
  lng = result['geometry']['lng'];
  lat = result['geometry']['lat'];
  cCode = result['annotations']['currency']['iso_code'];
  
  n=result['bounds']['northeast']['lng'];
  e=result['bounds']['northeast']['lat'];
  s=result['bounds']['southwest']['lng'];
  w=result['bounds']['southwest']['lat'];
  
  $("#flag").prop("src", "https://www.countryflagicons.com/FLAT/64/" + sabbr + ".png");
  
  cty = result['components']['country'].split(" ").join("%20");
  capi= result['components']['cap'].split(" ").join("%20");
  
  if (cty.toLowerCase() == "united%20kingdom") {
    cty2 = "england"
  } else {
    cty2 = cty;
  }
}

function getAll() {
  $.ajax({
    url: "libs/php/getAll.php",
    type: 'POST',
    dataType: 'json',
    data: {
      cap:capi,
      cc: cCode,
      sCode: sabbr,
      lCode: labbr,
      ctry: cty,
      ctry2: cty2,
      long: lng,
      lati: lat,
      north:n,
      east:e,
      south:s,
      west:w
    },
    success: function(result) {
      getMap(result['data']);
      //info
      $('#language').html(result['data']['info']['languages']);
      $('#area').html(result['data']['info']['areaInSqKm'] + "km <sup>2<sup>");
      $('#population').html(result['data']['info']['population']);
      $('#capital').html(result['data']['info']['capital']);
      //weather
      if(result['data']['weather']!=null){
        $('#weatherIcon').attr('src', result['data']['weather']['current']['condition']['icon']);
        $('#weather').html(result['data']['weather']['current']['condition']['text']);
        $('#wind').html(result['data']['weather']['current']['wind_mph'] + "mph")
        $('#temp').html(result['data']['weather']['current']['feelslike_c'] + "°" + " / " + result['data']['weather']['current']['humidity']);
        $('#timezone').html(result['data']['weather']['location']['tz_id']); 
        $('#time').html(result['data']['weather']['location']['localtime']);
      }
      // news
      // if(result['data']['news']['totalResults']!=0){
      //   $('.title').html(result['data']['news']['articles'][0]['title']);
      //   $('#author').html(result['data']['news']['articles'][0]['source']['name']);
        
      //   result['data']['news']['articles'].forEach(element => {
      //     if (element['title']&&element['urlToImage']&&element['description']&&element['url']){
      //       h2 = document.createElement("h2");
      //       h2.classList.add("oTitle");
      //       h2.appendChild(document.createTextNode(element['title']));
      //       document.getElementById("overlay").appendChild(h2);
      
      //       img = document.createElement("img");
      //       img.classList.add("nImg");
      //       img.src = element['urlToImage'];
      //       document.getElementById("overlay").appendChild(img);

      //       p = document.createElement("p");
      //       p.classList.add("nCont");
      //       p.appendChild(document.createTextNode(element['description']));
      //       document.getElementById("overlay").appendChild(p);

      //       a = document.createElement("a");
      //       a.classList.add("nSource");
      //       a.appendChild(document.createTextNode("Source"));
      //       a.href = element['url'];
      //       document.getElementById("overlay").appendChild(a);

      //       hr = document.createElement("hr");
      //       document.getElementById("overlay").appendChild(hr);
      //     }

      //   });
	  	// }
      //currency
      console.log(result['data']['currency']['CurrencySymbol'])
      $('#currency').html(result['data']['currency']['CurrencySymbol']);
      $('#rates').html(result['data']['rates']);
      
      document.getElementById("load").style.display = "none";
    },
    
  })
}

$(window).on('load', function() {
  $.when(
    fetch('http://ip-api.com/json/?fields=country')
  .then(response => response.json())
  .then(commits => cty =commits['country'].split(" ").join("%20"))
  
  ).done(function(){
    setTimeout(() => {
      $.when($.ajax({
      url: "libs/php/getSearch.php",
      type: 'POST',
      dataType: 'json',
      data: {
        name: cty,
      },
      success: function(result) {
        getVars(result);
      },
    })).done(function() {
      getAll();
    })
  }, 350);
  })
  
  $.ajax({
    url: "libs/php/fillSelect.php",
    type: 'POST',
    dataType: 'json',
    success: function(result) {
      result.sort();
      result.forEach(element => {
        if (element!="Antarctica"){
          var x = document.createElement("option");
          var t = document.createTextNode(element);
          x.appendChild(t);
          x.id=element;
          document.getElementById("selectCountry").appendChild(x);
        }
      });
    }
  })   
})

  $('#selectCountry').on('change', () => {
  document.getElementById("load").style.display = "block";
  $.when($.ajax({
    url: "libs/php/getSearch.php",
    type: 'POST',
    dataType: 'json',
    data: {
      name: $("#selectCountry option:selected").text().split(" ").join("%20"),
    },
    success: function(result) {
      getVars(result);
    },
   
  })).done(function() {
    getAll();
  })
})