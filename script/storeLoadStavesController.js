//----Attributes loaded staves------
var loadedIdStaves = null; //this attribute containt staves id reference from database
var loadedTitle = null;
var loadedId =  null;
var loadedAuthor = null;
var loadedLanguage = null;
var loadedMs = null;
var loadedF = null;
var loadedModernStyle = null; //this attribute is true if modern style was uploaded or checked
var loadedOldStyle = null;
var loadedModernStaves = null; // this attribute contain modern style staves content(payload)
var loadedOldStaves = null;
var loadedTextStaves = null;
var loadedAnnotationsStaves = null;
var loadedSettings = null;
var loadedIdPublisher = null; // this attribute contain who is the main/first publisher of staves
var loadedNamePublisher = null; // this attribute contain who is the main/first publisher of staves
var loadedVisibility = null;
var loadedSyncModernOld = true; // Set syncing as default

//----------Parameters loaded Staves--------------
//----Shapes parameters--------
var loadedLinesInLine = null;  //old staves parameter for number of lines in line
var loadedShapeGroupNote = null; //group shape id
var loadedShapeSingleNote = null; //single note shape id
var loadedStemSingleNote = null; //stem direction single note id
var loadedConnectGroupNote = null; //connecting line in group id
var loadedMelodicStructure = null;
var loadedBars = null;
//------default Shape Parameters------------------------------
var loadedPes_type = null;
var loadedUnited_clivis = null;
var loadedClimacus_type = null;
var loadedPorrectus_type = null;
var loadedPlica_type = null;
var loadedScandicus_type = null;
//------------------------------------------------
var loadStatus = false; // status of controller, if controller contain a loaded staves.
var loadStatusParametrs = false;
//------------------------------------------------

function loadStavesFromDatabase(idStaves){
    console.log("loadStavesFromDatabase");
    //current user----------------------------------------------
    let email = localStorage.getItem("session_email");
    let password = localStorage.getItem("session_psw");
    let page = localStorage.getItem("current_page");
    if (idStaves < 0 || idStaves == null || idStaves == '') { //check if id_staves is valid
      return -1;
    } else {
        $.ajax({
              type: "POST",
              url: "./php/loadStaves.php",
              data: {
                id_staves: idStaves, 
                email:email, 
                password:password, 
                page:page
              }
          }).done(function(data) {
              if (parseInt(data) == -1){
                callBackStavesToLoad(0);
                return 0;
              } else {
                
                let obj = jQuery.parseJSON(data);
                let dimStaves = Object.keys(obj.staves).length;
                let dimParameters = Object.keys(obj.parameters).length;
                let dataStaves = obj.staves;
                let dataParameters = obj.parameters;

                if (dimStaves > 0) {
                    setupLoadStaves(
                      dataStaves[0], // id_staves 
                      dataStaves[1], // title 
                      dataStaves[2], // id
                      dataStaves[3], // author
                      dataStaves[4], // language
                      dataStaves[5], // ms
                      dataStaves[6], // folio 
                      dataStaves[7], // modernStyle
                      dataStaves[8], // oldStyle
                      dataStaves[9], // staves JSON
                      dataStaves[10], // text JSON
                      dataStaves[11], // annotations JSON
                      dataStaves[12], // settings JSON 
                      dataStaves[13], // visibility INT
                      dataStaves[14], // id_user(publisher) INT
                      dataStaves[15]+" "+dataStaves[16] // user name surname
                    );
                    
                    if (dimParameters > 0){
                        setupLoadStavesParameters(
                          dataParameters[0], // lines_in_line 
                          dataParameters[1], // shape_group_note
                          dataParameters[2], // shape_single_note
                          dataParameters[3], // stem_single_note
                          dataParameters[4], // connect_group_note
                          dataParameters[5], // pes_type
                          dataParameters[6], // united_clivis
                          dataParameters[7], // climacus_type
                          dataParameters[8], // porrectus_type
                          dataParameters[9], // plica_type
                          dataParameters[10], // scandicus_type
                          dataParameters[11], // melodic_structure
                          dataParameters[12] // bars
                        );
                        callBackParametersToLoad(1);
                    }
                    callBackStavesToLoad(1); //data to show is ready, call the searchTool and complete task.
                  } else {
                      callBackStavesToLoad(0);
                      return 0;
                  }
              }
          });
    }
    // setNotationFromURL();
}

