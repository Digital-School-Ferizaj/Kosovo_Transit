<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kosova Transit</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />  
  <link rel="stylesheet" href="bus-stops-components/styles/bus-stops.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: #f0f4f9;
      color: #333;
      overflow: hidden;
    }
    #map {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      height: 100vh;
      z-index: 1000;
    }
    
    /* Sidebar styles */
    .sidebar {
      width: 260px;
      background-color: #2a3042;
      display: flex;
      flex-direction: column;
      color: #a6b0cf;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      z-index: 1001;
      transition: all 0.3s ease;
    }

    .sidebar-header {
      padding: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h2 {
      color: #fff;
      font-size: 22px;
      font-weight: 600;
      display: flex;
      align-items: center;
    }

    .sidebar-header h2 i {
      color: #556ee6;
      margin-right: 10px;
      font-size: 24px;
    }

    .sidebar-menu {
      padding: 20px 0;
      flex: 1;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar li {
      margin: 8px 0;
    }

    .sidebar li a {
      text-decoration: none;
      color: #a6b0cf;
      padding: 12px 25px;
      display: flex;
      align-items: center;
      font-size: 15px;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .sidebar li a i {
      margin-right: 10px;
      font-size: 18px;
      width: 24px;
      text-align: center;
    }

    .sidebar li a:hover {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.05);
    }

    .sidebar li a.active {
      color: #fff;
      background-color: rgba(85, 110, 230, 0.25);
      border-left: 3px solid #556ee6;
    }
    
    /* Adjust map to account for sidebar */
    #map.with-sidebar {
      left: 260px;
    }
    
    /* Toggle button for sidebar */
    .sidebar-toggle {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1002;
      background: #2a3042;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 8px;
      cursor: pointer;
      display: none;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      #map {
        left: 0;
      }
      
      .sidebar-toggle {
        display: block;
      }
    }

    .logout-btn {
      color: #f46a6a;
      text-decoration: none;
      padding: 12px 25px;
      display: flex;
      align-items: center;
      font-size: 15px;
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .logout-btn i {
      margin-right: 10px;
      font-size: 18px;
      width: 24px;
      text-align: center;
    }

    .logout-btn:hover {
      color: #fff;
      background-color: rgba(244, 106, 106, 0.1);
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-header">
      <h2><i class="fas fa-chart-line"></i> Transit Admin</h2>
    </div>
    <div class="sidebar-menu">
      <ul>
        <li><a href="admin.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="road.php"><i class="fas fa-road"></i> Roads</a></li>
        <li><a href="bus.php"><i class="fas fa-bus"></i> Buses</a></li>
        <li><a href="map.php" class="active"><i class="fas fa-map-marked-alt"></i> Map</a></li>
        <li><a href="database.php"><i class="fas fa-database"></i> Database</a></li>
      </ul>
    </div>
    <a href="logout.php" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
  
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
  </button>
  
  <div id="map" class="with-sidebar"></div>
  
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
  <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
  <script>
    const map = L.map('map', {
      attributionControl: false
    }).setView([42.6629, 21.1655], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: ''
    }).addTo(map);

    let heatmapLayer = null;
    let heatmapLegend = null;

    // Enhanced traffic heatmap data with more places and varied intensity
    const trafficHeatmapData = [
      // Pristina and surrounding area - high density coverage
      [42.6629, 21.1655, 1.0],  // City center - maximum intensity
      [42.6540, 21.1590, 0.95], // Main boulevard
      [42.6580, 21.1520, 0.98], // Busy intersection
      [42.6610, 21.1700, 0.92], // Shopping area
      [42.6700, 21.1600, 0.90], // University area
      [42.6620, 21.1630, 0.94], // Downtown
      [42.6600, 21.1670, 0.91], // Commercial district
      [42.6590, 21.1600, 0.93], // Business center
      [42.6560, 21.1620, 0.89], // Residential area
      [42.6630, 21.1690, 0.92], // Government buildings
      
      // Pristina outskirts - lighter intensity
      [42.6700, 21.1800, 0.75],
      [42.6750, 21.1700, 0.72],
      [42.6500, 21.1500, 0.77],
      [42.6450, 21.1650, 0.73],
      [42.6800, 21.1550, 0.70],
      
      // Prizren area
      [42.2167, 20.7414, 0.95], // City center
      [42.2100, 20.7380, 0.90], // Main street
      [42.2150, 20.7400, 0.88], // Historic center
      [42.2200, 20.7450, 0.85], // Tourist area
      [42.2120, 20.7350, 0.82], // Residential district
      
      // Peja area
      [42.6697, 20.3100, 0.92], // City center
      [42.6650, 20.3050, 0.88], // Main road
      [42.6670, 20.3080, 0.85], // Commercial area
      [42.6720, 20.3150, 0.82], // Outskirts
      
      // Gjakova area
      [42.3829, 20.4303, 0.90], // City center
      [42.3800, 20.4250, 0.87], // Main street
      [42.3810, 20.4280, 0.84], // Shopping district
      [42.3850, 20.4320, 0.81], // Residential area
      
      // Mitrovica area
      [42.8833, 20.8667, 0.93], // City center
      [42.8800, 20.8600, 0.90], // Bridge area
      [42.8820, 20.8630, 0.87], // Commercial district
      [42.8850, 20.8700, 0.84], // Residential area
      
      // Additional cities with lighter traffic
      // Ferizaj
      [42.3700, 21.1500, 0.85], // City center
      [42.3650, 21.1450, 0.80], // Main road
      [42.3720, 21.1520, 0.75], // Commercial area
      [42.3680, 21.1580, 0.65], // Residential area
      
      // Gjilan
      [42.4614, 21.4689, 0.82], // City center
      [42.4580, 21.4650, 0.78], // Main street
      [42.4630, 21.4700, 0.73], // Commercial district
      [42.4560, 21.4750, 0.68], // Residential area
      
      // Podujevo
      [42.9092, 21.1933, 0.80], // City center
      [42.9042, 21.1883, 0.75], // Main road
      [42.9082, 21.1833, 0.70], // Commercial area
      [42.9122, 21.1783, 0.65], // Residential area
      
      // Vushtrri
      [42.8231, 20.9675, 0.82], // City center
      [42.8181, 20.9625, 0.77], // Main road
      [42.8131, 20.9575, 0.72], // Commercial area
      [42.8081, 20.9525, 0.67], // Residential area
      
      // Suhareka
      [42.3592, 20.8250, 0.80], // City center
      [42.3542, 20.8200, 0.75], // Main road
      [42.3492, 20.8150, 0.70], // Commercial area
      [42.3442, 20.8100, 0.65], // Residential area
      
      // Rahovec
      [42.4000, 20.6500, 0.82], // City center
      [42.3950, 20.6450, 0.77], // Main road
      [42.3900, 20.6400, 0.72], // Commercial area
      [42.3850, 20.6350, 0.67], // Residential area
      
      // Istog
      [42.7808, 20.4867, 0.80], // City center
      [42.7758, 20.4817, 0.75], // Main road
      [42.7708, 20.4767, 0.70], // Commercial area
      [42.7658, 20.4717, 0.65], // Residential area
      
      // Deçan
      [42.5392, 20.2917, 0.82], // City center
      [42.5342, 20.2867, 0.77], // Main road
      [42.5292, 20.2817, 0.72], // Commercial area
      [42.5242, 20.2767, 0.67], // Residential area
      
      // Major highways with high intensity
      // M9 Highway (Pristina - Peja)
      [42.6629, 21.0655, 0.95],
      [42.6629, 20.9655, 0.92],
      [42.6629, 20.8655, 0.90],
      [42.6629, 20.7655, 0.88],
      [42.6629, 20.6655, 0.86],
      [42.6629, 20.5655, 0.84],
      [42.6629, 20.4655, 0.82],
      [42.6629, 20.3655, 0.80],
      
      // M2 Highway (Pristina - Mitrovica)
      [42.7000, 21.0655, 0.94],
      [42.7300, 21.0255, 0.92],
      [42.7600, 20.9855, 0.90],
      [42.7900, 20.9455, 0.88],
      [42.8200, 20.9055, 0.86],
      
      // R7 Highway (Pristina - Prizren)
      [42.6000, 21.0655, 0.95],
      [42.5500, 21.0155, 0.93],
      [42.5000, 20.9655, 0.91],
      [42.4500, 20.9155, 0.89],
      [42.4000, 20.8655, 0.87],
      [42.3500, 20.8155, 0.85],
      [42.3000, 20.7655, 0.83],
      [42.2500, 20.7414, 0.81],
      
      // M25 Highway (Pristina - Gjilan)
      [42.6629, 21.2655, 0.93],
      [42.6329, 21.3155, 0.91],
      [42.6029, 21.3655, 0.89],
      [42.5729, 21.4155, 0.87],
      [42.5429, 21.4655, 0.85],
      
      // Additional traffic hotspots
      [42.5700, 21.0800, 0.96], // Major intersection
      [42.4500, 20.9000, 0.94], // Highway junction
      [42.7200, 20.5800, 0.92], // Toll plaza
      [42.3000, 20.6000, 0.90], // Border crossing
      [42.5000, 21.0000, 0.88]  // Rest area
    ];

    // Function to show the traffic heatmap
    function showTrafficHeatmap() {
      if (heatmapLayer) {
        map.removeLayer(heatmapLayer);
      }
      
      // Configure heatmap for maximum visibility
      heatmapLayer = L.heatLayer(trafficHeatmapData, {
        radius: 40,           // Large radius for better visibility
        blur: 25,             // Smooth blur
        maxZoom: 17,
        max: 1.0,             // Maximum intensity
        minOpacity: 0.6,      // High minimum opacity
        gradient: {
          0.1: 'rgba(0,0,255,0.7)',    // Blue with opacity
          0.3: 'rgba(0,255,255,0.8)',  // Cyan with opacity
          0.5: 'rgba(0,255,0,0.8)',    // Green with opacity
          0.7: 'rgba(255,255,0,0.9)',  // Yellow with opacity
          0.9: 'rgba(255,128,0,0.9)',  // Orange with opacity
          1.0: 'rgba(255,0,0,1.0)'     // Red with full opacity
        }
      }).addTo(map);
      
      if (!heatmapLegend) {
        heatmapLegend = createHeatmapLegend();
        heatmapLegend.addTo(map);
      }
    }

    // Create a more visually appealing legend and position it on the right side
    function createHeatmapLegend() {
      const legend = L.control({ position: 'topright' }); // Changed from 'bottomleft' to 'topright'
      
      legend.onAdd = function() {
        const div = L.DomUtil.create('div', 'heatmap-legend');
        div.innerHTML = `
          <div style="
            background: rgba(255,255,255,0.9);
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            border: 1px solid rgba(0,0,0,0.1);
            margin-top: 10px;
            margin-right: 10px;
          ">
            <h4 style="margin: 0 0 8px 0; font-size: 15px; font-weight: bold; color: #333; text-align: center;">Traffic Intensity</h4>
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(255,0,0,1.0); margin-right: 8px; border-radius: 4px;"></div>
                <span>Severe Congestion</span>
              </div>
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(255,128,0,0.9); margin-right: 8px; border-radius: 4px;"></div>
                <span>Heavy Traffic</span>
              </div>
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(255,255,0,0.9); margin-right: 8px; border-radius: 4px;"></div>
                <span>Moderate Traffic</span>
              </div>
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(0,255,0,0.8); margin-right: 8px; border-radius: 4px;"></div>
                <span>Light Traffic</span>
              </div>
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(0,255,255,0.8); margin-right: 8px; border-radius: 4px;"></div>
                <span>Free Flowing</span>
              </div>
              <div style="display: flex; align-items: center;">
                <div style="width: 24px; height: 24px; background: rgba(0,0,255,0.7); margin-right: 8px; border-radius: 4px;"></div>
                <span>Very Light Traffic</span>
              </div>
            </div>
          </div>
        `;
        return div;
      };
      
      return legend;
    }

    // Set default view to Pristina
    map.setView([42.6629, 21.1655], 10);

    // Show heatmap on page load
    document.addEventListener('DOMContentLoaded', function() {
      showTrafficHeatmap();
      
      // Handle sidebar toggle for responsive design
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');
      const mapElement = document.getElementById('map');
      
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
      });
      
      // Adjust map size when window is resized
      function handleResize() {
        if (window.innerWidth <= 768) {
          mapElement.classList.remove('with-sidebar');
        } else {
          mapElement.classList.add('with-sidebar');
        }
      }
      
      window.addEventListener('resize', handleResize);
      handleResize(); // Initial check
    });

    // Show heatmap immediately in case DOMContentLoaded already fired
    showTrafficHeatmap();
  </script>
  <script src="script.js"></script>
</body>
</html>