<?php
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
?>

<script type="text/javascript" src="../../external/dojo/dojo.js?version=<?php echo $version.'.'.$build;?>"></script>

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
            <div id="result"></div>
            <button data-dojo-type="dijit/form/Button" data-dojo-id="myToggleButton" onClick="oui(dojo.byId('login').value, dojo.byId('password').value);" iconClass="iconNisa iconSize22 roundedIconButton imageColorNewGui" type="button">
                bouton dojo
            </button>

            <iframe name="votar"></iframe>
            <form action="../model/custom/functionNisa.php" method="post" target="votar">
                
                Name: <input type="text" name="loginNisa">
                <br><br>
                Password: <input type="password" name="passNisa">
                <br><br>
                <input type="submit" value="bouton php">
            </form>

            <button tabindex="3" id="loginButton"  dojoType="dijit.form.Button" type="submit" class="largeTextButton" showlabel="true" >
            <?php echo i18n('loginLib');?>
                <script type="dojo/connect" event="onClick" args="evt">
                    bonjour();
                </script>
            </button>
            

        </td>
        <td></td>
        </tr>
	</table>
    </div>
</div>







