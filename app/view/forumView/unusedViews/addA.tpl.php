<article class="">

    <?php
    if (isset($content)) {
        echo $content;
    }
    if (isset($tab)) {
			$add = $this->url->create('commentsdb/add/' . $tab);        
		echo "<a href='" . $add . "'><button class='bigButton' >SKRIV</button></a>";
    }
    ?>
</article> 
