<!-- end of div id #content -->
</div>


<div id="footer">
	<div id="footer-signature" class="vcard">
		<p>Regards <em class="fn">Leigh Scott</em> <strong class="org"><?php echo $g_company_name; ?></strong></p>
		<dl>
			<dt>Tel:</dt>
			<dd class="tel"><abbr class="type" title="Telephone">Tel:</abbr> <span class="value">01785 716 136</span></dd>
			<dt>Fax:</dt>
			<dd class="tel fax"><abbr class="type" title="Facsimile">Fax:</abbr> <span class="value">01785 716 137</span></dd>
			<dt>Website:</dt>
			<dd><a href="<?php echo $domain ?>" class="url" rel="me"><?php echo $g_company_domain ?></a></dd>
		</dl>
		
		<div id="signature-details">
			<p class="org"><span class="organization-name">Creation</span> <span class="organization-unit">design &#38; marketing</span></p>
			<p>Registered office address:</p>
			<address class="adr">
				<span class="street-address">76 Pinfold Lane</span>,
				<span class="locality">Penkridge</span>,
				<span class="region">Stafford</span>,
				<span class="postal-code">ST19 5AP</span>
				<span class="geo">
					<span class="latitude" title="52.72570">52:43:33N</span>, 
					<span class="longitude" title="-2.11885">2:07:04W</span>
				</span>
				<a href="#branding" class="include"></a>
			</address>
			<p>Registered in England no. 3025432</p>
			<p>VAT registration no. 650052071</p>
		</div>
		
		<div id="eshot-details">
			<p>This email was sent to [email].</p>
			<?php
			if(!empty($eshot_array[0]['permalink']) && !empty($eshot_array[0]['permalink']['link'])) {
				echo '<p>You can view this page online at <a href="'.$domain.$eshot_array[0]['permalink']['link'].'">'.$domain.$eshot_array[0]['permalink']['link'].'</a></p>'."\n";
			}
			?>
			<p>You can <unsubscribe>instantly unsubscribe from these emails</unsubscribe>.</p>
		</div>
	<!-- end of div id #footer-signature -->
	</div>
	
	<div id="footer-copyright">
		<p class="vcard author"><span class="gl-ir"></span>© 1995-<?php echo date('Y',strtotime('NOW'));?> <strong class="org fn"><?php echo formatText($header->sitename); ?></strong>
		<?php if(!empty($g_company_tagline)) echo ' - <em class="strapline">'.formatText($g_company_tagline).'</em>'; ?></p>
		<p class="top"><a href="#www-creation-uk-com"><span class="gl-ir"></span>Go to top</a></p>
	<!-- end of div id #footer-copyright -->
	</div>
<!-- end of div id #footer -->
</div>

<!-- end of div id #container -->
</div>

</body>
</html>