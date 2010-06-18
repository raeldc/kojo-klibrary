<?php defined('SYSPATH') or die('404 Not Found.');?>

<form action="<?php echo HTML::uri(array('controller' => 'library', 'action' => 'genre'));?>" method="post" name="adminForm">

	<fieldset>
		<legend><?php echo JText::_('Genre'); ?></legend>
		
			<table class="admintable">
				<tr>
					<td width="100" align="right" class="key">
						<label for="title">
						<?php echo JText::_( 'Genre' ); ?>:
						</label>
					</td>
					<td>
						<input type="text" name="name" id="title" size="48" maxlength="250" value="<?php echo $genre->name; ?>" />
					</td>
				</tr>			
			</table>
			
	</fieldset>
	<input type="hidden" name="id" value="<?php echo $genre->id;?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="action" value="genre" />
</form>