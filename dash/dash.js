// is IE
if (!!window.MSInputMethodContext && !!document.documentMode) {
    // Create Promise polyfill script tag
    var promiseScript = document.createElement("script");
    promiseScript.type = "text/javascript";
    promiseScript.src =
        "https://cdn.jsdelivr.net/npm/promise-polyfill@8.1.3/dist/polyfill.min.js";

    // Create Fetch polyfill script tag
    var fetchScript = document.createElement("script");
    fetchScript.type = "text/javascript";
    fetchScript.src =
        "https://cdn.jsdelivr.net/npm/whatwg-fetch@3.4.0/dist/fetch.umd.min.js";

    // Add polyfills to head element
    document.head.appendChild(promiseScript);
    document.head.appendChild(fetchScript);
}

// auto dark mode
const html = document.querySelector('html');
let currdate = new Date();
if (currdate.getHours() >= 6 && currdate.getHours() < 18) {
    html.classList.add('day');
} else {
    html.classList.add('night');
}

// get account information
(typeof (sessionStorage.lightname) == 'undefined' || typeof (sessionStorage.filepath) == 'undefined' || typeof (sessionStorage.write) == 'undefined') && (window.location.pathname = '/login/');
(sessionStorage.lightname == null || sessionStorage.filepath == null || sessionStorage.write == null) && (window.location.pathname = '/login/');
let lightname = sessionStorage.lightname;
let filepath = sessionStorage.filepath;
let write = sessionStorage.write;

// loading
let htmlLoading = document.querySelector('#loading');
function loadingOn() {
    htmlLoading.classList.remove('loadingShow');
    htmlLoading.classList.remove('loadingHide');
    htmlLoading.classList.add('loadingShow');
}
function loadingOff() {
    htmlLoading.classList.remove('loadingShow');
    htmlLoading.classList.remove('loadingHide');
    htmlLoading.classList.add('loadingHide');
}

// download
function downloadFile(filename) {
    console.log('download:' + filename);
    window.open('/api/file/download.php?filepath=' + filepath + '&filename=' + filename);
}

// api get filelist 
let htmlFileList = document.querySelector('#filelist');
function getFileList() {
    console.log('get file list');
    loadingOn();
    let url = '/api/file/list.php?filepath=' + filepath;
    fetch(url, {
        method: "GET",
    })
        .then(function (res) {
            if (res.ok) {
                res.json().then(function (fileList) {
                    if (fileList.success == 'success') {
                        // convert json to htmlFileList <p>
                        delete fileList.success;
                        list = '';

                        // add <p>
                        for (i in fileList) {
                            list = '<p id="file' + i + '">' + fileList[i] + '</p>' + list;
                        }
                        list += '<p>No more</p>';
                        htmlFileList.innerHTML = list;

                        // add event
                        let htmlP = document.querySelectorAll('p');
                        for (let i = 0; i < htmlP.length - 1; i++) {
                            let name = htmlP[i].innerHTML;
                            htmlP[i].onclick = function () { downloadFile(name) };
                        }
                        loadingOff();
                        return;
                    } else {
                        alert('Invalid login information');
                        window.location.pathname = '/login/';
                        return;
                    }
                });
            } else {
                alert('Network problem or request too frequently');
                loadingOff();
                return;
            }
        });
}

// refresh btn
let htmlBtnRefresh = document.querySelector('#refresh');
htmlBtnRefresh.onclick = getFileList;

// quit btn
let htmlBtnQuit = document.querySelector('#quit');
htmlBtnQuit.onclick = function () {
    loadingOn();
    if (write == 'true' || write == true) {
        fetch('/api/account/quit.php', { method: 'GET' })
            .then(function (res) {
                if (res.ok) {
                    sessionStorage.clear();
                    window.location.pathname = '/login/';
                    return;
                } else {
                    alert('Network problem or request too frequently');
                    loadingOff();
                    return;
                }
            });
    } else {
        sessionStorage.clear();
        window.location.pathname = '/login/';
        return;
    }
};

// upload div
let htmlBtnUpload = document.querySelector('#upload');
if (write == 'true' || write == true) {
    htmlBtnUpload.className = 'uploadShow';
} else {
    htmlBtnUpload.className = 'uploadHide';
}

// first open page
window.onload = function () {
    getFileList();
    loadingOff();
}