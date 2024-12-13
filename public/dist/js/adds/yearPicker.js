var startYear = 1800;
for (i = new Date().getFullYear(); i > startYear; i--)
{
    $('#yearpicker1').append($('<option />').val(i).html(i));
}

var startYear = 1800;
for (i = new Date().getFullYear()+1; i > startYear; i--)
{
    $('#yearpicker2').append($('<option />').val(i).html(i));
}