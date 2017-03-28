$(function() {
$(".submit").click(function()
{
var name = $("#name").val();
var email = $("#email").val();
var comment = $("#comment").val();
var post_id = $("#post").val();
var dataString = 'name='+ name + '&email=' + email + '&comment=' + comment+ '&post_id=' + post_id;
if(name=='' || email=='' || comment=='')
{
alert('Please Give Valid Details');
}
else
{
$("#flash").show();
$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" />Loading Comment...');
$.ajax({
type: "POST",
url: "commentajax.php",
data: dataString,
cache: false,
success: function(html){
$("ol#update").append(html);
$("ol#update li:last").fadeIn("slow");
$("#flash").hide();
}
});
}return false;
}); });