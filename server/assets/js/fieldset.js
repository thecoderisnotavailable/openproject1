/**
 * Form Wizard a take on rendering form schema
 * Requires CashJS
 */
if (typeof formWiz === "undefined") {
    window.formWiz = new Object();
} else if (typeof formWiz !== "object") {
    throw ("formWiz already exists");
}
(function () {
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function (searchString, position) {
            position = position || 0;
            return this.indexOf(searchString, position) === position;
        };
    }
    if (!String.prototype.endsWith) {
        String.prototype.endsWith = function (searchString, position) {
            var subjectString = this.toString();
            if (position === undefined || position > subjectString.length) {
                position = subjectString.length;
            }
            position -= searchString.length;
            var lastIndex = subjectString.indexOf(searchString, position);
            return lastIndex !== -1 && lastIndex === position;
        };
    }
    if (!String.prototype.includes) {
        String.prototype.includes = function () {
            'use strict';
            return String.prototype.indexOf.apply(this, arguments) !== -1;
        };
    }
    if (!String.prototype.format) {
        String.prototype.format = function () {
            var formatted = this;
            for (var i = 0; i < arguments.length; i++) {
                var regexp = new RegExp('\\{' + i + '\\}', 'gi');
                formatted = formatted.replace(regexp, arguments[i]);
            }
            return formatted;
        };
    }
    if(!String.prototype.replaceAll){
        String.prototype.replaceAll = function(search, replacement) {
            var target = this;
            return target.split(search).join(replacement);
        };
    }
    var FormWizard = new Object();

    /**
     * Instances created in the document
     * @type Array
     */
    FormWizard.instances = new Array();
    
    FormWizard.getXMLSchema = function(params) {
        function parseXML(text){
            parser = new DOMParser();
            return parser.parseFromString(text,"text/xml");
        }
        return fetch(params.url)
            .then(response => response.text())
            .then(xmlString => parseXML(xmlString))
    };

    FormWizard.start = function(){
        var self = this;
        /**
         * Variables
         */
        var formId = "FormWizard#" + FormWizard.instances.length;
        var functions = {};
        var schema = null;
        var obj = new Object();
        var container = null;
        var root = new Array();
        var classNames = new Object();
        var finalHTML = "";
        var optionIDs = new Array();
        var formData = new Object();
        /**
         * Prototypes
         */
        obj.setFunctions = function(params){
            functions = params;
        }
        obj.setSchema = function(params){
            schema = params;
        }
        obj.setClasses = function(params){
            classNames = params;
        }
        obj.setContainer = function(param){
            container = $("#" + param);
        }
        obj.create = function(){
            validSets();
        }
        obj.show = async function(){
            show();
        }
        obj.getRoot = function(){
            return root;
        }
        obj.activate = function() {
            setOptionsAndEvents();
        }
        obj.getOptions = function(params) {
            return optionIDs;
        }

        FormWizard.instances[FormWizard.instances.length] = obj;
        return obj;

        /**
         * Function lists
         */
        function templates(params){
            return {
                "text" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="text" id="{{id}}" ${params.attr}/></label>`,
                "date" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="date" id="{{id}}" ${params.attr}/></label>`,
                "number" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="number" id="{{id}}" ${params.attr}/></label>`,
                "email" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="email" id="{{id}}" ${params.attr}/></label>`,
                "password" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="password" id="{{id}}" ${params.attr}/></label>`,
                "textarea" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><textarea id="{{id}}" ${params.attr}>${params.value || ""}</textarea></label>`,
                "checkbox" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><input type="checkbox" id="{{id}}" ${params.attr}/></label>`,
                "options" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><select id="{{id}}" ${params.attr}>${params.value}</select></label>`,
                "option" : `<option ${params.attr}>${params.value}</option>`,
                "file" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><div><input id="{{id}}" type="file" ${params.attr}/></div></label>`,
                "files" : `<label class="block text-sm"><span class="text-gray-700 dark:text-gray-400" data-title="{{id}}">${params.title}</span><div><input id="{{id}}" type="file" multiple="true" ${params.attr}/></div></label>`
            }
        }
        function getTag(xml, params) {
            return xml.getElementsByTagName(params);
        }
        function validSets(){
            var s = getTag(schema, "set");
            for (let i = 0; i < s.length; i++) {
                var r = s[i];
                root[i] = readAttributes(r);
                root[i]["_type"] = "section";
                root[i]["root"] = new Array();
                setFields(root[i]["root"], r);
            }
            console.log(root)
        }
        function setFields(obj, xml){
            const e = xml.children;
            for (let j = 0; j < e.length; j++) {
                const n = e[j];
                const attr = readAttributes(n);
                obj[j] = {...attr};
                //localName  = div
                if(attr.type == "options"){
                    const c = getTag(n, "option");
                    var options = new Array();
                    for (let i = 0; i < c.length; i++) {
                        const v = c[i];
                        const attr2 = {
                            ...readAttributes(v),
                            value: v.textContent,
                            type: "option"
                        };
                        options[i] = {
                            ...attr2,
                            selected: attr.selected == attr2.data
                        }
                        getProp(options[i], attr2);
                    }
                    obj[j]["options"] = options;
                    optionIDs.push(obj[j]);
                }
                if(n.localName == "div" || attr.type == "custom"){
                    obj[j]["type"] = attr.type || "div";
                    obj[j]["root"] = new Array();
                    setFields(obj[j]["root"], n);
                    obj[j]["_type"] = "sub";
                } else {
                    obj[j]["_type"] = "field";
                    getProp(obj[j], attr);
                }
            }
        }
        function readAttributes(r){
            var z = new Object();
            for (let j = 0; j < r.attributes.length; j++) {
                z = {...z, [r.attributes[j].name] : r.attributes[j].value}
            }
            return z;
        }
        function getProp(obj, attrib){
            var attr = new Array();
            var attr2 = new Array();
            var value = "";
            // if(attrib.id){
            //     attr.push(`id="${attrib.id}"`);
            // }
            if(classNames.hasOwnProperty(attrib.type)){
                attr.push(`class="${classNames[attrib.type]}"`);
            }
            attrib.placeholder && attr.push(`placeholder="${attrib.placeholder}"`);
            attrib.req && attr.push(`required`);
            if(attrib.type == "option"){
                attrib.data && attr.push(`value="${attrib.data}"`);
                obj.selected && attr.push("selected");
                value = attrib.value;
            }
            attrib.max && attr.push(`maxlength="${attrib.max}"`);
            attrib.regex && attr.push(`regex="${attrib.max}"`);
            if(attrib.type=="textarea" && attrib.value){
                value = attrib.value;
            }
            var opHTML = "";
            if(attrib.type=="options"){
                for (let i = 0; i < obj.options.length; i++) {
                    const o = obj.options[i];
                    opHTML += o["template"];
                }
                value = opHTML;
            }
            obj["template"] = templates({
                attr: attr.join(" "),attr2,value, title: attrib.title || ""
            })[attrib.type];
        }
        async function show(){
            await makeIt({root : root, i: 0});
            container[0].innerHTML = finalHTML;
        }
        async function makeIt(params) {
            var q = params.root[params.i];
            if(q == undefined) return;
            var newI = params.i + 1;
            if(q["_type"] == "section"){
                finalHTML += `<section data-type="section"><h4 class="mb-4 text-lg font-semibold text-gray-600 dark:text-gray-300">
                ` + q.title + `</h4><div class="${classNames["section"]}" title="` + q.title + `">`;
                newI = 0;
                await makeIt({
                    root: q.root,
                    i: newI,
                    id: q.id
                });
                finalHTML += "</div></section>";
            }
            if(params.root[params.i + 1] != undefined && q["_type"] == "section") {
                await makeIt({
                    root: params.root,
                    i : params.i + 1
                });
            }
            /**
             * DIV && Custom
             */
            if(q["type"] == "div"){
                finalHTML += "<div title=\"" + q.title + "\">";
                newI = 0;
                console.log(q)
                await makeIt({
                    root: q.root,
                    i: newI,
                    id: q.id
                });
                finalHTML += "</div>";
                newI = params.i + 1;
                await makeIt({
                    root: params.root,
                    i: newI,
                    id: params.id
                });
            } else if(q["_type"] == "sub"){
                if(q.template){
                    finalHTML += q.template.replaceAll("{{id}}", q.id);
                }
                newI = params.i + 1;
                await makeIt({
                    root: params.root,
                    i: newI,
                    id: params.id
                });
            }
            /**
             * Fields
             */
            if(q["_type"] != undefined && q["_type"] == "field"){
                finalHTML += q.template.replaceAll("{{id}}", "form" + q.id);
                newI = params.i + 1;
                await makeIt({
                    root: params.root,
                    i: newI,
                    id: params.id
                });
            }
        }
        function setOptionsAndEvents(){
            if(typeof Select == 'undefined' || optionIDs[0] == undefined) return;
            function observer() {
                var composeBox = document.querySelectorAll("#form"+optionIDs[0].id)[0];
                if(!composeBox) {
                    window.setTimeout(observer,500);
                    return;
                }
                for (let i = 0; i < optionIDs.length; i++) {
                    const e = optionIDs[i];
                    var mySelect = new Select('#form'+e.id);
                    optionIDs[i]["select"] = mySelect;
                }
            }
            observer();
        }
    };

    formWiz = FormWizard;
}());