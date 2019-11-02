(function($){

	$.fn.wait = function(time) {
  		var collector = new ChainCollector(this), self = this;
  		// Deal with scoping issues...
  		var fire = function() { collector.fire(self); };
  		setTimeout(fire, Number(time) * 1000);
  		return collector;
	};

	var ChainCollector = function() {
  		this.initialize.apply(this, arguments);
	};

	ChainCollector.prototype = {
		initialize: function() {
    		this.length = 0;
    		this.then = this.and = this;
    		this.__base = arguments[0];
    		this.__addMethods(this.__base);
    		for (var i = 1, n = arguments.length; i < n; i++)
    		  this.__addMethods(arguments[i]);
  		},

  		fire: function(base) {
    		var object = base || this.__base, method, property;
    		for (var i = 0, n = this.length; i < n; i++) {
      			method = this[i];
      			property = object[method.name];
      			object = (typeof property == 'function')
        			? property.apply(object, method.args)
        			: property;
    		}
    		return object;
  		},

		__addMethods: function(object) {
    		var methods = [], property, i, n, name;
    		for (property in object) {
      		if (Number(property) != property)
        		methods.push(property);
    		}
    		if (object instanceof Array) {
      			for (i = 0, n = object.length; i < n; i++) {
        			if (typeof object[i] == 'string')
          				methods.push(object[i]);
      				}
    			}
    			for (i = 0, n = methods.length; i < n; i++) {
      				name = methods[i];
      				if (this[name]) continue;
      				this[name] = function() {
        				this.__enqueue(arguments.callee.methodName, arguments);
        				return this;
      				};
      				this[name].methodName = name;
    			}
  		},
  		 __enqueue: function(method, args) {
    		[].push.call(this, {name: method, args: args});
  		}
	};
})(jQuery);