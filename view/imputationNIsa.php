<?php
function loginNisa() {
   $login= dojo.byId("login").value;
   echo $login;
}

function loginNisa1(str $login) {
    echo $login;
 
}

function bonjour(){
    echo 'Bonjour à tous <br>';
}
?>


<div data-dojo-type="dijit/form/DropDownButton" iconClass="iconNisa iconSize22 roundedIconButton imageColorNewGui">
    <span>Login</span><!-- Text for the button -->
    <!-- The dialog portion -->
    <div data-dojo-type="dijit/TooltipDialog" id="tttDialog">
    <div style="text-align: center; margin-bottom:25px;">
        <span class="title">
            Identifiants NISA
        </span>
    </div>   
    <table width="100%">
        <tr>     
        <td title="<?php echo i18n("login");?>" style="background:transparent !important;width: 100px;">   
        </td>
        <td title="<?php echo i18n("login");?>" style="width:<?php echo (isNewGui())?'450px':'250px';?>">
            <?php if(isNewGui())echo '<div class="loginDivContainer container">'; ?>
            <div class="<?php echo (isNewGui())?'inputLoginIconNewGui iconLoginUserNewGui imageColorNewGui iconSize22':'inputLoginIcon iconLoginUser';?> ">&nbsp;</div>
            <input tabindex="1" id="login" type="text"  class="<?php echo (isNewGui())?'inputLoginNewGui':'inputLogin';?>"
            dojoType="dijit.form.TextBox" />
            <input type="hidden" id="hashStringLogin" name="login" value=""/>  
            <?php if(isNewGui())echo '</div>'; ?>
        </td>
        <td width="100px">&nbsp;</td>
        </tr>
        <tr style="font-size:50%"><td colspan="3">&nbsp;</td></tr>
        <tr>
        <td title="<?php echo i18n("password");?>" style="background:transparent !important;">
            
        </td>  
        <td title="<?php echo i18n("password");?>">
        <?php if(isNewGui())echo '<div class="loginDivContainer container" style="float:left">'; ?>
            <div  class="<?php echo (isNewGui())?'inputLoginIconNewGui iconLoginPasswordNewGui imageColorNewGui iconSize22':'inputLoginIcon iconLoginPassword';?> ">&nbsp;</div>
            <input  tabindex="2" id="password" type="password" class="<?php echo (isNewGui())?'inputLoginNewGui':'inputLogin';?>" dojoType="dijit.form.TextBox" />
            <input type="hidden" id="hashStringPassword" name="password" value=""/>
            <?php if(isNewGui()){
            echo '<div class="iconView imageColorNewGui iconSize22" style="cursor:pointer;float:right;position:relative;top:6px;margin-right:4px;" onClick="dojo.setAttr(\'password\',\'type\',((dojo.getAttr(\'password\',\'type\')==\'password\')?\'text\':\'password\'));" ></div>';
            echo '</div>';
            }?>
        </td>
        <tr>
        <td style="background:transparent !important;">&nbsp;</td>
        <td style="text-align:center" >
            <button tabindex="3" id="loginButton"  dojoType="dijit.form.Button" type="submit" class="largeTextButton" showlabel="true" >
            <?php echo i18n('loginLib');?>
            <script type="dojo/connect" event="onClick" args="evt">
                bonjour();
                console.log('you pressed the button');
                alert(dojo.byId("login").value);
                loginNisa();
                loginNisa1(dojo.byId("login").value);
                
            </script>
            </button>

            <button id="foo" dojoType="dijit.form.Button" onclick="foo">click me
            <script type="dojo/method" event="onClick" args="evt">
                alert("Button fired onClick");
            </script>
            </button>
            

            <button data-dojo-type="dijit/form/Button" id="T1465" data-dojo-props='onClick:function(){console.log($login); }, iconClass:"plusIcon", value:"Create"'>
                Create
            </button>


        </td>
        <td></td>
        </tr>
			              
	
	</table>
    </div>
</div>