//0:id_staves 1:title 2:id 3:author 4:language 5:ms 6:f 7:modernStyle 8:oldStyle 9:staves(JSON) 10:multiple-text 11:multiple-Annotations 12: settingsJSON 13:visibility 14:id_user(publisher). 15: namePublisher
function setupLoadStaves(idS, t, id, a, lang, ms, f, mStyle, oStyle, staves, texts, annotations, settings, visibility, idPublisher, namePublisher){
      try {
        loadedIdStaves = idS;
        loadedTitle = t;
        loadedId =  id;
        loadedAuthor = a;
        loadedLanguage = lang;
        loadedMs = ms;
        loadedF = f;
        loadedModernStyle = mStyle; //this attribute is true if modern style was uploaded or checked
        loadedOldStyle = oStyle; //this attribute is true if old style was uploaded or checked
        loadedModernStaves = parseJsonToString(staves, 0);
        loadedOldStaves = parseJsonToString(staves, 1);
        loadedTextStaves = JSON.parse(texts);
        loadedAnnotationsStaves = JSON.parse(annotations);
        loadedSettings = JSON.parse(settings);
        loadedIdPublisher = idPublisher;
        loadedNamePublisher = namePublisher;
        loadedVisibility = visibility;
        loadAdvancedParametersFromDatabase(loadedSettings);

        setLoadStatus(true);
    }
    catch (error) {
        resetLoadStaves();
        setLoadStatus(false);
    }
}
//0:lines_in_line 1: shape_group_note
//2: shape_single_note 3: stem_single_note 4: connect_group_note
//5: pes_type 6: united_clivis 7: climacus_type 8: porrectus_type
//9: plica_type 10: scandicus_type 11: melodic_structure
function setupLoadStavesParameters(lil, sgn, ssn, stemsn, cgn, ps, uc, ct, pt, plicat, st, melodics, bg){
    try{
        //-------------Shape Parameters------------------------------
        loadedLinesInLine = JSON.parse(lil);  //old staves parameter for number of lines in line
        loadedShapeGroupNote = JSON.parse(sgn); //group shape id
        loadedShapeSingleNote = JSON.parse(ssn); //single note shape id
        loadedStemSingleNote = JSON.parse(stemsn); //stem direction single note id
        loadedConnectGroupNote = JSON.parse(cgn); //connecting line in group id
        loadedMelodicStructure = melodics;
        try{
          loadedBars = JSON.parse(bg);
        }catch{
          loadedBars = [];
        }

        // Default Shape Parameters
        loadedPes_type = ps;
        loadedUnited_clivis = uc;
        loadedClimacus_type = ct;
        loadedPorrectus_type = pt;
        loadedPlica_type = plicat;
        loadedScandicus_type = st;
        setLoadParametersStatus(true);
    }catch{
        resetLoadParametersStaves();
        setLoadParametersStatus(false);
    }

}
//--------check if staves is loaded for search tool-------
function setLoadStatus(status){
    if (status == true)
        loadStatus = status;
    else
        loadStatus = false;
}
function getLoadStatus(){
    return loadStatus;
}

function setLoadParametersStatus(status){
    if (status == true)
        loadStatusParametrs = status;
    else
        loadStatusParametrs = false;
}

function getLoadParametersStatus(){
    return loadStatusParametrs;
}
//----------------------------------------------------------

