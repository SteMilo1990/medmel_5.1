window.onload = localStorage.setItem("current_page", "searchMelody.php");
setUserCredentials();
setCB();

var noteString = "";
var statusAction = true;
var notePositionX = 20;
var noteHeadPath = "M 303.13715,299.65106 C 299.74131,301.47103 297.93187,304.76561 299.04493,307.24402 C 300.23219,309.88766 304.31194,310.63374 308.15151,308.90939 C 311.99107,307.18503 314.14367,303.63999 312.95641,300.99636 C 311.76914,298.35272 307.6894,297.60664 303.84983,299.33099 C 303.60986,299.43876 303.36355,299.52973 303.13715,299.65106 z";
var flatPath = "m 27,41 -1,-66 v -11 c 0,-22 1,-44 4,-66 45,38 93,80 93,139 0,33 -14,67 -43,67 C 49,104 28,74 27,41 z m -42,-179 -12,595 c 8,5 18,8 27,8 9,0 19,-3 27,-8 L 20,112 c 25,21 58,34 91,34 52,0 89,-48 89,-102 0,-80 -86,-117 -147,-169 -15,-13 -24,-38 -45,-38 -13,0 -23,11 -23,25 z";
var svgNS = 'http://www.w3.org/2000/svg';
var ledgerPathCenter = "M 317,309.9 295,309.9";
var ledgerPathHigher = "M 317,315.8 295,315.8";
var ledgerPathHighest = "M 317,321.7 295,321.7";


function setupTollerance(tol){
    if(tol != ''){
        document.getElementById('tollerance').value = tol;
        document.getElementById('txtTollerance').innerHTML = tol+'%';
    }
}

function setCB() {
  if (document.cookie.indexOf("search_transpositions=1") == -1){
    document.getElementById('intervalCB').checked = false;
  }
  else{
    document.getElementById('intervalCB').checked = true;
  }
}


function setupTollerance(tol){
    if(tol != ''){
        document.getElementById('tollerance').value = tol;
        document.getElementById('txtTollerance').innerHTML = tol+'%';
    }
}

