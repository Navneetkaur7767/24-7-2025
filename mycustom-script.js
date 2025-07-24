
document.addEventListener('DOMContentLoaded', () => {
  let isSelecting = false;
  let startCell = null;
  let selectedCells = new Set();

  function clearSelection() {
    selectedCells.forEach(cell => cell.classList.remove('selected'));
    selectedCells.clear();
  }

  // --- Debugging: check cell width ---
  const sampleCell = document.querySelector('td[data-date]');
  if (sampleCell) {
    console.log('Sample cell width:', sampleCell.offsetWidth);
  } else {
    console.log('No calendar cell found!');
  }
  // --- Debugging ends ---

  function calendar_init(){
  	// Mouse down on a cell - start selection
  document.querySelectorAll('td[data-date]').forEach(cell => {
    cell.addEventListener('mousedown', (e) => {

    	// NEWW also changed here that if mouse is on event strip stop there
      if (e.target.closest('.event-strip')) return;	
      e.preventDefault();
      clearSelection();
      isSelecting = true;
      startCell = cell;
      cell.classList.add('selected');
      selectedCells.add(cell);
    });
  });

  // Mouse over cells while mouse is down - extend selection
  document.querySelectorAll('td[data-date]').forEach(cell => {
    cell.addEventListener('mouseenter', () => {
      if (!isSelecting || !startCell) return;

      clearSelection();

      //Creates an array of all calendar date cells. This lets us calculate indexes of selected cells.
      const allCells = Array.from(document.querySelectorAll('td[data-date]')); 
      const startIndex = allCells.indexOf(startCell);
      const currentIndex = allCells.indexOf(cell);

      if (startIndex === -1 || currentIndex === -1) return; //means no cell is selected

      // Select all cells between startIndex and currentIndex inclusive
      //This ensures the loop goes from the smaller index to the larger one, no matter which direction you dragged.
      const [from, to] = startIndex < currentIndex ? [startIndex, currentIndex] : [currentIndex, startIndex];
      for (let i = from; i <= to; i++) { // now taking from and to index from above 
        allCells[i].classList.add('selected');
        selectedCells.add(allCells[i]);
      }
    });
  });

  // Mouse up anywhere - stop selection and prompt for event title
 document.addEventListener('mouseup', () => {
  if (!isSelecting) return;
    isSelecting = false;

    const dates = Array.from(selectedCells).map(cell => cell.getAttribute('data-date'));
    if (dates.length === 0) return;

    // Sort dates
    dates.sort();
    const startDate = dates[0]; //start date 
    const endDate = dates[dates.length - 1]; //enddate from dates array of strings 

    // Prompt for event title
    const eventTitle = prompt(`Enter event title for ${startDate} to ${endDate}:`);
    if (!eventTitle) {
    	clearSelection(); // clear the selection even if user cancels
    	return;
    }

    // const formData = new FormData();
    // formData.append('action',"add_event");
		// formData.append('event_title',eventTitle);
		// formData.append('start_date',startDate);
		// formData.append('end_date',endDate);

    // Send to PHP to save in DB
    fetch(cal_vars.ajax_url, {
      method: 'POST',
     headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({
      	action:'add_event',
        event_title: eventTitle,
        start_date: startDate,
        end_date: endDate
      })
    })
    .then(response => response.json())
    .then(data => {

      if (!data.success) return alert("Error" + data.message); 
        // After saving, dynamically add the event strip in overlay
        const weekContainers = document.querySelectorAll('.calendar-row');

        weekContainers.forEach(week => {
          const weekStartCell = week.querySelector('td[data-date]');
          if (!weekStartCell) return;

          const weekStart = new Date(weekStartCell.dataset.date);
          const weekEnd = new Date(weekStart);
          weekEnd.setDate(weekEnd.getDate() + 6);

          const start = new Date(startDate);
          const end = new Date(endDate);

          if (end < weekStart || start > weekEnd) return;

          const actualStart = start < weekStart ? weekStart : start;
          const actualEnd = end > weekEnd ? weekEnd : end;
          function normalizeDate(d) {
		  return new Date(d.getFullYear(), d.getMonth(), d.getDate());
		  }
		  const actualStartNormalized = normalizeDate(actualStart);
		  const actualEndNormalized = normalizeDate(actualEnd);
		  const weekStartNormalized = normalizeDate(weekStart);

		  const offsetDays = (actualStartNormalized - weekStartNormalized) / (1000 * 60 * 60 * 24);
		  const duration = ((actualEndNormalized - actualStartNormalized) / (1000 * 60 * 60 * 24)) + 1;

		  const cellWidth = 185.5;
		  const left = offsetDays * cellWidth;
		  const width = duration * cellWidth;

		  // code dubugging
		  console.log('actualStart:', actualStart);
		  console.log('weekStart:', weekStart);
		  console.log('offsetDays:', offsetDays);
		  console.log('left:', left);


          const overlay = week.querySelector('.event-overlay-container');
          const eventDiv = document.createElement('div');
          eventDiv.className = 'event-strip';
          eventDiv.id = `event-${data.event_id}`; 
          eventDiv.setAttribute('draggable' , 'true');

           // NEWWL
          // NEWWTILL

          // overlapping logic for events handdling with js without reload
			let existingStrips = overlay.querySelectorAll('.event-strip');
			let laneIndex = 0;

			while (true) {
			  let conflict = false;
			  for (let strip of existingStrips) {
			    let stripLeft = parseFloat(strip.style.left); // 370
			    let stripWidth = parseFloat(strip.style.width); 
			    let stripRight = stripLeft + stripWidth; //555.5 
			    let newLeft = left; // eg 520
			    let newRight = left + width; //eg 705

			    let stripTop = parseFloat(strip.style.top);
			    if (stripTop !== laneIndex * 28) continue; // Only check events in this lane

			    // Check for horizontal overlap
			    if (!(newRight <= stripLeft || newLeft >= stripRight)) {
			      conflict = true;
			      break; // means you cannot put in lane 0 shift to lane 1 and break 
			    }
			  }

			  if (!conflict) break;
			  laneIndex++;
			}

			const topOffset = laneIndex * 28;

		  eventDiv.dataset.duration = duration;
          eventDiv.style.cssText = `position:absolute;top:${topOffset}px;left:${left}px;width:${width}px;`;
          eventDiv.title = eventTitle;
          eventDiv.innerHTML = `
            <span class="event-text"  id="title-${data.event_id}">${eventTitle}</span>
            <span class="event-actions">
              <button class="edit-btn" onclick="event.stopPropagation(); promptEditEvent(${data.event_id})"><i class="fa fa-pencil"></i></button>
              <button class="dlt-btn" onclick="event.stopPropagation(); deleteEvent(${data.event_id})"><i class="fa fa-remove"></i></button>
            </span>
          `;
          overlay.appendChild(eventDiv);
          bindDragEvents(eventDiv , duration); //so it can be draggable
        });
        clearSelection(); //clear cells after selection
      })
       // else {
      //   alert('Failed to save event: ' + data.message);
      // }
    .catch(err => {
      console.error('Error:', err);
      alert('Error saving event');
    });
});

    // one bracket is pending here
  // Prevent text selection during drag
  document.body.style.userSelect = 'none';

  // normalise function declared for all
  function normalize(d) {
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
  }

  function bindDragEvents(eventDiv, duration=null){
  		 if (eventDiv.classList.contains('drag-bound')) return; // prevent duplicate
         eventDiv.classList.add('drag-bound');
  	
  	const id = eventDiv.id.split('-')[1]; //only event-id id select
  	// duration = duration || Math.round(parseFloat(eventDiv.style.width) / 185.5);
  	duration = duration || parseInt(eventDiv.dataset.duration||1);

  	eventDiv.addEventListener('dragstart', e => {
  		 console.log('Dragging started:', id); // DEBUG
  		e.dataTransfer.setData('text/plain' ,JSON.stringify({eventId: id,duration}));

  	});

}

 // initial bind for all strips
 // document.querySelectorAll('.event-strip').forEach(div => bindDragEvents(div));

 // Bind drag events initially (only to valid draggable strips)
  document.querySelectorAll('.event-strip[draggable="true"]').forEach(div => {
    bindDragEvents(div);
  });

 //drag targets
// DRAGOVER

 document.querySelectorAll('td[data-date]').forEach(cell => {
    cell.addEventListener('dragover', e =>{
    	console.log('dragover on', cell.dataset.date); // DEBUG
     e.preventDefault()});
    cell.addEventListener('drop' ,e => {
    	e.preventDefault();
    	const droppedDate = cell.dataset.date;
    	const data = JSON.parse(e.dataTransfer.getData('text/plain'));
    	const newStart = new Date(droppedDate);
    	const newEnd = new Date(newStart);
    	newEnd.setDate(newStart.getDate() + data.duration - 1);

    	fetch(cal_vars.ajax_url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
          event_id: data.eventId,
          new_start: droppedDate,
          action:'move_event',
          new_end: newEnd.toISOString().slice(0,10)
        })
      })
    	 .then(res => res.json())
    	 .then(response => {
    	 	if (!response.success) return alert("Move failed: " + response.message);

    	 // first store the old title it was causing problem of event spanning over multiple lines
    	 const oldTitle = document.getElementById(`title-${data.eventId}`)?.textContent || 'Moved Event';

		// Remove old strip(s)
		document.querySelectorAll(`#event-${data.eventId}`).forEach(e => e.remove());

		const newStartDate = new Date(droppedDate);
		const newEndDate = new Date(newStartDate);
		newEndDate.setDate(newStartDate.getDate() + data.duration - 1);

		// DEBUGGING
			console.log('Dragged Event:', {
			  id: data.eventId,
			  start: newStart.toISOString().slice(0, 10),
			  end: newEnd.toISOString().slice(0, 10),
			  duration: data.duration
			});

		// Loop through all weeks
		document.querySelectorAll('.calendar-row').forEach(row => {
		  const overlay = row.querySelector('.event-overlay-container');
		  const weekStartCell = row.querySelector('td[data-date]');
		  if (!weekStartCell) return;

		  const weekStart = new Date(weekStartCell.dataset.date);
		  const weekEnd = new Date(weekStart);
		  weekEnd.setDate(weekEnd.getDate() + 6);

		  if (newEndDate < weekStart || newStartDate > weekEnd) return;

		  const actualStart = newStartDate < weekStart ? weekStart : newStartDate;
		  const actualEnd = newEndDate > weekEnd ? weekEnd : newEndDate;

		  const offsetDays = (normalize(actualStart) - normalize(weekStart)) / 86400000;
		  const duration = (normalize(actualEnd) - normalize(actualStart)) / 86400000 + 1;

		  const left = offsetDays * 185.5;
		  const width = duration * 185.5;

		  // lane stacking
		  const existingStrips = overlay.querySelectorAll('.event-strip');
		  let laneIndex = 0;
		  while (true) {
		    let conflict = false;
		    for (let strip of existingStrips) {
		      let stripLeft = parseFloat(strip.style.left);
		      let stripWidth = parseFloat(strip.style.width);
		      let stripRight = stripLeft + stripWidth;
		      let newLeft = left;
		      let newRight = left + width;
		      let stripTop = parseFloat(strip.style.top);
		      if (stripTop !== laneIndex * 28) continue;
		      if (!(newRight <= stripLeft || newLeft >= stripRight)) {
		        conflict = true;
		        break;
		      }
		    }
		    if (!conflict) break;
		    laneIndex++;
		  }

		  const topOffset = laneIndex * 28;
		  // stored the old Title
		  // const oldTitle = document.getElementById(`title-${data.eventId}`)?.textContent || 'Moved Event';

		  const eventDiv = document.createElement('div');
		  eventDiv.className = 'event-strip';
		  eventDiv.id = `event-${data.eventId}`;
		  eventDiv.setAttribute('draggable', 'true');
		  // setting event duration DEBUG
		  eventDiv.dataset.duration = duration;
		  
		  eventDiv.style.cssText = `position:absolute;top:${topOffset}px;left:${left}px;width:${width}px;`;
		  // document.querySelectorAll(`#event-${data.eventId}`).forEach(e => e.remove());
		  eventDiv.title = oldTitle;
		  eventDiv.innerHTML = `
		    <span class="event-text" id="title-${data.eventId}">${oldTitle}</span>
		    <span class="event-actions">
		      <button class="edit-btn" onclick="event.stopPropagation(); promptEditEvent(${data.eventId})"><i class="fa fa-pencil"></i></button>
		      <button class="dlt-btn" onclick="event.stopPropagation(); deleteEvent(${data.eventId})"><i class="fa fa-remove"></i></button>
		    </span>`;

		  overlay.appendChild(eventDiv);
		  bindDragEvents(eventDiv, data.duration);
		});

    	 })
    	 .catch(err => {
    	 	alert("Error during drop");
    	 	console.error(err);
    	 });
    });
});
  }
  calendar_init();

  document.querySelector('.next-month-btn').onclick=function(){
  		const formData =new FormData();
		formData.append('action',"calender__render");
		formData.append('method',"next");
		formData.append("c_month",cal_vars.c_month);
		formData.append("c_year",cal_vars.c_year);
     	fetch(cal_vars.ajax_url, {
		  method: 'POST',
		  body: formData,
		})
		.then(res => res.json())
		.then(data => {
		  if (data.success) {
		  	  console.log(data,"data")
		   // titleElement.textContent = newTitle;
		    document.querySelector('.calender-wrap').innerHTML=data.html;
		    cal_vars.c_month=data.month;
		    cal_vars.c_year=data.year;
		     // calendar_init();
		      document.querySelector('.month-name-wrap').innerHTML=data.name;
		    document.querySelector('.calendar-full-name-wrap').innerHTML="Calendar for "+ data.name;
		    calendar_init();
		    console.log("Updated:", data.message);
		  } else {
		    throw new Error(data.message || "Update failed");
		  }
		})
		.catch(err => {
		  alert("Error in ajax");
		  console.error(err);
		}); 
  		return false;
  };

   document.querySelector('.previous-month-btn').onclick=function(){
   	const formData =new FormData();
		formData.append('action',"calender__render");
			formData.append('method',"prev");
				formData.append("c_month",cal_vars.c_month);
		formData.append("c_year",cal_vars.c_year);

      	fetch(cal_vars.ajax_url, {

			  method: 'POST',
			  body: formData,
			})
			.then(res => res.json())
			.then(data => {
			  if (data.success) {
			    //titleElement.textContent = newTitle;
			     document.querySelector('.calender-wrap').innerHTML=data.html;
			       cal_vars.c_month=data.month;
		    cal_vars.c_year=data.year;
		    document.querySelector('.month-name-wrap').innerHTML=data.name;
		    document.querySelector('.calendar-full-name-wrap').innerHTML="Calendar for "+ data.name;
		     calendar_init();
			    console.log("Updated:", data.message);
			  } else {
			    throw new Error(data.message || "Update failed");
			  }
			})
			.catch(err => {
			  alert("Error in Updating event");
			  console.error(err);
			});
  		return false;
  };

