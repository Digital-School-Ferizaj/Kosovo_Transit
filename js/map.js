const searchInput = document.getElementById('search');
const dropdown = document.getElementById('dropdown');
const routeOptions = document.getElementById('routeOptions');
const startRouteBtn = document.getElementById('startRouteBtn');
const locateBtn = document.getElementById('locate-btn');
const mapDiv = document.getElementById('map');
const directionBox = document.getElementById('directionBox');
const directionIcon = document.getElementById('directionIcon');
const directionDistance = document.getElementById('directionDistance');
const directionInstruction = document.getElementById('directionInstruction');
const endRouteBtn = document.getElementById('endRouteBtn');
const directionStats = document.getElementById('directionStats');
const directionTransport = document.getElementById('directionTransport');
const transportModeSelector = document.getElementById('transportModeSelector');
const transportModeIcons = transportModeSelector.querySelectorAll('.transport-mode-icon');

const map = L.map('map', {
  attributionControl: false,
  zoomAnimation: true,
  fadeAnimation: true
}).setView([42.6629, 21.1655], 10);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: ''
}).addTo(map);

let userLocation = null;
let selectedDestination = null;
let routingControl = null;
let currentRouteType = 'fastest';
let userMarker = null;
let directionMarker = null;
let endMarker = null;
let gpsPointerMarker = null;
let gpsWatchId = null;
let routeInstructions = [];
let currentInstructionIndex = 0;
let currentTransportMode = 'car';
let currentRouteSummary = null;
let lastViewState = null;

