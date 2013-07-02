
function updateTags(prod_id){
	tagList='tagList'+prod_id;
	tagNames='tagNames'+prod_id;
	tags=$(tagList).getElements('input');
	tags_q='';

	for(i=0; i<tags.length; i++){
		if(tags[i].checked) tags_q+=('&tag_id[]='+tags[i].value);
	}

	var myreq=new Request.HTML({
		url:'index.php?option=com_productbuilder&task=compat.addtags&prod_id='+prod_id+tags_q,
		method:'get',
		onRequest:function(){
			$(tagNames).setStyle('background','url(components/com_productbuilder/assets/images/loader.gif) center center no-repeat');
		},
		onSuccess:function(result){
			$(tagNames).setStyle('background','none');
			$(tagNames).innerHTML=this.response.text;
		},
		onFailure:function(xhr){
			$(tagNames).setStyle('background','none');
			$(tagNames).innerHTML='Failure updating';
		}
	}).send();
}