function searchTranspositions(){
  let cb = document.getElementById("intervalCB");
  if (cb.checked) {
    document.cookie = "search_transpositions=1";
  }else{
    document.cookie = "search_transpositions=0";
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
    if(statusAction){
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

// Notehead cursor
$('#svgSearchWrap').mousemove(function(e) {
    if(statusAction) {
        try {
          mouseX = e.pageX;
          mouseY = e.pageY;
          let diff = document.getElementById('divSearch').offsetLeft + 36;
          if (document.getElementById('collapseDiv').style.display == 'none') {
              document.getElementById('cursor').style.left = (notePositionX + diff) + "px";
              document.getElementById('cursor').style.top = (mouseY - 80) + "px";
          } else {
              let diff2 = document.getElementById('collapseDiv').offsetHeight;
              document.getElementById('cursor').style.left = (notePositionX + diff) + "px";
              document.getElementById('cursor').style.top = (mouseY - 80. - diff2) + "px";
            }
        } catch {
          document.getElementById('cursor').style.display = 'none';
        }
    } else {
      document.getElementById('cursor').style.display= 'none';
    }
});

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

function nth_occurrence(string, char, nth) {
    let first_index = string.indexOf(char);
    let length_up_to_first_index = first_index + 1;

    if (nth == 1) {
        return first_index;
    } else {
        let string_after_first_occurrence = string.slice(length_up_to_first_index);
        let next_occurrence = nth_occurrence(string_after_first_occurrence, char, nth - 1);

        if (next_occurrence === -1) {
          return -1;
        } else {
          return length_up_to_first_index + next_occurrence;
        }
    }
}


// CALCULATE NOTE HEIGHT
function noteHeightUI(x) {
  if (x === "J") {
    return "485";
  } else if (x === "A") {
    return "478";
  } else if (x === "H" || x === "B") {
    return "471";
  } else if (x === "C") {
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
  } else if (x === "u") {
    return "379";
  } else if (x === "q") {
    return "372";
  } else if (x === "p") {
    return "372";
  }
}

function getLineStavesUI(strNotes, idStaves, id, ms, folio, title, author, text, row, score, transposed){
    let tr = document.createElement('tr');
    tr.setAttribute('style', 'background:white;');
    tr.setAttribute('onClick', 'window.location.href="viewer.html?id='+idStaves+'"');

    let td1 = document.createElement('td');
    td1.setAttribute('style', 'cursor:pointer');
    td1.innerHTML = ""+id+"<br>line "+(parseInt(row)+1)+"<br>"+ms+"<br><div style='font-size:14px;'>Folio : "+folio+"</div><div style='font-size:12px;'>"+author+"</div>";
    let td2 = document.createElement('td');
    td2.setAttribute('style', 'cursor:pointer');
    let td3 = document.createElement('td');
    td3.setAttribute('style', 'cursor:pointer');
    if (transposed == true){transp = "Transposed"}else{transp = ""}
    td3.innerHTML = "<div style='font-size:12px;'><b>Score : "+score+"%</b><br/>"+transp+"</div>";
    let a = document.createElement('a');
    a.setAttribute('href', 'viewer.html?id='+idStaves+'');
    td2.innerHTML = "<b class='title'>"+title+"</b>";
    let svg = document.createElement('div');
    svg.setAttribute('style', 'zoom:0.5');
    svg.disabled = true;
        // added
    // strNotes = strNotes.replace(/C1 |C2 |C3 |C4 |F1 |F2 |F3 |F4 |F5 |b} /g, "");
    // strNotes = strNotes.replace(/C1|C2|C3|C4|F1|F2|F3|F4|F5|b}/g, "");
    strNotes = strNotes.replace(/[CFGAD]\d ?|[bh] ?}/gi, "");
    // end added
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



// SEARCH
function updateProgressBar(perc){
    $('.progress-bar').attr('aria-valuenow', perc).css('width', perc+'%');
    document.getElementsByClassName('progress')[0].style.display = 'flex';
    document.getElementById('txtProgressBar').innerHTML = "<b>"+perc.toFixed(2)+"% Completed</b>";
    document.getElementById('startQuerySearch').disabled = true;
    document.getElementById('tollerance').disabled = true;
    document.getElementById('selectLanguage').disabled = true;
    statusAction=false;
    if(perc == 100){
        setTimeout(()=> document.getElementsByClassName('progress')[0].style.display = 'none', 100);
        setTimeout(()=> document.getElementById('startQuerySearch').disabled = false, 100);
        setTimeout(()=> document.getElementById('tollerance').disabled = false, 100);
        setTimeout(()=> document.getElementById('selectLanguage').disabled = false, 100);
        setTimeout(()=> statusAction = true, 200);
    }
}

// CREATE NOTE
function createNote(noteId){
  notePositionX = notePositionX + 20;
  
  if (noteId != " ") {
    // Make space for flat
    if (noteId === "b" || noteId == "B" || noteId == "+b") {
       notePositionX = notePositionX + 20;
    }
    
    let y = noteHeight(noteId);
    let coordinates = "matrix(-1.19, 0, 0, -1.19," + (390 + notePositionX) + "," + y + ")";
    let path = document.createElementNS(svgNS, "path");
    path.setAttribute("d", noteHeadPath);
    path.setAttribute("fill", "black");
    let g = document.createElementNS(svgNS, "g");
    g.setAttribute("transform", coordinates);
    g.setAttribute("class", "appendedNote searchNote");
    g.appendChild(path);

    if (noteId === "C" || noteId === "A" || noteId === "+a"){
      let cLine = document.createElementNS(svgNS, "path");
      cLine.setAttribute("d", "M 317,304 295,304");
      cLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(cLine);
    }
    if (noteId === "B" || noteId === "H" || noteId === "*G"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathCenter);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (noteId === "b" || noteId === "B") {
      let flat = document.createElementNS(svgNS, "path");
      flat.setAttribute("d", flatPath);
      flat.setAttribute("transform","matrix(-0.035, 0,  0,0.035, 324,303)");
      g.appendChild(flat);
    }
    if (noteId === "A"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathHigher);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (noteId === "*G"){
      let bLine = document.createElementNS(svgNS, "path");
      bLine.setAttribute("d", ledgerPathHighest);
      bLine.setAttribute("style", "fill:none;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1");
      g.appendChild(bLine);
    }
    if (noteId === "+h" || noteId === "+b"){
      if (noteId === "+b") {
        notePositionX = notePositionX+20;
        let flat = document.createElementNS(svgNS, "path");
        flat.setAttribute("d", flatPath);
        flat.setAttribute("transform","matrix(-0.035, 0,  0,0.035, 324,303)");
        g.appendChild(flat);
      }
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
        document.getElementById('cursor').style.top = (mouseY - 80)+"px";
      } else {
        let diff2 = document.getElementById('collapseDiv').offsetHeight;
        document.getElementById('cursor').style.left = (notePositionX+diff)+"px";
        document.getElementById('cursor').style.top = (mouseY - 80 - diff2)+"px";
      }
    }catch{}
  }

  noteString = noteString + noteId;

  document.getElementById('searchText').value = noteString;
  updateMusicString();
}

function updateMusicString() {
  document.getElementById('musicStringInput').value = noteString;
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
//prevent page scroll at spacebar input
window.addEventListener('keydown', function(e) {
  if(e.keyCode == 32 && e.target == document.body) {
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
    }

});

function createSpace() {
  notePositionX = notePositionX + 40;
  noteString = noteString + " ";
}

function deleteNote() {
  let nOfG = document.getElementsByClassName('searchNote').length;
  if (nOfG > 0){
    nOfG = nOfG -1;
    document.getElementsByClassName('searchNote')[nOfG].remove();
  }
  noteString = noteString.substring(0, noteString.length - 1);
  noteString = noteString.trim();

  if (notePositionX > 20 && noteString.length > 0) {
    notePositionX = noteString.length * 20 + 20
  } else { 
    notePositionX = 20; 
  };
  
  document.getElementById('searchText').value = noteString;
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
  updateMusicString();
}

