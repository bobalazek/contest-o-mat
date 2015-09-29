var Application = function () {
	var initialized = false;

	return {
		initialize: function()
		{
			// Some stuff here
			jQuery(document).ready( function() {
				Application.tooltipsInitialize();
				Application.timeAgoInitialize();
				Application.paginatorInitialize();
				Application.facebookLoginInitialize();
				Application.selectsInitialize();

                jQuery('#preloader').fadeOut(); // Hide preloader, when everything is ready...

                initialized = true;
                console.log('Application Initialized');
            });
		},
		selectsInitialize: function() {
			jQuery('.select-picker').selectpicker();
		},
		facebookLoginInitialize: function() {
			if(doJavascriptFacebookLogin) {
				jQuery('.btn-facebook-authenticate').on('click', function() {
					FB.login( function(response) {
                        if (response.authResponse) {
							var url = facebookAuthenticatedUrl+
								'?via_javascript=true&facebook_access_token='+
								response.authResponse.accessToken
							;

                            self.location.href = url;
                        } else {
                            alert('Whops, login was NOT successful!')
                        }
                    },
					{
						scope: facebookApplicationScope,
					});

                    return false;
				});
			}
		},
		paginatorInitialize: function() {
			var currentUrl = window.location.href;
			var limitPerPageParameter = 'limit_per_page';
			var pageParameter = 'page';
			var searchParameter = 'search';
			var url = new URI(currentUrl);

			if( jQuery('#paginator-limit-per-page-select').length ) {
				jQuery('#paginator-limit-per-page-select').on('change', function() {
					var value = jQuery(this).val();

					url.removeQuery(limitPerPageParameter);
					url.addQuery(limitPerPageParameter, value);

					url.removeQuery(pageParameter);
					url.addQuery(pageParameter, 1);

					window.location.href = url.toString();
				});
			}

			if( jQuery('#paginator-search-input').length ) {
				jQuery('#paginator-search-button').on('click', function() {
					var value = jQuery('#paginator-search-input').val();

					url.removeQuery(searchParameter);
					url.addQuery(searchParameter, value);

					window.location.href = url.toString();
				});

				jQuery('#paginator-search-clear-button').on('click', function() {
					var value = '';

					url.removeQuery(searchParameter);

					window.location.href = url.toString();
				});
			}
		},
		tooltipsInitialize: function() {
			jQuery('[data-toggle="tooltip"]').tooltip();
		},
		timeAgoInitialize: function() {
			function updateTime() {
				var now = moment();

				jQuery('time.time-ago').each( function() {
					var time = moment(jQuery(this).attr('datetime'));

					jQuery(this).text(time.from(now));
				});
			}

			updateTime();

			setInterval(updateTime, 10000);
		},
	}
}();
