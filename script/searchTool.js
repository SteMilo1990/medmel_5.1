// SETUP PAGE
window.onload = localStorage.setItem("current_page", "searchMelody.php");
setUserCredentials();
loadOptions();

// CONSTANTS AND START PARAMS
var noteString = "";
var statusAction = true;
var notePositionX;
const noteHeadPath = "M 303.13715,299.65106 C 299.74131,301.47103 297.93187,304.76561 299.04493,307.24402 C 300.23219,309.88766 304.31194,310.63374 308.15151,308.90939 C 311.99107,307.18503 314.14367,303.63999 312.95641,300.99636 C 311.76914,298.35272 307.6894,297.60664 303.84983,299.33099 C 303.60986,299.43876 303.36355,299.52973 303.13715,299.65106 z";
const flatPath = "m 27,41 -1,-66 v -11 c 0,-22 1,-44 4,-66 45,38 93,80 93,139 0,33 -14,67 -43,67 C 49,104 28,74 27,41 z m -42,-179 -12,595 c 8,5 18,8 27,8 9,0 19,-3 27,-8 L 20,112 c 25,21 58,34 91,34 52,0 89,-48 89,-102 0,-80 -86,-117 -147,-169 -15,-13 -24,-38 -45,-38 -13,0 -23,11 -23,25 z";
const naturalPath = "m 3.0741498,14.12384 0,-7.3833424 -2.10403349,0.6552034 0,-2.6711843 -0.3402905,0 0,7.3960313 2.10441799,-0.655203 0,2.658495 z M 0.97011631,8.6053665 2.7342438,7.9501631 l 0,2.3182049 -1.76412749,0.642515 z";
const sharpPath = "M 86.102000,447.45700 L 86.102000,442.75300 L 88.102000,442.20100 L 88.102000,446.88100 L 86.102000,447.45700 z M 90.040000,446.31900 L 88.665000,446.71300 L 88.665000,442.03300 L 90.040000,441.64900 L 90.040000,439.70500 L 88.665000,440.08900 L 88.665000,435.30723 L 88.102000,435.30723 L 88.102000,440.23400 L 86.102000,440.80900 L 86.102000,436.15923 L 85.571000,436.15923 L 85.571000,440.98600 L 84.196000,441.37100 L 84.196000,443.31900 L 85.571000,442.93500 L 85.571000,447.60600 L 84.196000,447.98900 L 84.196000,449.92900 L 85.571000,449.54500 L 85.571000,454.29977 L 86.102000,454.29977 L 86.102000,449.37500 L 88.102000,448.82500 L 88.102000,453.45077 L 88.665000,453.45077 L 88.665000,448.65100 L 90.040000,448.26600 L 90.040000,446.31900 z";
const svgNS = 'http://www.w3.org/2000/svg';
const ledgerPathCenter = "M 317,309.9 295,309.9";
const ledgerPathHigher = "M 317,315.8 295,315.8";
const ledgerPathHighest = "M 317,321.7 295,321.7";
var existingIds = new Set();
var accidental_status = "none";
var plica_status = 0;
var syl_number = 0;
var notePositionX = 40;

function loadOptions() {
  if (document.cookie.indexOf("search_transpositions=1") == -1){
    document.getElementById('intervalCB').checked = false;
  } else {
    document.getElementById('intervalCB').checked = true;
  }
  
  if (document.cookie.indexOf("spacesCB=0") == -1){
    document.getElementById('spacesCB').checked = true;
  } else {
    document.getElementById('spacesCB').checked = false;
  }
  
  if (document.cookie.indexOf("plicaCB=0") == -1){
    document.getElementById('plicaCB').checked = true;
  } else {
    document.getElementById('plicaCB').checked = false;
  }
  
  if (document.cookie.indexOf("searchPosition=") == -1) {
     document.getElementById('searchAnywhereRadio').checked = true;
 } else {
     const searchPosition = document.cookie.replace(/(?:(?:^|.*;\s*)searchPosition\s*\=\s*([^;]*).*$)|^.*$/, "$1");
     if (searchPosition === "0") {
         document.getElementById('searchAnywhereRadio').checked = true;
     } else if (searchPosition === "1") {
         document.getElementById('lineBeginningRadio').checked = true;
     } else if (searchPosition === "2") {
         document.getElementById('lineEndingRadio').checked = true;
     } else if (searchPosition === "3") {
         document.getElementById('searchWholeLine').checked = true;
     }
 }
}

