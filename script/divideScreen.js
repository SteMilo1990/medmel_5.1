var zoomMeasure = 170;
var facsimileX;
var facsimileY;


setTimeout(function(){
  try {
    facsimileX = settings.facsimileX;
    facsimileY = settings.facsimileY;
  }catch{
     facsimileX = 0;
     facsimileY = 0;
  }
}, 100);


function processURL(url) {
  checkZoomSettings();

  if (typeof url === "undefined" && document.getElementById("urlInput").value !== 'undefined'){
    var url = document.getElementById("urlInput").value;
    console.log(1);
  }

  if (url === "" || url.indexOf("http://213.21.172.25/") != -1) {
    document.getElementById("urlInput").value = "";
    tryToGetFacsimile();
    console.log(2);
  }
  else if (url.indexOf("gallica") == -1) {
    console.log(3);
    // Create simple frame for everything that is not gallica
    var iframe = "<iframe id=\"myFrame\" src=\""+ url +"\" width=\"100%\" height=\"100%\" frameBorder=\"0\"/>";
    try {
      frameBox.innerHTML = iframe;
    }catch {
      console.log("Image URL not found")
    }
  }

  else {
    console.log(4);
    // If it's gallica
    // If it's not already in iiif format retrieve native image 
    if (url.indexOf("iiif") == -1){
        document.getElementById('loadingText').style.visibility = "visible";
        url = url.replace(/\s/g, "");
        url = url.replace("http://", "");
        url = url.replace("https://", "");
        url = url.replace("gallica.bnf.fr", "");
        url = url.substring(0, url.indexOf(".item"));
        url = "https://gallica.bnf.fr/iiif"+url+"/full/full/0/native";
    }
    frameBox.innerHTML = "<img id=\"msImg\" style='width:"+zoomMeasure+"%'>";
    
    document.getElementById("zoomIn").style.display = "block";
    document.getElementById("zoomOut").style.display = "block";
    document.getElementById("nextPage").style.display = "block";
    document.getElementById("previousPage").style.display = "block";
    document.getElementById("msImg").setAttribute("src", url);
    document.getElementById("msImg").setAttribute("onload", "imageLoaded()");
  }

  // const ids = ['urlInput', 'urlSubmitBtn', 'fetchFacsimileBtn'];
  // ids.forEach((id, i) => {
  //   document.getElementById(id).classList.add("fadeAway");
  // });
  
  setFacsimileCoordinates();
}


function closeIframe(){
  toggleDivideScreen();
}


function zoomIn() {
  zoomMeasure = zoomMeasure + 10;
  document.getElementById("msImg").style.width = zoomMeasure + "%";
}


function zoomOut() {
  zoomMeasure = zoomMeasure - 10;
  document.getElementById("msImg").style.width = zoomMeasure + "%";
}


