// Report dell'ultimo tentativo di fix
// adesso l'ordine definito nell'url dovrebbe essere collegato a quello nel drag and drop e nella visualizzazione
// Il problema è che non funzionano gli url con diverse canzoni, solo canzoni con multipli mss.
// Al momento se si refresh con un url con multipli id, cerca gli altri mss del primo id e cancella i successivi

var idsToCompare = [];
var stavesToCompare = [];
var textToCompare = [];
var msToCompare = [];
var foliosToCompare = [];
var numberStavesToCompare = 0;

var idStavesToCompare = [];
var titlesToCompare = [];
var authorsToCompare = [];
var annotationsToCompare = [];
var settingsToCompare = [];

var parsedNotes = [];
var differenceNotes = [];

var stavesToCompare_orig = [];
var textToCompare_orig = [];
var setOfLines = [];

var useIdAsStaffLabel = false;


String.prototype.replaceAt = function(index, replacement) {
    if (index >= this.length) {
        return this.valueOf();
    }
    return this.substring(0, index) + replacement + this.substring(index + 1);
}

function updateCompare(updateURL = true, source="unknown") {
   window.history.replaceState(null, null, "?id=" + "&id_label=0");

    //-------setup url with id-------
    let currentIdsInURL =  new URL(window.location.href).searchParams.get('id').split(",").map(Number);
    // Check if current URL ids is the same 
    if (updateURL) {
      let labelURMLParam = "";
      if (useIdAsStaffLabel) {
         labelURMLParam = "&id_label="+useIdAsStaffLabel;
      }
      let linesURLparam = "";
      if (setOfLines.length > 0) {
        linesURLparam = "&lines=" + encodeURIComponent(JSON.stringify(setOfLines));
      }
      window.history.replaceState(null, null, "?id="+idStavesToCompare + labelURMLParam + linesURLparam);
    }
    //---------------------------------------------------------------------
    let notes =  "";
    let texts = "";
    let number_staves = 0;
    let ms_staves = [];
    let authorsToCompare_ = titlesToCompare_ = idsToCompare_ = foliosToCompare_ = annotationsToCompare_ = settingsToCompare_ = setOfLines_ = "";
    

    if (stavesToCompare.length > 0){
      notes = serializeData(stavesToCompare);
    }
    notes = notes.replace(/\'/g, '');
    notes = notes.replace(/i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|/g, "");
    
    notes = cleanKeys(notes);
    notes = notes.replace(/ +/g, ' ');

    if (textToCompare.length > 0)
        texts = serializeData(textToCompare);
    number_staves = numberStavesToCompare;
    if (msToCompare.length > 0)
        ms_staves = JSON.stringify(parseMs(msToCompare));
    if (authorsToCompare.length > 0)
        authorsToCompare_ = JSON.stringify(authorsToCompare);
    if (titlesToCompare.length > 0)
        titlesToCompare_ = JSON.stringify(titlesToCompare);
    if (idsToCompare.length > 0)
        idsToCompare_ = JSON.stringify(idsToCompare);
    if (foliosToCompare.length > 0){
        foliosToCompare_ = JSON.stringify(foliosToCompare);
    }
    
    if (annotationsToCompare.length > 0)
        annotationsToCompare_ = JSON.stringify(annotationsToCompare);
    if (settingsToCompare.length > 0)
        settingsToCompare_ = JSON.stringify(settingsToCompare);
    if (setOfLines.length > 0){
        setOfLines_ = JSON.stringify(setOfLines);
    }
        
    $.ajax({
        type : "POST",  //type of method
        url  : "comparetool/php/compareStaves.php",  //your page
        data : {notes : notes.replace(/\n/g, "|"), text_string : texts,
                number_staves:number_staves, ms_staves:ms_staves, titles:titlesToCompare_,
                authors:authorsToCompare_, ids: idsToCompare_, folios: foliosToCompare_, 
                annotations: annotationsToCompare_, settings:settingsToCompare_,
                setOfLines:setOfLines_, useIdAsStaffLabel:useIdAsStaffLabel
                },// passing the values
        success: function(res){            
            const staves_container = document.getElementById("stavesContainer");
            staves_container.innerHTML = "";
            const div = formatHTML(res);

            staves_container.appendChild(div);

            if (notes.length > 0) {
                let d = scopeDifference(notes, number_staves);
                differenceNotes = JSON.parse(JSON.stringify(d));
                let color = false;
                let hide = false;
                try{
                    color = document.getElementById("btnColorNoteCompare").checked;
                    hide = document.getElementById("btnHideNotesCompare").checked;
                }catch(err){console.log(err)}
                resizeCompareToolStaves();
                setupDifference(color, hide);
            }
        }
    });
}

var promises = [];

//----this function searches all id_staves with same ID------
function loadMulipleStaves_mss(id_staves, refresh, reset=true){
    if (reset) {
      resetCompare();
    }
    $.ajax({
          type: "POST",
          url: "comparetool/php/loadMultipleStavesToCompare_mss.php",
          data: {id_staves: id_staves,
                email : localStorage.getItem("session_email"),
                password : localStorage.getItem("session_psw")}
      }).done(function(data) {

              let dataStaves = jQuery.parseJSON(data);
              let countStaves = dataStaves.length;
              promises = [];
              dataStaves.forEach(id => {
                  promises.push(loadOneOfManyStavesToCompare(id));
              });

              Promise.all(promises)
                  .then(data => {
                      data.forEach((dataStaves, i) => {
                      
                        
                        // Process results in the order of the IDs
                        dataStaves = jQuery.parseJSON(dataStaves);

                        let countStaves = dataStaves.length;

                        if (countStaves > 0) {
                            idsToCompare.push(dataStaves[0]);
                            msToCompare.push(dataStaves[1]);
                            stavesToCompare.push(JSON.parse(dataStaves[2])[0]);
                            textToCompare.push(JSON.parse(dataStaves[3])[0]);
                            numberStavesToCompare = stavesToCompare.length;
                            titlesToCompare.push(dataStaves[4]);
                            authorsToCompare.push(dataStaves[5]);
                            idStavesToCompare.push(dataStaves[6]);
                            foliosToCompare.push(dataStaves[7]);
                            annotationsToCompare.push(dataStaves[8]);
                            settingsToCompare.push(dataStaves[9]);

                            let option = document.createElement("option");
                            option.setAttribute("value", (numberStavesToCompare-1));
                            option.text = "" + dataStaves[0] + " " + dataStaves[1].substring(0,10);

                            document.getElementById("ListStavesToCompare").appendChild(option);
                            
                            if (refresh) {
                                setTimeout(() => {try {
                                  hideOverlay();
                                } catch(err){
                                  console.log(err);
                                }
                              },  30);
                            }
                            setTimeout(() => updateCompare(),  22);
                        }
                    });

                  })
                  .catch(error => {
                      console.error("Error:", error);
                  });
      });
}

function loadContrafacta(id_staves){
  // Load json file with connections between contrafacta
  var rep_id = "";
  $.ajax({
        type: "POST",
        url: "php/getRepIdFromMedMelId.php",
        data: {id_staves: id_staves}
    }).done(function(rep_id) {
        $.getJSON("comparetool/json/models_and_contra.json", function(models_and_contra) {
          if (rep_id in models_and_contra) {
            var correlated_songs = models_and_contra[rep_id];

            loadMulipleStaves_mss(id_staves, false, true);
            
            for (var i = 0; i < correlated_songs.length; i++) {
              $.ajax({
                    type: "POST",
                    url: "php/getMedMelIdFromRepId.php",
                    data: {id: correlated_songs[i]}
                }).done(function(medmel_id) {
                  if (medmel_id != "") { 
                    loadMulipleStaves_mss(medmel_id, false, false);
                  }
                  setTimeout(() => {
                    try {
                      hideOverlay()
                    } catch(err) {
                      console.log(err);
                    }
                  },  20);
                  setTimeout(() => updateCompare(),  30);
                });
            }
            
          } else {
            setTimeout(() => {
              try {
                hideOverlay()
            } catch(err){
              console.log(err)
            }
          },  20);
            setTimeout(() => updateCompare(),  10);
          }
        });

    });
  
  }

function loadMulipleStaves(id_staves, refresh){          
  resetCompare();

  id_staves.forEach(id => {
    promises.push(loadOneOfManyStavesToCompare(id));
  });

  Promise.all(promises)
    .then(data => {
        data.forEach((dataStaves, i) => {
        
          // Process results in the order of the IDs
          dataStaves = jQuery.parseJSON(dataStaves);

          let countStaves = dataStaves.length;

          if (countStaves > 0) {
              idsToCompare.push(dataStaves[0]);
              msToCompare.push(dataStaves[1]);
              stavesToCompare.push(JSON.parse(dataStaves[2])[0]);
              textToCompare.push(JSON.parse(dataStaves[3])[0]);
              numberStavesToCompare = stavesToCompare.length;
              titlesToCompare.push(dataStaves[4]);
              authorsToCompare.push(dataStaves[5]);
              idStavesToCompare.push(dataStaves[6]);
              foliosToCompare.push(dataStaves[7]);
              annotationsToCompare.push(dataStaves[8]);
              settingsToCompare.push(dataStaves[9]);

              let option = document.createElement("option");
              option.setAttribute("value", (numberStavesToCompare-1));
              option.text = ""+dataStaves[0]+" "+dataStaves[1].substring(0,10);

              document.getElementById("ListStavesToCompare").appendChild(option);
              
          }
      });
      
      if (refresh) {
          setTimeout(() => {
            try {
              hideOverlay()
            } 
            catch (err){
              console.log(err);
            }
          },  10);
          setTimeout(() => updateCompare(),  10);
      }

    })
    .catch(error => {
        console.error("Error:", error);
    });
}


function loadOneOfManyStavesToCompare(id_staves){
  return new Promise((resolve, reject) => {
    $.ajax({
        type: "POST",
        url: "comparetool/php/loadSingleStavesToCompare.php",
        data: {
          id_staves: id_staves,
          email : localStorage.getItem("session_email"),
          password : localStorage.getItem("session_psw")
        },
        success: function(response) {
          resolve(response); // Resolve the promise with the response data
        },
        error: function(xhr, status, error) {
          reject(error); // Reject the promise with the error
        }
    });
  });
}

function loadSingleStavesToCompare(id_staves, refresh, overlayHide = true){
  $.ajax({
      type: "POST",
      url: "comparetool/php/loadSingleStavesToCompare.php",
      data: {
        email : localStorage.getItem("session_email"),
        password : localStorage.getItem("session_psw"),
        id_staves: id_staves
      }
    }).done(function(dataStaves) {
      dataStaves = jQuery.parseJSON(dataStaves);

      let countStaves = dataStaves.length;

      if (countStaves > 0) {
        idsToCompare.push(dataStaves[0]);
        msToCompare.push(dataStaves[1]);
        stavesToCompare.push(JSON.parse(dataStaves[2])[0]);
        textToCompare.push(JSON.parse(dataStaves[3])[0]);
        numberStavesToCompare = stavesToCompare.length;
        titlesToCompare.push(dataStaves[4]);
        authorsToCompare.push(dataStaves[5]);
        idStavesToCompare.push(dataStaves[6]);
        foliosToCompare.push(dataStaves[7]);
        annotationsToCompare.push(dataStaves[8]);
        settingsToCompare.push(dataStaves[9]);

        let option = document.createElement("option");
        option.setAttribute("value", (numberStavesToCompare-1));
        option.text = ""+dataStaves[0]+" "+dataStaves[1].substring(0,10);

        document.getElementById("ListStavesToCompare").appendChild(option);
        
        if (refresh) {
            if (overlayHide) setTimeout(() => {
              try {
                hideOverlay()
              }
              catch {}
            },  10);
            setTimeout(() => updateCompare(),  10);
        }
      }
    });
    updateCompare();
}


function resizeCompareToolStaves(){
    if (!allowResize) {
      let stavesElement = document.getElementById('staves')
      stavesElement.style.transform = `scale(${zoom})`;
      stavesElement.style.transformOrigin = "top left";
      stavesElement.style.width = `${100 / zoom}%`;
      return
    }

    zoom = 1;
    var largestLineWidth = 100;
    try {largestLineWidth = document.getElementById('largest_line').value;}catch{}
    var width = document.getElementById('stavesContainer').offsetWidth;

    if (largestLineWidth > width-150){
        zoom = (width-150)/(largestLineWidth);
        document.getElementById('staves').style.zoom = zoom;
    }
    else{
      try{
        document.getElementById('staves').style.zoom = 1;
      }catch{}
    }
}

function parseMs(ms){
    let parsedMs = [];
    for (let i = 0; i<ms.length; i= i+1){
        parsedMs[i] = ms[i].substring(0,1);
    }
    for (let i = 0; i<parsedMs.length-1; i= i+1){
        let index = 2;
        let tmp = parsedMs[i];
        for(let j = i+1; j <parsedMs.length; j = j+1){
            if(tmp == parsedMs[j] && tmp.replace(/ +/g, '').length>0){
                parsedMs[j] = parsedMs[j]+" "+index;
                index = index +1;
            }
        }
        if(index > 2 ){
            parsedMs[i] = parsedMs[i]+" "+1;
        }
    }
    return parsedMs;
}

function serializeData(data){
    let mergeData = "";
    let splitData = [];
    for (let i = 0; i < data.length; i = i+1){
        splitData[i] = data[i].split("\n");
    }
    let maxDim = 0;
    for (let i = 0; i< splitData.length; i = i+1){
        if(maxDim < splitData[i].length)
            maxDim = splitData[i].length;
    }
    for (let i = 0; i < maxDim; i = i+1){
        for (let j = 0; j < splitData.length; j = j+1){

            if (i != 0 || j != 0)
                mergeData = mergeData + "\n";
            if (String(splitData[j][i]) != "undefined"){

                mergeData = mergeData+String(splitData[j][i]).trim();
            }
        }
    }
    return mergeData;
}


function setupDifference(color=false, hide=false){
    let difference = differenceNotes;
    let number_staves = numberStavesToCompare;
    let id = 1;
    
    //row
    for (let i = 0; i < difference.length; i++){
        
        //col
        for (let j= 0; j < difference[i].length ; j++){
            let sameDiff = true;
            
            //group
            for (let x = 0; x< difference[i][j].length; x++){
                if (difference[i][j].charAt(x) == '1' || parsedNotes[i][j].charAt(x) == '2'){
                    sameDiff = false;
                }
            }
            // Loop through group
            for (let x = 0; x < difference[i][j].length; x++){ 
                // Skip first row for every compare lines
                if (i % number_staves == 0){ /* pass */ }
                else {
                    try{
                        let diff = i % number_staves;

                        if (parsedNotes[i-diff][j] != parsedNotes[i][j]){
                            sameDiff = false;
                        } else {
                            sameDiff = true;
                        }
                        if (document.getElementById("note"+id) != null){
                            if(difference[i][j].charAt(x) == '1'){
                                if (color)
                                    document.getElementById("note"+id).classList.add('differentNote');
                                else
                                    document.getElementById("note"+id).classList.remove('differentNote');
                            }
                            else if (difference[i][j].charAt(x) == '0'){
                                if (hide && sameDiff){
                                    document.getElementById("note"+id).classList.add('hideSameNote');
                                    if (document.getElementById("hyphen"+id) != null)
                                        document.getElementById("hyphen"+id).classList.add('hideSameNote');
                                    if (document.getElementById("hyphenA"+id) != null)
                                        document.getElementById("hyphenA"+id).classList.add('hideSameNote');
                                    if (document.getElementById("slur"+id) != null)
                                        document.getElementById("slur"+id).classList.add('hideSameNote');
                                    if (document.getElementById("b_note"+id) != null)
                                        document.getElementById("b_note"+id).classList.add('hideSameNote');
                                    if (document.getElementById("sharp"+id) != null)
                                        document.getElementById("sharp"+id).classList.add('hideSameNote');
                                }

                                else{
                                    document.getElementById("note"+id).classList.remove('hideSameNote');
                                    if(document.getElementById("hyphen"+id) != null)
                                        document.getElementById("hyphen"+id).classList.remove('hideSameNote');
                                    if(document.getElementById("hyphenA"+id) != null)
                                        document.getElementById("hyphenA"+id).classList.remove('hideSameNote');
                                    if(document.getElementById("slur"+id) != null)
                                        document.getElementById("slur"+id).classList.remove('hideSameNote');
                                    if(document.getElementById("b_note"+id) != null)
                                        document.getElementById("b_note"+id).classList.remove('hideSameNote');
                                    if(document.getElementById("sharp"+id) != null)
                                        document.getElementById("sharp"+id).classList.remove('hideSameNote');
                                }

                            }
                        }
                    }catch{}
                }
                if(difference[i][j].charAt(x) != '2'){
                    id = id+1
                }
            }
        }
    }
}
function parseNotes(n, number_staves){
    let notes = n.replace(/i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|W|X|Y|Z|/g, "");
    // notes = notes.replace(/F1 |F2 |F3 |F4 |F5 |F6 |F7 |F8 |F9 |C1 |C2 |C3 |C4 |C5 |C6 |C7 |C8 |C9 |G1 |G2 |G3 |G4 |G5 |G6 |G7 |G8 |G9 /g, '');
    // notes = notes.replace(/F1|F2|F3|F4|F5|F6|F7|F8|F9|C1|C2|C3|C4|C5|C6|C7|C8|C9|G1|G2|G3|G4|G5|G6|G7|G8|G9/g, '');
    //codifica casi speciali
    notes = notes.replace(/\+a/g, 'u');
    notes = notes.replace(/\+b/g, 'p');
    notes = notes.replace(/\+h/g, 'q');
    notes = notes.replace(/\*G/g, 'J');
    // notes = notes.replace(/b\}/g, '');
    // notes = notes.replace(/h\}/g, '');
    notes = cleanKeys(notes);
    notes = notes.replace(/ +/g, ' ');
    notes = notes.replace(/[^a-hA-H|u|p|q|J|\n| /|-|?]+/g, '');
    notes = notes.replace(/ +/g, ' ');
    notes  = notes.split("\n");
    // console.log("Notes - parseNotes");
    // console.log(notes);
    let formattedNotes = [];
    let index = 0;
    for(let i = 0; i<notes.length; i = i+parseInt(number_staves)){
        let rows =[];
        let rowId = 0;
        let maxLength = 0;
        for(let y = i; y<(i+number_staves); y=y+1){
            // console.log("ciclo y: "+y);
            try{
                let row = notes[y].trim().split(' ');
                // console.log("row "+y+" : "+row);
                rows[rowId] = row;
                if(maxLength < rows[rowId].length) maxLength = rows[rowId].length;
            }catch{
                //if(maxLength < rows[rowId].length) maxLength = rows[rowId].length;
            }
            rowId = rowId +1;
        }
        for(let j = 0; j< maxLength; j=j+1){
            let maxGroupSize = 0;
            for(let z = 0; z<number_staves; z = z+1){
                try{
                    if(maxGroupSize < rows[z][j].length)
                        maxGroupSize = rows[z][j].length;
                }catch{}
            }
            // console.log("max group size : "+maxGroupSize);
            for(let x = 0; x<number_staves; x = x+1){
            // console.log(String(notes[i]).length+" x: "+x);
                try{
                    while(maxGroupSize > rows[x][j].length){
                        rows[x][j] = rows[x][j]+"0";
                    }
                }catch{
                    rows[x].push("0");
                    while(maxGroupSize > String(rows[x][j]).length){
                        rows[x][j] = rows[x][j]+"0";
                    }
                }
                //console.log("rows: "+rows[i+x]);
            }
        }

        rowId = 0;
        for(let y = i; y<(i+number_staves); y=y+1){
            formattedNotes[y] = rows[rowId];
            rowId = rowId +1;
        }
        rowId = 0;
    }
    // console.log("formatted Note");
    //console.log(formattedNotes);
    parsedNotes = formattedNotes;
    return formattedNotes;
}

function scopeDifference(n, number_staves){
    let notes = parseNotes(n, number_staves);
    let difference = JSON.parse(JSON.stringify(notes)); //make an array copy
    let index = 0;
    //concrete difference array
    for(let i = 0; i < difference.length; i = i+1){
        for(let j= 0; j< difference[i].length; j=j+1){
            difference[i][j] = difference[i][j].replace(/[^/-]/g, '0');
        }
    }
    // console.log(difference);
    // console.log("notes : ");
    // console.log(notes);
    for(let i = 0; i < notes.length; i = i+number_staves){
        for(let j = 0; j < notes[i].length; j=j+1){
            //console.log("i: "+i+" - j:"+j);
            for(let y = 0; y < notes[i][j].length; y = y+1){
                for(let x = 1; x < numberStavesToCompare; x = x+1){
                    //console.log(String(notes[i]).length*x);
                    try{
                        // console.log("i: "+i+" - j: "+j+" - x0:"+notes[i][j].charAt(y)+" x1:"+notes[i+x][j].charAt(y));
                        if(notes[i][j].charAt(y) != notes[i+x][j].charAt(y)){
                            if(notes[i+x][j].charAt(y) == '0')
                                difference[i+x][j] = String(difference[i+x][j]).replaceAt(y,"2");
                            else
                                difference[i+x][j] = String(difference[i+x][j]).replaceAt(y,"1");
                            if(notes[i][j].charAt(y) == '0')
                                difference[i][j] = String(difference[i][j]).replaceAt(y,"2");
                            else
                                difference[i][j] = String(difference[i][j]).replaceAt(y,"1");
                        }
                        else{
                            if(notes[i+x][j].charAt(y) == "0"){
                                difference[i+x][j] = String(difference[i+x][j]).replaceAt(y,"2");
                                difference[i][j] = String(difference[i][j]).replaceAt(y,"2");
                            }
                            else {
                                difference[i+x][j] = String(difference[i+x][j]).replaceAt(y,"0");
                                difference[i][j] = String(difference[i][j]).replaceAt(y,"0");
                            }
                        }
                    }catch{}
                }
            }
        }

    }
    //console.log(difference);
    return difference;
}

function removeStaveToCompare(index){
    // console.log(index);
    stavesToCompare.splice(index, 1);
    textToCompare.splice(index, 1);
    msToCompare.splice(index, 1);
    titlesToCompare.splice(index, 1);
    authorsToCompare.splice(index, 1);
    idsToCompare.splice(index, 1);
    idStavesToCompare.splice(index, 1);
    //listTransposeController.splice(index, 1);
    numberStavesToCompare = stavesToCompare.length;
    updateCompare();
}
function resetCompare(){
    stavesToCompare = [];
    textToCompare = [];
    msToCompare = [];
    titlesToCompare = [];
    authorsToCompare = [];
    idsToCompare = [];
    idStavesToCompare = [];
    numberStavesToCompare = 1;
    document.getElementById("ListStavesToCompare").innerHTML = "";
}

function concreteDragAndDrop(start, end){
    let tmp = stavesToCompare[end];
    stavesToCompare[end] = stavesToCompare[start];
    stavesToCompare[start] = tmp;

    tmp = idsToCompare[end];
    idsToCompare[end] = idsToCompare[start];
    idsToCompare[start] = tmp;

    tmp = textToCompare[end];
    textToCompare[end] = textToCompare[start];
    textToCompare[start] = tmp;

    tmp = msToCompare[end];
    msToCompare[end] = msToCompare[start];
    msToCompare[start] = tmp;

    tmp = titlesToCompare[end];
    titlesToCompare[end] = titlesToCompare[start];
    titlesToCompare[start] = tmp;

    tmp = authorsToCompare[end];
    authorsToCompare[end] = authorsToCompare[start];
    authorsToCompare[start] = tmp;

    tmp = idStavesToCompare[end];
    idStavesToCompare[end] = idStavesToCompare[start];
    idStavesToCompare[start] = tmp;
}
//--------------Drag and Drop List----------------------------------------------
(function($){
    $.fn.extend({
        dragOptions: function(params) {
            var defaults = {
                highlight: "►"
            };

            var options = $.extend(defaults, params);

            var curOption = null;
            var isMouseDown = false;
            var startIndex = 0;
            var lastIndex = 0;
            var selectionOption = false;

            $(document.body).on("mouseup", function(){
                isMouseDown = false;
            });

            return this.each(function() {

                var _v = {
                    initial:    [],
                    hover:      -1,
                    current:    -1
                };
                var _o = options;
                var object = $(this);

                $(object).on("mousedown", "option", function(e){

                    if (!isMouseDown && !_v.initial.length ) {

                        isMouseDown = true;

                    }

                }).on("mousemove", "option", function(){

                    if (isMouseDown && !_v.initial.length) {
                      _updateIndexes();
                      if(!selectionOption){
                          selectionOption = true;
                          startIndex = $(this).data("index");
                        //  console.log($(this).data("index"));
                      }
                      _v.initial = [];
                      _v.initialObjects = [];

                      $("option", object).each(function(){
                        _v.initial.push({
                          key: $(this).val(),
                          text: $(this).text()
                        });
                        _v.initialObjects.push(this);
                      });

                      $(this).text(_o.highlight+$(this).text());
                      _v.drag = $(this).data("index")
                      _v.current = _v.drag;

                      curOption = this;
                    }

                    if (!isMouseDown && !_v.initial.length) return false;

                    _v.hover = $(this).data("index");

                    if (_v.current != _v.hover) {
                        _v.current = _v.hover;

                        var tempOptions = _v.initial.slice(0);
                        var showOptions = [];

                        var dragOption = tempOptions.splice(_v.drag,1);
                        for (var i = 0, k = tempOptions.length; i <= k; i++) {
                            if (i != _v.current) {
                                showOptions.push(tempOptions.shift());
                            } else {
                                showOptions.push(dragOption[0]);
                            }
                        }

                        $("option", object).each(function(i){
                            if (i==_v.current) {
                                $(this).text(_o.highlight+showOptions[i].text);
                            } else {
                                $(this).text(showOptions[i].text);
                            }
                        });

                        $("option", object).removeAttr('selected').eq(_v.current).attr('selected', 'selected');

                        (_o.onDrag || $.noop).call(object);
                    }

                }).on("mouseup", "option", function(){
                    selectionOption = false;
                    lastIndex = $(this).data("index");
                    if (!_v.initial.length) {
                        isMouseDown = false;
                        return false;
                    }
                    $("option", object).each(function(i){
                        $(this).text(_v.initial[i].text);
                    });

                    object.html('');

                    var cutOption = _v.initialObjects.splice(_v.drag,1);
                    for (var i = 0, k = _v.initialObjects.length; i <= k; i++) {
                        if (i != _v.current) {
                            object.append(_v.initialObjects.shift());
                        } else {
                            object.append(cutOption[0]);
                        }
                    }

                    $(object)[0].selectedIndex = _v.current;

                    _updateIndexes();
                    concreteDragAndDrop(startIndex, lastIndex);
                    //console.log("start : "+startIndex+" - last"+lastIndex);
                    updateCompare();
                    _v.hover = -1;
                    _v.initial = [];
                    _v.initialObjects = [];

                    curOption = null;

                    (_o.onChange || $.noop).call(object);

                }).on("mouseleave", function(){

                 // $(curOption).trigger("mouseup");

                }).on("mousemove", function(){

                    if (!isMouseDown && curOption) {
                        //$(curOption).trigger("mouseup");
                    }

                });

                var _updateIndexes = function(){
                    $("option", object).each(function(i){
                        $(this).data("index", i);
                    });
                };

            });
        }
    });
})(jQuery);


function formatHTML(html) {
    const div = document.createElement('div');
    div.innerHTML = html.trim();
    return beautifyHTML(div, 0); // Return the beautified node, not the innerHTML
}

function beautifyHTML(node, level) {
    const indentBefore = '  '.repeat(level);
    const indentAfter = '  '.repeat(Math.max(level - 1, 0));
    let textNode;

    for (let i = 0; i < node.children.length; i++) {
        textNode = document.createTextNode(`\n${indentBefore}`);
        node.insertBefore(textNode, node.children[i]);
        beautifyHTML(node.children[i], level + 1);
        if (i === node.children.length - 1) {
            textNode = document.createTextNode(`\n${indentAfter}`);
            node.appendChild(textNode);
        }
    }
    return node; // Return the node itself
}


function selectSpecificVersesToCompare(setOfLines, close=true){
  // setOfLines structure [ [int, int], [int, int] ]
  // Select from stavesToCompare   textToCompare
  let stavesToCompare_new = [];
  let textToCompare_new = [];
  
  // initialize stavesToCompare_orig and textToCompare_orig
  if (stavesToCompare_orig.length === 0) {
    stavesToCompare_orig = stavesToCompare;
    textToCompare_orig = textToCompare;
  }
  
  setOfLines.forEach((set, i) => {
    set.forEach((line, j) => {
      stavesToCompare_new.push(stavesToCompare_orig[j].split("\n")[line-1]);
      textToCompare_new.push(textToCompare_orig[j].split("\n")[line-1]);
    });
  });
  stavesToCompare = stavesToCompare_new;
  textToCompare = textToCompare_new;
  updateCompare();
  
  if (close){
    document.getElementById('settings').style.display = "none";
  }
}

function loadSelectedLinesOnLoad(){
  if (setOfLines.length > 0){
    selectSpecificVersesToCompare(setOfLines);
    buildSlotsFromArray(setOfLines);
  }
}
