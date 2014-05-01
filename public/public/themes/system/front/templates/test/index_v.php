<h1><?php echo ucfirst(\Uri::string()); ?></h1>
<h3>Hello <?php if (isset($name)) {echo $name;} ?></h3>
<p>
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed id velit et neque tempor convallis et eget neque. Vestibulum convallis tempus lectus, nec imperdiet leo auctor ut. Aliquam a dapibus urna, in dapibus arcu. Etiam massa nunc, convallis at urna dapibus, consequat elementum velit. Quisque sit amet sem et ipsum ultrices feugiat a non nunc. Praesent et eleifend massa, in porttitor ipsum. Etiam sit amet feugiat ligula. Proin sed malesuada lorem. Donec tincidunt viverra interdum. In hac habitasse platea dictumst. Maecenas eget ligula egestas, adipiscing mi eget, interdum risus. Nullam consequat vehicula eleifend. Nunc ut egestas neque. Fusce hendrerit tortor vel ligula gravida lobortis.
</p>
<p>
	Nam posuere nunc et leo auctor, id dictum magna elementum. Nam condimentum arcu eget libero cursus aliquam. Maecenas porttitor iaculis lacus, vitae imperdiet ligula interdum a. Sed sit amet venenatis libero, eget auctor turpis. In hac habitasse platea dictumst. Fusce dignissim mauris vel pharetra tempor. Aenean vehicula dui ut orci mattis, nec placerat nisi gravida. Quisque iaculis odio ac risus pretium ullamcorper. 
</p>
<p>
	Vivamus vel faucibus dolor. Maecenas sapien metus, aliquet id venenatis sit amet, elementum et nulla. Cras fermentum porttitor euismod. Aliquam id justo felis. Donec a hendrerit orci. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sit amet risus in neque scelerisque posuere et venenatis nibh. Sed facilisis facilisis interdum. Vivamus elit turpis, feugiat non tortor eu, faucibus commodo dolor. Cras dignissim euismod arcu vitae mattis. 
</p>
<p>
	Nunc non molestie nunc. Donec eros ligula, pretium et dui eget, laoreet fermentum orci. Vivamus dictum libero at sem condimentum, a molestie magna dictum. Donec bibendum sagittis enim, vehicula ornare lorem. Sed scelerisque iaculis odio dictum pellentesque. Sed viverra ac dui non tincidunt. Fusce arcu ipsum, luctus elementum lacus et, consequat ultricies lorem. Sed congue ante accumsan arcu malesuada, vel auctor eros accumsan.
</p>
<p>
	<i class="glyphicon glyphicon-link"></i> <a href="<?php echo \Uri::create('test'); ?>">Go to 1 column test.</a><br>
	<i class="glyphicon glyphicon-link"></i> <a href="<?php echo \Uri::create('test/2column'); ?>">Go to 2 column test.</a><br>
	<i class="glyphicon glyphicon-link"></i> <a href="<?php echo \Uri::create('test/1inside2column'); ?>">Go to 1 column inside 2 column test.</a> (This is multiple sub layout)<br>
</p>