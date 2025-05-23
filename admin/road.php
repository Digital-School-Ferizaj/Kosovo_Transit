<?php
require_once 'config.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Road Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f6fa;
      display: flex;
      height: 100vh;
      color: #333;
    }

    .sidebar {
      width: 260px;
      background-color: #2a3042;
      display: flex;
      flex-direction: column;
      color: #a6b0cf;
      position: relative;
      transition: all 0.3s ease;
      z-index: 10;
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

    .main-content {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background-color: #f5f6fa;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e9ecef;
    }

    .header h1 {
      font-size: 24px;
      font-weight: 600;
      color: #495057;
    }

    .header-actions {
      display: flex;
      gap: 10px;
    }

    .btn {
      background-color: #556ee6;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #4458ca;
    }

    .btn-outline {
      background-color: transparent;
      color: #556ee6;
      border: 1px solid #556ee6;
    }

    .btn-outline:hover {
      background-color: #556ee6;
      color: white;
    }

    .city-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .city-card {
      background-color: #ffffff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
      overflow: hidden;
      height: fit-content;
    }

    .city-header {
      padding: 15px 20px;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .city-header:hover {
      background-color: #f8f9fa;
    }

    .city-header h3 {
      font-size: 16px;
      font-weight: 600;
      color: #495057;
      margin: 0;
      display: flex;
      align-items: center;
    }

    .city-header h3 i {
      margin-right: 10px;
      color: #556ee6;
    }

    .city-header .toggle-icon {
      color: #74788d;
      transition: transform 0.3s ease;
    }

    .city-content {
      padding: 0;
      max-height: 0;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .city-card.active .city-content {
      padding: 20px;
      max-height: 1000px;
    }

    .city-card.active .toggle-icon {
      transform: rotate(180deg);
    }

    .suggestion {
      display: flex;
      align-items: flex-start;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f0f0f0;
    }

    .suggestion:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .suggestion-icon {
      width: 36px;
      height: 36px;
      background-color: #f8f9fc;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      flex-shrink: 0;
      font-size: 16px;
      color: #556ee6;
    }

    .suggestion-content {
      flex: 1;
    }

    .suggestion-title {
      font-weight: 500;
      margin: 0 0 5px 0;
      color: #495057;
      font-size: 14px;
    }

    .suggestion-desc {
      margin: 0;
      color: #74788d;
      font-size: 13px;
      line-height: 1.5;
    }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 3px;
      font-size: 11px;
      font-weight: 500;
      margin-left: 5px;
    }

    .badge-high {
      background-color: rgba(244, 106, 106, 0.1);
      color: #f46a6a;
    }

    .badge-medium {
      background-color: rgba(241, 180, 76, 0.1);
      color: #f1b44c;
    }

    .badge-low {
      background-color: rgba(52, 195, 143, 0.1);
      color: #34c38f;
    }

    .search-container {
      margin-bottom: 20px;
    }

    .search-input {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #e2e8f0;
      border-radius: 5px;
      font-size: 14px;
      font-family: 'Poppins', sans-serif;
      background-color: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      transition: all 0.3s ease;
    }

    .search-input:focus {
      outline: none;
      border-color: #556ee6;
      box-shadow: 0 0 0 3px rgba(85, 110, 230, 0.1);
    }

    .search-input::placeholder {
      color: #a0aec0;
    }

    .no-results {
      text-align: center;
      padding: 30px;
      color: #74788d;
      font-size: 15px;
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

    .priority-indicator {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }

    .priority-high {
      background-color: #f46a6a;
    }

    .priority-medium {
      background-color: #f1b44c;
    }

    .city-card {
      position: relative;
    }

    .summary-stats {
      background-color: #ffffff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.03);
      padding: 20px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
    }

    .stat-item {
      text-align: center;
      flex: 1;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 600;
      color: #556ee6;
      margin-bottom: 5px;
    }

    .stat-label {
      font-size: 14px;
      color: #74788d;
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
        <li><a href="road.php" class="active"><i class="fas fa-road"></i> Roads</a></li>
        <li><a href="bus.php"><i class="fas fa-bus"></i> Buses</a></li>
        <li><a href="map.php"><i class="fas fa-map-marked-alt"></i> Map</a></li>
        <li><a href="database.php"><i class="fas fa-database"></i> Database</a></li>
      </ul>
    </div>
    <a href="logout.php" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>

  <div class="main-content">
    <div class="header">
      <h1>Road Improvement Management</h1>
      <div class="header-actions">
        <button class="btn btn-outline"><i class="fas fa-plus"></i> Add Suggestion</button>
        <button class="btn"><i class="fas fa-file-export"></i> Export Report</button>
      </div>
    </div>

    <div class="summary-stats">
      <div class="stat-item">
        <div class="stat-value">6</div>
        <div class="stat-label">Cities Requiring Work</div>
      </div>
      <div class="stat-item">
        <div class="stat-value">9</div>
        <div class="stat-label">High Priority Issues</div>
      </div>
      <div class="stat-item">
        <div class="stat-value">7</div>
        <div class="stat-label">Medium Priority Issues</div>
      </div>
      <div class="stat-item">
        <div class="stat-value">42.6629</div>
        <div class="stat-label">Highest Traffic Latitude</div>
      </div>
      <div class="stat-item">
        <div class="stat-value">21.1655</div>
        <div class="stat-label">Highest Traffic Longitude</div>
      </div>
    </div>

    <div class="search-container">
      <input type="text" class="search-input" id="citySearch" placeholder="Search for a city..." onkeyup="filterCities()">
    </div>

    <div class="city-list" id="cityList">
      <!-- Cities will be dynamically generated here -->
    </div>
  </div>

  <script>
    // City data with road suggestions - only including cities that need work
    const cityData = [
      {
        name: "Pristina",
        hasPriority: true,
        suggestions: [
          {
            icon: "road",
            title: "Main Boulevard Expansion",
            priority: "high",
            description: "Severe congestion detected at coordinates 42.6629, 21.1655. Recommend widening the boulevard and adding dedicated bus lanes to improve traffic flow."
          },
          {
            icon: "traffic-light",
            title: "Downtown Intersection Redesign",
            priority: "high",
            description: "Heavy traffic at coordinates 42.6610, 21.1700. Implement smart traffic lights with AI-based timing to reduce wait times during peak hours."
          },
          {
            icon: "parking",
            title: "University Area Parking",
            priority: "medium",
            description: "Congestion at 42.6700, 21.1600 due to street parking. Build multi-level parking structure to free up road space and improve flow."
          }
        ]
      },
      {
        name: "Prizren",
        hasPriority: true,
        suggestions: [
          {
            icon: "road",
            title: "Historic Center Traffic Management",
            priority: "high",
            description: "High traffic concentration at 42.2167, 20.7414. Implement one-way street system and pedestrian-only zones to preserve historic area."
          },
          {
            icon: "traffic-light",
            title: "Main Street Optimization",
            priority: "medium",
            description: "Congestion at 42.2100, 20.7380. Synchronize traffic lights and add turning lanes at key intersections to improve flow."
          }
        ]
      },
      {
        name: "Peja",
        hasPriority: true,
        suggestions: [
          {
            icon: "road",
            title: "City Center Road Repair",
            priority: "high",
            description: "High traffic at 42.6697, 20.3100 with road surface issues. Resurface and widen main roads to accommodate traffic volume."
          },
          {
            icon: "store",
            title: "Commercial District Access",
            priority: "medium",
            description: "Congestion at 42.6670, 20.3080. Create dedicated delivery zones and time restrictions for commercial vehicles."
          }
        ]
      },
      {
        name: "Mitrovica",
        hasPriority: true,
        suggestions: [
          {
            icon: "road",
            title: "Bridge Area Improvement",
            priority: "high",
            description: "Significant traffic at 42.8800, 20.8600. Widen approach roads and implement traffic calming measures to improve safety and flow."
          },
          {
            icon: "car-side",
            title: "East-West Connector",
            priority: "high",
            description: "Missing connection at 42.8820, 20.8630. Build new connector road to improve cross-city mobility and reduce congestion on main streets."
          }
        ]
      },
      {
        name: "Ferizaj",
        hasPriority: true,
        suggestions: [
          {
            icon: "road",
            title: "Main Road Expansion",
            priority: "high",
            description: "High traffic at 42.3700, 21.1500. Add additional lanes and improve intersections to handle increasing traffic volume."
          },
          {
            icon: "train",
            title: "Railway Crossing Improvement",
            priority: "high",
            description: "Dangerous railway crossing at 42.3650, 21.1450. Build grade-separated crossing to improve safety and reduce delays."
          }
        ]
      },
      {
        name: "Vushtrri",
        hasPriority: true,
        suggestions: [
          {
            icon: "truck",
            title: "Bypass Road",
            priority: "high",
            description: "Heavy through traffic at 42.8131, 20.9575. Construct bypass road to divert regional traffic away from city center."
          },
          {
            icon: "road",
            title: "Main Road Widening",
            priority: "medium",
            description: "Traffic bottleneck at 42.8231, 20.9675. Widen main road through city center to accommodate traffic volume."
          }
        ]
      }
    ];

    // Function to generate city cards
    function generateCityCards() {
      const cityList = document.getElementById('cityList');
      cityList.innerHTML = '';

      cityData.forEach(city => {
        const cityCard = document.createElement('div');
        cityCard.className = 'city-card';
        cityCard.dataset.city = city.name.toLowerCase();

        // Add priority indicator
        if (city.hasPriority) {
          const priorityDot = document.createElement('div');
          priorityDot.className = 'priority-indicator priority-high';
          cityCard.appendChild(priorityDot);
        }

        const cityHeader = document.createElement('div');
        cityHeader.className = 'city-header';
        cityHeader.innerHTML = `
          <h3><i class="fas fa-city"></i> ${city.name}</h3>
          <i class="fas fa-chevron-down toggle-icon"></i>
        `;
        cityHeader.onclick = function() {
          toggleCity(cityCard);
        };

        const cityContent = document.createElement('div');
        cityContent.className = 'city-content';

        // Generate suggestions for this city
        city.suggestions.forEach(suggestion => {
          const suggestionElement = document.createElement('div');
          suggestionElement.className = 'suggestion';
          suggestionElement.innerHTML = `
            <div class="suggestion-icon">
              <i class="fas fa-${suggestion.icon}"></i>
            </div>
            <div class="suggestion-content">
              <h4 class="suggestion-title">${suggestion.title} <span class="badge badge-${suggestion.priority}">${suggestion.priority.charAt(0).toUpperCase() + suggestion.priority.slice(1)} Priority</span></h4>
              <p class="suggestion-desc">${suggestion.description}</p>
            </div>
          `;
          cityContent.appendChild(suggestionElement);
        });

        cityCard.appendChild(cityHeader);
        cityCard.appendChild(cityContent);
        cityList.appendChild(cityCard);
      });
    }

    // Function to toggle city dropdown - modified to prevent shifting
    function toggleCity(cityCard) {
      const isActive = cityCard.classList.contains('active');
      
      // Close all cards first
      document.querySelectorAll('.city-card').forEach(card => {
        card.classList.remove('active');
      });
      
      // Then open the clicked one if it wasn't already open
      if (!isActive) {
        cityCard.classList.add('active');
      }
    }

    // Function to filter cities based on search input
    function filterCities() {
      const searchInput = document.getElementById('citySearch');
      const filter = searchInput.value.toLowerCase();
      const cityCards = document.querySelectorAll('.city-card');
      let hasResults = false;
      
      cityCards.forEach(card => {
        const cityName = card.dataset.city;
        if (cityName.includes(filter)) {
          card.style.display = '';
          hasResults = true;
        } else {
          card.style.display = 'none';
        }
      });
      
      // Show no results message if needed
      const noResultsElement = document.querySelector('.no-results');
      if (!hasResults && filter !== '') {
        if (!noResultsElement) {
          const noResults = document.createElement('div');
          noResults.className = 'no-results';
          noResults.innerHTML = 'No cities found matching your search.';
          document.getElementById('cityList').appendChild(noResults);
        }
      } else if (noResultsElement) {
        noResultsElement.remove();
      }
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
      generateCityCards();
      
      // Open the first city by default
      const firstCity = document.querySelector('.city-card');
      if (firstCity) {
        toggleCity(firstCity);
      }
    });
  </script>
</body>
</html>
