 $(document).ready(function() {
     $('#questionSets').on('click', '.viewReport', function() {
         // showUserAnswers($(this).attr("data-question-id"));
         showReport($(this).attr("data-question-id"));
     });

     function showReport(questionId) {
         var formData = new FormData();
         formData.append('do', 'showReport');
         formData.append("questionId", questionId);
         $.ajax({
             url: "ajax/dbfunctions.ajax.php",
             type: 'POST',
             data: formData,
             dataType: 'html',
             processData: false,
             contentType: false,
             cache: false,
             success: function(response) {
                 var obj = jQuery.parseJSON(response);
                 var table = $('table#reportsTable');
                 var tbody = $('<tbody></tbody>');
                 var thead = $('<head><tr><th>User ID </th><th>Question</th><th>Answer</th></head >');

                 $.each(obj.data, function(i, v) {
                     console.log(v);
                     var rowData = '<tr><td>' + v.user_id + '</td><td>' + v.question + '</td><td>' + v.answer + '</td></tr>';

                     tbody.append(rowData);
                 })

                 table.html(tbody);
             }
         })
     }
 });