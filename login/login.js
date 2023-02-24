const loading = document.querySelector('iframe');
const btnLogin = document.querySelector('#btnLogin');
const loginName = document.querySelector('#loginname');

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

// pressign enter means clicking button
const input = document.getElementById('loginname');
input.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        document.getElementById('btnLogin').click();
    }
});

function checkInput(inputContent) {
    return (inputContent.length == 16 || (inputContent.length <= 10 && inputContent.length > 0)) ? true : false;
}

function loginFail() {
    loading.classList.remove('loadingShow');
    loading.classList.add('loadingHide');
    loginName.value = '';
    return;
}

// api login
function login() {
    let inputContent = input.value;
    if (checkInput(inputContent)) {
        loading.classList.remove('loadingHide');
        loading.classList.add('loadingShow');
    } else {
        alert('Please input correctly');
        loginFail();
        return;
    }

    fetch('/api/account/information.php', {
        body: "name=" + inputContent,
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    })
        .then(function (res) {
            if (res.ok) {
                res.json().then(function (information) {
                    if (information.success == 'success') {
                        sessionStorage.lightname = information.lightname;
                        sessionStorage.filepath = information.filepath;
                        sessionStorage.write = information.write;
                        window.location.pathname = '/dash/';
                        return;
                    } else {
                        alert('Invalid login information');
                        loginFail();
                        return;
                    }
                })
            } else {
                alert('Network problem or request too frequently');
                loginFail();
                return;
            }
        });
}

btnLogin.onclick = login;

// auto dark mode
const html = document.querySelector('html');
let currdate = new Date();
if (currdate.getHours() >= 6 && currdate.getHours() < 18) {
    html.classList.add('day');
} else {
    html.classList.add('night');
}
