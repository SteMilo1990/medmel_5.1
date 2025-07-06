// Load header
$( document ).ready(function() {
  if(document.getElementById('header').innerHTML == ''){    
      $("#header").load("header.html");
  }
});

var overlayPage = "";
//-----------Reset fields--------------------------
function resetFields(){
    let conf = confirm("Do you want to clear all fields?");
    if (conf == true){
        setupStavesUI('', '', '', '', '','', '','', [""], [""], 0);

        resetLoadStaves();
        resetLoadParameters();
        resetController();
        resetFacsimile();
        try{
             disableControls(-1);
        }catch{}
    }
}
//-------End Reset fields--------------------------
//----------Download a PDF copy of Staves----------
function printScoreInPages() {
    let svg = document.querySelector("#staves");
    let groups = svg.querySelectorAll("g.break"); // Sostituisci con il selettore giusto
    let perPage = 5; // Numero di pentagrammi per pagina (da regolare)

    for (let i = 0; i < groups.length; i += perPage) {
        let clone = svg.cloneNode(true);
        
        // Rimuovi i gruppi fuori dalla pagina corrente
        clone.querySelectorAll("g.break").forEach((g, index) => {
            if (index < i || index >= i + perPage) {
                g.remove();
            }
        });

        let newWindow = window.open();
        newWindow.document.write("<svg>" + clone.innerHTML + "</svg>");
        newWindow.document.close();
        newWindow.print();
        newWindow.close();
    }
}
function printPDF() {
  let page = localStorage.getItem("current_page");
  if (page == "editor.html" || page == "viewer.html"){
    printPDF_editorViewer()
  }else{
    printPDFCompare();
  }
}

function getFont(val){
  val = JSON.parse(val);
  if (val == 0){
    return "Times New Roman";
  }else if (val == 1) {
    return "Garamond";
  }else if (val == 2) {
    return "Courier";
  }else if (val == 3) {
    return "Roboto";
  }else if (val == 4) {
    return "Juniper";
  }
}

