// date picker
$(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        locale: {
            format: 'YYYY-MM-DD'
        }
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
});

// tabs navigation
document.addEventListener("DOMContentLoaded", function () {
    const previousBtn = document.getElementById("previous-btn");
    const nextBtn = document.getElementById("next-btn");
    const confirmBtn = document.getElementById("confirm-btn");
    const totalAmount  = document.getElementById("totalAmount");
    const navTabs = document.querySelectorAll('[data-bs-toggle="tab"]');

    navTabs.forEach((tab, index) => {
        tab.addEventListener("shown.bs.tab", function () {
            // Update button visibility based on the active tab
            if (index === 0) {
                previousBtn.style.display = "none";
                confirmBtn.style.display = "none";
                nextBtn.style.display = "inline-block";
                totalAmount.style.display = "none";
            } else if (index === navTabs.length - 1) {
                previousBtn.style.display = "inline-block";
                nextBtn.style.display = "none";
                confirmBtn.style.display = "inline-block";
                totalAmount.style.display = "inline-block";
            } else {
                previousBtn.style.display = "inline-block";
                confirmBtn.style.display = "none";
                nextBtn.style.display = "inline-block";
                totalAmount.style.display = "none";
            }
        });
    });

    previousBtn.addEventListener("click", function () {
        const activeTab = document.querySelector('.nav-link.active');
        const prevTab = activeTab.closest('li').previousElementSibling?.querySelector('.nav-link');
        if (prevTab) prevTab.click();
    });

    nextBtn.addEventListener("click", function () {
        const activeTab = document.querySelector('.nav-link.active');
        const nextTab = activeTab.closest('li').nextElementSibling?.querySelector('.nav-link');
        if (nextTab) nextTab.click();
    });
});