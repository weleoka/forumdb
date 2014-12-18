<div class="commentNewForm">
    <form method=post>
        <input type="hidden" name="redirect" value="<?=$this->url->create($redirect)?>">
        <input type="hidden" name="key" value="<?=$key?>"> 
        <fieldset>
        <legend></legend>
        <div class="commentNewFormContent">
        		<textarea  rows="5" placeholder="Lämna kommentar här..."name='content'><?=$content?></textarea>
        </div>
        <p class='toggler'></p>
		<div class='hidden'>
		  <div class="commentNewFormData">        
        		<input type='text' name='name' placeholder="Ditt namn..." value='<?=$name?>'/>
        		<input type='text' name='web' placeholder="Din hemsida..." value='<?=$web?>'/>
        		<input type='text' name='mail' placeholder="Din email..." value='<?=$mail?>'/>
        </div>
        <p class="commentNewFormButtons">
            <input type='submit' class='bigButton' name='doCreate' value='Skicka' onClick="this.form.action = '<?=$this->url->create('comment/add')?>'"/>
            <input type='reset' class='bigButton' value='Återställ'/>
           <br>
        		<input type='submit' class='smallButton' name='doRemoveAll' value='Ta bort alla kommentarer' onClick="this.form.action = '<?=$this->url->create('comment/removeAll')?>'"/>
        </p>
       </div>
        <output><?=$output?></output>
        </fieldset>
    </form>
</div>