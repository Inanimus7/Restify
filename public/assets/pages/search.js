

// Ajax request
$.ajax(
    'http://hotel.collegelink.localhost/Project/public/ajax/search_results.php',
    {
        type: "GET",
        dataType: "html",
        data: formData 
    }).done(function(result){
        //clear results container
        $('#search-results-container').html('');
        
        //Append results to container
        $('#search-results-container').append(result);
        
        // Reinitialize zoom functionality for new results
        initializeZoom();

        //Push URL state
        history.pushState({},'','http://hotel.collegelink.localhost/Project/public/assets/list.php?'+formData);
    });