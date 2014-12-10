<article class="article1">

    <?php
    if (isset($tab)) {
			$add = $this->url->create('commentsdb/add/' . $tab);        
		echo "<a href='" . $add . "'><button class='bigButton' >SKRIV</button></a>";
    }
    if (isset($content)) {
        echo $content;
    }
    ?>
</article> 
