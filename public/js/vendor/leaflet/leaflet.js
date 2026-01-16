/* Leaflet JS fallback */
(function() {
  console.log('Leaflet fallback loaded');
  
  // Basic Leaflet-like functionality
  window.L = {
    map: function() {
      console.log('Leaflet map fallback initialized');
      return {
        setView: function() { return this; },
        addLayer: function() { return this; }
      };
    },
    tileLayer: function() {
      return {
        addTo: function() { return this; }
      };
    },
    marker: function() {
      return {
        addTo: function() { return this; },
        bindPopup: function() { return this; }
      };
    }
  };
})();