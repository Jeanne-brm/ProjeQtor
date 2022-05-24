<body>
  <button id="add">Add 5 + 3</button>
  <div id="result"></div>
  <script>
    let btn = document.getElementById("add");
    let loginNisa='admin_nisa';
    let passNisa='Je suis 1 mot de passe.';


    btn.addEventListener("click", function(){
      fetch("http://localhost/jeanne/ProjeQtor/view/test/test.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body:`loginNisa=${loginNisa}&passNisa=${passNisa}`,
      })
      .then((response) => response.text())
      .then((res) => (document.getElementById("result").innerHTML = res));
    })

    console.log('admin_nisa');
  </script>
</body>