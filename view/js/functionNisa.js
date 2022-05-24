
function show_hide() {
    var x = document.getElementById("parag");
    if (x.style.display === "none") 
    {
      x.style.display = "block";
    } 
    else 
    {
      x.style.display = "none";
    }
}

function bonjour() {   
    console.log("bonjour");
}



function oui(loginNisa, passNisa){
    fetch("http://localhost/jeanne/ProjeQtor/model/custom/functionNisa.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body:`loginNisa=${loginNisa}&passNisa=${passNisa}`,
    })
    .then((response) => response.text())
    .then((res) => (document.getElementById("result").innerHTML = res));
    console.log(loginNisa);
}