function parseJsonToString(jsonStaves, index){
    // JSON contain 0:modernPayload 1:oldPayload
    let objJson = jQuery.parseJSON(jsonStaves);
    let dimJson = Object.keys(objJson).length;
    if(index < dimJson && dimJson > 0){
        return objJson[index];
    }
    return '';
}
function resetLoadStaves(){
    loadedIdStaves = null;
    loadedTitle = null;
    loadedId =  null;
    loadedAuthor = null;
    loadedLanguage = null;
    loadedMs = null;
    loadedF = null;
    loadedModernStyle = null;
    loadedOldStyle = null;
    loadedModernStaves = null;
    loadedOldStaves = null;
    loadedTextStaves = null;
    loadedAnnotationsStaves = null;
    loadedSettings = null;
    loadedIdPublisher = null;
    loadedVisibility = null;

    setLoadStatus(false);
}
function resetLoadParameters(){
    // Shape Parameters
    loadedLinesInLine = null;  //old staves parameter for number of lines in line
    loadedShapeGroupNote = null; //group shape id
    loadedShapeSingleNote = null; //single note shape id
    loadedStemSingleNote = null; //stem direction single note id
    loadedConnectGroupNote = null; //connecting line in group id
    loadedMelodicStructure = null;
    loadedBars = null;
    // Default Shape Parameters
    loadedPes_type = null;
    loadedUnited_clivis = null;
    loadedClimacus_type = null;
    loadedPorrectus_type = null;
    loadedPlica_type = null;
    loadedScandicus_type = null;
    setLoadParametersStatus(false);
}
function storeStavesToDatabase(visibility, upload){
    if (syncModernOld) syncModernOldStaves();
    
    // current user
    let email = localStorage.getItem("session_email");
    let password = localStorage.getItem("session_psw");

    // Attributes
    let currentTitle = removeExtraSpaceFromString(getTitleUI());
    let currentId = removeExtraSpaceFromString(getIdUI());
    let currentAuthor = removeExtraSpaceFromString(getAuthorUI());
    let currentLanguage = removeExtraSpaceFromString(getLanguageUI());
    let currentMs = removeExtraSpaceFromString(getMsUI());
    let currentF = removeExtraSpaceFromString(getFUI());
    let modernStyle = 0;
    let oldStyle = 0;
    if (parseInt(upload) == 0) modernStyle = 1;
    else if(parseInt(upload) == 1) oldStyle = 1;
    else if (parseInt(upload) == 2) {
      modernStyle = 1;
      oldStyle = 1;
    }

    // Payload
    let currentModernStaves = getModernStavesUI().replace(/\n/g, "\\n");
    let currentOldStaves = getOldStavesUI().replace(/\n/g, "\\n");
    let staves = JSON.stringify([currentModernStaves,currentOldStaves]);

    let currentText = [];
    getTextsUI().forEach(element => currentText.push(element.replace(/\n/g, "\\n")));
    currentText = JSON.stringify(currentText);
    let currentAnnotations = [];
    getAnnotationsUI().forEach(element => currentAnnotations.push(element.replace(/\n/g, "\\n")));
    currentAnnotations = JSON.stringify(currentAnnotations);
    settings = getSettingsUI();
    const settingsToStore = JSON.stringify(settings);
    
    //--------Parameters-----------------------------------------
    let lines = JSON.stringify(getLinesInLineUI());
    let shapeGroup = JSON.stringify(getShapeGroupNoteUI());
    let shapeNote = JSON.stringify(getShapeSingleNoteUI());
    let stemNote = JSON.stringify(getStemSingleNoteUI());
    let connectNote = JSON.stringify(getConnectGroupNoteUI());
    let barsGroup = JSON.stringify(getBarsGroupNoteUI());
    let pes = getPesUI();
    let clivis = getClivisUI();
    let climacus = getClimacusUI();
    let porrectus = getPorrectusUI();
    let plica = getPlicaUI();
    let scandicus = getScandicusUI();
    let melodic = getMelodicStructureUI();
    //-----------------------------------------------------------
    //what is different in this moment between loaded version and current ?
    let c_title = 1;
    let c_id = 1;
    let c_author = 1;
    let c_language = 1;
    let c_ms = 1;
    let c_f = 1;
    let c_modernStaves = 1;
    let c_oldStaves = 1;
    let c_text = 1;
    let c_annotations = 1;
    let c_lines = 1;
    let c_shapeGroup = 1;
    let c_shapeNote = 1;
    let c_stemNote = 1;
    let c_connectNote = 1;
    let c_barsGroup = 1;

    if (getLoadStatus()){
        if(loadedTitle == currentTitle) c_title = 0;
        if(loadedId == currentId) c_id = 0;
        if(loadedAuthor == currentAuthor) c_author = 0;
        if(loadedLanguage == currentLanguage) c_language = 0;
        if(loadedMs == currentMs) c_ms = 0;
        if(loadedF == currentF) c_f = 0;
        if(loadedModernStaves == currentModernStaves.replace(/\\n/g, "\n")) c_modernStaves = 0;
        if(loadedOldStaves == currentOldStaves.replace(/\\n/g, "\n")) c_oldStaves = 0;
        if(JSON.stringify(loadedTextStaves) == currentText.replace(/\\n/g, "n")) c_text = 0;
        if(JSON.stringify(loadedAnnotationsStaves) == currentAnnotations.replace(/\\n/g, "n")) c_annotations = 0;
    }
    //shape changes or lines number in line
    if (getLoadParametersStatus()){
        if(JSON.stringify(loadedLinesInLine) == lines) c_lines = 0;
        if(JSON.stringify(loadedShapeGroupNote) == shapeGroup) c_shapeGroup = 0;
        if(JSON.stringify(loadedShapeSingleNote) == shapeNote) c_shapeNote = 0;
        if(JSON.stringify(loadedStemSingleNote) == stemNote) c_stemNote = 0;
        if(JSON.stringify(loadedConnectGroupNote) == connectNote) c_connectNote = 0;
    }

    if (!currentId.replace(/\s/g, '').length || !currentMs.replace(/\s/g, '').length){
        alert("Id and Ms cannot be empty!");
        hideOverlay();
        return;
    }
    
    if(!getLoadStatus() || loadedMs == null || loadedId == null){// if no staves was load
        loadedId = currentId;
        loadedMs = currentMs;
    }
    
    $.ajax({
        type : "POST",  //type of method
        url  : "./php/checkOverrideStaves.php",  //your page
        data : {email: email, password : password,
                id:currentId, ms:currentMs, visibility:visibility
            },
        success : function(resCode){
            let code = String(JSON.parse(resCode));
            if(code == '-1'){
                return false;
            }
            else if(code != 'NA'){ // If id already exist
                let conf = true;
                if (code != String(loadedIdStaves))conf = confirm('Another melody with the same Id and MS was detected (id_staves = '+code+'). Override?');
                if (conf == true){
                    $.ajax({
                        type : "POST",  //type of method
                        url  : "./php/storeStaves.php",  //your page
                        data : {email: email, password : password,
                                title:currentTitle, id:currentId, author:currentAuthor,
                                language:currentLanguage, ms:currentMs, f:currentF,
                                modernStyle: modernStyle, oldStyle:oldStyle,
                                staves:staves, textStaves:currentText,
                                annotationStaves:currentAnnotations,
                                settings:settingsToStore,
                                visibility:visibility, upload:upload,
                                lines:lines, shapeGroup:shapeGroup, shapeNote:shapeNote, barsGroup:barsGroup,
                                stemNote:stemNote, connectNote:connectNote,
                                pes:pes, clivis:clivis, climacus: climacus,
                                porrectus: porrectus, plica:plica, scandicus:scandicus,
                                melodic:melodic, loadedId:loadedId, loadedMs:loadedMs, useManualMelodicStructure:useManualMelodicStructure,
                                c_title:c_title, c_id:c_id, c_author:c_author, c_language:c_language,
                                c_ms:c_ms, c_f:c_f, c_modernStaves:c_modernStaves, c_oldStaves:c_oldStaves,
                                c_text:c_text,c_annotations:c_annotations, c_lines:c_lines,
                                c_shapeGroup:c_shapeGroup, c_shapeNote:c_shapeNote, c_stemNote:c_stemNote,
                                c_connectNote:c_connectNote, c_barsGroup:c_barsGroup},
                        success : function(res){
                            let newIdStaves = JSON.parse(res);

                            loadStavesFromDatabase(newIdStaves);

                        uploadMsg();
                        let div = document.getElementById("overlayView");
                        try{div.classList.remove("fadeInOverlay");
                          div.classList.add("fadeOutOverlay");
                          div = document.getElementById("overlayPanel");
                          setTimeout(() => {document.body.style.overflow = "auto"; checkHiddenOverlay();},  400);
                        }catch{}
                        }
                    });
                }
                else{
                    hideOverlay();
                }
            }
            else if(code == 'NA') {
                $.ajax({
                    type : "POST",  //type of method
                    url  : "./php/storeStaves.php",  //your page
                    data : {email: email, password : password,
                            title:currentTitle, id:currentId, author:currentAuthor,
                            language:currentLanguage, ms:currentMs, f:currentF,
                            modernStyle: modernStyle, oldStyle:oldStyle,
                            staves:staves, textStaves:currentText,
                            annotationStaves:currentAnnotations,
                            settings:settingsToStore,
                            visibility:visibility, upload:upload,
                            lines:lines, shapeGroup:shapeGroup, shapeNote:shapeNote,
                            barsGroup:barsGroup,
                            stemNote:stemNote, connectNote:connectNote,
                            pes:pes, clivis:clivis, climacus: climacus,
                            porrectus: porrectus, plica:plica, scandicus:scandicus,
                            melodic:melodic, loadedId:loadedId, loadedMs:loadedMs,
                            c_title:c_title, c_id:c_id, c_author:c_author, c_language:c_language,
                            c_ms:c_ms, c_f:c_f, c_modernStaves:c_modernStaves, c_oldStaves:c_oldStaves,
                            c_text:c_text,c_annotations:c_annotations, c_lines:c_lines,
                            c_shapeGroup:c_shapeGroup, c_shapeNote:c_shapeNote, c_stemNote:c_stemNote,
                            c_connectNote:c_connectNote, c_barsGroup:c_barsGroup},
                    success : function(res){


                        let newIdStaves = JSON.parse(res);

                        loadStavesFromDatabase(newIdStaves);

                        uploadMsg();
                        let div = document.getElementById("overlayView");
                        div.classList.remove("fadeInOverlay");
                        div.classList.add("fadeOutOverlay");
                        div = document.getElementById("overlayPanel");
                        setTimeout(() => {document.body.style.overflow = "auto"; checkHiddenOverlay();},  400);
                    }
                });
            }

        }
    });
}

