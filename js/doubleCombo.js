	function DoubleCombo (masterId, slaveId, options) {
		this.master     = document.getElementById(masterId);
		this.slave      = document.getElementById(slaveId);
		this.options    = options;
		this.masterIndex = 1;
		
		this.initializeBehavior();
	}
	
	DoubleCombo.prototype = {
		initializeBehavior: function() {
			var oThis = this; // Make it to use inside function reference (this) to object 
			this.master.onchange = function() { oThis.masterComboChanged(); };
		},
		
		masterComboChanged: function() {
			this.masterIndex = this.master.options[this.master.selectedIndex].value;
			this.updateOptions();
		},
		
		updateOptions: function() {
			var slaveOptions = this.createOptions();
			this.slave.length = 0;
			var optionsObj = this.slave.options;
			for ( var i = 0 ; i < slaveOptions.length ; i++ ){
				optionsObj.add( slaveOptions[i] );
			}
		},
		
		createOptions: function() {
			var newOptions = [];
			var entries = this.options[this.masterIndex];
			for ( var i in entries) {
				var value = entries[i][0];
				var text  = entries[i][1];
				var selected = entries[i][2];//this.getElementContent(entries[i],'optionSelected');

				newOptions.push( new Option(text, value, selected) );
			}
			return newOptions;
		}
	};
