/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */
Event.observe(window, "load", function(){
    getContractInfo(this);
    Event.observe($('pagaio_contract'),'change', getContractInfo);

    // Replace label
    if ($$('[title=Pagaio]').length) {
        $$('[title=Pagaio]')[0].innerHTML = $$('[title=Pagaio]')[0].innerHTML.replace('Pagaio', '<img style="height: 16px;float: left;margin-top: 1px;margin-right: 3px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgBAMAAACBVGfHAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAnUExURUxpcQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAK/XnHYAAAAMdFJOUwAEcB7Vi+y3oFgNPG+L2hUAAAE1SURBVCjPVZG/S8NQEMfvNa8Qk+UKDo6BrBmyuWTQxcUOD6wgkkWhNpQOwaFThji6ZepgOxSEDtJBcJI6xbRp7P1R5jUhP244uA/f+/F9DyCP6ZvMCFXMktfN17JJLCJKTFbVaA3n3wMyKtDdL/N8nmIlEPpgItAKS6Ckej8Jwl4nK6Yw9eA+2txHuCp7VrvtEnKAi+hI8Ici5UYq1mEBFrHWiSXgF+WSS9BM7ufDHoqhfankGftQ/471CQkE5LR7onGxlXypGN7tbqNjCx/9yuwr+9Ld+j5lEqgHVhzmGK5gPe5bojzUMc5yt9yrzDoGut58Ri9QAz0gumY1eAdtYxlYA8vOnX62AOC0fnMJmB6zFsDTMbSBG7VbulvWUpiBaPyjI1zKGgJwRhPPbghgRc92LfgHvUZUq6hqIZcAAABXelRYdFJhdyBwcm9maWxlIHR5cGUgaXB0YwAAeJzj8gwIcVYoKMpPy8xJ5VIAAyMLLmMLEyMTS5MUAxMgRIA0w2QDI7NUIMvY1MjEzMQcxAfLgEigSi4A6hcRdPJCNZUAAAAASUVORK5CYII=" /> Pagaio');
    }
});

/**
 * Retrieve contract information
 *
 * @param event
 */
function getContractInfo(event)
{
    var item = (this == window) ? $('pagaio_contract') : this;
    var contractUrl = BASE_URL + 'pagaio_contract/details';

    if (item.value == '') {
        $('pagaio_contract_info').hide();
        $('pagaio_contract_clauses').hide();
        $('pagaio_contract_error').hide();
        return;
    }

    new Ajax.Request(contractUrl, {
        method: 'get',
        parameters: {id: item.value},
        onSuccess: function(response) {
            var result = response.responseJSON;

            if (result.error) {
                $('pagaio_contract_error_content').update(result.message);
                $('pagaio_contract_info').hide();
                $('pagaio_contract_clauses').hide();
                $('pagaio_contract_error').show();
            } else {
                var contract = result.contract;
                $('pagaio_contract_id').update(contract.data.id);
                $('pagaio_contract_title').update(contract.data.attributes.title);
                $('pagaio_contract_description').update(contract.data.attributes.description);
                $('pagaio_contract_currency').update(contract.data.attributes.currency);
                $('pagaio_contract_created_at').update(contract.data.attributes.created_at_formatted);

                $('pagaio_contract_info').show();
                $('pagaio_contract_error').hide();

                // debugger;
                // Add clauses
                if (typeof contract.data.relationships !== 'undefined' && typeof contract.data.relationships.clauses !== 'undefined') {
                    var clausesHtml = '';
                    contract.data.relationships.clauses.data.each(function (clause) {
                        contract.included.each(function (object) {
                            if (object.type === "clauses" && object.id === clause.id) {
                                clausesHtml += buildClauseHtml(object);
                            }
                        });
                    });
                    $('pagaio_contract_clauses_info').update(clausesHtml);
                    $('pagaio_contract_clauses').show();
                } else {
                    $('pagaio_contract_clauses_info').update('<li>' + Translator.translate('Contract has no clause') + '</li>');
                    $('pagaio_contract_clauses').show();
                }
            }
        }
    });
}

/**
 * Build HTML with clause info
 *
 * @param clause
 * @returns {string}
 */
function buildClauseHtml(clause){
    var html = '';
    var attributesHtml = '';

    attributesHtml += '<ul class="pagaio_contract_clause_attibutes" style="padding-left:30px;">';
    Object.keys(clause.attributes.attributes).forEach(function (key) {
        attributesHtml += '<li>' + key + ' : ' + clause.attributes.attributes[key] + '</li>';
    });
    attributesHtml += '</ul>';

    html += '<ul class="pagaio_contract_info_list" style="border-top:1px solid black;">';
    html += '   <li>' + Translator.translate('ID:') + ' ' + clause.id + '</li>';
    html += '   <li>' + Translator.translate('Type:') + ' ' + clause.attributes.type + '</li>';
    html += '   <li>' + Translator.translate('Name:') + ' ' + clause.attributes.name + '</li>';
    html += '   <li>' + Translator.translate('Description:') + ' ' + clause.attributes.description + '</li>';
    html += '   <li>' + Translator.translate('Enabled:') + ' ' + clause.attributes.enabled + '</li>';
    html += '   <li>' + Translator.translate('Attributes:') + ' ' + attributesHtml + '</li>';
    html += '</ul>';

    return html;
}