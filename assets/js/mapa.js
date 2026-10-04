// Se ejecuta cuando la Maps JavaScript API termina de cargar (callback=initMap)
function initMap() {
  const el = document.getElementById('mapa-js');
  if (!el) return;

  const posicion = { lat: parseFloat(el.dataset.lat), lng: parseFloat(el.dataset.lng) };

  const mapa = new google.maps.Map(el, {
    center: posicion,
    zoom: 16,
    mapTypeControl: true,
    streetViewControl: true,
    fullscreenControl: true
  });

  const marcador = new google.maps.Marker({
    position: posicion,
    map: mapa,
    title: el.dataset.nombre
  });

  const info = new google.maps.InfoWindow({
    content: '<strong>' + el.dataset.nombre + '</strong><br>¡Aquí estudio!'
  });

  marcador.addListener('click', () => info.open({ anchor: marcador, map: mapa }));
  info.open({ anchor: marcador, map: mapa });
}
