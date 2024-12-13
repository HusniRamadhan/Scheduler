document.addEventListener('DOMContentLoaded', function () {
    const sortableList = document.getElementById('sortable-list').querySelector('tbody');
    const addedCourses = new Set();  // Track added courses
    let draggedItem = null;
    const maxSKS = 24;

    // Function to apply drag-and-drop to all types of courses
    function addDragAndDropHandlers(item) {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleDrop);
        item.addEventListener('dragend', handleDragEnd);
    }

    // Function to handle drag start
    function handleDragStart(event) {
        draggedItem = event.target.closest('.sortable-item');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', draggedItem.innerHTML);
    }

    // Function to handle drag over
    function handleDragOver(event) {
        event.preventDefault();
    }

    // Function to handle dropping dragged item
    function handleDrop(event) {
        event.preventDefault();
        const targetItem = event.target.closest('.sortable-item');
        if (draggedItem && targetItem && draggedItem !== targetItem) {
            const parent = targetItem.parentNode;
            const nextSibling = targetItem.nextSibling === draggedItem ? targetItem : targetItem.nextSibling;
            parent.insertBefore(draggedItem, nextSibling);
            updateItemValues(); // Update SKS and order
        }
    }

    // Function to handle end of drag
    function handleDragEnd() {
        draggedItem = null;
    }

    // Use event delegation to handle the delete button click events
    sortableList.addEventListener('click', function (event) {
        // Check if the click happened on a delete button or its child element
        if (event.target.closest('.btn-danger')) {
            const listItem = event.target.closest('.sortable-item');
            if (listItem) {
                removeItem(listItem);
            }
        }
    });

    // Function to handle up/down arrows
    function addUpDownListeners(item) {
        const upButton = item.querySelector('.arrow.up');
        const downButton = item.querySelector('.arrow.down');

        // Move item up
        upButton.addEventListener('click', function () {
            const prevSibling = item.previousElementSibling;
            if (prevSibling) {
                sortableList.insertBefore(item, prevSibling);
                updateItemValues();
            }
        });

        // Move item down
        downButton.addEventListener('click', function () {
            const nextSibling = item.nextElementSibling;
            if (nextSibling) {
                sortableList.insertBefore(nextSibling, item);
                updateItemValues();
            }
        });
    }

    // Remove an item from the list
    function removeItem(item) {
        console.log('Removing item:', item); // Debug log
        const makul = item.querySelector('td').innerText;
        item.parentNode.removeChild(item);

        // Remove the course from the addedCourses set
        addedCourses.delete(makul);

        // Recalculate the SKS and update the textarea
        updateTotalSKS();
        updateTextarea();
    }

    // Adding new courses (handles regular, elective, and special courses)
    document.querySelectorAll('.btn-success').forEach(function (addButton) {
        addButton.addEventListener('click', function () {
            const makul = addButton.getAttribute('data-makul');
            const sks = parseInt(addButton.getAttribute('data-sks'));
            const kode = addButton.getAttribute('data-kode');

            // Check if the course has been added before
            if (!addedCourses.has(makul)) {
                // Calculate current total SKS
                let currentTotalSKS = parseInt(document.getElementById('inputName').value) || 0;

                // Check if adding this course exceeds the maximum allowed SKS (24)
                if (currentTotalSKS + sks <= maxSKS) {
                    // Create new row for the course in the sortable list
                    let newItem = document.createElement('tr');
                    newItem.classList.add('sortable-item');
                    newItem.setAttribute('data-kode', kode);
                    newItem.setAttribute('draggable', true);
                    newItem.style.height = "53px";  // Menambahkan tinggi tetap 53px
                    newItem.style.border = "3px solid black";  // Menambahkan border hitam tebal 5px
                    newItem.innerHTML = `
                        <td class="text-center align-middle"><strong>${makul}</strong></td>
                        <td class="text-center align-middle"><strong>${sks}</strong></td>
                        <td class="text-center align-middle">
                            <span class="btn btn-danger" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trash"></i>
                            </span>
                        </td>
                    `;

                    // <td class="text-center align-middle" width="10%">
                    //     <span class="icon square arrow up"></span>
                    //     <span class="icon square arrow down"></span>
                    // </td>

                    // Append new item to the sortable list
                    sortableList.appendChild(newItem);
                    addedCourses.add(makul); // Track the course
                    updateItemValues(); // Update SKS and order

                    // Apply drag-and-drop functionality
                    addDragAndDropHandlers(newItem);

                    // Apply up/down buttons functionality
                    addUpDownListeners(newItem);

                    // Recalculate the SKS and textarea content after addition
                    updateItemValues();
                    updateTextarea();
                } else {
                    alert('Total SKS tidak dapat lebih dari 24!');
                }
            } else {
                alert('Mata kuliah sudah ditambahkan sebelumnya!');
            }
        });
    });

    // Function to update total SKS and apply color coding
    function updateTotalSKS() {
        const items = sortableList.querySelectorAll('.sortable-item');
        let cumulativeSKS = 0;
        const maxSKS = 24; // Set the maximum SKS value

        items.forEach(item => {
            const sks = parseInt(item.querySelector('td:nth-child(2)').innerText);
            cumulativeSKS += sks;

            // Apply class based on SKS thresholds
            item.classList.remove('sks-low', 'sks-medium', 'sks-high');
            if (cumulativeSKS <= 10) {
                item.classList.add('sks-low');
            } else if (cumulativeSKS <= 18) {
                item.classList.add('sks-medium');
            } else {
                item.classList.add('sks-high');
            }
        });

        // Update the SKS total display in the format "0/24"
        document.getElementById('inputName').value = `${cumulativeSKS}`;
    }

    // Function to update the textarea with JSON data
    function updateTextarea() {
        const sortableItems = document.querySelectorAll('.sortable-item');
        const textarea = document.getElementById('inputDescription');
        const data = [];

        // Gather relevant data for each sortable item
        sortableItems.forEach((item, index) => {
            const urutan = index + 1; // Order starts from 1
            const kode = item.getAttribute('data-kode');
            const sks = parseInt(item.querySelector('td:nth-child(2)').innerText);
            data.push({ urutan, kode, sks });
        });

        // Update the textarea with the data as a JSON string
        textarea.value = JSON.stringify(data);
    }

    function updateItemValues() {
        updateTotalSKS();  // Recalculate SKS after reorder
        updateTextarea();   // Update textarea JSON
    }

    // // Function to add a course to the sortable list and update inputDescription
    // function addCourseToSortableList(makul, sks, kode) {
    //     var sortableList = document.getElementById('sortable-list').querySelector('tbody');

    //     // Check if the course is already added to avoid duplicates
    //     var existingCourse = sortableList.querySelector(`[data-kode="${kode}"]`);
    //     if (existingCourse) {
    //         alert('This course has already been added.');
    //         return;
    //     }

    //     // If the course isn't added, proceed to create a new row
    //     var newRow = document.createElement('tr');
    //     newRow.classList.add('sortable-item');
    //     newRow.setAttribute('data-kode', kode);
    //     newRow.setAttribute('data-sks', sks);
    //     newRow.setAttribute('data-makul', makul);
    //     newRow.setAttribute('draggable', true);

    //     // Define the inner HTML for the new row
    //     newRow.innerHTML = `
    //         <td class="align-middle">${makul}</td>
    //         <td class="text-center">${sks}</td>
    //         <td class="text-center" style="width: 100px">
    //             <span class="btn btn-danger">
    //                 <i class="fas fa-trash"></i>
    //             </span>
    //         </td>
    //         <td class="text-center">
    //             <span class="icon square arrow up"></span>
    //             <span class="icon square arrow down"></span>
    //         </td>
    //     `;

    //     // Append the new row to the sortable list
    //     sortableList.appendChild(newRow);

    //     // Add drag-and-drop event listeners for the new row
    //     addDragAndDropHandlers(newRow);

    //     // Add up/down button listeners
    //     addUpDownListeners(newRow);

    //     // Update SKS and inputDescription immediately
    //     updateTotalSKS();
    //     updateTextarea(); // Ensure the inputDescription is updated immediately
    // }
});
