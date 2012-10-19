<?php require_once 'app.php'; ?>
<html>
    <head>
        <title>Send message example</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>Send message example</h1>
        <?php showFormMessage() ?>
        <form method="POST" action="check-delivery-status-action.php">
            <fieldset>
                <legend>Client correlator:</legend>

                Client correlator for this message:<br/>
                <input type="text" name="clientCorrelator" value="<?php echo getFormParam('clientCorrelator') ?>" size="15"/><br/><br/>
            </fieldset>
            <br/>
            <input type="submit" value="Check status" />
        </form>
    </body>
</html>
