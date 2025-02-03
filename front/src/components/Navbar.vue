<template>
    <nav class="navbar">
      <ul>
        <li><router-link to="/recetas"><img src="../assets/images/homee.png" alt="Home" /></router-link></li>
        <li><router-link to="/search"><img src="../assets/images/lupa.png" alt="Search" class="search-icon" /></router-link></li>
        <li><router-link to="/agregar"><img src="../assets/images/mas.png" alt="Post" /></router-link></li>
        <li><router-link to="/guardar"><img src="../assets/images/guardar.png" alt="Saved" /></router-link></li>
        
        <li><button><img src="../assets/images/perfil.png" alt="Profile" /></button></li>
       


      </ul>
    </nav>
  </template>
  
  <script>
  export default {
    name: 'Navbar',
  };
  </script>
  
  <style scoped>
  .navbar {
    background-color: #343330;
    padding: 10px 0;
    text-align: center;
    position: fixed; 
    bottom: 0;
    left: 0;
    width: 100%; 
    z-index: 1000; 
  }
  
  .navbar ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: space-around; 
  }
  
  .navbar button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
  }
  
  .navbar img {
    width: 30px;
    height: 30px;
    object-fit: contain;
  }
  
  .navbar button:hover img {
    opacity: 0.8;
  }
  </style>
  