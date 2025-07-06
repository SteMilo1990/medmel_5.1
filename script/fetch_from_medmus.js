const url ='https://medmus.seai.uniroma1.it/alternative_attributions'; // Replace with your actual REST export URL

fetch(url, {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  },
  credentials: 'include' // Use 'same-origin' if not sending credentials
})
.then(response => {
  if (!response.ok) {
    throw new Error('Network response was not ok ' + response.statusText);
  }
  return response.json(); // Assuming the response is JSON
})
.then(data => {
  console.log('Data retrieved:', data);
  // Process the data here
})
.catch(error => {
  console.error('There has been a problem with your fetch operation:', error);
});