function setuptolerance(tol){
  if(tol != ''){
    document.getElementById('tolerance').value = tol;
    document.getElementById('txttolerance').innerHTML = tol+'%';
  }
}

function setConsiderSpaces() {
  let cb = document.getElementById("spacesCB");
  if (cb.checked) {
    document.cookie = "spacesCB=1";
  }else{
    document.cookie = "spacesCB=0";
  }
}

function setConsiderPlicae() {
  let cb = document.getElementById("plicaCB");
  if (cb.checked) {
    document.cookie = "plicaCB=1";
  }else{
    document.cookie = "plicaCB=0";
  }
}

function setSearchPosition(position) {
  document.cookie = "searchPosition="+position;
}


function searchTranspositions(){
  let cb = document.getElementById("intervalCB");
  if (cb.checked) {
    document.cookie = "search_transpositions=1";
    
    // also check and disable plica
    document.getElementById("plicaCB").checked = true;
    setConsiderPlicae();
    document.getElementById("plicaCB").disabled = true;
  }else{
    document.cookie = "search_transpositions=0";
    document.getElementById("plicaCB").disabled = false;
  }
}

function setupLenguageUI(prev=''){
    let optionDefault = document.createElement("option");
    optionDefault.setAttribute("value",'') ;
    optionDefault.text = "All";

    let objSelect = document.getElementById('selectLanguage');
    objSelect.appendChild(optionDefault);

    $.ajax({
        type : "POST",  //type of method
        url  : "php/getLanguageList.php",  //your page
        data: {},
        success: function(data){
            try{
                let query = jQuery.parseJSON(data);
                let dim = Object.keys(query).length;
                // console.log(dim);
                for(let i =0; i< dim; i = i+1){
                    let option = document.createElement("option");
                    option.setAttribute("value", query[i]);
                    option.text = ""+query[i];
                    objSelect.appendChild(option);
                }
            } catch {}

            if (prev!='') {
              objSelect.value = prev;
            }
        }
    });
}
// TRIGGER CREATION ON CLICK ON POLYGON
$(".clickablePolygon").click(function(){
    if (statusAction) {
        let noteId = this.id;
        createNote(noteId);
    }
});

function drawHeader() {
  $("#header").load("header.html");
}

function setUserCredentials() {
  let session_email = localStorage.getItem('session_email');
  let pw = localStorage.getItem('session_psw');
  
  if (session_email !== null) {
    document.getElementById('userId').value = session_email;
    document.getElementById('password_input').value = pw;
  }
}

// Helper functions

// SEARCH
function printResult(array, prog) {
  let div = document.getElementById('resultDiv');
  let strTable = '<center>';
  updateProgressBar(prog);
  
  setTimeout(function (){
    for(let i = 0; i < 100000; i++)
      {
        strTable += '<tr>'+
                    '<td>'+array[i][0]+
                    '</td>'+
                    '<td>'+array[i][1]+
                    '</td><tr>';
        prog = ((i + 100001) * 100) / 200000;
        updateProgressBar(prog);
      }
      
  strTable += '</table></center>';
  div.innerHTML = strTable;
  strTable = "";
  array = null;
    
  }, 500);
}

