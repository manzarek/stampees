window.addEventListener("load", function(){

    const menu = document.querySelector(".burger-menu");
    const menuItems = document.querySelectorAll(".burger-menu li");
    const hamburger= document.querySelector(".hamburger");
    const closeIcon= document.querySelector(".close-icon");
    const menuIcon = document.querySelector(".menu-icon");

    closeIcon.classList.add("hidden-menu");

    menuItems.forEach( 
        function(menuItem) { 
            menuItem.classList.add("hidden-li");
        }
    )

    function toggleMenu() {
        if (menu.classList.contains("showMenu")) {
            menu.classList.remove("showMenu");
            closeIcon.classList.remove("displayed-menu");
            closeIcon.classList.add("hidden-menu");
            menuIcon.classList.add("displayed-menu");
            menuIcon.classList.remove("hidden-menu");

            menuItems.forEach( 
                function(menuItem) { 
                    menuItem.classList.remove("displayed-li");
                    menuItem.classList.add("hidden-li");
                }
            )

        } else {
            menu.classList.add("showMenu");
            closeIcon.classList.remove("hidden-menu");
            closeIcon.classList.add("displayed-menu");
            menuIcon.classList.add("hidden-menu");

            menuItems.forEach( 
                function(menuItem) { 
                    menuItem.classList.remove("hidden-li");
                    menuItem.classList.add("displayed-li");
                }
            )
        }
    }

    hamburger.addEventListener("click", toggleMenu);

})
