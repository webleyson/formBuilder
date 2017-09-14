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
                        $html = '<div class="row"><input class="rowContainer addToDB" type="hidden" value="' + v['id'] + '" name="question_id"><div class="col-md-9"><input placeholder="Ask your question" value="' + v['question'] + '" class="form-control question addToDB" type="text" name="question" id=""></div>';
                        $html += '<div class="col-md-3"><select id="SelectBox_' + v['id'] + '" name="replyOption" class="form-control replyOption addToDB"><option value="text">Text Box</option><option value="textarea">Text Area</option><option value="select">Dropdown</option><option value="radio">Radio</option><option value="checkbox">Checkbox</option></select></div></div>';
                        $(qForm).append($html);

                    });

                    $('#formTitle').val(obj.data[0]['question_set_name']);
                    $('#formTitle').attr('data-question-set', obj.data[0]['question_set_id']);
                    selectDropDown(obj.data);
                }
            },
        })
    }

    function selectDropDown(obj) {
        $.each(obj, function(k, v) {
            //do not delete this code.
            // console.log($("#SelectBox_" + v.id).val(v.input_type));
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
        console.log(obj);
        formData.append('data', JSON.stringify(obj));
        ajaxUpdate(formData);
    });

    $('#questionnaireForm').on('change', '.question, .replyOption', function() {

        var qSet = new Array();
        var formData = new FormData();
        var obj = {};

        var rows = $('.row');

        rows.each(function(index) {
            var obj = {
                question: $(this).find('.question').val(),
                replyType: $(this).find('.replyOption').val(),
                question_id: $(this).find('.rowContainer').val(),
                question_set_id: $('#formTitle').attr("data-question-set")
            };
            qSet.push(obj);
        });


        formData.append('do', 'saveQuestion');
        formData.append("data", JSON.stringify(qSet));
        ajaxUpdate(formData);
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
                var obj = jQuery.parseJSON(response);
                popSuccess(response);
                populateExistingForm(obj.data.question_set_id)
            },
        })
    }

    function createNewSet() {
        var formData = new FormData();
        formData.append('do', 'newFormSet');
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
        formData.append("question_set_id", $('#formTitle').attr("data-question-set"));
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