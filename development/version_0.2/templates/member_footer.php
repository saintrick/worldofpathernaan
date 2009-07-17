</p>
</div>
<div class="clear"></div>
</div>
<div id="footer">
<p>Copyright &copy; <?=date(Y)?> <?=$settings->gamename?> | <a href="http://pathernaan.info">Pathernaan</a></p>
</div>
</div>
</body>
</html>
<?php
$set_last_active=$db->execute("update `users` set `last_active`=? where `id`=?",array(time(), $user->id));