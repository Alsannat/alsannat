require(
	['jquery'],
	function($){
		'use strict',
		$(document).ready(function(){
			$(document).ajaxStop(function ()
			{	console.log("test vest");
            	var weight_kg=$('div[data-index="weight_kg"] .admin__control-text').val();
            	var weight_lbs=$('div[data-index="weight_kg"] .admin__control-addon .admin__control-text').val();
			 	$("input[name='product\\[weight_kg\\]']").on("keyup",function()
			 	{
			 		var currentWeight = parseInt($(this).val());
			 		var weightInPounds = parseFloat(currentWeight*2.205);
			 		if(!isNaN(weightInPounds))
			 		{
			 			$('div[data-index="weight"] .admin__control-addon .admin__control-text').val(weightInPounds);
			 		}
			 		

			 	});

			 	if((weight_kg!=undefined&&weight_kg!="")&&(weight_lbs==undefined||weight_lbs==""))
			 	{
			 		if(!isNaN(parseFloat(weight_kg*2.205)))
			 		{
			 			$('div[data-index="weight"] .admin__control-addon .admin__control-text').val(parseFloat(weight_kg*2.205));
			 		}
			 		
			 	}

			 	if((weight_kg==undefined||weight_kg=="")&&(weight_lbs!=undefined&&weight_lbs!=""))
			 	{
			 		if(!isNaN(parseFloat(weight_lbs/2.205)))
			 		{
			 			$('div[data-index="weight_kg"] .admin__control-text').val(parseFloat(weight_lbs/2.205));
			 		}
			 	}

			 	/*Custom Logic to Update the 7 Volumetric Weight*/
			 	var dividingFactor = 5000;
			 	$(document).on("keyup", "input[name='product\\[dimension_height_1\\]'], input[name='product\\[dimension_width_1\\]'], input[name='product\\[dimension_length_1\\]']", function()
			 	{
			 		console.log("keyup");
			 		var dimension_height_1 = $('div[data-index="dimension_height_1"] .admin__control-text').val();
			 		var dimension_width_1 = $('div[data-index="dimension_width_1"] .admin__control-text').val();
			 		var dimension_length_1 = $('div[data-index="dimension_length_1"] .admin__control-text').val();

			 		if((dimension_height_1!=undefined&&dimension_height_1!="")
			 			&&(dimension_width_1!=undefined&&dimension_width_1!="")
			 			&&(dimension_length_1!=undefined&&dimension_length_1!="")
			 		)
			 		{
			 			var volumetric_weight_1 = parseFloat((parseFloat(dimension_height_1)*parseFloat(dimension_width_1)*parseFloat(dimension_length_1))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_1 = "+volumetric_weight_1);
			 			if(!isNaN(volumetric_weight_1))
			 			{
			 				$("input[name='product\\[volumetric_weight_1\\]']").val(volumetric_weight_1);
			 			}
			 			if(!isNaN(volumetric_weight_1))
			 			{
			 				$("input[name='product\\[volumetric_weight_1\\]']").val(volumetric_weight_1);
			 			}
			 		}	
			 	});
			 	

			 	/*Second Volumetric Weight*/

			 	$(document).on("keyup","input[name='product\\[dimension_height_2\\]'], input[name='product\\[dimension_width_2\\]'], input[name='product\\[dimension_length_2\\]']", function()
			 	{
			 		var dimension_height_2 = $('div[data-index="dimension_height_2"] .admin__control-text').val();
			 		var dimension_width_2 = $('div[data-index="dimension_width_2"] .admin__control-text').val();
			 		var dimension_length_2 = $('div[data-index="dimension_length_2"] .admin__control-text').val();

			 		if((dimension_height_2!=undefined&&dimension_height_2!="")
			 			&&(dimension_width_2!=undefined&&dimension_width_2!="")
			 			&&(dimension_length_2!=undefined&&dimension_length_2!="")
			 		)
			 		{
			 			var volumetric_weight_2 = parseFloat((parseFloat(dimension_height_2)*parseFloat(dimension_width_2)*parseFloat(dimension_length_2))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_2 = "+volumetric_weight_2);
			 			if(!isNaN(volumetric_weight_2))
			 			{
			 				$("input[name='product\\[volumetric_weight_2\\]']").val(volumetric_weight_2);
			 			}
			 		}
			 	});
			 	

			 	/*Third Volumetric Weight*/
			 	$(document).on("keyup","input[name='product\\[dimension_height_3\\]'], input[name='product\\[dimension_width_3\\]'], input[name='product\\[dimension_length_3\\]']", function()
			 	{
			 		var dimension_height_3 = $('div[data-index="dimension_height_3"] .admin__control-text').val();
			 		var dimension_width_3 = $('div[data-index="dimension_width_3"] .admin__control-text').val();
			 		var dimension_length_3 = $('div[data-index="dimension_length_3"] .admin__control-text').val();

			 		if((dimension_height_3!=undefined&&dimension_height_3!="")
			 			&&(dimension_width_3!=undefined&&dimension_width_3!="")
			 			&&(dimension_length_3!=undefined&&dimension_length_3!="")
			 		)
			 		{
			 			var volumetric_weight_3 = parseFloat((parseFloat(dimension_height_3)*parseFloat(dimension_width_3)*parseFloat(dimension_length_3))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_3 = "+volumetric_weight_3);
			 			if(!isNaN(volumetric_weight_3))
			 			{
			 				$("input[name='product\\[volumetric_weight_3\\]']").val(volumetric_weight_3);
			 			}
			 		}
			 	});
			 	

			 	/*Fourth Volumetric Weight*/
			 	$(document).on("keyup","input[name='product\\[dimension_height_4\\]'], input[name='product\\[dimension_width_4\\]'], input[name='product\\[dimension_length_4\\]']",function()
			 	{
			 		var dimension_height_4 = $('div[data-index="dimension_height_4"] .admin__control-text').val();
			 		var dimension_width_4 = $('div[data-index="dimension_width_4"] .admin__control-text').val();
			 		var dimension_length_4 = $('div[data-index="dimension_length_4"] .admin__control-text').val();

			 		if((dimension_height_4!=undefined&&dimension_height_4!="")
			 			&&(dimension_width_4!=undefined&&dimension_width_4!="")
			 			&&(dimension_length_4!=undefined&&dimension_length_4!="")
			 		)
			 		{
			 			var volumetric_weight_4 = parseFloat((parseFloat(dimension_height_4)*parseFloat(dimension_width_4)*parseFloat(dimension_length_4))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_4 = "+volumetric_weight_4);
			 			if(!isNaN(volumetric_weight_4))
			 			{
			 				$("input[name='product\\[volumetric_weight_4\\]']").val(volumetric_weight_4);
			 			}
			 		}
			 	});
			 	

			 	/*Fifth Volumetric Weight*/
			 	$(document).on("keyup","input[name='product\\[dimension_height_5\\]'], input[name='product\\[dimension_width_5\\]'], input[name='product\\[dimension_length_5\\]']", function()
			 	{
			 		var dimension_height_5 = $('div[data-index="dimension_height_5"] .admin__control-text').val();
			 		var dimension_width_5 = $('div[data-index="dimension_width_5"] .admin__control-text').val();
			 		var dimension_length_5 = $('div[data-index="dimension_length_5"] .admin__control-text').val();

			 		if((dimension_height_5!=undefined&&dimension_height_5!="")
			 			&&(dimension_width_5!=undefined&&dimension_width_5!="")
			 			&&(dimension_length_5!=undefined&&dimension_length_5!="")
			 		)
			 		{
			 			var volumetric_weight_5 = parseFloat((parseFloat(dimension_height_5)*parseFloat(dimension_width_5)*parseFloat(dimension_length_5))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_5 = "+volumetric_weight_5);
			 			if(!isNaN(volumetric_weight_5))
			 			{
			 				$("input[name='product\\[volumetric_weight_5\\]']").val(volumetric_weight_5);
			 			}
			 		}
			 	});
			 	

			 	/*Sixth Volumetrc Weight*/
			 	$(document).on("keyup","input[name='product\\[dimension_height_6\\]'], input[name='product\\[dimension_width_6\\]'], input[name='product\\[dimension_length_6\\]']", function()
			 	{
					var dimension_height_6 = $('div[data-index="dimension_height_6"] .admin__control-text').val();
			 		var dimension_width_6 = $('div[data-index="dimension_width_6"] .admin__control-text').val();
			 		var dimension_length_6 = $('div[data-index="dimension_length_6"] .admin__control-text').val();

			 		if((dimension_height_6!=undefined&&dimension_height_6!="")
			 			&&(dimension_width_6!=undefined&&dimension_width_6!="")
			 			&&(dimension_length_6!=undefined&&dimension_length_6!="")
			 		)
			 		{
			 			var volumetric_weight_6 = parseFloat((parseFloat(dimension_height_6)*parseFloat(dimension_width_6)*parseFloat(dimension_length_6))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_6 = "+volumetric_weight_6);
			 			if(!isNaN(volumetric_weight_6))
			 			{
			 				$("input[name='product\\[volumetric_weight_6\\]']").val(volumetric_weight_6);
			 			}
			 		}			 		

			 	});
			 	

			 	/*Seventh Volumetric Weight*/
			 	$(document).on("keyup","input[name='product\\[dimension_height_7\\]'], input[name='product\\[dimension_width_7\\]'], input[name='product\\[dimension_length_7\\]']",function()
			 	{
			 		var dimension_height_7 = $('div[data-index="dimension_height_7"] .admin__control-text').val();
			 		var dimension_width_7 = $('div[data-index="dimension_width_7"] .admin__control-text').val();
			 		var dimension_length_7 = $('div[data-index="dimension_length_7"] .admin__control-text').val();

			 		if((dimension_height_7!=undefined&&dimension_height_7!="")
			 			&&(dimension_width_7!=undefined&&dimension_width_7!="")
			 			&&(dimension_length_7!=undefined&&dimension_length_7!="")
			 		)
			 		{
			 			var volumetric_weight_7 = parseFloat((parseFloat(dimension_height_7)*parseFloat(dimension_width_7)*parseFloat(dimension_length_7))/parseFloat(dividingFactor));
			 			console.log("volumetric_weight_7 = "+volumetric_weight_7);
			 			if(!isNaN(volumetric_weight_7))
			 			{
			 				$("input[name='product\\[volumetric_weight_7\\]']").val(volumetric_weight_7);
			 			}
			 		}
			 	});
			 	


        	});
			 
			 
		});
	});