function getLineStavesUI(strNotes, idStaves, id, ms, folio, title, author, text, row, score, transposed, code){
    const tr = document.createElement('tr');
    tr.setAttribute('style', 'background:white;');
    tr.setAttribute('onClick', 'window.location.href="viewer.html?id='+idStaves+'"');

    const td1 = document.createElement('td');
    td1.style.cursor = 'pointer';
    td1.innerHTML = `
      ${id}<br>
      line ${parseInt(row) + 1}<br>
      ${ms} ${folio}
      <div style="font-size:12px;">${author}</div>
    `;

    const td2 = document.createElement('td');
    td2.style.cursor = 'pointer';
    td2.innerHTML = `<b class="title">${title}</b>`;

    const td3 = document.createElement('td');
    td3.style.cursor = 'pointer';
    const transp = transposed ? 'Transposed' : '';
    td3.innerHTML = `
      <div style="font-size:12px;">
        <b>Score : ${score}%</b><br>
        ${transp}
      </div>
    `;

    const a = document.createElement('a');
    a.href = `viewer.html?id=${idStaves}`;

    const svg = document.createElement('div');
    svg.style.zoom = '0.5';
    svg.disabled = true;
        
    strNotes = strNotes.replace(/[CFGAD]\d ?|[bh] ?}/gi, "");

    $.ajax({
        type : "POST",  //type of method
        url  : "php/printStaves.php",  //your page
        data : {notes : strNotes, text_string : text, callingFrom : "searchTool"},// passing the values
        success: function(res){
            svg.innerHTML = res;
            svg.innerHTML = svg.getElementsByTagName("div")[0].innerHTML;
            try{
                svg.getElementsByTagName('br')[0].remove();
                svg.getElementsByTagName('br')[1].remove();
                svg.getElementsByTagName('br')[2].remove();
                svg.getElementsByTagName('br')[3].remove();
            }catch{}
        }
    });

    td2.appendChild(svg);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    return tr;
}

function updateProgressBar(perc){
    $('.progress-bar').attr('aria-valuenow', perc).css('width', perc+'%');
    document.getElementsByClassName('progress')[0].style.display = 'flex';
    document.getElementById('txtProgressBar').innerHTML = "<b>"+perc.toFixed(2)+"% Completed</b>";
    document.getElementById('startQuerySearch').disabled = true;
    document.getElementById('tolerance').disabled = true;
    document.getElementById('selectLanguage').disabled = true;
    statusAction=false;
    if(perc == 100){
        setTimeout(()=> document.getElementsByClassName('progress')[0].style.display = 'none', 100);
        setTimeout(()=> document.getElementById('startQuerySearch').disabled = false, 100);
        setTimeout(()=> document.getElementById('tolerance').disabled = false, 100);
        setTimeout(()=> document.getElementById('selectLanguage').disabled = false, 100);
        setTimeout(()=> statusAction = true, 200);
    }
}


// CREATE NOTE
var searchArray = [];
function createNote(pitch, params = "") {
  if (params != "") {
    accidental_status = params["accidental_status"];
    plica_status = params["plica_status"];
  }
  u_id = generateUniqueId(existingIds);
  
  if (pitch == "h" && accidental_status == -1) {
    pitch = "b";
  } else if (pitch == "+h" && accidental_status == -1) {
    pitch = "+b";
  } else if (pitch == "H" && accidental_status == -1) {
    pitch = "B";
  }
  
  if (pitch == "b" || pitch == "+b" || pitch == "B") {
    this_accidental_status = -1;
  }else{
    this_accidental_status = accidental_status
  }
  var newNote = {
    "pitch": pitch,
    "accidental": this_accidental_status,
    "plica": plica_status,
    "syl_number": syl_number,
    "u_id": u_id
  }
  searchArray.push(newNote);
  arrayToString();
  updateSearchViz();
  resetModifiers();
}

