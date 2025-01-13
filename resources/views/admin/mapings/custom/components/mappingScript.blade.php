<script>
    window.exampleJson = {
        "test": "[[test]]",
        "obj": {
            "myData": {
                "name": "[[obj.myData.name]]",
                "customer": {
                    "id": "[[obj.myData.customer.id]]",
                    "name": "[[obj.myData.customer.name]]"
                }
            },
            "createdAt": "[[obj.createdAt]]"
        }
    };

    function findValue(obj, map) {
        function valueFixer(obj, map) {
            map = map.replaceAll('{', '');
            map = map.replaceAll('}', '');
            let path = map.split('.');
            path.forEach(key => {
                if (key != "") {
                    obj = obj[key] ?? {};
                }
            });
            obj = (typeof obj == 'object' || typeof obj == 'undefined') ? "" : obj;
            obj = ["null", null, undefined, "undefined"].includes(obj) ? "" : obj;
            return obj;
        }
        const matches = map.match(/\{\{\s*[\w.]+\s*\}\}/g);

        if (matches) {
            matches.forEach(t => {
                map = map.replaceAll(t, valueFixer(obj, t));
            })
            obj = map;
        } else {
            obj = valueFixer;
        }


        return obj;
    }


    let appender = {
        left: '{',
        right: '}',
    };

    let ignoreList = ['Collection', 'createdAt', 'updatedAt', 'deactivatedAt', 'deletedAt', 'lastActiveAt'];


    function flattenKeys(fields, key, res = {}) {
        fields.forEach(t => {
            if (!ignoreList.some(p => t.name.includes(p))) {
                let oftypee = t.type.ofType ?? null;
                oftypee = oftypee ? (oftypee.name ?? 'nott') : 'nott';
                if (oftypee.includes("Connection") === false) {

                    let allData = t.type.fields && typeof t.type.fields == 'object' ? flattenKeys(t.type.fields,
                        t.name, {}) : null;
                    res[t.name] = allData;
                }
            }

        });
        // console.log(res);
        return res;
    }

    function processData(obj) {
        let res = {};

        try {
            for (let [key, value] of Object.entries(obj)) {
                key = key.replaceAll('Type', '');
                let allFields = flattenKeys(value.fields, key);

                res[key] = allFields;

            }
        } catch (error) {
            console.log(error)
        }
        return res;
    }


    function flattenKeysList(obj, parent = '', res = {}) {
        for (let key in obj) {

            let propName = `${parent ? `${parent}.${key}` : key}`;
            if (typeof obj[key] === 'object' && obj[key] !== null) {
                flattenKeysList(obj[key], propName, res);
            } else {
                res[`${appender.left}${appender.left}${propName}${appender.right}${appender.right}`] = obj[key];
            }
        }
        return res;
    }

    function convertToList(data, includeTable = false) {
        let processKeys = processData(data);
        let tableFieldsList = {};
        for (let [k, v] of Object.entries(processKeys)) {
            tableFieldsList[k] = flattenKeysList(v, includeTable ? k : '', {});
        }
        return tableFieldsList;
    }


    function makeMappedList(data, includeTable = false) {
        return convertListToMap(convertToList(data, includeTable));
    }

    function convertListToMap(data) {
        let allMaps = {};
        for (let [k, v] of Object.entries(data)) {
            allMaps[k] = convertToNestedObject(v);
        }
        return allMaps;
    }



    function convertToNestedObject(keys, remover = '') {
        const result = {};

        Object.keys(keys).forEach(key => {
            if (remover != '') {
                key = key.replaceAll(remover, '');
            }

            let data = key.replaceAll(appender.left, '');
            data = data.replaceAll(appender.right, '');
            const parts = data.split('.'); // Split the key by '.'
            let current = result;

            parts.forEach((part, index) => {
                if (!current[part]) {
                    // Create nested objects if they don't exist
                    current[part] = index === parts.length - 1 ? key : {};
                }
                current = current[part]; // Move deeper into the object
            });
        });
        // console.log(result);
        return result;
    }

    function initMappingPicker(picker, jsonData = {}, headerIncluded = false, is_default = null) {
        function valuePicker(parent, jsonData = {}, parentPicker = '', headerIncluded = false, userSelection = false) {
            let allListClass = 'allList';
            let listItemClass = 'list-group-item';
            let jsonListClass = 'jsonList';
            let currentPath = [];

            if (is_default) {
                jsonData = jsonDataa;
            }
            if (userSelection) {
                jsonData = getcorrectJsonData(userSelection);
            }
            if (typeof jsonData == 'string') {
                jsonData = window[jsonData] ?? {};
            }

            if (headerIncluded) {
                jsonData = jsonData[Object.keys(jsonData)[0]] ?? {};
            }

            if (Object.keys(jsonData).length == 0) {
                return;
            }
            let listPicker = 'listPicker';
            if (parentPicker) {
                document.querySelectorAll(parentPicker + " ." + listPicker).forEach(t => {
                    hideParent(t);
                });
            }
            let selectedvalue = parent; //.classList.contains('selectedvalue') ? parent : null;
            if (selectedvalue) {
                selectedvalue.onblur = function(e) {
                    return;
                    let target = e.currentTarget;
                    setTimeout(function(target) {
                        try {
                            if (window.isInsidePicker) {
                                return;
                            }
                        } catch (error) {

                        }
                        hideParent(target.parentElement);
                    }, 200, target);
                }
            }

            function hideParent(parent) {
                if (parent) {
                    parent.classList.add('hidden');
                }

            }


            let parentElem = selectedvalue ? selectedvalue.parentElement : parent;

            let pickerElem = parentElem ? parentElem.querySelector('.' + listPicker) : null;
            if (pickerElem) {
                pickerElem.classList.remove('hidden');
            }

            let jsonList = parentElem.querySelector('.' + jsonListClass);
            if (!jsonList) {
                const ul = document.createElement('ul');
                ul.classList.add('list-group', listPicker, 'absolute');

                const liSearch = document.createElement('li');
                liSearch.classList.add(listItemClass, allListClass);

                const closePicker = document.createElement('li');
                closePicker.innerHTML = "<span class='close '>Close</span>";
                closePicker.classList.add(listItemClass, allListClass, 'text-center');
                closePicker.onclick = function(e) {
                    let target = e.currentTarget;
                    hideParent(target.closest('.' + listPicker));
                }
                ul.appendChild(closePicker);
                const searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.classList.add('form-control', 'searchBar');
                searchInput.placeholder = 'Search...';
                searchInput.oninput = function(e) {
                    handleBlur();
                    const searchTerm = this.value.toLowerCase();
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(function() {
                        $('li', elem).each(function() {
                            const text = $(this).text().toLowerCase();
                            $(this).toggle(text.indexOf(searchTerm) > -1);
                        });
                    }, 300);
                }
                liSearch.appendChild(searchInput);
                const liJsonList = document.createElement('li');
                liJsonList.classList.add(listItemClass, allListClass);
                jsonList = document.createElement('ul');
                jsonList.classList.add('list-group', jsonListClass);
                liJsonList.appendChild(jsonList);
                ul.appendChild(liSearch);
                ul.appendChild(liJsonList);
                parentElem.appendChild(ul);
            }


            let elem = jsonList;
            selectedvalue = selectedvalue ? selectedvalue : parentElem.querySelector('.selectedvalue');
            let isMulti = typeof(selectedvalue.getAttribute('multiple') ?? false) == 'string';

            function renderJsonList(obj, path = []) {
                let listHTML = '';
                if (currentPath.length > 0) {
                    listHTML +=
                        `<li class="${listItemClass}" disabled hidden>Current : ${currentPath.join(' > ')}</li><li class="${listItemClass}" data-next="back"> < Back</li>`;
                }

                Object.keys(obj).forEach(key => {
                    const value = obj[key];

                    const itemId = path.join('.') + '.' + key;
                    let isObj = typeof value == 'object';
                    let dataId = !isObj ? value : "";
                    dataId = userSelection ? dataId.replace(/{{/g, '{{' + userSelection + '.') : dataId;
                    listHTML += `<li class="${listItemClass}" data-id="${dataId}" data-next="${isObj ? itemId : "-"}">
                            <span class="fw-bold" >${key}</span>
                            ${isObj ?
                                        `<span class="fa fa-right"> > </span>` :
                                        ``
                        }
                         </li>`;
                });
                elem.innerHTML = listHTML;
            }

            function handleBlur() {
                window.isInsidePicker = true;
                setTimeout(function() {
                    window.isInsidePicker = false;
                }, 500);
            }
            $(parentElem).on('click', `.${jsonListClass} .${listItemClass}:not(disabled)`, function(e) {
                handleBlur();
                e.preventDefault();
                let item = $(this);
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(function(item) {
                    let next = item.attr('data-next');
                    let id = item.attr('data-id');
                    if (next != '-') {
                        let targetObj = jsonData;
                        let path = {};
                        if (next == 'back') {
                            currentPath.pop();
                            path = currentPath;
                        } else {
                            path = next.split('.');
                            currentPath.push(path[path.length - 1]);
                        }
                        path.forEach(key => {
                            if (key != "") {
                                targetObj = targetObj[key] ?? {};
                            }
                        });
                        renderJsonList(targetObj, path);
                    } else {
                        let lastValue = selectedvalue.value;
                        if (!lastValue.includes(id)) {
                            selectedvalue.value = isMulti ? lastValue + id : id;
                        }
                    }
                }, 100, item);
            });


            let debounceTimeout = null;
            renderJsonList(jsonData);
        }
        document.querySelectorAll(picker + ' input').forEach(t => {
            t.setAttribute('parent', picker);
            t.addEventListener('focus', function(e) {
                let target = e.currentTarget;
                if ($(this).hasClass('options')) {
                    const options = ["contact", "dealership", "vehicle", "customer"];
                    let userSelection = null;

                    const modalHTML = `
                    <div id="selectionModal" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 20px; background: #fff; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); z-index: 1000;">
                        <h3>Please choose an option:</h3>
                        ${options.map(option => `<button class="optionButton" data-option="${option}">${option}</button>`).join('')}
                        <button id="closeModal" style="margin-top: 10px;">Close</button>
                    </div>`;
                    document.body.insertAdjacentHTML('beforeend', modalHTML);

                    const optionButtons = document.querySelectorAll('.optionButton');
                    optionButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            userSelection = this.getAttribute(
                                'data-option'); // Get selected option
                            document.getElementById('selectionModal')
                                .remove(); // Close the modal

                            // Now run the valuePicker function with the selected option
                            if (userSelection) {
                                let mapping = target.getAttribute('data-mapping') ??
                                    jsonData;
                                valuePicker(target, mapping, target.getAttribute(
                                        'parent') ?? "", headerIncluded,
                                    userSelection);
                            }
                        });
                    });

                    // Close the modal if the close button is clicked
                    document.getElementById('closeModal').addEventListener('click', function() {
                        document.getElementById('selectionModal').remove();
                    });
                } else {
                    let mapping = target.getAttribute('data-mapping') ?? jsonData;
                    valuePicker(target, mapping, target.getAttribute('parent') ?? "", headerIncluded);
                }
                // Create the modal with options

            });

        })
    }

    function getcorrectJsonData(userSelection) {
        if (userSelection == 'contact') {
            return contact
        }
        if (userSelection == 'vehicle') {
            return vehicle
        }
        if (userSelection == 'customer') {
            return customer
        }
        if (userSelection == 'dealership') {
            return dealership
        }
    }
</script>
