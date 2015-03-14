jQuery(function() {
	jQuery('#select-all-regions').on('click',function(){
		selectAll('regionFilter');
	});


	jQuery("#filterCL").submit(function(event){
		event.preventDefault();
		resetFilter();		
		
		jQuery("#awardFilter input").each(function(){
			if(this.checked == false) {
				jQuery("[data-award='" + jQuery(this).val() + "']").addClass("hidden");
			}
		});
		
		jQuery("#levelFilter input").each(function(){
			if(this.checked == false) {
				jQuery("[data-awardlevel='" + jQuery(this).val() + "']").each(function(){
					var levelCount = jQuery(this).closest("td").attr("data-levelcount");
					
					jQuery(this).addClass("hidden");
					jQuery(this).closest("td").attr("data-levelcount", (levelCount - 1));
				});
			}
		});
		
		jQuery("#regionFilter input").each(function(){
			if(this.checked == false) {
				jQuery("[" + jQuery(this).val() + "]").each(function(){
					var providerCount = jQuery(this).closest("td").attr("data-providercount");
					var regionCount = parseInt(jQuery(this).attr("data-regioncount"));					
					
					jQuery(this).attr("data-regioncount", (regionCount - 1));

					if(jQuery(this).attr("data-regioncount") == 0) {
						jQuery(this).addClass("hidden");
						jQuery(this).closest("td").attr("data-providercount", (providerCount - 1));
					}
				});
			}
		});
		
		checkRows();
	});

	jQuery("#filterCL :reset").click(function(){
		resetFilter();
		selectAll("awardFilter");
		selectAll("levelFilter");
		selectAll("regionFilter");

		jQuery("#filteredData h4").each(function(){
			jQuery(this).show();
		});
	});
});

function resetFilter() {	
	jQuery("#filteredData table, #filteredData tr, #filteredData span").each(function(){
		jQuery(this).show();
	});

	jQuery("#filteredData .hidden").each(function(){
		if(jQuery(this).text() != "") {
			jQuery(this).removeClass("hidden");
		}
	});
	
	jQuery("[data-regioncount]").each(function(){
		jQuery(this).attr("data-regioncount", jQuery(this).attr("data-defaultregioncount"));
	});
	
	jQuery("[data-providercount]").each(function(){
		jQuery(this).attr("data-providercount", jQuery(this).attr("data-defaultprovidercount"));
	});
	
	jQuery("[data-levelcount]").each(function(){
		jQuery(this).attr("data-levelcount", jQuery(this).attr("data-defaultlevelcount"));
	});
	
	jQuery("#noData").hide();
}

function checkRows() {
	var noData = true;
	jQuery("#filteredData table").each(function(){
		var theTable = jQuery(this);
		var hideTable = true;
		
		theTable.find("tr").each(function(index, item){
			if(index != 0) {
				var theRow = jQuery(this);
				var hideRow = false;
				
				theRow.find("span").each(function(){
					if(jQuery(this).hasClass("hidden")) {
						var theTD = jQuery(this).closest("td");

						if(theTD.attr("data-providercount") != undefined) {
							// check if providers still active.

							if(theTD.attr("data-providercount") != 0) {
								hideRow = false;
							}
							else {
								hideRow = true;
								return false;
							}
						}
						
						if(theTD.attr("data-levelcount") != undefined) {
							// check if levels are still active.

							if(theTD.attr("data-levelcount") != 0) {
								hideRow = false;
							}
							else {
								hideRow = true;
								return false;
							}
						}
						
						if(theTD.attr("data-providercount") == undefined & theTD.attr("data-levelcount") == undefined) {
							hideRow = true;
							return false;
						}
					}
				});				

				if(hideRow) {
					theRow.hide();
				}
				else {
					hideTable = false;
					jQuery("#course_accordion").find("div").each(function(index, item){
						if(index == theTable.attr("data-accordionid")) {
							jQuery(this).removeClass("hidden");
						}
					});

					jQuery("#course_accordion").find("h4").each(function(index, item){
						if(index == theTable.attr("data-accordionid")) {
							jQuery(this).show();
						}
					});

					jQuery("#noData").hide();
				}
			}
		});

		if(hideTable) {
			theTable.hide();
			theTable.parent("div").addClass("hidden");
			jQuery("#course_accordion").find("[data-pos='#collapseid" +  theTable.attr("data-accordionid") + "']").addClass("hidden");
		}
		else {
			noData = false;
		}
	});
	
	if(noData) {
		jQuery("#noData").show();
	}
	else {
		jQuery("#noData").hide();
	}

	checkTables();
}

function checkTables(){
	var noData = true;

	jQuery("#filteredData table").each(function(){
		if(!jQuery(this).is(":visible")) {
			noData = false;
		}
	});

	if(noData) {
		jQuery("#noData").show();
	}
}

function selectAll(theFilter) {
	jQuery("#" + theFilter + " input").each(function(){
		this.checked=true;
	});
}

function deselectAll(theFilter) {
	jQuery("#" + theFilter + " input").each(function(){
		this.checked=false;
		//jQuery(this).attr("checked", false);
	});
}
