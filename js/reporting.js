 $(document).ready(function() {



     $('#reportsModal').on('show.bs.modal', function(e) {
         var getIdFromRow = $(e.relatedTarget).data('question-id');
         showReport(getIdFromRow);
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
                 console.log();
                 $('h4.modal-title').html(obj.data[0].question_set_name);
                 var table = $('table#modalTable');
                 var tbody = $('<tbody></tbody>');
                 var thead = $('<head><tr><th>User ID </th><th>Question</th><th>Answer</th></head >');
                 var headings = [];
                 $.each(obj.data, function(i, v) {
                     console.log(obj);
                     if (headings.indexOf(v.question) == -1) {
                         var rowData = '<tr><th colspan="2">' + v.question + '</th></tr>';
                         rowData += '<tr><td>' + v.user_id + '</td><td>' + v.answer + '</td></tr>';
                     } else {
                         var rowData = '<tr><td>' + v.user_id + '</td><td>' + v.answer + '</td></tr>';
                     }
                     headings.push(v.question);

                     tbody.append(rowData);
                 })

                 table.html(tbody);
             }
         })
     }
 });