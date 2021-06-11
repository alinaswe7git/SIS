jQuery(document).ready(function(){

	$("#username").change(function() { 
		var path = $("#path").text();
		var templatepath = $("#templatepath").text();
		var usr = $("#username").val();

		if(usr.length >= 10){
			$("#status").html('<img src="' + templatepath + '/images/tviload.gif" align="absmiddle">&nbsp;Checking for duplicates...');
 
			$.ajax({
				type: "POST",
				url:  path + '/api/checkvalue/?nrc=' + usr,
				data: usr,
				dataType: 'json',
				success: function(msg){
					if(msg.status == 'FALSE'){
						$("#status").empty();
						$("#status").addClass("object_error");
						$("#status").html('&nbsp;<img src="' + templatepath + '/images/error.png"><font color="red"> <b>YOUR NRC IS NOT FOUND</b></font>');
						jQuery('#submit-registrar').hide();						     
					}else{
						$("#username").removeClass('object_ok');
						$("#status").addClass("object_error");
						$("#status").html('&nbsp;<img src="' + templatepath + '/images/check.png"><font color="green"> <b>Please continue</b></font>');
						jQuery('#submit-registrar').show();
					}
				}
			 });
		} else {
			$("#status").empty();
			$("#status").html('<font color="red">' + '&nbsp;<img src="' + templatepath + '/images/error.png"> Your NRC is invalid <strong></strong></font>');
			$("#username").removeClass('object_ok'); 
			$("#username").addClass("object_error");
		}
	});


jQuery('#mda').ddslick({width:280,height:200,
	onSelected: function(selectedData) { },
	enableKeyboard: true,
	keyboard: [{ "up":38, "down":40, "select":13 }]
});
jQuery('#mdb').ddslick({width:280,height:200,
	onSelected: function(selectedData){
		console.log(selectedData.selectedData.text);
	}
});
jQuery('#gender').ddslick({width:280,
	onSelected: function(selectedData){
		console.log(selectedData.selectedData.text);
	}
});
jQuery('#nationality').ddslick({width:280, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#country').ddslick({width:280, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#dissability').ddslick({width:80,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#dissabilitytype').ddslick({width:200,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#mstatus').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#studytype').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
	}
});
jQuery('#payment').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#day').ddslick({width:80, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#month').ddslick({width:120, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#year').ddslick({width:80, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#examcenter').ddslick({width:280, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#placement').hide();
jQuery('#placementprov').hide();
jQuery('#placementdis').hide();
jQuery('#yearofstudy').ddslick({width:280, height:250,
    onSelected: function(selectedData){
	if(selectedData.selectedData.text == '3rd Year'){
		jQuery('#placement').show();
		jQuery('#placementprov').show();
		jQuery('#placementdis').show();
	} else {
		jQuery('#placement').hide();
		jQuery('#placementprov').hide();
		jQuery('#placementdis').hide();
	}
    }
});
jQuery('#education_0_type').ddslick({width:280, height:250,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#econtact_0_relationship').ddslick({width:280, height:250,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
                                   

	var ExpandingFormElement = Class.create({

		initialize: function(options) {
		
       	this.options = options

        this.entryModel = $(options.entryModel)
        this.container = $(this.entryModel.parentNode)

        this.container.cleanWhitespace()

        if(this.container.childNodes.length > 1) {
            throw new Error("The container (parentNode) of the entryModel must contain only the entryModel, and no other nodes (put it in a <div> of its own). The container has " + this.container.childNodes.length + " elements after white space removal.")
        }

        this.entryModel.remove()

		$(options.addEntryLinkElement).observe('click',function() {
				this.addEntry()
			}.bind(this));
		} ,

		addEntry: function(values) {
			var copiedElement = this.entryModel.cloneNode(true)

			this.observeCopiedElement(copiedElement)

			var index = this.getNumberOfEntries()

			this.replaceInputNamesInElement(copiedElement, index)

			this.container.appendChild(copiedElement);

			if(values != null) {
				this.setEntryValues(copiedElement, values)
		}

		jQuery('#spouse').ddslick({width:280,
			onSelected: function(selectedData){
				console.log(selectedData.selectedData.text);
			}   
		});

		jQuery('#education').ddslick({width:280,
			onSelected: function(selectedData){
				console.log(selectedData.selectedData.text);
			}   
		});

    } ,

    setEntryValues: function(element, values) {
       $H(values).each(function(entry) {
          var input = this.getInputFromElementByName(element, entry.key)

          if(input) {
              input.value = entry.value;
          }
       }.bind(this));
    } ,

    getInputFromElementByName: function(element, name) {
        var matchedInput = null;

        var inputs = element.select('input','textarea','select')

        inputs.each(function(input) {
           if(input.name.indexOf("[" + name + "]") != -1) {
               matchedInput = input;

               return $break;
           }

           return null;
        });

        return matchedInput;
    } ,

    getNumberOfEntries: function() {
        return this.container.childNodes.length
    } ,

    observeCopiedElement: function(element) {
        var deleteEntryElement;

        if((deleteEntryElement = element.down('.' + this.options.deleteEntryElementClass))) {
            deleteEntryElement.observe('click',function() {
                if(this.options.deletionConfirmText) {
                    if(confirm(this.options.deletionConfirmText)) {
                        element.remove()
                    }
                }
                else {
                    element.remove()
                }
            }.bind(this))
        }
    } ,

    replaceInputNamesInElement: function(element, index) {
        $(element).select("input","textarea","select").each(function(input) {
            input.name = input.name.replace("#index",index)
        }.bind(this))
    }
	});

   var contactsExpander = new ExpandingFormElement({
      entryModel: 'contact-element',
      addEntryLinkElement: 'add-contact',
      deleteEntryElementClass: 'delete-contact',
      deletionConfirmText: "Are you sure you want to delete contact?"
   })


   var educationExpander = new ExpandingFormElement({
      entryModel: 'education-element',
      addEntryLinkElement: 'add-education',
      deleteEntryElementClass: 'delete-education',
      deletionConfirmText: "Are you sure you want to delete education?"
   })

});

