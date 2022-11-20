
<form method="post" action="<?php  echo $_SERVER['PHP_SELF'].'?action=Search'; ?>">
    <p>Chercher un sondage sur...</p>
    <label class="label" for="keyword">Keyword : </label>
    <input name="keyword" class="field" type="text"/>
    <input type="hidden" name="action" value="Search"/>
    <input class="submit" type="submit" value="Chercher"/>

</form>
<br />

<?php  if ($model->getLogin()===null) { ?>
<div class="message">
Vous souhaitez poster des sondages :
<a href="<?php  echo $_SERVER['PHP_SELF'].'?action=SignUpForm';?>">inscrivez-vous</a>
</div>
<?php } ?>

