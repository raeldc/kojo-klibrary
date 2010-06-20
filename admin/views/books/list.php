<?php defined('SYSPATH') or die('404 Not Found.');?>

<form action="<?php echo HTML::uri(array('controller' => 'library', 'action' => 'book'));?>" method="post" name="adminForm">

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?php echo "#"; ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?echo count($books); ?>);" />
				</th>
				<th>
					<?php 
					// First parameter should be the current ordering
					echo HTML::ordering($ordering, 'title', JText::_('Title'), 'default', array('action' => 'books'));
					?>
				</th>
				<th>
					<?php echo JText::_('Author');?>
				</th>
				<th>
					<?php echo JText::_('Genre');?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; $m = 0; ?>
			<?php foreach ($books as $book) : ?>
			<tr class="<?php echo 'row'.$m; ?>">
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td align="center">
					<?php echo JHtml::_('grid.id', $i, $book->id); ?>
				</td>
				<td>
					<?php $url = array( 'action' => 'book', 'task' => 'edit', 'id' => $book->id); ?>
					<?php echo HTML::anchor($url, $book->title); ?>
				</td>
				<td>
						<?php echo $book->author_name; ?>
				</td>
				<td>
						<?php echo $book->genre_name; ?>
				</td>		
			</tr>
			<?php $i = $i + 1; $m = (1 - $m); ?>
			<?php endforeach; ?>

			<?php if ( ! count($books)) : ?>
			<tr>
				<td colspan="8" align="center">
					<?php echo JText::_('No items found'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					<?php //echo @$pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="action" value="book" />
	<input type="hidden" name="boxchecked" value="0" />
</form>