// controlla che l'overlay sia stato nascosto
function checkHiddenOverlay(){
    setTimeout(function(){
      if (document.getElementById("overlayPanel").style.display == "block") {
        document.getElementById("overlayPanel").style.display = "none";
    }
  }, 100);
}

// confirmation message upload
function uploadMsg(){
    let div = document.createElement("div");
    div.setAttribute("id","uploadedMsg");
    div.setAttribute("width","300px");
    div.setAttribute("height","300px");
    div.innerHTML = "Melody uploaded";
    div.setAttribute("style","background-color:white; text-align:center; position:absolute;top:10px; left:40%; right:40%;padding:20px;border:2px solid lightgray;border-radius:5px");
    div.setAttribute("z-index","10000");
    document.getElementById("bodyContainer").appendChild(div);
    setTimeout(function(){$("#uploadedMsg").fadeOut(1000);},1000);
    setTimeout(function(){div.remove();},2000);
}

//update data to current after upload
function updateLoadStaves(ids, t, i, a, l, m, f, mstave, ostave,txt, ann, sett, v){
    loadedIdStaves = ids;
    loadedTitle = t;
    loadedId =  i;
    loadedAuthor = a;
    loadedLanguage = l;
    loadedMs = m;
    loadedF = f;
    loadedModernStaves = mstave.replace(/\\n/g, "\n");
    loadedOldStaves = ostave.replace(/\\n/g, "\n");
    loadedTextStaves = JSON.parse(txt.replace(/\\n/g, "n"));
    loadedAnnotationsStaves = JSON.parse(ann.replace(/\\n/g, "n"));
    loadedSettings = sett;
    loadedVisibility = v;
    setLoadStatus(true);
}
function updateLoadParameters(lil, sg, sn, ssn, cs){
    //-------------Shape Parameters------------------------------
    loadedLinesInLine = JSON.parse(lil);  //old staves parameter for number of lines in line
    loadedShapeGroupNote = JSON.parse(sg); //group shape id
    loadedShapeSingleNote = JSON.parse(sn); //single note shape id
    loadedStemSingleNote = JSON.parse(ssn); //stem direction single note id
    loadedConnectGroupNote = JSON.parse(cs); //connecting line in group id

    setLoadParametersStatus(true);
}
function syncModernOldStaves(){

    let select = document.getElementById("shapeSelection").value;
    currentStyle = select;


    if (currentStyle == 1){
        //document.getElementById('checkOldVisible').checked = false;
        //updateStaves();
        if(oldStaves != "" && modernStaves!= ""){
            oldStaves = document.getElementById("music_input").value;
            modernStaves = oldStaves.replace(/  +/g, ' ');
            modernStaves = modernStaves.replace(/\' \n/g, " '\n");
            modernStaves = modernStaves.replace(/ \'/g, "'");
            modernStaves = modernStaves.replace(/\n /g, "\n");
            modernStaves = modernStaves.replace(/\n/g, ".");
            modernStaves = modernStaves.replace(/\'\./g, ".'");
            modernStaves = modernStaves.replace(/\'/g, "\n");
            modernStaves = modernStaves.replace(/\./g, " '");
        }
        else if(modernStaves == ""){
            modernStaves = document.getElementById("music_input").value;
            modernStaves = modernStaves.replace(/ \n/g, "\n");
            modernStaves = modernStaves.replace(/\'/g, "");
            modernStaves = modernStaves.replace(/\n/g, " '\n");
            modernStaves = modernStaves.replace(/  +/g, ' ');
        }

    }
    else {
        if (modernStaves != "" && oldStaves!= ""){
            modernStaves = document.getElementById("music_input").value;
            oldStaves = modernStaves.replace(/  +/g, ' ');
            oldStaves = oldStaves.replace(/\' \n/g, " '\n");
            oldStaves = oldStaves.replace(/ \'/g, "'");
            oldStaves = oldStaves.replace(/\n /g, "\n");
            oldStaves = oldStaves.replace(/\n/g, ".");
            oldStaves = oldStaves.replace(/\'\./g, ".'");
            oldStaves = oldStaves.replace(/\'/g, "\n");
            oldStaves = oldStaves.replace(/\./g, " '");
        }
        else if (oldStaves == ""){
            oldStaves = document.getElementById("music_input").value;
            oldStaves = oldStaves.replace(/ \n/g, "\n");
            oldStaves = oldStaves.replace(/\'/g, "");
            oldStaves = oldStaves.replace(/\n/g, " '\n");
            oldStaves = oldStaves.replace(/  +/g, ' ');
        }
    }
    
}
// parse attributes like id, staves or title staves,
// remove all space at the end/start of string
function removeExtraSpaceFromString(str){
    return String(str).trim();
}

function loadAdvancedParametersFromDatabase(settings) {  
  toggleSettingsMenù();
  
  setTimeout(function(){
    if (settings){
      // Set view parameters from database
      // melodic structure
      try {
        if (settings.MelodicStructure === false){
          document.getElementById('checkMelodicStructure').checked = false;
        } else if (settings.MelodicStructure === true){
          document.getElementById('checkMelodicStructure').checked = true;
        }
      }catch{
        console.log("checkMelodicStructure not loaded");
      }
      try {
        if (settings.UseManualMelodicStructure == 1){
          document.getElementById('checkAutomaticMelodicStructure').checked = false;
        }else{
          document.getElementById('checkAutomaticMelodicStructure').checked = true;
        }
        if (settings.ManualMelodicStructure != "" && settings.ManualMelodicStructure != undefined){
          manualMelodicStructure = settings.ManualMelodicStructure;
          document.cookie.manualMelodicStructure = manualMelodicStructure
          document.getElementById('manualMelodicStructure').value = manualMelodicStructure;
          document.getElementById('manualMelodicStructure').innerHTML = manualMelodicStructure;
          manualMelodicStructureFirstUse = false;
        }
      }catch{}
      
      if (settings.FontSizeOld){
        document.getElementById('nFontSizeOld').value = settings.FontSizeOld;
      }
      if (settings.LineNumber == true){
        document.getElementById('checkLineNumber').checked = true;
      } else if (settings.LineNumber == false){
        document.getElementById('checkLineNumber').checked = false;
      }

      if (settings.Plica == true){
        document.getElementById('checkPlica').checked = true;
      } else if (settings.Plica == false){
        document.getElementById('checkPlica').checked = false;
      }

      if (settings.OctaveClef == true){
         document.getElementById('checkOctaveClef').checked = true;
      }else if (settings.OctaveClef == false){
         document.getElementById('checkOctaveClef').checked = false;
      }

      if (settings.Alphabetic == 0){
        document.getElementById('changeMelodicStructure').value = 0;
      }else if (settings.Alphabetic == 1){
        document.getElementById('changeMelodicStructure').value = 1;
      }

      if (settings.Annotations == true){
        document.getElementById('checkAnnotation').checked = true;
      }else if (settings.Annotations == false){
        document.getElementById('checkAnnotation').checked = false;
      }

      if (settings.MsLineBreaks == true){
        document.getElementById('checkMsLineBreaks').checked = true;
      }else if (settings.MsLineBreaks == false){
        document.getElementById('checkMsLineBreaks').checked = false;
      }

      if (settings.CarryBFlat == true){
       document.getElementById('checkCarryBFlat').checked = true;
      }else if (settings.CarryBFlat == false){
       document.getElementById('checkCarryBFlat').checked = false;
      }

      if (settings.CarriedBFlatBrackets == true){
        document.getElementById('checkCarriedBFlatBrackets').checked = true;
      }else if (settings.CarriedBFlatBrackets == false){
        document.getElementById('checkCarriedBFlatBrackets').checked = false;
      }

      if (settings.BFlatAlwaysInKeySignature == 1){
        document.getElementById('checkBFlatAlwaysInKeySignature').checked = true;
      }else if (settings.CarriedBFlatBrackets == 0){
        document.getElementById('checkBFlatAlwaysInKeySignature').checked = false;
      }
        
      if (settings.TextFontModern){
          document.getElementById('changeTextFontModern').value = settings.TextFontModern;
      }

      if (settings.FontSizeModern){
          document.getElementById('nFontSizeModern').value = settings.FontSizeModern;
      }
      if (settings.TextFontOld){
          document.getElementById('changeTextFontOld').value = settings.TextFontOld;
          fontOld = settings.TextFontOld;
      }
      if (settings.FontSizeOld){
          document.getElementById('nFontSizeOld').value = settings.FontSizeOld;
      }
      if (settings.custos) {
        custos = JSON.parse(settings.custos);
      }
      if (settings.RepetitionPattern) {
        repetitionPattern = settings.RepetitionPattern;
      }
      if (settings.NotationType) {
        notationType = settings.NotationType;
        document.getElementById('changeNotationType').value = notationType;
      }
      if (settings.hasOwnProperty("SyncModernOld")) {
        syncModernOld = settings.SyncModernOld;
        if  (syncModernOld === false) {
          document.getElementById('independent-transcriptions').checked = true;
        }else{
          syncModernOld = true;
          document.getElementById('independent-transcriptions').checked = false;
        }
      }else{
        console.log("SyncModernOld not existing");
      }
      try {
        statusMelodicStructure();
      }catch {console.log("statusMelodicStructure not loaded")}
      try{
        statusAutomaticMelodicStructure();
      }catch {console.log("statusAutomaticMelodicStructure not loaded")}
      try{
        updateManualMelodicStructure();
      }catch {console.log("updateManualMelodicStructure not loaded")}
      try{
        statusLineNumber();
      }catch {console.log("statusLineNumber not loaded")}
      try{
        statusPlica();
      }catch {console.log("statusPlica not loaded")}
      try{
        statusOctaveClef();
      }catch {console.log("statusOctaveClef not loaded")}
      try{
        statusAlphabetic();
      }catch {console.log("statusAlphabetic not loaded")}
      try{
        statusAnnotations();
      }catch {console.log("statusAnnotations not loaded")}
      try{
        statusSettings();
      }catch {console.log("statusSettings not loaded")}
      try{
        statusMsLineBreaks();
      }catch {console.log("statusMsLineBreaks not loaded")}
      try{
        statusCarryBFlat();
      }catch {console.log("statusCarryBFlat not loaded")}
      try{
        statusCarriedBFlatBrackets();
      }catch {console.log("statusCarriedBFlatBrackets not loaded")}
      try{
        statusBFlatAlwaysInKeySignature();
      }catch {console.log("statusBFlatAlwaysInKeySignature not loaded")}
      try{
        changeTextFontModern();
      }catch {console.log("changeTextFontModern not loaded")}
      try{
        changeFontSizeModern();
      }catch {console.log("changeFontSizeModern not loaded")}
      try{
        changeTextFontOld();
      }catch {console.log("changeTextFontOld not loaded")}
      try{
        changeFontSizeOld();
      }catch {console.log("changeFontSizeOld not loaded")}
      try{
        checkLink();
      }catch {//c("Link not loaded");
      }
      try{
        checkIIIFManifest();
      }catch(error) {console.log("IIIFManifest not loaded");c(error)}
      try{
        checkIIIFid();
      }catch {console.log("IIIFid not loaded");}
      try{
        statusSequenceRepetition();
      }catch(error){
      }
      try{
        changeNotationType();
      }catch(error){
        console.log("notationType not loaded");
      }
      try{
        statusSyncTranscriptions();
      }catch(error){
        // console.log("syncTranscriptions not loaded"); // this would be triggered all the time...
      }
    }

  },30);
  document.getElementById("settings").style.display = "none";
}

function checkLink() {
  let link = settings.Link;
  if (link != ""){
    document.getElementById("urlInput").value = link;
    processURL(link);
  }
}

function checkIIIFManifest() {
  let iiifManifest = settings.IIIFManifest;
  if (iiifManifest != "" && iiifManifest != "undefined"){
    document.getElementById("manifestInput").value = iiifManifest;
  }
}

function checkIIIFid() {
  let iiifId = settings.IIIFid;
  if (iiifId != "" && iiifId != "undefined"){
    document.getElementById("iiifIdInput").value = iiifId;
  }
}