const busStations = [
  { id: 'ps1', name: "Pristina Main Station", lat: 42.6629, lon: 21.1655, routes: ['PR-PZ', 'PR-PE', 'PR-GJ'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps2', name: "Pristina South Terminal", lat: 42.6400, lon: 21.1500, routes: ['PR-PZ', 'PR-FE'], amenities: ['ticket_office'] },
  { id: 'ps3', name: "Lipjan Station", lat: 42.5220, lon: 21.1250, routes: ['PR-PZ', 'PR-FE'], amenities: ['waiting_room'] },
  { id: 'ps4', name: "Shtime Bus Stop", lat: 42.4380, lon: 21.0370, routes: ['PR-PZ'], amenities: ['bench'] },
  { id: 'ps5', name: "Suhareka North", lat: 42.3800, lon: 20.8600, routes: ['PR-PZ'], amenities: ['waiting_room', 'cafe'] },
  { id: 'ps6', name: "Suhareka Central", lat: 42.3592, lon: 20.8250, routes: ['PR-PZ', 'SU-GJ'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps7', name: "Prizren East Terminal", lat: 42.2300, lon: 20.7600, routes: ['PR-PZ'], amenities: ['bench'] },
  { id: 'ps8', name: "Prizren Main Station", lat: 42.2167, lon: 20.7414, routes: ['PR-PZ', 'PZ-GJ', 'PZ-PE'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps9', name: "Pristina West Terminal", lat: 42.6700, lon: 21.1400, routes: ['PR-PE'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps10', name: "Drenas Station", lat: 42.6283, lon: 20.9006, routes: ['PR-PE'], amenities: ['waiting_room'] },
  { id: 'ps11', name: "Skenderaj Bus Stop", lat: 42.7472, lon: 20.7892, routes: ['PR-PE'], amenities: ['bench'] },
  { id: 'ps12', name: "Klina Station", lat: 42.6217, lon: 20.5778, routes: ['PR-PE', 'KL-GJ'], amenities: ['waiting_room'] },
  { id: 'ps13', name: "Peja East Terminal", lat: 42.6600, lon: 20.3300, routes: ['PR-PE'], amenities: ['bench'] },
  { id: 'ps14', name: "Peja Main Station", lat: 42.6697, lon: 20.3100, routes: ['PR-PE', 'PE-GJ', 'PE-PZ'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps15', name: "Pristina East Terminal", lat: 42.6550, lon: 21.1900, routes: ['PR-GJ'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps16', name: "Llabjan Bus Stop", lat: 42.5900, lon: 21.2700, routes: ['PR-GJ'], amenities: ['bench'] },
  { id: 'ps17', name: "Kamenica Station", lat: 42.5778, lon: 21.5719, routes: ['PR-GJ'], amenities: ['waiting_room'] },
  { id: 'ps18', name: "Gjilan North Terminal", lat: 42.4700, lon: 21.4600, routes: ['PR-GJ'], amenities: ['bench'] },
  { id: 'ps19', name: "Gjilan Main Station", lat: 42.4614, lon: 21.4689, routes: ['PR-GJ', 'GJ-FE'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps20', name: "Pristina North Terminal", lat: 42.6800, lon: 21.1600, routes: ['PR-MI'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps21', name: "Vushtrri South", lat: 42.7900, lon: 20.9800, routes: ['PR-MI'], amenities: ['bench'] },
  { id: 'ps22', name: "Vushtrri Central", lat: 42.8231, lon: 20.9675, routes: ['PR-MI', 'VU-PE'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps23', name: "Mitrovica South Terminal", lat: 42.8700, lon: 20.8700, routes: ['PR-MI'], amenities: ['waiting_room'] },
  { id: 'ps24', name: "Mitrovica Main Station", lat: 42.8833, lon: 20.8667, routes: ['PR-MI', 'MI-PE'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps25', name: "Ferizaj Central", lat: 42.3700, lon: 21.1500, routes: ['PR-FE', 'FE-GJ'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps26', name: "Gjakova Main Station", lat: 42.3829, lon: 20.4303, routes: ['PE-GJ', 'PZ-GJ'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps27', name: "Istog Station", lat: 42.7808, lon: 20.4867, routes: ['PE-IS'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps28', name: "Deçan Station", lat: 42.5392, lon: 20.2917, routes: ['PE-DE'], amenities: ['waiting_room', 'ticket_office'] },
  { id: 'ps29', name: "Podujevo Main Station", lat: 42.9092, lon: 21.1933, routes: ['PR-PO'], amenities: ['waiting_room', 'cafe', 'ticket_office'] },
  { id: 'ps30', name: "Rahovec Station", lat: 42.4000, lon: 20.6500, routes: ['PZ-RA'], amenities: ['waiting_room', 'ticket_office'] }
];

const busRoutes = [
  { id: 'PR-PZ', name: 'Pristina - Prizren', stations: ['ps1', 'ps2', 'ps3', 'ps4', 'ps5', 'ps6', 'ps7', 'ps8'], color: '#0077cc' },
  { id: 'PR-PE', name: 'Pristina - Peja', stations: ['ps1', 'ps9', 'ps10', 'ps11', 'ps12', 'ps13', 'ps14'], color: '#cc7700' },
  { id: 'PR-GJ', name: 'Pristina - Gjilan', stations: ['ps1', 'ps15', 'ps16', 'ps17', 'ps18', 'ps19'], color: '#00cc77' },
  { id: 'PR-MI', name: 'Pristina - Mitrovica', stations: ['ps1', 'ps20', 'ps21', 'ps22', 'ps23', 'ps24'], color: '#cc0077' },
  { id: 'PR-FE', name: 'Pristina - Ferizaj', stations: ['ps1', 'ps2', 'ps3', 'ps25'], color: '#7700cc' },
  { id: 'PE-GJ', name: 'Peja - Gjakova', stations: ['ps14', 'ps12', 'ps26'], color: '#00cccc' },
  { id: 'PZ-GJ', name: 'Prizren - Gjakova', stations: ['ps8', 'ps26'], color: '#cccc00' }
];

let busStationsLayer = null;
let busRoutesLines = [];

function showBusStationsAndRoutes() {
  hideBusStationsAndRoutes();

  busStationsLayer = L.layerGroup().addTo(map);

  const busStationIcon = L.divIcon({
    className: 'bus-station-icon',
    html: '<span style="font-size:18px;">🚌</span>',
    iconSize: [24, 24],
    iconAnchor: [12, 12]
  });

  busStations.forEach(station => {
    const marker = L.marker([station.lat, station.lon], {
      icon: busStationIcon,
      title: station.name
    }).addTo(busStationsLayer);

    marker.bindPopup(`<b>${station.name}</b>`);
  });

  busRoutes.forEach(route => {
    const coords = route.stations.map(id => {
      const s = busStations.find(st => st.id === id);
      return [s.lat, s.lon];
    });
    const polyline = L.polyline(coords, {
      color: route.color,
      weight: 5,
      opacity: 0.7,
      dashArray: '10, 10'
    }).addTo(map);
    polyline.bindTooltip(route.name, {permanent: false});
    busRoutesLines.push(polyline);
  });
}

function hideBusStationsAndRoutes() {
  if (busStationsLayer) {
    map.removeLayer(busStationsLayer);
    busStationsLayer = null;
  }
  busRoutesLines.forEach(line => map.removeLayer(line));
  busRoutesLines = [];
}

const destinations = [
  { name: "Pristina", lat: 42.6629, lon: 21.1655, type: "city" },
  { name: "Prizren", lat: 42.2139, lon: 20.7417, type: "city" },
  { name: "Ferizaj", lat: 42.3702, lon: 21.1550, type: "city" },
  { name: "Gjakova", lat: 42.3803, lon: 20.4300, type: "city" },
  { name: "Peja", lat: 42.6591, lon: 20.2883, type: "city" },
  { name: "Mitrovica", lat: 42.8900, lon: 20.8667, type: "city" },
  { name: "Gjilan", lat: 42.4631, lon: 21.4694, type: "city" },
  { name: "Vushtrri", lat: 42.8231, lon: 20.9675, type: "city" },
  { name: "Podujeva", lat: 42.9106, lon: 21.1922, type: "city" },
  { name: "Rahovec", lat: 42.3997, lon: 20.6547, type: "city" },
  { name: "Suhareka", lat: 42.3583, lon: 20.8250, type: "city" },
  { name: "Malisheva", lat: 42.4847, lon: 20.7450, type: "city" },
  { name: "Drenas", lat: 42.6136, lon: 20.8025, type: "city" },
  { name: "Kamenica", lat: 42.5764, lon: 21.5806, type: "city" },
  { name: "Dragash", lat: 42.0667, lon: 20.6500, type: "city" },
  { name: "Shtime", lat: 42.4333, lon: 21.0400, type: "city" },
  { name: "Skenderaj", lat: 42.7467, lon: 20.7931, type: "city" },
  { name: "Deçan", lat: 42.5461, lon: 20.2911, type: "city" },
  { name: "Istog", lat: 42.7800, lon: 20.4872, type: "city" },
  { name: "Klinë", lat: 42.6217, lon: 20.5761, type: "city" },
  { name: "Lipjan", lat: 42.5300, lon: 21.1200, type: "city" },
  { name: "Kaçanik", lat: 42.2322, lon: 21.2592, type: "city" },
  { name: "Obiliq", lat: 42.6861, lon: 21.0772, type: "city" },
  { name: "Viti", lat: 42.3206, lon: 21.3586, type: "city" },
  { name: "Fushë Kosovë", lat: 42.6631, lon: 21.0964, type: "city" },
  { name: "Prishtina Mall", lat: 42.6017, lon: 21.1706, type: "mall" },
  { name: "Albi Mall", lat: 42.6382, lon: 21.1703, type: "mall" },
  { name: "ITP Prizren", lat: 42.2175, lon: 20.7215, type: "object" },
  { name: "Grand Store", lat: 42.6462, lon: 21.1576, type: "mall" },
  { name: "Royal Mall", lat: 42.6611, lon: 21.1742, type: "mall" },
  { name: "Emerald Hotel", lat: 42.6172, lon: 21.2238, type: "hotel" },
  { name: "Swiss Diamond Hotel", lat: 42.6637, lon: 21.1637, type: "hotel" },
  { name: "National Library of Kosovo", lat: 42.6553, lon: 21.1622, type: "object" },
  { name: "Cathedral of Saint Mother Teresa", lat: 42.6583, lon: 21.1578, type: "object" },
  { name: "Germia Park", lat: 42.6783, lon: 21.1942, type: "object" },
  { name: "Bear Sanctuary Prishtina", lat: 42.5219, lon: 21.1878, type: "object" }
];

let dropdownMouseOver = false;

searchInput.addEventListener('focus', () => {
  if (dropdown.innerHTML.trim() !== '') {
    dropdown.style.display = 'block';
  }
});

searchInput.addEventListener('blur', () => {
  setTimeout(() => {
    if (!dropdownMouseOver) {
      dropdown.style.display = 'none';
    }
  }, 120);
});

dropdown.addEventListener('mouseenter', () => {
  dropdownMouseOver = true;
});
dropdown.addEventListener('mouseleave', () => {
  dropdownMouseOver = false;
});

function getPlaceIcon(result) {
  if (result.type === "city") return "🏙️";
  if (result.type === "mall") return "🏬";
  if (result.type === "hotel") return "🏨";
  if (result.type === "object") return "📍";
  return "📍";
}

searchInput.addEventListener('input', () => {
  const query = searchInput.value.trim().toLowerCase();
  dropdown.innerHTML = '';
  if (!query) {
    dropdown.style.display = 'none';
    return;
  }
  const filtered = destinations.filter(dest =>
    dest.name.toLowerCase().includes(query)
  );
  if (filtered.length === 0) {
    dropdown.style.display = 'none';
    return;
  }
  filtered.forEach(dest => {
    const item = document.createElement('div');
    item.classList.add('dropdown-item');
    let iconEmoji = getPlaceIcon(dest);
    item.innerHTML = `<span>${iconEmoji} ${dest.name}</span>`;
    const icon = document.createElement('span');
    icon.classList.add('route-icon');
    icon.textContent = '🛣️';
    icon.title = "Select as destination";
    icon.addEventListener('click', (e) => {
      e.stopPropagation();
      selectedDestination = dest;
      searchInput.value = dest.name;
      dropdown.style.display = 'none';
      setDefaultRouteType();
      showTransportModeSelector();
      showRouteOptions();
      map.setView([dest.lat, dest.lon], 14, { animate: true });
    });
    item.appendChild(icon);
    item.addEventListener('click', () => {
      selectedDestination = dest;
      searchInput.value = dest.name;
      dropdown.style.display = 'none';
      setDefaultRouteType();
      showTransportModeSelector();
      showRouteOptions();
      map.setView([dest.lat, dest.lon], 14, { animate: true });
    });
    dropdown.appendChild(item);
  });
  dropdown.style.display = 'block';
});

document.addEventListener('click', e => {
  if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
    dropdown.style.display = 'none';
  }
});

function setDefaultRouteType() {
  currentRouteType = 'fastest';
  routeOptions.querySelectorAll('.route-option').forEach(opt => {
    if (opt.dataset.type === 'fastest') {
      opt.classList.add('selected');
    } else {
      opt.classList.remove('selected');
    }
  });
}

function showRouteOptions() {
  if (currentTransportMode !== 'bus') {
    routeOptions.style.display = 'flex';
  } else {
    routeOptions.style.display = 'none';
  }
  updateRouteTypeVisibility();
  updateStartRouteButtonVisibility();
}

function hideRouteOptions() {
  routeOptions.style.display = 'none';
}

function showTransportModeSelector() {
  transportModeSelector.classList.add('active');
}

function hideTransportModeSelector() {
  transportModeSelector.classList.remove('active');
}

function updateRouteTypeVisibility() {
  routeOptions.classList.remove('car-mode', 'bus-mode', 'walk-mode');
  if (currentTransportMode === 'car') {
    routeOptions.classList.add('car-mode');
  } else if (currentTransportMode === 'bus') {
    routeOptions.classList.add('bus-mode');
  } else if (currentTransportMode === 'walk') {
    routeOptions.classList.add('walk-mode');
  }
  updateStartRouteButtonVisibility();
}

function updateStartRouteButtonVisibility() {
  const startRouteBtn = document.getElementById('startRouteBtn');
  if (currentTransportMode === 'bus') {
    startRouteBtn.style.display = 'none';
  } else {
    startRouteBtn.style.display = 'flex';
  }
}

function saveCurrentViewState() {
  lastViewState = {
    center: map.getCenter(),
    zoom: map.getZoom()
  };
}

function restoreViewState() {
  if (lastViewState) {
    map.setView(lastViewState.center, lastViewState.zoom, { animate: true });
  }
}

transportModeIcons.forEach(icon => {
  icon.addEventListener('click', () => {
    transportModeIcons.forEach(i => i.classList.remove('selected'));
    icon.classList.add('selected');
    
    // Save current view state before changing mode
    if (currentTransportMode !== 'bus' && icon.dataset.mode === 'bus') {
      saveCurrentViewState();
    }
    
    currentTransportMode = icon.dataset.mode;
    setDefaultRouteType();
    
    if (currentTransportMode === 'car') {
      routeOptions.querySelectorAll('.route-option').forEach(opt => {
        if (['shortest', 'fastest', 'scenic'].includes(opt.dataset.type)) {
          opt.style.display = 'flex';
        } else {
          opt.style.display = 'none';
        }
      });
      hideBusStationsAndRoutes();
      if (lastViewState) {
        restoreViewState();
      }
      showRouteOptions();
    // In the transport mode selection event listener:
} else if (currentTransportMode === 'bus') {
  routeOptions.querySelectorAll('.route-option').forEach(opt => {
    if (opt.dataset.type === 'bus') {
      opt.style.display = 'flex';
    } else {
      opt.style.display = 'none';
    }
  });
  showBusStationsAndRoutes();
  // Adjust the center coordinates and zoom level to better fit Kosovo
  map.setView([42.5500, 20.9000], 9, { animate: true });
  hideRouteOptions();

    } else if (currentTransportMode === 'walk') {
      routeOptions.querySelectorAll('.route-option').forEach(opt => {
        if (opt.dataset.type === 'walk') {
          opt.style.display = 'flex';
        } else {
          opt.style.display = 'none';
        }
      });
      hideBusStationsAndRoutes();
      if (lastViewState) {
        restoreViewState();
      }
      showRouteOptions();
    }
    updateRouteTypeVisibility();
    updateStartRouteButtonVisibility();
  });
});

routeOptions.style.display = 'none';
routeOptions.querySelectorAll('.route-option').forEach(el => {
  el.addEventListener('click', () => {
    routeOptions.querySelectorAll('.route-option').forEach(opt => opt.classList.remove('selected'));
    el.classList.add('selected');
    currentRouteType = el.dataset.type;
  });
});

startRouteBtn.addEventListener('click', () => {
  if (!selectedDestination) {
    alert('Please select a destination first!');
    return;
  }
  if (!userLocation) {
    alert('User location not found yet!');
    return;
  }
  // Only start routing if not in bus mode
  if (currentTransportMode === 'bus') {
    // In bus mode, just show stations/routes, do not start routing
    showBusStationsAndRoutes();
    return;
  }
  startRouting(userLocation, selectedDestination);
  hideRouteOptions();
  hideTransportModeSelector();
  document.getElementById('search-container').style.display = 'none';
});

locateBtn.addEventListener('click', () => {
  if (!navigator.geolocation) {
    alert("Geolocation not supported by your browser");
    return;
  }
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation = {
        lat: pos.coords.latitude,
        lon: pos.coords.longitude
      };
      if (!userMarker) {
        userMarker = L.circleMarker([userLocation.lat, userLocation.lon], {
          radius: 6,
          color: '#0077ff',
          fillColor: '#3399ff',
          fillOpacity: 0.9,
          weight: 2
        }).addTo(map).bindPopup('Your Location');
      } else {
        userMarker.setLatLng([userLocation.lat, userLocation.lon]);
      }
      map.setView([userLocation.lat, userLocation.lon], 14, { animate: true });
    },
    () => alert("Unable to retrieve your location")
  );
});

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      userLocation = { lat: pos.coords.latitude, lon: pos.coords.longitude };
      if (!userMarker) {
        userMarker = L.circleMarker([userLocation.lat, userLocation.lon], {
          radius: 6,
          color: '#0077ff',
          fillColor: '#3399ff',
          fillOpacity: 0.9,
          weight: 2
        }).addTo(map).bindPopup('Your Location');
      } else {
        userMarker.setLatLng([userLocation.lat, userLocation.lon]);
      }
      map.setView([userLocation.lat, userLocation.lon], 14, { animate: true });
    },
    () => {
      map.setView([42.6629, 21.1655], 10, { animate: true });
    }
  );
} else {
  map.setView([42.6629, 21.1655], 10, { animate: true });
}

function getDirectionIcon(type, text) {
  if (type === "Head" || /head/i.test(text)) return "⬆️";
  if (/left/i.test(text)) return "⬅️";
  if (/right/i.test(text)) return "➡️";
  if (/circle|roundabout/i.test(text)) return "🌀";
  if (/arriv/i.test(text)) return "🏁";
  if (/continue/i.test(text)) return "⬆️";
  if (/merge/i.test(text)) return "↗️";
  if (/keep right/i.test(text)) return "↗️";
  if (/keep left/i.test(text)) return "↖️";
  if (/exit/i.test(text)) return "⏩";
  if (/fork/i.test(text)) return "⤴️";
  return "⬆️";
}

function showDirectionBox(instruction, distance, summary) {
  directionBox.style.display = 'flex';
  directionBox.classList.remove('box-animate');
  void directionBox.offsetWidth;
  directionBox.classList.add('box-animate');
  directionIcon.textContent = getDirectionIcon(instruction.type, instruction.text);
  directionDistance.textContent = distance;
  directionInstruction.textContent = instruction.text;
  if (summary) {
    let km = formatDistance(summary.totalDistance);
    let min = formatTimeRealistic(summary.totalDistance, currentTransportMode);
    directionStats.innerHTML = `<span>${km} • ${min}</span>`;
  }
  directionTransport.innerHTML = currentTransportMode === 'car' ? '🚗' : (currentTransportMode === 'bus' ? '🚌' : '🚶‍♂️');
}

function hideDirectionBox() {
  directionBox.style.display = 'none';
}

function formatDistance(meters) {
  if (!meters && meters !== 0) return "";
  if (meters < 1000) return `${Math.round(meters)} m`;
  return `${(meters / 1000).toFixed(2)} km`;
}

function formatTimeRealistic(meters, mode) {
  let km = meters / 1000;
  let speedKmh = 50;
  if (mode === 'car') speedKmh = 60;
  if (mode === 'bus') speedKmh = 40;
  if (mode === 'walk') speedKmh = 5;
  let hours = km / speedKmh;
  let totalMinutes = Math.round(hours * 60);
  if (totalMinutes < 60) return `${totalMinutes} min`;
  let h = Math.floor(totalMinutes / 60);
  let m = totalMinutes % 60;
  return `${h}h ${m}min`;
}

function getRoutingProfile() {
  if (currentTransportMode === 'car') return 'car';
  if (currentTransportMode === 'bus') return 'car';
  if (currentTransportMode === 'walk') return 'foot';
  return 'car';
}

function startRouting(start, destination) {
  if (routingControl) {
    map.removeControl(routingControl);
    routingControl = null;
  }
  if (gpsPointerMarker) {
    map.removeLayer(gpsPointerMarker);
    gpsPointerMarker = null;
  }
  if (userMarker) {
    map.removeLayer(userMarker);
    userMarker = null;
  }
  if (directionMarker) {
    map.removeLayer(directionMarker);
    directionMarker = null;
  }
  if (endMarker) {
    map.removeLayer(endMarker);
    endMarker = null;
  }
  mapDiv.classList.add('map-curve');
  map.setView([start.lat, start.lon], 20, { animate: true });

  let startLatLng = L.latLng(start.lat, start.lon);
  let endLatLng = L.latLng(destination.lat, destination.lon);

  directionMarker = L.marker(startLatLng, {
    icon: L.icon({
      iconUrl: 'direction.png',
      iconSize: [48, 48],
      iconAnchor: [24, 24]
    })
  }).addTo(map);

  endMarker = L.marker(endLatLng, {
    icon: L.icon({
      iconUrl: 'destination.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40]
    })
  }).addTo(map);

  routingControl = L.Routing.control({
    waypoints: [
      startLatLng,
      endLatLng
    ],
    routeWhileDragging: false,
    draggableWaypoints: false,
    addWaypoints: false,
    profile: getRoutingProfile(),
    createMarker: function() {
      return null;
    },
    show: false,
    itinerary: { show: false },
    lineOptions: {
      styles: [
        {color: '#b3e0ff', weight: 14, opacity: 0.7},
        {color: '#0077ff', weight: 8, opacity: 0.98}
      ],
      addWaypoints: false,
      extendToWaypoints: false,
      missingRouteTolerance: 0,
      className: 'route-main route-main-outline'
    }
  }).addTo(map);

  routingControl.on('routesfound', function(e) {
    const routes = e.routes;
    if (routes.length > 0) {
      routeInstructions = [];
      currentInstructionIndex = 0;
      let summary = routes[0].summary || {};
      summary.totalDistance = summary.totalDistance || routes[0].summary.totalDistance || routes[0].summary.distance || routes[0].distance || 0;
      currentRouteSummary = summary;
      if (routes[0].instructions && routes[0].instructions.length > 0) {
        routeInstructions = routes[0].instructions;
      } else if (routes[0].segments && routes[0].segments.length > 0) {
        for (const seg of routes[0].segments) {
          if (seg.steps) routeInstructions = routeInstructions.concat(seg.steps);
        }
      }
      if (routeInstructions.length > 0) {
        showDirectionBox(routeInstructions[0], formatDistance(routeInstructions[0].distance), summary);
      }
    }
  });

  if (navigator.geolocation) {
    if (gpsWatchId) {
      navigator.geolocation.clearWatch(gpsWatchId);
    }
    gpsWatchId = navigator.geolocation.watchPosition(
      (pos) => {
        userLocation = { lat: pos.coords.latitude, lon: pos.coords.longitude };
        if (directionMarker) {
          directionMarker.setLatLng([userLocation.lat, userLocation.lon]);
        }
        if (routeInstructions.length > 0) {
          for (let i = currentInstructionIndex; i < routeInstructions.length; i++) {
            const instr = routeInstructions[i];
            if (instr.latLng) {
              const d = map.distance([userLocation.lat, userLocation.lon], instr.latLng);
              if (d < 30) {
                currentInstructionIndex = i + 1;
              }
            }
          }
          if (currentInstructionIndex < routeInstructions.length) {
            const instr = routeInstructions[currentInstructionIndex];
            showDirectionBox(instr, formatDistance(instr.distance), currentRouteSummary);
          } else {
            showDirectionBox({ type: "Arrive", text: "You have arrived!", distance: "" }, "", currentRouteSummary);
            if (gpsWatchId) {
              navigator.geolocation.clearWatch(gpsWatchId);
              gpsWatchId = null;
            }
          }
        }
      },
      (err) => {},
      { enableHighAccuracy: true, maximumAge: 1000, timeout: 10000 }
    );
  }
}

endRouteBtn.addEventListener('click', () => {
  if (routingControl) {
    map.removeControl(routingControl);
    routingControl = null;
  }
  if (gpsWatchId) {
    navigator.geolocation.clearWatch(gpsWatchId);
    gpsWatchId = null;
  }
  if (gpsPointerMarker) {
    map.removeLayer(gpsPointerMarker);
    gpsPointerMarker = null;
  }
  if (directionMarker) {
    map.removeLayer(directionMarker);
    directionMarker = null;
  }
  if (endMarker) {
    map.removeLayer(endMarker);
    endMarker = null;
  }
  mapDiv.classList.remove('map-curve');
  hideDirectionBox();
  hideRouteOptions();
  hideTransportModeSelector();
  document.getElementById('search-container').style.display = 'block';
  searchInput.value = '';
  selectedDestination = null;
  dropdown.style.display = 'none';
  if (userLocation) {
    if (!userMarker) {
      userMarker = L.circleMarker([userLocation.lat, userLocation.lon], {
        radius: 6,
        color: '#0077ff',
        fillColor: '#3399ff',
        fillOpacity: 0.9,
        weight: 2
      }).addTo(map).bindPopup('Your Location');
    } else {
      userMarker.setLatLng([userLocation.lat, userLocation.lon]);
    }
  }
  hideBusStationsAndRoutes();
});

setDefaultRouteType();
hideRouteOptions();
hideTransportModeSelector();

map.on('zoomstart', () => {
  mapDiv.style.transition = 'transform 0.3s cubic-bezier(.4,2,.6,1)';
  mapDiv.style.transform += ' scale(0.97)';
});
map.on('zoomend', () => {
  mapDiv.style.transition = 'transform 0.5s cubic-bezier(.4,2,.6,1)';
  mapDiv.style.transform = mapDiv.classList.contains('map-curve')
    ? 'perspective(900px) rotateX(18deg) scale(1.08)'
    : 'scale(1)';
  setTimeout(() => {
    mapDiv.style.transform = mapDiv.classList.contains('map-curve')
      ? 'perspective(900px) rotateX(18deg) scale(1.08)'
      : 'scale(1)';
  }, 350);
});
