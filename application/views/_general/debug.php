<?php /* a hidden div with debug messages for quick'n'dirty debugging */ ?>
<?php if(isset($DEBUGMESSAGES) && count($DEBUGMESSAGES) > 0) { ?>
    <div id="debug" style="display: none;">
        <pre>
            <?php foreach ($DEBUGMESSAGES as $line) { ?>
                <?= $line ?><br/>
            <?php } ?>
		</pre>
    </div>
<?php } ?>