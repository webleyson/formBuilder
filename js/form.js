$(document).ready(function() {

    getQuestionSets();

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
                console.log(obj);
                if (obj.status == "ok") {
                    var questionDiv = $("#questionSets");
                    questionDiv.empty();
                    var list = $(questionDiv).append('<ul></ul>').find('ul');
                    $.each(obj.data, function(i, v) {
                        list.append('<li class="box"><a class="form_delete"  data-value="' + v.question_set_id + '">delete form</a><h2>' + v.count + '</h2><p>Questions</p><a data-question-set-id="' + v.question_set_id + '" class="loadExistingForm hover" data-toggle="modal" data-target="#addForm">' + v.question_set_name + '</a></li>');
                    });
                }
            },
        })
    }

    $('#questionnaireForm').on('mouseenter', '.questionContainer', function() {
        $(this).find(".remove").show();
    });

    $('#questionnaireForm').on('mouseleave', '.questionContainer', function() {
        $(this).find(".remove").hide();
    });

    $('#questionnaireForm').on('click', '.remove', function(e) {
        e.preventDefault();
        if (confirm("Do you really want to remove this question?")) {
            deleteQuestion($(this).attr("data-question-id"));
        } else {
            alert('Question not deleted');
            return false;
        }

    });

    $('#addForm').on('hidden.bs.modal', function() {
        $('.modal-body #questionnaireForm').empty();
        getQuestionSets();
    });

    $('#questionnaireForm').on('click', '.ordering span', function() {
        updateOrdering($(this));
    });

    $('#saveChanges').click(function() {
        var $questions = $('ul#questionnaireForm'),
            $questionsli = $questions.children('li');

        removeRedundantData();
    })

    function removeRedundantData(questions) {
        $('#questionnaireForm select').each(function() {
            // do something
            console.log($(this).attr("data-question-id") + "::" + $('option:selected', this).val());

        });
    }


    $('#newQuestionSet').click(function() {
        createNewSet();
    });

    $('.createQuestion').click(function() {
        addField();
    });

    $('#questionnaireForm').on('click', '.optionRemove', function(e) {
        e.preventDefault();
        if (confirm("Do you really want to remove this option?")) {
            deleteOption($(this).attr("data-option-id"));
        } else {
            alert('Option not deleted');
            return false;
        }

    });

    $('#questionnaireForm').on('click', '.addOption', function() {
        addSelectOption($(this).attr('data-question-id'));
    });

    $('#questionSets').on('click', '.loadExistingForm', function() {
        populateExistingForm($(this).attr("data-question-set-id"));
    });

    $('#questionSets').on('click', '.form_delete', function(e) {
        e.preventDefault();
        if (confirm("Delete Question set?")) {
            deleteForm($(this).attr('data-value'));
        } else {
            alert('Question set not deleted');
            return false;
        }
    });

    $('#questionnaireForm').on('change', '.additionalOption', function() {
        addNewOption($(this));
    });


    function updateOrdering(el) {
        var positionValue = el.parent().parent().attr("data-question-position");
        var theDiv = el.parent().parent();
        if (el.hasClass('moveDown')) {
            var orderValue = parseFloat(positionValue) + Number(1);
            $(theDiv).next().attr("data-question-position", orderValue - Number(1));
        } else {
            var orderValue = parseFloat(positionValue) - Number(1);
            $(theDiv).prev().attr("data-question-position", orderValue + Number(1));
        }
        el.parent().parent().attr("data-question-position", orderValue);
        updatePositions($('#formTitle').attr('data-question-set'));
    }

    function hideOptions(qid) {
        $('.optionContainer').attr('data-options-container-id', qid).slideUp();
    }

    function updatePositions(questionSet) {

        var $questions = $('ul#questionnaireForm'),
            $questionsli = $questions.children('li');


        $questionsli.sort(function(a, b) {
            var an = a.getAttribute('data-question-position');
            bn = b.getAttribute('data-question-position');

            if (an > bn) {
                return 1;
            }

            if (an < bn) {
                return -1;
            }

            return 0;
        })
        $questionsli.detach().appendTo($questions);
        renumberElements($questionsli);
        updateForm();
    }

    function renumberElements($questionsli) {
        $.each($questionsli, function(i, v) {
            //console.log(i + ":::" + v.getAttribute('data-question-position'));
            v.setAttribute("data-question-position", i);
        });
    }

    function addNewOption(details) {
        var formData = new FormData();
        formData.append('do', 'updateOption');
        var obj = {
            question_id: $(details).attr("data-question-id"),
            option_id: $(details).attr("data-option-id"),
            option_value: $(details).val(),
        }
        formData.append("data", JSON.stringify(obj));
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
                populateExistingForm($('#formTitle').attr("data-question-set"));
            }
        })
    }

    function deleteOption(optionId) {

        var formData = new FormData();
        formData.append('do', 'deleteOption');
        formData.append("option_id", optionId);
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
                populateExistingForm($('#formTitle').attr("data-question-set"));
            }
        })
    }


    function deleteQuestion(questionId) {
        var formData = new FormData();
        formData.append('do', 'deleteQuestion');
        formData.append("question_id", questionId);
        $.ajax({
            url: "ajax/dbfunctions.ajax.php",
            type: 'POST',
            data: formData,
            dataType: 'html',
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                populateExistingForm($('#formTitle').attr("data-question-set"));
            }
        })
    }

    function deleteForm(formId) {
        var formData = new FormData();
        formData.append('do', 'deleteForm');
        formData.append("question_set_id", formId);
        $.ajax({
            url: "ajax/dbfunctions.ajax.php",
            type: 'POST',
            data: formData,
            dataType: 'html',
            processData: false,
            contentType: false,
            cache: false,
            success: function(response) {
                getQuestionSets();
            }
        })
    }

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
                        $html = '<li data-question-position="' + v['position'] + '" id="row_' + v['id'] + '" class="row questionContainer"><input class="rowContainer addToDB" type="hidden" value="' + v['id'] + '" name="question_id"><div class="col-md-9 extraSpacingBottom"><input placeholder="Ask your question" value="' + v['question'] + '" class="form-control question addToDB" type="text" name="question" id=""><a data-question-id="' + v['id'] + '"  class="remove hover">Remove Question</a></div>';
                        $html += '<div class="col-md-3"><select id="SelectBox_' + v['id'] + '"data-question-id="' + v['id'] + '" name="replyOption" class="form-control replyOption addToDB"><option value="text">Text Box</option><option value="textarea">Text Area</option><option value="select">Dropdown</option><option value="radio">Radio</option><option value="checkbox">Checkbox</option></select></div><div class="pull-right ordering"><span class="hover moveDown"><i class="fa fa-angle-down" aria-hidden="true"></i></span><span class="moveUp hover"><i class="fa fa-angle-up" aria-hidden="true"></i></span></div> </li>';
                        $(qForm).append($html);


                        var optionsArray = ["text", 'textarea'];
                        if (optionsArray.indexOf(v.input_type) == -1) {
                            getAdditionalOptions(v['id']);
                        }
                    });

                    $('#formTitle').val(obj.data[0]['question_set_name']);
                    $('#formTitle').attr('data-question-set', obj.data[0]['question_set_id']);
                    selectDropDown(obj.data);
                    var $questions = $('ul#questionnaireForm'),
                        $questionsli = $questions.children('li');
                    renumberElements($questionsli);

                }
            },
        })
    }


    function getAdditionalOptions(qid) {
        var formData = new FormData();
        formData.append('do', 'getOptions');
        formData.append("question_id", qid);

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
                    var optionContainer = $('<div data-options-container-id="' + qid + '" class="col-xs-12 optionContainer"></div>');

                    if (obj.data.length === 0) {
                        $("#row_" + qid + "").append(optionContainer);

                    } else {

                        $.each(obj.data, function(i, v) {
                            var formValue = v['answer_option'] ? v['answer_option'] : "";
                            var html = '<div class="row"><div class="col-xs-12"><input data-question-id="' + v['question_id'] + '" data-option-id="' + v['id'] + '" type="text" placeholder="Add option" class="form-control additionalOption" value="' + formValue + '"><span data-option-id="' + v['id'] + '" class="optionRemove hover"> x </span></div></div>';
                            $("#row_" + v['question_id'] + "").append(optionContainer);

                            $(optionContainer).append(html);
                        })

                    }

                    var addText = '<i data-question-id="' + qid + '" class="fa fa-plus hover addOption" aria-hidden="true"></i>';
                    optionContainer.append(addText);

                }
            }
        })
    }

    function selectDropDown(obj) {
        $.each(obj, function(k, v) {
            //do not delete this code.
            console.log($("#SelectBox_" + v.id).val(v.input_type));
        })
    }


    function popSuccess($response) {
        $('.messageUpdate').html("Question Saved....");
        $(".messageUpdate").show().delay(1000).fadeOut();
    }

    function addField() {

        $html = '<li class="row questionContainer"><input class="rowContainer" type="hidden" name="rowContainer"><div class="col-md-9 extraSpacingBottom"><input placeholder="Ask your question" class="form-control question" type="text" name="question"><a class="remove">Remove Question</a></div>';
        $html += '<div class="col-md-3"><select name="replyOption" class="form-control replyOption"><option value="text">Text Box</option><option value="textarea">Text Area</option><option value="select">Dropdown</option><option value="radio">Radio</option><option value="checkbox">Checkbox</option></select></div><div class="pull-right ordering"><span class="hover moveDown"><i class="fa fa-angle-down" aria-hidden="true"></i></span><span class="hover moveUp"><i class="fa fa-angle-up" aria-hidden="true"></i></span></div> </li>';

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
                $('#formTitle').attr('data-question-set', obj.data.question_set);
            },
        })
    });

    $('#questionnaireForm').on('change', '.question, .replyOption', function() {
        updateForm();
    });

    function updateForm() {
        var qSet = new Array();
        var formData = new FormData();
        var obj = {};

        var rows = $('.questionContainer');

        rows.each(function(index) {
            var obj = {
                question: $(this).find('.question').val(),
                replyType: $(this).find('.replyOption').val(),
                question_id: $(this).find('.rowContainer').val(),
                question_set_id: $('#formTitle').attr("data-question-set"),
                position: $(this).attr('data-question-position') ? $(this).attr('data-question-position') : 0,
            };
            qSet.push(obj);
        });


        formData.append('do', 'saveQuestion');
        formData.append("data", JSON.stringify(qSet));
        ajaxUpdate(formData);


        switch (this.value) {
            default: hideOptions($(this).attr('data-question-id'));
            break;
            case "select":
                    addSelectOption($(this).attr('data-question-id'));
                break;
            case "radio":
                    addSelectOption($(this).attr('data-question-id'));
                break;
            case "checkbox":
                    addSelectOption($(this).attr('data-question-id'));
                break;
        }


    }

    function addSelectOption(qid) {
        createAndGetOptionId(qid);
    }

    function createAndGetOptionId(qid) {
        var formData = new FormData();
        formData.append("question_id", qid);
        formData.append('do', 'createAndGetOptionId');
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
                    var html = '<div class="row"><div class="col-xs-12"><input data-question-id="' + qid + '" data-option-id="' + obj.data + '" type="text" class="form-control additionalOption" placeholder="Add option" value=""><span data-option-id="" class="optionRemove hover"> x </span></div></div>';
                    $('*[data-options-container-id="' + qid + '"]').append(html);
                }
            },
        })
    }

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
                populateExistingForm(obj.data.question_set_id);
            },
        })
    }

    function createNewSet() {
        var formData = new FormData();
        formData.append('do', 'newFormSet');
        $("#formTitle").removeData("data-question-set");
        $('#formTitle').attr('data-question-set', null);
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
                    $('#formTitle').val("");
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


});