//query selector for options
  document.querySelector('.calender-btn-select').onclick=function(){

  	const selectedMonth = document.querySelector('#c_month').value;
  	const selectedYear = document.querySelector('#c_year').value;

  	if(!selectedMonth||!selectedYear){
  		alert("please add the month and year for result");
  		return false;
  	};

  	const formData =new FormData();
		formData.append('action',"calender__render");
		formData.append('method',"filter");
		formData.append("c_month",selectedMonth);
		formData.append("c_year",selectedYear);

		// console.log("reached here");
     	fetch(cal_vars.ajax_url, {
		  method: 'POST',
		  body: formData,
		})
		.then(res => res.json())
		.then(data => {
		  if (data.success) {
		  	  console.log(data,"data")
		   // titleElement.textContent = newTitle;
		    document.querySelector('.calender-wrap').innerHTML=data.html;
		    cal_vars.c_month=data.month;
		    cal_vars.c_year=data.year;
		     // calendar_init();
		      document.querySelector('.month-name-wrap').innerHTML=data.name;
		    document.querySelector('.calendar-full-name-wrap').innerHTML="Calendar for "+ data.name;
		    calendar_init();
		    console.log("Updated:", data.message);
		  } else {
		    throw new Error(data.message );
		  }
		})
		.catch(err => {
		  alert("Error in ajax");
		  console.error(err);
		}); 
  		return false;
  };

});



