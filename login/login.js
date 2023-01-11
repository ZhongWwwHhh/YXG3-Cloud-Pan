const html = document.querySelector('html');
const loading = document.querySelector('iframe');
const btnLogin = document.querySelector('#btnLogin');

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

/*
check
loading
fetch
if bad
    alert
else
    sessionStorage
        lightname
        filepath
        write
    header to /dash
*/
function login() {
    let inputContent = input.value;
    if (checkInput(inputContent)) {
        loading.classList.remove('loadingHide');
        loading.classList.add('loadingShow');
    } else {
        alert('Please input correctly');
        return;
    }

    fetch('/api/account/information.php', {
        body: "name=" + inputContent,
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    })
        .then(res => {
            if (res.ok) {
                res.json().then(information => {
                    if (information.success == 'success') {
                        sessionStorage.lightname = information.lightname;
                        sessionStorage.filepath = information.filepath;
                        sessionStorage.write = information.write;
                        window.location.assign('/dash');
                        return;
                    } else {
                        alert('Invalid login information');
                        loading.classList.remove('loadingShow');
                        loading.classList.add('loadingHide');
                        return;
                    }
                })
            } else {
                alert('Network problem or request too frequently');
                loading.classList.remove('loadingShow');
                loading.classList.add('loadingHide');
                return;
            }
        })

    /*
        .then(res => res.text())
        .then(function (res) {
            if (res == 'bad' || res == null) {
                alert('Invalid login information, network problem or request too frequently');
                loading.classList.remove('loadingShow');
                loading.classList.add('loadingHide');
                return;
            } else {
                const information = JSON.parse(res);
                sessionStorage.lightname = information.lightname;
                sessionStorage.filepath = information.filepath;
                sessionStorage.write = information.write;
                window.location.assign('/dash');
                return;
            }
});*/
}

btnLogin.onclick = login;

// auto dark mode
var currdate = new Date();
if (currdate.getHours() >= 6 && currdate.getHours() <= 18) {
    html.classList.add('day');
} else {
    html.classList.add('night');
}
