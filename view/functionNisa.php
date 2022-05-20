<button data-dojo-type="dijit/form/Button" type="button">je devienne fou!
    <script type="dojo/on" data-dojo-event="click" data-dojo-args="evt">
        require(["dojo/dom"], function(dom){
            dom.byId("result2").innerHTML += dojo.byId("login").value;
        });
    </script>
</button>
<div id="result2"></div>

<?php 
include 'functionNisaa.php';

echo "<button onClick='show_hide()'>Show/Hide</button>";
//display this paragraph when button is clicked
echo'<button id="parag" style="display:none;">Welcome to Codespeedy.</button>';



?>
