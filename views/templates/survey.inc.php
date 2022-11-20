<div class="survey">

<div class="question">
<?php  echo $survey->getQuestion() ?>
</div>
<ul>
<?php  foreach ($survey->getResponses() as $response) { ?>
 <li><?php echo $response ?></li>
<?php } ?>

</ul>
?>

</div>

