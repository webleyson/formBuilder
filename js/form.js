$(document).ready(function() {

    getQuestionSets();
    $('#addForm').on('hidden.bs.modal', function() {
        $('.modal-body #questionnaireForm').empty();
        getQuestionSets();
    })

    $('#newQuestionSet').click(function() {
        createNewSet();
    })

    $('.createQuestion').click(function() {
        addField();
    })

    $('#questionSets').on('click', '.loadExistingForm', function() {
        populateExistingForm($(this).attr("data-question-set-id"));
    })

    function populateExistingForm(question_set_id) {
        var formData = new FormData();
        formData.append('do', 'getExisting');
        formData.append("question_set_id", question_set_id);
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

                if (obj.status == "ok") {
                    var qForm = $('#questionnaireForm');
                    qForm.empty();
                    $.each(obj.data, function(i, v) {
                        $html = '<div class="row"><input class="rowContainer addToDB" type="hidden" value="' + v[0] + '" name="rowContainer"><div class="col-md-9"><input placeholder="Ask your question" value="' + v[1] + '" class="form-control question addToDB" type="text" name="question" id=""></div>';
                        $html += '<div class="col-md-3"><select id="SelectBox_' + v[0] + '" name="replyOption" class="form-control replyOption addToDB"><option value="text">Text Box</option><option value="textarea">Text Area</option><option value="select">Dropdown</option><option value="radio">Radio</option><option value="checkbox">Checkbox</option></select></div></div>';
                        $(qForm).append($html);
                        $(document).on('DOMNodeInserted', function(e) {
                            $("#SelectBox_" + v[0]).val(v[2]);
                        });
                    });
                    $('#formTitle').val(obj.data[0][5]);
                    $('#formTitle').attr('data-question-set', obj.data[0][6]);
                }
            },
        })
    }

    function popSuccess($response) {
        $('.messageUpdate').html("Question Saved....");
        $(".messageUpdate").show().delay(1000).fadeOut();
    }

    function addField() {

        $html = '<div class="row"><input class="rowContainer" type="hidden" name="rowContainer"><div class="col-md-9"><input placeholder="Ask your question" class="form-control question" type="text" name="question" id=""></div>';
        $html += '<div class="col-md-3"><select name="replyOption" class="form-control replyOption"><option value="text">Text Box</option><option value="textarea">Text Area</option><option value="select">Dropdown</option><option value="radio">Radio</option><option value="checkbox">Checkbox</option></select></div></div>';

        $('#questionnaireForm').append($html);

        createEmptyDBField();
    }
    $('body').on('change', '#formTitle', function() {
        var formData = new FormData();
        formData.append('do', 'saveTitle');
        var obj = {
            title: this.value,
            set_id: $(this).attr("data-question-set"),
        }
        formData.append('data', JSON.stringify(obj));
        ajaxUpdate(formData);
    });

    $('#questionnaireForm').on('change', '.question, .replyOption', function() {

        var qSet = new Array();
        var formData = new FormData();

        $('#questionnaireForm .addToDB').each(function(k, v) {


            console.log(k + ":::" + v);

            /* var obj = {
     id: $(".rowContainer").val(),
     question_set: $("#formDataSet").val(),
     name: this.name,
     value: this.value,
 };
 qSet.push(obj);*/
        })

        /* formData.append('do', 'saveQuestion');
 formData.append("data", JSON.stringify(qSet));
 ajaxUpdate(formData);*/
    });

    function ajaxUpdate(formData) {

        $.ajax({
            url: "ajax/dbfunctions.ajax.php",
            type: 'POST',
            data: formData,
            dataType: 'html',
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                popSuccess(response);
            },
        })
    }

    function createNewSet() {
        var formData = new FormData();
        formData.append('do', 'getNewId');
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
                console.log(obj);
                if (obj.status == "ok") {
                    $('#formDataSet').val(obj.data);
                }
            },
        })
    }

    function createEmptyDBField() {
        var formData = new FormData();
        formData.append("question_set_id", $('#formDataSet').val());
        formData.append('do', 'createAndGetId');
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
                if (obj.status == "ok") {
                    $(".rowContainer").last().val(obj.data.last_row);
                    $(".question").last().attr('data-question-set', obj.data.question_set);
                }

            },
        })
    }

    function getQuestionSets() {
        var formData = new FormData();
        formData.append('do', 'getAllSets');
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

                if (obj.status == "ok") {
                    var questionDiv = $("#questionSets");
                    questionDiv.empty();
                    var list = $(questionDiv).append('<ul></ul>').find('ul');
                    $.each(obj.data, function(i, v) {
                        list.append('<li  class="box"><h2>' + v[2] + '</h2><p>Questions</p><a data-question-set-id="' + v[1] + '" class="loadExistingForm hover" data-toggle="modal" data-target="#addForm">' + v[0] + '</a></li>');
                    });
                }
            },
        })
    }
});