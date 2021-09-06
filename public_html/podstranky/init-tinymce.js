tinymce.init({
	selector: "#cl_content",

	height: 500,
	plugins: [
		"advlist autolink link image lists charmap print preview hr pagebreak anchor",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"save insertdatetime table emoticons template contextmenu media paste"
		],

	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",

	entities: "160,nbsp",

	entity_encoding: "named",
	entity_encoding: "raw"
});
