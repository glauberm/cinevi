/*! Fabrik */
var fabriktablesElement=new Class({Implements:[Options,Events],options:{conn:null,connInRepeat:!0,container:""},initialize:function(a,b){this.el=a,this.setOptions(b),this.elements=[],this.elementLists=$H({}),this.waitingElements=$H({}),"null"===typeOf(document.id(this.options.conn))?this.periodical=this.getCnn.periodical(500,this):this.setUp()},getCnn:function(){"null"!==typeOf(document.id(this.options.conn))&&(this.setUp(),clearInterval(this.periodical))},registerElement:function(a){this.elements.push(a),this.updateElements()},setUp:function(){if(this.el=document.id(this.el),this.cnn=document.id(this.options.conn),"null"!==this.cnn){this.loader=document.id(this.el.id+"_loader");var a=this;this.cnn.hasClass("chzn-done")&&jQuery("#"+this.cnn.id).on("change",function(){document.id(a.cnn).fireEvent("change",new Event.Mock(document.id(a.cnn),"change"))}),this.cnn.addEvent("change",function(a){this.updateMe(a)}.bind(this)),this.el.hasClass("chzn-done")&&jQuery("#"+this.el.id).on("change",function(){document.id(a.el.id).fireEvent("change",new Event.Mock(document.id(a.el.id),"change"))}),this.el.addEvent("change",function(a){this.updateElements(a)}.bind(this));var b=this.cnn.get("value");""!==b&&-1!==b&&this.updateMe()}},updateMe:function(a){a&&a.stop();var b=this.cnn.get("value");if(b){this.loader&&this.loader.show();var c=new Request({url:"index.php",data:{option:"com_fabrik",format:"raw",task:"plugin.pluginAjax",g:"element",plugin:"field",method:"ajax_tables",showf:"1",cid:b.toInt()},onSuccess:function(a){var b=JSON.decode(a);"null"!==typeOf(b)&&(b.err?alert(b.err):(this.el.empty(),b.each(function(a){var b={value:a.id};a.id===this.options.value&&(b.selected="selected"),new Element("option",b).appendText(a.label).inject(this.el)}.bind(this)),this.loader&&this.loader.hide(),this.el.hasClass("chzn-done")&&jQuery("#"+this.el.id).trigger("liszt:updated"),this.updateElements()))}.bind(this),onFailure:function(a){console.log("fabriktables request failure",a.getResponseHeader("Status"))}.bind(this),onException:function(a,b){console.log("fabriktables request exception",a,b)}.bind(this)});Fabrik.requestQueue.add(c)}},updateElements:function(){this.elements.each(function(a){var b=a.getOpts(),c=this.el.get("value");if(""!==c){this.loader&&this.loader.show();var d=b.getValues().toString()+","+c;if(this.waitingElements.has(d)||(this.waitingElements[d]=$H({})),void 0!==this.elementLists[d])""===this.elementLists[d]?this.waitingElements[d][a.el.id]=a:this.updateElementOptions(this.elementLists[d],a);else{var e=this.cnn.get("value");this.elementLists.set(d,"");var f={option:"com_fabrik",format:"raw",task:"plugin.pluginAjax",g:"element",plugin:"field",method:"ajax_fields",cid:e.toInt(),showf:"1",k:"2",t:c};b.each(function(a,b){f[b]=a});{new Request({url:"index.php",data:f,onComplete:function(b){this.elementLists.set(d,b),this.updateElementOptions(b,a),this.waitingElements.get(d).each(function(a,c){this.updateElementOptions(b,a),this.waitingElements[d].erase(c)}.bind(this))}.bind(this),onFailure:function(a){this.waitingElements.get(d).each(function(a,b){this.updateElementOptions("[]",a),this.waitingElements[d].erase(b)}.bind(this)),this.loader&&this.loader.hide(),alert(a.status+": "+a.statusText)}.bind(this)}).send()}}}}.bind(this))},updateElementOptions:function(r,element){var target,dotValue;if(""!==r){var table=document.id(this.el).get("value"),key=element.getOpts().getValues().toString()+","+table,opts=eval(r);target="textarea"===element.el.get("tag")?element.el.getParent().getElement("select"):element.el,target.empty();var o={value:""};""===element.options.value&&(o.selected="selected"),new Element("option",o).appendText("-").inject(target),dotValue=element.options.value.replace(".","___"),opts.each(function(a){var b=a.value.replace("[]",""),c={value:b};(b===element.options.value||b===dotValue)&&(c.selected="selected"),new Element("option",c).set("text",a.label).inject(target)}.bind(this)),this.loader&&this.loader.hide()}},cloned:function(a,b){if(this.options.connInRepeat===!0){var c=this.options.conn.split("-");c.pop(),this.options.conn=c.join("-")+"-"+b}this.el=a,this.elements=[],this.elementLists=$H({}),this.waitingElements=$H({}),this.setUp(),FabrikAdmin.model.fields.fabriktable[this.el.id]=this}});