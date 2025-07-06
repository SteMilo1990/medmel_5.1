const manifests = {
  "french": {
    "A": "https://api.irht.cnrs.fr/ark:/63955/f2h3p7upoyoo/manifest.json",
    "B": "https://e-codices.ch/metadata/iiif/bbb-0231/manifest.json",
    "D": "https://sammlungen.ub.uni-frankfurt.de/i3f/v20/4617677/manifest",
    "F": "",
    "I": "",
    "K": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b550063912/manifest.json",
    "L": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b8454670d/manifest.json",
    "M": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b84192440/manifest.json",
    "N": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b6000955r/manifest.json",
    "O": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b6000950p/manifest.json",
    "P": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b8454673n/manifest.json",
    "Q": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b8454666h/manifest.json",
    "R": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b8454668b/manifest.json",
    "T": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b60007945/manifest.json",
    "U": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b60009580/manifest.json",
    "V": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b84386028/manifest.json",
    "W": "https://gallica.bnf.fr/view3if/ga/ark:/12148/btv1b6001348v",
    "X": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b530003205/manifest.json",
    "a": "https://digi.vatlib.it/iiif/MSS_Reg.lat.1490/manifest.json",
    "g": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b6000803p/manifest.json",
    "i": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b8454680s/manifest.json",
    "j": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b52512868f/manifest.json",
    "m": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b10032175s/manifest.json",
    "o": ""
  },
  "galician-portuguese": {
    "E": "https://rbdigital.realbiblioteca.es/files/manifests/b-I-2.json"
  },
  "german": {
    "k": "https://api.digitale-sammlungen.de/iiif/presentation/v2/bsb00105055/manifest",
    "J": "https://collections.thulb.uni-jena.de/api/iiif/presentation/v2/HisBest_derivate_00001155/manifest"
  },
  "latin": {
    "F": "https://tecabml.contentdm.oclc.org/iiif/2/plutei:753799/manifest.json"
  },
  "occitan": {
    "G": "https://digitallibrary.unicatt.it/veneranda/data/public/manifests/0b/02/da/82/80/05/1b/f4/0b02da8280051bf4.json",
    "X": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b60009580/manifest.json",
    "W": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b84192440/manifest.json",
    "R": "https://gallica.bnf.fr/iiif/ark:/12148/btv1b60004306/manifest.json"
  },
}
function tryToGetFacsimile() {
  
  const urlInput = document.getElementById("urlInput").value;
  const lang = document.getElementById("language").value;
  const siglum = document.getElementById("ms_input").value;
  let folio = document.getElementById("f_input").value;
  folio = cleanFolioInput(folio);
  let manifestInput = document.getElementById("manifestInput");
  
  let manifestURL = manifestInput.value;
  if (manifestURL == "" || manifestURL == "undefined") {
    manifestURL = getManifestURL(siglum, lang);
    manifestInput.value = manifestURL;
  }
  
  manifestInput.addEventListener('keyup', (e) => {
    let mf = document.getElementById("manifestInput").value
    try{
      createFolioSelectFromManifest(mf);
    }catch{
      
      console.log("Manifest URL not valid");
    }
  });
  
  let iiifIdInput = document.getElementById("iiifIdInput").value;
  if (iiifIdInput == "" || iiifIdInput == "undefined") {
    iiifIdInput = fetchCanvasIdFromManifest(manifestURL, folio);
    if (iiifIdInput !== undefined){
      document.getElementById("iiifIdInput").value = iiifIdInput;
    }
  }
  
  if (urlInput != ""){
    processURL(urlInput);
    return 1;
  }
  else if (manifestURL) {
    loadFolioOnMirador(manifestURL, iiifIdInput);
    return 2
  }  
}

function getManifestURL(siglum, lang) {
  lang = lang.toLowerCase();
  if (manifests[lang] && manifests[lang][siglum]) {
    return manifests[lang][siglum];
  } else {
    console.log("Manifest not found");
    return null;
  }
}

function fetchCanvasIdFromManifest(manifestURL, folio) {
  if (manifestURL == null) return;
  fetch(manifestURL)
    .then(response => response.json())
    .then(manifest => {
      const canvases = manifest.sequences[0].canvases;
      createFolioSelect(canvases);
      const canvas = canvases.find(c => {
            const regex = new RegExp(`(^|[^\\d])${folio}$`); // Match either start or non-digit before the folio
            return regex.test(c.label);
          });
      if (canvas) {
        document.getElementById("iiifIdInput").value = canvas["@id"];
        document.getElementById('foliosSelect').value = canvas["@id"];

        loadFolioOnMirador(manifestURL, canvas["@id"])
      } else {
        console.log('Folio not found ('+folio+'})');
      }
    })
}

function createFolioSelectFromManifest(manifestURL) {
  if (manifestURL == null) return;
  fetch(manifestURL)
    .then(response => response.json())
    .then(manifest => {
      const canvases = manifest.sequences[0].canvases;
      createFolioSelect(canvases);
    })
}