function printPDF_editorViewer(){
    try{
        printContents.getElementsByClassName("selectedNote")[0].classList.remove("selectedNote");
    }catch{}
    
    let title = "";
    let id_input = "";
    let author = "";
    let language = "";
    let ms = "";
    let f_input = "";
    let select = 0;
    const a4Height = 1588; // Pixels
    const a4Width = 1123;
    const margins = 50;
    const svgNS = "http://www.w3.org/2000/svg";
    
    let fontSize = JSON.parse(document.getElementById("nFontSizeModern").value);
    let fontFamily = 19;

    try {
      fontFamily = getFont(document.getElementById("changeTextFontModern").value);
    }catch{
      fontSize = 19;
    }
    if (currentStyle == 1){
      try {
        fontSize = JSON.parse(document.getElementById("nFontSizeOld").value);
      }catch{
        fontSize = 19;
      }
      fontFamily = getFont(document.getElementById("changeTextFontOld").value);
    }
    const titleFontSize = fontSize + 15;
    const authorFontSize = fontSize + 10;
    const idFontSize = fontSize;
    const msFontSize = fontSize;

    const headerContainer = document.createElement("div");
    headerContainer.setAttribute("style", "display:flex;flex-direction: column; align-items: center;");
    
    // create title slot
    let title_value = document.getElementById("title_input").value;
    if (title_value != "") title_value = document.getElementById("title").innerHTML; // get value directly from element (graces)
    const title_slot = document.createElement("div");
    title_slot.innerHTML = title_value;
    title_slot.setAttribute("style", `font-family:'${fontFamily}';font-size:${titleFontSize}px;`);
    
    // create author slot
    let author_value = document.getElementById("author_input").value;
    if (author_value != "") author_value = document.getElementById("author").innerHTML; // get value directly from element (graces)
    const author_slot = document.createElement("div");
    author_slot.innerHTML = author_value;  
    author_slot.setAttribute("style", `font-family:'${fontFamily}';font-size:${authorFontSize}px;`);
    
    // create id slot
    const id_value = document.getElementById("id_input").value;
    const id_slot = document.createElement("div");
    id_slot.innerHTML = id_value;
    id_slot.setAttribute("style", `font-family:'${fontFamily}';font-size:${idFontSize}px;`);
  
    const ms_value = document.getElementById("ms_input").value;
    const f_value = document.getElementById("f_input").value;
    const ms_f_slot = document.createElement("div");
    ms_f_slot.innerHTML = `${ms_value} ${f_value}`;  
    ms_f_slot.setAttribute("style", `font-family:'${fontFamily}';font-size:${msFontSize}px;`);
    
    // Clone main svg and remove old append annotations and append new one
    const svg_clone = document.getElementById("staves").cloneNode(true);
  
    // get and recreate Annotation text in svg (if any annotation)
    const annotations_text_value = document.getElementById("annotationsSection").innerHTML;
    if (annotations_text_value != ""){
      const annotations_slot = document.createElementNS(svgNS, "svg");
      const annotations_text = document.createElementNS(svgNS, "text");
      annotations_text.textContent = annotations_text_value; // Use textContent instead of innerHTML
      annotations_slot.setAttribute("style", `font-family:'${fontFamily}';font-size:${msFontSize}px;`);
      annotations_slot.appendChild(annotations_text);
    }

    
    // calculate scale of svg
    const singleStavesGroupHeight = svg_clone.querySelectorAll("svg")[0].width;    
    let scale = 100;
    if (singleStavesGroupHeight < a4Width){
      scale = (a4Width - margins * 2) / singleStavesGroupWidth * 100;
      console.log(scale);
    }else{
      console.log(`singleStavesGroupHeight ${singleStavesGroupHeight} > a4Width ${a4Width}`);
    }
    scale = window.prompt(`Enter preferred zoom level (auto: ${(scale)}%)`, scale);
    scale = scale / 100;
    svg_clone.style.transform = `scale(${scale})`;
    
    let container = document.createElement("div");
    [title_slot, author_slot, id_slot, ms_f_slot].forEach((item, i) => {
      headerContainer.appendChild(item);
    });
    container.appendChild(headerContainer);
    container.appendChild(svg_clone);
    

    
    // Open a new window for printing
    const print_title = `${id_value}_${ms_value}_${f_value}`;
    
    let printWindow = window.open('', '_blank');
    
    if (currentStyle == 0) {
      printWindow.document.write('<html><head><title>'+print_title+'</title></head><body>');
    }else{
      printWindow.document.write('<html><head><title>'+print_title+'</title><style>.groupClickableRect {fill: transparent}' 
      + ' .staffLines line {stroke:black; stroke-width:1}'
      + ' #staves {display:flex;flex-direction:column;}</style></head><body>');
    }
    printWindow.document.write('<div id="printContent"></div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    // Append the paginated SVG
    let printContent = printWindow.document.getElementById('printContent');
    printContent.appendChild(container);
    printContent.setAttribute("style", "zoom:" + scale);

    // Print after a short delay to ensure rendering
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

//-------toggle visibility overlay panel------------------
function toggleVisibility() {
  var div = document.getElementById("overlayPanel");
  if (div.style.display === "none") {
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}

function closeOverlayPanel() {
  var div = document.getElementById("overlayPanel");
  div.style.display = "none";
}

function switchOverlayContent(page, pageType){
    hideOverlay();
    setTimeout(addContentToOverlay, 400, page, pageType);
}
var overlay = 0
function addContentToOverlay(page, pageType, message=false){
    overlay = 1
    document.body.style.overflow = 'hidden';

    var div = document.getElementById("overlayPanel");
    div.innerHTML = "";
    overlayPage = page;
    toggleVisibility();
    if (pageType == "logo"){
        $(div).load("./templateOverlay.html");
    }
    else if(pageType == "blank"){
        $(div).load("./blankTemplateOverlay.html");
    }

    var view = document.getElementById("overlayView");
    
    if (message == true){
      
      setTimeout(() => {
        try {
              document.getElementById("message").style.display = "block";
          } catch {}
      },100);
    }
}

function hideOverlay(){
    overlay = 0;
    try {
      var div = document.getElementById("overlayView");
      div.classList.remove("fadeInOverlay");
      div.classList.add("fadeOutOverlay");
    }catch(e){}
    setTimeout(closeOverlayPanel, 400);
    setTimeout(() => document.body.style.overflow = "auto",  400);
}

$(document).keyup(function(e) {
  if (e.which == 27 && overlay == 1){
    hideOverlay();
  }
});


function toggleSettingsMenù(){
    let div = document.getElementById("settings");
    
    if (div.style.display === "none") {
      div.style.display = "block";
    } else {
      div.style.display = "none";
    }
}

function toggleImpExMenu(){
    let div = document.getElementById("importExport");
    if (div.style.display === "none") {
      div.style.display = "block";
    } else {
      div.style.display = "none";
    }
}

function showSettings() {
    hidePopup();
    divSetting.style.animation = 'fading 0.0s';
    toggleSettingsMenù();
    setFontSizeValues();
  }

function showImportExportMenu() {
    hidePopup();
    let x = event.clientX;
    if (window.innerWidth-event.clientX < 250) {
      x = x-100;
    }else{
      x = x-100;
    }
    let y = event.clientY+20;
    let divImpEx = document.getElementById("importExport");

    $(divImpEx).css({position:"absolute", left:x,top:y});
    divImpEx.style.animation = 'fading 0.0s';
    toggleImpExMenu();
}

function togglePlayerOverlay(){
  let div = document.getElementById("player");

  if (div.style.display === "none") {
    div.style.display = "block";
  } else {
    div.style.display = "none";
    pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  }
}

function toggleUserOptionsMenu(action="none") {
    let x = event.clientX;
    if (window.innerWidth-event.clientX < 250) {
      x = x - 140;
    }
    let y = event.clientY + 20;
    let userOptionsMenu = document.getElementById("userOptionsMenu");

    $(userOptionsMenu).css({position:"absolute", left:x, top:y});
    userOptionsMenu.style.animation = 'fading 0.0s';
    
    if (userOptionsMenu.style.display === "none" || action == "") {
      userOptionsMenu.style.display = "block";
    } else {
      userOptionsMenu.style.display = "none";
    }
}


function showPlayerOverlay() {
    let x = event.clientX;
    let y = event.clientY+20;
    let divPlayerOverlay = document.getElementById("player");
    $(divPlayerOverlay).css({position:"absolute", left:x,top:y});
    divPlayerOverlay.style.animation = 'fading 0.0s';
    togglePlayerOverlay();
}

function getCookie(name) {
  return Object.fromEntries(document.cookie.split("; ").map(c => c.split("=")))[name];
}

function setFontSizeValues() {
  let fontSizeOldValue = getCookie("fontSizeOld") || "17";
  let fontSizeValue = getCookie("fontSize") || "16";

  document.getElementById("nFontSizeOld").value = parseInt(fontSizeOldValue);
  document.getElementById("nFontSizeModern").value = parseInt(fontSizeValue);
}


//-------End Settings Menù------------------------------------

function resetFacsimile() {
  document.getElementById("urlInput").value = "";
  processURL();
  closeIframe();
}

