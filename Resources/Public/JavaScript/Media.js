
$(document).ready(function () {
	/**
	 * Table initialisation
	 *
	 * Properties of Datatables have prefix: b, s, i, o, a, fn etc...
	 * this corresponds to the variable type e.g. boolean, string, integer, object, array, function
	 */
	$('#example').dataTable({
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/index.php",
		"fnServerParams": function (aoData) {
			aoData.push({ "name": "type", "value": "1349784673" });
			aoData.push({ "name": "tx_media_pi1[format]", "value": "json" });
		}
	});
});