function createFolioSelect(canvases) {
  let select = document.getElementById("foliosSelect");
  // Populate <select> options
  canvases.forEach((canvas, index) => {
    const option = document.createElement('option');
    option.value = canvas['@id']; // Use the canvas ID as the value
    option.textContent = canvas.label; // Use the label as the display text
    select.appendChild(option);
  });
  
  select.style.display = ""
  // Optional: Add an event listener to handle selection
  select.addEventListener('change', (e) => {
    document.getElementById("iiifIdInput").value = e.target.value;
  });
}

function loadFolioOnMirador(manifestLink, canvasId) {
  viewerInstance = Mirador.viewer({
    id: "frameBox",
    windows: [{
      manifestId: manifestLink,
      canvasId: canvasId,
      sideBarOpen: false,
      view: 'single',
      zoomToBoundsEnabled: true,
    }],
    window: {
      allowWindowSideBar: false,
    },
    workspaceControlPanel: {
      enabled: false
    }
  });

  const focusRegion = {
    x: 1000,   // Adjust X coordinate as needed
    y: 500,    // Adjust Y coordinate as needed
    width: 800, // Define the width of the zoomed region
    height: 600 // Define the height of the zoomed region
  };

  const state = viewerInstance.store.getState();
  const viewerWindow = state.windows && Object.values(state.windows)[0];

  if (viewerWindow) {
    viewerInstance.store.dispatch(Mirador.actions.updateViewport({
      windowId: viewerWindow.id,
      x: focusRegion.x,
      y: focusRegion.y,
      width: focusRegion.width,
      height: focusRegion.height
    }));
  }
}

document.addEventListener("click", function(event) {
  if (event.target.closest(".mirador-window-close")) {
    // Call the function you want to trigger
    toggleDivideScreen();
  }
});

function resizeMirador(viewerInstance) {
  const windowIds = Object.keys(viewerInstance.store.getState().windows);
  const windowId = windowIds[0]; // Assuming there's one window
  if (windowId) {
    viewerInstance.store.dispatch({
      type: 'mirador/UPDATE_WINDOW_VIEWPORT',
      windowId: windowId,
      payload: {
        zoom: 1.0 // Adjust zoom level or keep the current value if needed
      }
    });
  }
}

function cleanFolioInput(folio) {
  // Remove everything before the first digit and clean after any dash
  return folio.replace(/^[^\d]+/, '').replace(/-.*/, ''); 
}

function fetchFacsimile() {
  tryToGetFacsimile();
  toggleDivideScreen()
  closeFacsimileOverlay(event, true);
}

// Function to open overlay
function openFacsimileOverlay() {
  tryToGetFacsimile();

  document.getElementById("facsimile-overlay").style.display = "flex";
}

// Function to close oondocverlay
function closeFacsimileOverlay(event, force=false) {
  if (event.target.id === "facsimile-overlay" || event.target.id === "closeBtnFacsimileOverlay" || force) {
    document.getElementById("facsimile-overlay").style.display = "none";
  }
}

function openFacsimileSideBar() {  
  const urlInput = document.getElementById("urlInput").value;
  const manifestURL = document.getElementById("manifestInput").value;  
  const iiifIdInput = document.getElementById("iiifIdInput").value;
  // If we have the info, open the sidebar
  if (urlInput != "" || (manifestURL != "" && iiifIdInput != "")) {
    toggleDivideScreen();
    tryToGetFacsimile();
  }
  // Otherwise, open the detail overlay
  else {
    openFacsimileOverlay();
  }
}

function toggleManifestList() {
  let manifestList = document.getElementById("manifestListContainer");
  if (manifestList.style.display == "none") {
    manifestList.style.display = "block";
    createManifestList();
  }else{
    manifestList.style.display = "none";
  }
}

function createManifestList() {
  let manifestListSelect = document.getElementById("manifestListSelect");
  manifestListSelect.innerHTML = "";
  
  let option = document.createElement("option");
  option.innerHTML = "Select manuscript"
  option.value = "";
  manifestListSelect.appendChild(option);

  let manifestList = document.getElementById("manifestListContainer");
  let langs = Object.keys(manifests);
  
  langs.forEach((lang, i) => {
    let siglae = Object.keys(manifests[lang]);
    siglae.forEach((siglum, i) => {
    
      option = document.createElement("option");
      option.innerHTML = capitalizeEachWord(lang) + " " + siglum;
      option.value = manifests[lang][siglum];
      
      manifestListSelect.appendChild(option);
    });
  });  
}

function selectManifestFromList() {
  let manifestURL = document.getElementById("manifestListSelect").value;
  document.getElementById("manifestInput").value = manifestURL;
   // toggleManifestList();
   resetFoliosSelect();
   createFolioSelectFromManifest(manifestURL);
}
function resetFoliosSelect() {
  let foliosSelect = document.getElementById("foliosSelect");
  foliosSelect.display = "none";
  foliosSelect.innerHTML = "";
}
function capitalizeEachWord(string) {
  return string
    .split('-') // Split string into an array of words
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()) // Capitalize each word
    .join(' '); // Join the words back into a string
}