function nextPage() {
  var msImg = document.getElementById("msImg");
  var str = msImg.getAttribute("src");
  var patt1 = /f\d{1,4}/g;
  var result = str.match(patt1);
  result = JSON.stringify(result);
  result = result.replace(/[\"]/g,"");
  result = result.replace("]","");
  result = result.replace("[","");
  number = result.replace("f","");
  number = parseInt(number);
  add = number + 1;
  fAdd = "f"+add;
  var newUrl = str.replace(result, fAdd);
  msImg.setAttribute("src",newUrl);
  startLoading();
}


function previousPage() {
  var msImg = document.getElementById("msImg");
  var str = msImg.getAttribute("src");
  var patt1 = /f\d{1,4}/g;
  var result = str.match(patt1);
  result = JSON.stringify(result);
  result = result.replace(/[\"]/g,"");
  result = result.replace("]","");
  result = result.replace("[","");
  number = result.replace("f","");
  number = parseInt(number);
  add = number - 1;
  fAdd = "f"+add;
  var newUrl = str.replace(result, fAdd);
  msImg.setAttribute("src",newUrl);
  startLoading();
}

function togglePlayerSettings() {
  let status = ""
  if (document.getElementById("speed").style.display != "none") {
    status = "none";
  } else {
    status = "block";
  }
  
  ["speed", "octave", "txtSpeed", "txtOctave"].forEach((id, i) => {
    document.getElementById(id).style.display = status;
  });
}

function startLoading() {
  document.getElementById("loadingText").style.visibility = "visible";
}


function imageLoaded() {
  document.getElementById("loadingText").style.visibility = "hidden";
  setTimeout(function(){setFacsimileCoordinates()}, 10);
}

function getXY() {
  var elmnt = document.getElementById("frameBox");
    facsimileX = elmnt.scrollLeft;
    facsimileY = elmnt.scrollTop;
}

function checkZoomSettings() {
  try {
    zoomMeasure = settings.ZoomFacsimile;
  }catch{}
}

function setFacsimileCoordinates() {
  try{
    facsimileX = settings.facsimileX;
    facsimileY = settings.facsimileY;
  }catch{}
  document.getElementById("frameBox").scrollLeft = facsimileX;
  document.getElementById("frameBox").scrollTop = facsimileY;
}


//-------end toggle visibility overlay panel------------------
function toggleEdit() {
    let page = localStorage.getItem("current_page");
    let frameSidebar = document.getElementById("frameSidebar");
    if(frameSidebar == null) return false;
    let editBtn = $('#editBtn');
    if(page == "editor.html"){
        if (editBtn.hasClass('noedit')) {
            frameSidebar.style.display = "none";
            editBtn.attr("class", 'menuButton edit');
            document.getElementById("rightSideBar").style.display = "block";

            document.getElementById('boxStavesContainer').style.height = "73vh";
            document.getElementById('stavesBox').style.height = "73vh";
            
            // Clean col-md-* in stavesBox
            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-9");

            // Clean col-md-* in containerSideBar
            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-3");

            // this do not seem to be used anymore
            // document.getElementById('stavesBox').classList.remove("large");
            // document.getElementById('stavesBox').classList.add("short");
            
            // Change icon
            editBtn.attr("src","img/icons/noedit.png");
            
            try {
              document.getElementById('hideSidebarBtn').style.display = "block";
            }catch{
              console.log('hideSidebarBtn not found (1)');
            }
            document.getElementById('showSidebarBtn').style.display = "none";
            document.getElementById('containerSideBar').style.display = "block";
        }
        else {
            editBtn.attr("class", 'menuButton noedit');
            document.getElementById("rightSideBar").style.display = "none";
          
            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-12");
            
            // document.getElementById('stavesBox').classList.add("large");
            // document.getElementById('stavesBox').classList.remove("short");

            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-6");


            editBtn.attr("src","img/icons/edit.png");
            frameSidebar.style.display = "none";
            try {
              document.getElementById('hideSidebarBtn').style.display = "none";
              document.getElementById('showSidebarBtn').style.display = "block";
            }catch{
              console.log("hideSidebarBtn not found (2)")
            }
            document.getElementById('containerSideBar').style.display = "none";
        }
        toggleSidebarCommandsInNavBar(); // only in viewer and editor
    }
    else if(page == "viewer.html"){
        if (editBtn.hasClass('noedit')) {
            frameSidebar.style.display = "none";
            editBtn.attr("class", 'menuButton edit');
            document.getElementById("rightSideBar").style.display = "block";

            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-9");
            
            // document.getElementById('stavesBox').classList.remove("large");
            // document.getElementById('stavesBox').classList.add("short");

            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-3");

            editBtn.attr("src","img/icons/noedit.png");
            
            document.getElementById('containerSideBar').style.display = "block";
            document.getElementById('hideSidebarBtn').style.display = "block";
            document.getElementById('showSidebarBtn').style.display = "none";

        } else {
            editBtn.attr("class", 'menuButton noedit');
            document.getElementById("rightSideBar").style.display = "none";

            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-12");
            
            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-6");

            editBtn.attr("src","img/icons/edit.png");
            frameSidebar.style.display = "none";
            document.getElementById('containerSideBar').style.display = "none";
            document.getElementById('hideSidebarBtn').style.display = "none";
            document.getElementById('showSidebarBtn').style.display = "block";
        }
        toggleSidebarCommandsInNavBar(); // only for viewer and editor
    }
    else if (page  == "compareTool.html"){
        if (editBtn.hasClass('noedit')) {
            frameSidebar.style.display = "none";
            editBtn.attr("class", 'menuButton edit');
            document.getElementById("rightSideBar").style.display = "block";

            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-9");

            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-3");

            editBtn.attr("src","img/icons/noedit.png");
            
            document.getElementById('containerSideBar').style.display = "block";
        }else {
            editBtn.attr("class", 'menuButton noedit');
            document.getElementById("rightSideBar").style.display = "none";

            for (let i = 1; i <= 12; i++) {
              document.getElementById('stavesBox').classList.remove("col-md-" + i);
            }
            document.getElementById('stavesBox').classList.add("col-md-12");
            
            for (let i = 1; i <= 12; i++) {
              document.getElementById('containerSideBar').classList.remove("col-md-" + i);
            }            
            document.getElementById('containerSideBar').classList.add("col-md-6");

            editBtn.attr("src","img/icons/edit.png");
            frameSidebar.style.display = "none";
            document.getElementById('containerSideBar').style.display = "none";
        }
    }

    try{
        resizeStaves();
    }catch{}
    try{
        resizeCompareToolStaves();
    }catch{}
    
    
}