this.imagePreview = function(){	
	/* CONFIG */
		
		xOffset = 50;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	jQuery("a.watermark_preview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		jQuery("div#apply_watermark").append("<p id='watermark_preview_popup'><img src='"+ this.href +"' alt='Image preview' />"+ c +"</p>");								 
		jQuery("p#watermark_preview_popup")
			
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
    },
	function(){
		this.title = this.t;	
		jQuery("p#watermark_preview_popup").remove();
    });	
	jQuery("a.watermark_preview").mousemove(function(e){
		jQuery("#watermark_preview_popup")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};