function updateFromString(string) {
  if (string[0] == " ") {
    string = string.substring(1);
  }
  string = string.replace(/[^abcdefgh+*_#%() ]/gi, "");
  document.getElementById("musicStringInput").value = string;

  stringToArray(string);
  
  updateSearchViz();
}

function cleanPentagram() {

  var svg = document.getElementById('svgSearchWrap');
  // Select all <g> elements within the SVG
  var gElements = svg.querySelectorAll('g');
  // Loop through the selected elements and remove each one
  gElements.forEach(function(gElement) {
      gElement.remove();
  });
}

function updateSearchViz() {
  cleanPentagram();
  
  notePositionX = 40;
  current_syllable = 0;
  searchArray.forEach((noteObj, i) => {
    if (current_syllable != noteObj["syl_number"]) {
      current_syllable = noteObj["syl_number"];
      notePositionX += 40;
    }
    
    visualizeNote(noteObj, notePositionX);

    notePositionX += 20;
    if (noteObj["accidental"] == -1 || noteObj["accidental"] == +1 ||noteObj["accidental"] === 0 ) {
      notePositionX += 20;
    }
  });
  
}

function visualizeNote(noteObj, notePositionX){
    var pitch = noteObj["pitch"];
    // Make space for flat
    if (noteObj["accidental"] != "none") {
       notePositionX = notePositionX + 20;
    }
    
    
    let notePositionY = noteHeight(pitch);
    var coordinates = "";
    if (noteObj["plica"] == 0) {
      coordinates = "matrix(-1.19, 0, 0, -1.19," + (390 + notePositionX) + "," + notePositionY + ")";
    }
    else {
      if (notePositionY < 408) {
        plicaYmodifier = 118;
      }else{
        plicaYmodifier = 119;
      }
      coordinates = "matrix(-0.8, 0, 0, -0.8," + (271 + notePositionX) + "," + (notePositionY - plicaYmodifier) + ")";    
    }
    
    let path = document.createElementNS(svgNS, "path");
    path.setAttribute("d", noteHeadPath);
    path.setAttribute("fill", "black");
    let g = document.createElementNS(svgNS, "g");
    g.setAttribute("transform", coordinates);
    g.setAttribute("id", noteObj["u_id"]);
    g.setAttribute("class", "appendedNote searchNote");
    g.appendChild(path);
    

    if (pitch === "C" || pitch === "A" || pitch === "+a"){
      let cLine = document.createElementNS(svgNS, "path");
      cLine.setAttribute("d", "M 317,304 295,304");
      cLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(cLine);
    }
    if (pitch === "B" || pitch === "H" || pitch === "*G"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathCenter);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (noteObj["accidental"] == -1) {
      let flat = document.createElementNS(svgNS, "path");
      flat.setAttribute("d", flatPath);
      flat.setAttribute("transform","matrix(-0.035, 0,  0,0.035, 324,303)");
      g.appendChild(flat);
    }
    else if (noteObj["accidental"] == +1) {
      let sharp = document.createElementNS(svgNS, "path");
      sharp.setAttribute("d", sharpPath);
      sharp.setAttribute("transform","matrix(1, 0, 0, 1, 235, -141)");
      g.appendChild(sharp);
    }
    else if (noteObj["accidental"] == 0) {
      let natural = document.createElementNS(svgNS, "path");
      natural.setAttribute("d", naturalPath);
      natural.setAttribute("transform","matrix(2, 0,  0, 2, 320,285)");
      g.appendChild(natural);
    }

    if (pitch === "A"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathHigher);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (pitch === "*G"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathHighest);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (pitch === "+h" || pitch === "+b"){
      
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", "M 317,298 295,298");
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }

    document.getElementById("svgSearchWrap").appendChild(g);
    
    try {
      let diff = document.getElementById('divSearch').offsetLeft+36;
      if (document.getElementById('collapseDiv').style.display == 'none') {
        document.getElementById('cursor').style.left = (notePositionX+diff)+"px";
        document.getElementById('cursor').style.top = (mouseY - 62)+"px";
      } else {
        let diff2 = document.getElementById('collapseDiv').offsetHeight;
        document.getElementById('cursor').style.left = (notePositionX+diff)+"px";
        document.getElementById('cursor').style.top = (mouseY - 62 - diff2)+"px";
      }
    }catch{}

}


//prevent page scroll at spacebar input
window.addEventListener('keydown', function(e) {
  if (e.keyCode == 32 && e.target == document.body) {
    e.preventDefault();
  }
});

// DELETE KEY
$(document).keyup(function(e) {

    if ($('input[type=search]').is(":focus") == false && statusAction) {
        if (e.which == 8){
          deleteNote();
        }
        // SPACE KEY
        if (e.which == 32){
          createSpace();
        }

      // LETTER KEYS
      if (event.shiftKey || event.getModifierState("CapsLock")){
         if (e.which == 65){
          createNote("A");
        }else if (e.which == 66){
          createNote("B");
        }else if (e.which == 72){
          createNote("H");
        }else if (e.which == 67){
          createNote("C");
        }else if (e.which == 68){
          createNote("D");
        }else if (e.which == 69){
          createNote("E");
        }else if (e.which == 70){
          createNote("F");
        }else if (e.which == 71){
          createNote("G");
        }
      }
      else{
        if (e.which == 65){
            createNote("a");
          }else if (e.which == 66){
            createNote("b");
          }else if (e.which == 72){
            createNote("h");
          }else if (e.which == 67){
            createNote("c");
          }else if (e.which == 68){
            createNote("d");
          }else if (e.which == 69){
            createNote("e");
          }else if (e.which == 70){
            createNote("f");
          }else if (e.which == 71){
            createNote("g");
          }

      }
      arrayToString();
    }
    // If input directly as string
    else {
      var string = document.getElementById("musicStringInput").value;
      stringToArray(string);
    }
});

function createSpace() {
  syl_number += 1;
  document.getElementById("musicStringInput").value += " ";
}

function arrayToString() {
  searchString = "";
  syl_n = 0;
  var current_syllable = 0;
  for (var i = 0; i < searchArray.length; i++) {
    var unit = "";
    var accid = "";
    var plica_start = "";
    var plica_end = "";
    var pitch = searchArray[i]["pitch"];
    syl_n = searchArray[i]["syl_number"];
    
    // add space if new syl
    if (current_syllable != syl_n) {
      searchString += " ";
      current_syllable = syl_n
    }
    
    // Set accidentals if necessary
    if (searchArray[i]["accidental"] == -1) {
      if (pitch != "b" && pitch != "+b" && pitch != "B") {
        accid = "_";
      }
    } else if (searchArray[i]["accidental"] == +1) {
      accid = "#";
    } else if (searchArray[i]["accidental"] == 0) {
      accid = "%";
    }
    
    // Set plica if necessary
    if (searchArray[i]["plica"] == 1) {
      plica_start = "(";
      plica_end = ")";
    }
    
    unit = plica_start + accid + pitch + plica_end;
    searchString += unit;
  }

  document.getElementById("musicStringInput").value = searchString;
}

function deleteNote() {
  if (searchArray.length > 0) {
    // Remove svg note
    var idNoteToDelete = searchArray[searchArray.length-1]["u_id"];
    document.getElementById(idNoteToDelete).remove();
    
    // Delete last element of searchArray
    searchArray.pop();
    
    // Update string
    arrayToString();
    
    // Set current cursor position
    if (searchArray.length > 1) {
      notePositionX = 40 + searchArray.length * 20 + (searchArray[searchArray.length-1]["syl_number"]-1) * 40;
    } else {
      notePositionX = 40;
    }
  } 
  
  mouseX = e.pageX;
  mouseY = e.pageY;

  let diff = document.getElementById('divSearch').offsetLeft + 36;
  
  if (document.getElementById('collapseDiv').style.display == 'none'){
      document.getElementById('cursor').style.left = (notePositionX + diff) + "px";
      document.getElementById('cursor').style.top = (mouseY - 80)+"px";
  } else {
      let diff2 = document.getElementById('collapseDiv').offsetHeight;
      document.getElementById('cursor').style.left = (notePositionX+diff)+"px";
      document.getElementById('cursor').style.top = (mouseY - 80 - diff2)+"px";
  }
  
  updateSearchViz();
}

function stringToArray(string) {
  existingIds = new Set();
	searchArray = [];
  
  string = string.replace(")", "");
  
  var syl_n = 0;

  for (var i = 0; i < string.length; i++) {
    var pitch = "";
    var accidental = "none"; 
    var plica = 0;
    
    if (string[i] == " ") {
    	syl_n++;
    }
    else {
      while (string[i] == "+" || string[i] == "*" || string[i] == "_" || string[i] == "#" || string[i] == "%" || string[i] == "(") {

        if (string[i] == "+" || string[i] == "*") {
          pitch = string[i];
        }

        if (string[i] == "_") {
          accidental = -1;
        }
        else if(string[i] == "#") {
          accidental = +1;
        }
        else if (string[i] == "%") {
          accidental = 0;
        }
        if (string[i] == "(") {
          plica = 1;
        }
        i++;
      }
      pitch += string[i]
			searchArray.push({
        "pitch": pitch, 
        "accidental": accidental, 
        "plica": plica, 
        "syl_number": syl_n, 
        "u_id": generateUniqueId(existingIds)
      });
    }
  }
}



function generateUniqueId(existingIds) {
    const letters = 'n';
    const chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let id;
    
    do {
        id = letters + Array.from({length: 3}, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    } while (existingIds.has(id));
    
    return id;
}

function changePlicaStatus() {
  if (plica_status == 0) {
    plica_status = 1;
    document.getElementById("plica-status-btn").classList.add("active");
  }else{
    plica_status = 0;
    document.getElementById("plica-status-btn").classList.remove("active");
  }
}

function changeAccidentalStatus(status, btnId) {
  var accMod = document.getElementsByClassName("accMod");
  for (var i = 0; i < accMod.length; i++) {
    accMod[i].classList.remove("active");
  }
  if (accidental_status != status) {
    accidental_status = status;
    document.getElementById(btnId).classList.add("active");
  } else {
    accidental_status = "none";
    document.getElementById(btnId).classList.remove("active");
  }
}

function resetModifiers() {
  document.getElementById("plica-status-btn").classList.remove("active");
  document.getElementById("acc-flat").classList.remove("active");
  document.getElementById("acc-sharp").classList.remove("active");
  document.getElementById("acc-nat").classList.remove("active");
  plica_status = 0;
  accidental_status = "none";

}

// CALCULATE NOTE HEIGHT
function noteHeight(x) {
  if (x === "*G") {
   return "485";
  } else if (x === "A") {
   return "478";
  }else if (x === "H" || x === "B") {
   return "471";
  }else if (x === "C") {
   return "464";
  } else if (x === "D") {
   return "457";
  } else if (x === "E") {
   return "450";
  } else if (x === "F") {
   return "442";
  } else if (x === "G") {
   return "436";
  } else if (x === "a") {
   return "428";
  } else if (x === "h" || x === "b") {
   return "422";
  } else if (x === "c") {
   return "414.5";
  } else if (x === "d") {
   return "407";
  } else if (x === "e") {
   return "400.3";
  } else if (x === "f") {
   return "393";
  } else if (x === "g") {
   return "386";
  } else if (x === "+a") {
   return "379";
  } else if (x === "+h") {
   return "372";
  } else if (x === "+b") {
   return "372";
  }
}

document.getElementById('deleteBtn').addEventListener('keydown', function(event) {
    // Check if the pressed key is the spacebar
    if (event.code === 'Space') {
        // Prevent the default action
        event.preventDefault();
    }
});
document.getElementById('plica-status-btn').addEventListener('keydown', function(event) {
    // Check if the pressed key is the spacebar
    if (event.code === 'Space') {
        // Prevent the default action
        event.preventDefault();
    }
});
document.getElementById('acc-flat').addEventListener('keydown', function(event) {
    // Check if the pressed key is the spacebar
    if (event.code === 'Space') {
        // Prevent the default action
        event.preventDefault();
    }
});
document.getElementById('acc-sharp').addEventListener('keydown', function(event) {
    // Check if the pressed key is the spacebar
    if (event.code === 'Space') {
        // Prevent the default action
        event.preventDefault();
    }
});
document.getElementById('acc-nat').addEventListener('keydown', function(event) {
    // Check if the pressed key is the spacebar
    if (event.code === 'Space') {
        // Prevent the default action
        event.preventDefault();
    }
});


function addToSearchHistory() {
  var search = document.getElementById("musicStringInput").value;
  // Get the search history cookie and parse it as an array
  var searchHistory = getCookie("melodySearchHistory");
  searchHistory = searchHistory ? JSON.parse(searchHistory) : []; // Parse JSON or create empty array

  // Check if the search term is already in the history
  if (searchHistory.includes(search)) {
    // Remove the existing term
    const index = searchHistory.indexOf(search);
    if (index !== -1) {
      searchHistory.splice(index, 1);
    }
  }

  // Add the search term at the beginning of the array
  if (search != "") searchHistory.unshift(search);
  
  // Keep history max 50 items long
  if (searchHistory.length > 50) {
    searchHistory.pop(); // Remove the last element
  }
  // Set the updated search history back to the cookie as a JSON string
  setCookieForOneMonth("melodySearchHistory", JSON.stringify(searchHistory))
}

function setCookieForOneMonth(cookieName, cookieValue) {
  // Get the current date
  const currentDate = new Date();
  
  // Set the expiration date to one month from now
  currentDate.setMonth(currentDate.getMonth() + 1);
  
  // Format the expiration date to GMT format required by the Expires attribute
  const expires = currentDate.toUTCString();

  // Set the cookie with the Expires attribute and Path attribute
  document.cookie = `${cookieName}=${cookieValue}; Expires=${expires}; Path=/; Secure; SameSite=Lax`;
}

function getSearchHistory() {
  var searchHistory = getCookie("melodySearchHistory");
  searchHistory = searchHistory ? JSON.parse(searchHistory) : []; // Parse JSON or create empty array
  let searchedMelodiesDiv = document.getElementById("searchedMelodies");
  for (var i = 0; i < searchHistory.length; i++) {
    let searchedSequence = searchHistory[i];
    let container = document.createElement("div");
    container.setAttribute("class", "searchedMelodyContainer");
    container.setAttribute("onclick", "updateFromString('"+searchedSequence+"')");
    let svg = document.createElement('div');
    svg.setAttribute('style', 'zoom:0.5');
    svg.disabled = true;
    
    $.ajax({
        type : "POST",  //type of method
        url  : "php/printStaves.php",  //your page
        data : {notes : searchedSequence, callingFrom:"searchTool"},// passing the values
        success: function(res) {
            svg.innerHTML = res;
            svg.innerHTML = svg.getElementsByTagName("div")[0].innerHTML;
            try{
                svg.getElementsByClassName('annotationsContainer')[0].remove();
                svg.getElementsByTagName('br')[0].remove();
                svg.getElementsByTagName('br')[1].remove();
                svg.getElementsByTagName('br')[2].remove();
                svg.getElementsByTagName('br')[3].remove();
            }catch{}
            container.appendChild(svg);

          }
      });
    
    searchedMelodiesDiv.appendChild(container);
  }
}
getSearchHistory() 

function toggleExpand() {
  const contentDiv = document.getElementById('searchedMelodies');
  const arrow = document.querySelector('.history-arrow');

  if (contentDiv.style.height === '100px' || contentDiv.style.height === '') {
    // Expand the div
    contentDiv.style.height = contentDiv.scrollHeight + 'px';
    contentDiv.classList.add("expanded");
    arrow.classList.add('expand-arrow'); // Rotate the arrow
  } else {
    // Collapse the div
    contentDiv.style.height = '100px';
    contentDiv.classList.remove("expanded");
    arrow.classList.remove('expand-arrow'); // Reset arrow rotation
  }
}

// Get cookie value by name
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  
  for (let i = 0; i < ca.length; i++) {
    var c = ca[i].trim(); // Remove leading spaces
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function clearHistory() {
  document.cookie = "melodySearchHistory=[]; path=/";
  let searchedMelodyContainer = document.getElementById("searchedMelodies")
  searchedMelodyContainer.innerHTML = "";
  searchedMelodyContainer.style.height = '100px';

}
