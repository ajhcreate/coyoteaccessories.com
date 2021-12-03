LayeredRideSelector = function() { return function($) { return {
	config: {
		baseURL: '',
		loaderTargetSelector: '',
		templates: {
		},
		messages: {
			error: 'Some error occurred, please contact site admin'
		},
		selectors: {
		}
	},
	loader: null,
	init: function(configValues) {
		$.extend(true, this.config, configValues);
		this.config.baseURL = this.config.baseURL.replace('http:', location.protocol);
		if (window.hasOwnProperty('loader')) {
			this.loader = window.loader;
		}
	},
	showLoader: function(targetSelector) {
		if ('undefined' == typeof(targetSelector)) {
			targetSelector = this.config.selectors.self;
		}
		if (this.loader) {
			this.config.loaderTargetSelector = targetSelector;
			this.loader.show(targetSelector);
		}
	},
	hideLoader: function() {
		if (this.loader) {
			this.loader.hide(this.config.loaderTargetSelector);
		}
	},
	inform: function(message, delayed) {
		if (window.hasOwnProperty('informer')) {
			if ('undefined' == typeof(delayed)) {
				delayed = true;
			}
			if (delayed) window.informer.appear(message, this.config.messageTimeoutInterval);
			else window.informer.show(message);
		} else alert(message);
	},
	show: function(selector, visible) {
		visible = (typeof(visible) !== 'undefined') ? visible : true;
		if (visible) {
			$(selector).removeClass('ftm2-hidden');
		} else {
			$(selector).addClass('ftm2-hidden');
		}
	},
	enable: function(selector, enabled) {
		enabled = (typeof(enabled) !== 'undefined') ? enabled : true;
		$(selector).prop('disabled', !enabled);
	},
	_processResponse: function(response) {
		if (response.errorMessage) {
			alert(response.errorMessage);
			return false;
		}
		if (response.message) {
			this.inform(response.message);
		}
		if (response.stop) {
			return false;
		}
		return true;
	},
	request: function(subject) {
		this.showLoader();
		$.ajax({
			url: this.config.baseURL + 'Request',
			data: {
			},
			dataType: 'json',
			type: 'POST',
			cache: false,
			success: this._requestSuccess.bind(this),
			complete: this.hideLoader.bind(this)
		});
	},
	_requestSuccess: function(response) {
		if (!this._processResponse(response)) return;
		this._populateSelect(response);
	},
	a: function() {
	}
}; }(jQuery);};
