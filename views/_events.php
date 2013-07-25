<ul class="events">
  <?php if(count($events) > 0): ?>
    <?php foreach($events as $event): ?>
     <li class="<?php echo $event->getType(); ?>">
       <span class="title"><?php echo ucfirst($event->getType()); ?>:</span>
       <span class="message"><?php echo $event->getMessage(); ?></span>
     </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>