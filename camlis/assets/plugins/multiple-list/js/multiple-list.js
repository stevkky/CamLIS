(function($) {
	var multipleList	= (function() {
		function multipleList(target, setting) {
			this.target			= target;	//Type is DOM Object
			this._$target		= $(target);
			this.setting		= setting;
			this.list_item_type	= this.setting.removable ? 'selected-choice-with-close' : 'selected-choice-no-close';

			this.validateSetting();
			this.init();
			this.setData();
			this.registerEvent();
		}
		
		multipleList.prototype.init = function() {
			this._$wrapper			= $("<div class='choice-list-wrapper'></div>");
			this._$choice_list		= $("<ul class='selected-choice-list'></ul>");
			if (this.setting.filter) this._$multiple_filter	= $("<li class='multiple-list-filter filter'><input type='search' name='list-filter'></li>");

			this._$target.hide();
			this._$target.wrap(this._$wrapper);
			this._$target.after(this._$choice_list);
		};
		
		//Reset to init state except selected choice
		multipleList.prototype.reset = function() {
			if (this.setting.filter) {
				this._$multiple_filter.find("input[type=search]").val("");
				this._$multiple_filter.find("input[type=search]").css("width", "1em");
			}
		}

		multipleList.prototype.setData = function() {
			this._$choice_list.empty();
			
			for(var i in this.setting.data) {
				var item = this.createListItem(this.setting.data[i]);
				this._$choice_list.append(item);
			}
		
			//Selection type is multiple
			this._$choice_list.append(this._$multiple_filter);
		};

		//Create List Item
		multipleList.prototype.createListItem = function(item) {
			var li		= $("<li><span>" + item + "</span></li>");
			li.addClass(this.list_item_type);
			li.addClass(this.setting.choiceStyle);
			var rm_icon	= this.setting.removable ? $('<a class="remove-choice"></a>').data("parent", li) : null;

			return li.append(rm_icon);
		}

		multipleList.prototype.destroy = function() {
			try {
				this._$choice_list.remove();
				this._$target.unwrap(this._$wrapper);
				this._$target.removeData("multipleList-instance");
				this._$target.show();
				return true;
			} catch(err) {
				return false;
			}
		};
		
		multipleList.prototype.validateSetting = function() {
			if (this.setting.onFocus !== false && {}.toString.call(this.setting.onFocus) !== "[object Function]") {
				this.setting.onFocus = false;
				throw new TypeError("onFocus is not a function!");
			}
			
			if (this.setting.onItemClick !== false && {}.toString.call(this.setting.onItemClick) !== "[object Function]") {
				this.setting.onItemClick = false;
				throw new TypeError("onItemClick is not a function!");
			}
			
			if (this.setting.onRemove !== false && {}.toString.call(this.setting.onRemove) !== "[object Function]") {
				this.setting.onRemove = false;
				throw new TypeError("onRemove is not a function!");
			}
		};

		multipleList.prototype.registerEvent = function() {
			var	multipleListRef	= this;
			var itemClickEvent	= this.setting.onItemClick;
			var itemRemoveEvent	= this.setting.onRemove;
			var focusEvent		= this.setting.onFocus;
			var reset			= this.reset;
			$multiple_filter	= this._$multiple_filter;
			
			this._$choice_list.on("click", function() {
				if ($multiple_filter) $multiple_filter.find("input[type=search]").focus();
				multipleListRef.reset();
			});
			
			//Multiple Filter
			if ($multiple_filter) {
				$multiple_filter.find("input[type=search]").on({
					focus : function() {
						$(this).val('');
						if (focusEvent) focusEvent();
					},
					keyup : function() {
						var char_count	= $(this).val().length;
						var width		= char_count == 0 ? 1 : char_count * 0.7;

						//Change input width
						$(this).css("width", width + 'em');
					}
				});
			}
			
			//On selected item
			if (this.setting.onItemClick) {
				this._$choice_list.find("li:not(.filter)").on("click", function(evt) {
					evt.preventDefault();
					evt.stopPropagation();
					
					if (itemClickEvent) itemClickEvent();
					
					return false;
				});
			}
			
			//Set Event Remove
			if (this.setting.removable) {
				$("a.remove-choice").on("click", function(evt) {
					evt.preventDefault();
					evt.stopPropagation();

					//Custom Event
					var isRemove	= true;
					if (itemRemoveEvent) {
						isRemove = itemRemoveEvent();
					}

					if (isRemove || isRemove == undefined) {
						var parent = $(this).data("parent");
						parent.fadeOut(150, function() {
							parent.remove();
						});
					}
					
					multipleListRef.reset();
					return false;
				});
			}
		}
		
		return multipleList;
	})();

	var multipleListApi	= (function() {
		function multipleListApi() {
			this.target = [];
		}
		
		multipleListApi.prototype.destroy = function() {
			for(var i in this.target) {
				var mList = this.target[i].data("multipleList-instance");
				if (mList instanceof multipleList) {
					mList.destroy();
				}
			}
		}
		
		return multipleListApi;
	})();
	
	//=========================================================

	$.fn.multipleList	= function(options) {
		var api = new multipleListApi();
		
		//Defautl Setting
		var setting	= $.extend({
			choiceStyle	: 'default',
			removable	: true,
			filter		: true,
			onRemove	: false,
			onItemClick	: false,
			onFocus		: false,
			destroy		: false,
			data		: []
		}, options);

		this.each(function() {
			//Check if current object is already initialized
			var mList = $(this).data("multipleList-instance");
			if (mList instanceof multipleList && setting.destroy){
				if(mList.destroy()) mList = null;
			}
			else if (mList instanceof multipleList && !setting.destroy){
				alert("Can not re-initialized Multiple List.");
				return null;
			}

			//Mark current Object as initialized item
			if (!(mList instanceof multipleList)) {
				var newObj = new multipleList(this, setting);
				$(this).data("multipleList-instance", newObj);
				api.target.push($(this));
			}
		});
		
		return api;
	};
}(jQuery));