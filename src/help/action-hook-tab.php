<p><strong>Action Hooks</strong></p>

<p>For complete control of where the <em>Share Buttons</em> and <em>Link Buttons</em> appear in your theme, you can use these action hooks in your template files:</p>
<ul>
	<li><code>&lt;?php do_action('crafty_social_share_buttons'); ?&gt;</code></li>
    <li><code>&lt;?php do_action('crafty_social_link_buttons'); ?&gt;</code></li>
    <li><code>&lt;?php do_action('crafty-social-share-page-buttons'); ?&gt;</code> (for use outside the loop)</li>
</ul>

<p>The buttons will be output using the settings you have configured on this page.</p>
<p>The <code>&lt;?php do_action('crafty_social_share_buttons'); ?&gt;</code> action hook can be configured with additional parameters.  Please see the documentation site at <a href="http://sarahhenderson.github.io/Crafty-Social-Buttons/#actionhooks">http://sarahhenderson.github.io/Crafty-Social-Buttons/#actionhooks</a> for more information.</p>
