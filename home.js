function searchLocation() {
    const searchInput = document.getElementById('searchBox').value.toLowerCase();
    const locations = document.querySelectorAll('.location');
  
    locations.forEach(location => {
      const city = location.getAttribute('data-city').toLowerCase();
      if (city.includes(searchInput)) {
        location.style.display = 'block';
      } else {
        location.style.display = 'none';
      }
    });
  }
  
  function navigate(city) {
    // Change the h1 text to "M O V I E S AT city"
    const title = document.getElementById('mainTitle');
    title.textContent = `MOVIES AT ${city}`;
  }
  
  // For cities not listed in the locations
  function navigateOtherCity() {
    const searchInput = document.getElementById('searchBox').value;
    if (searchInput) {
      navigate(searchInput);
    } else {
      alert("Please enter a city name");
    }
  }
  