// <!-- script for edit event-->

	function promptEditEvent(eventId)
	{	
		// we are taking value from the dom live updating because earlier was issue as promt value was still old
		 const titleElement = document.getElementById(`title-${eventId}`);
   		 const currentTitle = titleElement ? titleElement.textContent.trim() : '';

    	 const newTitle = prompt("Update the event title", currentTitle);
   		 if (!newTitle || newTitle === currentTitle) return;

		const formData =new FormData();
		formData.append('action',"edit_event");
		formData.append('event_id',eventId);
		formData.append('new_title', newTitle);

		console.log("Editing event ID:", eventId, "New title:", newTitle);

		fetch(cal_vars.ajax_url, {
		  method: 'POST',
		  body: formData,
		})
		.then(res => res.json())
		.then(data => {
		  if (data.success) {
		    titleElement.textContent = newTitle;
		    console.log("Updated:", data.message);
		  } else {
		    throw new Error(data.message || "Update failed");
		  }
		})
		.catch(err => {
		  alert("Error in Updating event");
		  console.error(err);
		});
	}


// <!-- Script for DELETE event -->

function deleteEvent(eventId) {
	
	if(!confirm("Do you want to delete event?")) return;
	
	const formData = new FormData();
	formData.append('action','delete_event');
	formData.append('event_id',eventId);

	fetch(cal_vars.ajax_url,
	{
		method:'POST',
		body: formData,
	})
	.then(res =>res.json())
	.then(data => {
	    if (data.success) {
	        const eventElement = document.getElementById(`event-${eventId}`);
	        if (eventElement) {
	            eventElement.remove();
	        }
	    } else {
	        alert(data.message);
	    }
		});

	
}	
