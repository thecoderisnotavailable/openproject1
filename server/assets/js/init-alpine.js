function data() {
  const self = this;
  function getThemeFromLocalStorage() {
    try {
      const self_ = this;
      //Set event listener
      function runResize() {
        const desktop = document.body.clientWidth > 768 ? true : false;
        const sideNav = document.getElementById("sideNav");
        if (desktop) {
          self.isMobileNSideBar = false;
          sideNav.classList =
            "z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0";
          self.isSideMenuOpen = true;
          sideNav.style.display = "block";
          document.getElementById("mobSidBr").style.display = "none";
        } else {
          const clsMobile =
            "fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden";
          if (sideNav.className != clsMobile) {
            self.isSideMenuOpen = false;
            sideNav.style.display = "none";
          }
          sideNav.classList = clsMobile;
        }
      }[]
      runResize();
    } catch(e){}
    window.addEventListener("resize", function (e) {
      runResize();
    });
    // if user already changed the theme, use it
    if (window.localStorage.getItem("dark")) {
      document.body.style.background = window.localStorage.getItem("dark") == "true" ? "black" : "white";
      return JSON.parse(window.localStorage.getItem("dark"));
    }

    // else return their preferences
    return (
      !!window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches
    );
  }

  function setThemeToLocalStorage(value) {
    window.localStorage.setItem("dark", value);
  }

  return {
    dark: getThemeFromLocalStorage(),
    toggleTheme() {
      this.dark = !this.dark;
      setThemeToLocalStorage(this.dark);
      document.body.style.background = this.dark ? "black" : "white";
    },
    isMobileNSideBar: false,
    toggleSideMenu() {
      this.isSideMenuOpen = !this.isSideMenuOpen;
      this.isMobileNSideBar = !this.isMobileNSideBar;
    },
    closeSideMenu() {
      const isDesktop = document.body.clientWidth > 768 ? true : false;
      this.isMobileNSideBar = false;
      if (isDesktop) return;
      this.isSideMenuOpen = false;
    },
    isNotificationsMenuOpen: false,
    toggleNotificationsMenu() {
      this.isNotificationsMenuOpen = !this.isNotificationsMenuOpen;
    },
    closeNotificationsMenu() {
      this.isNotificationsMenuOpen = false;
    },
    isProfileMenuOpen: false,
    toggleProfileMenu() {
      this.isProfileMenuOpen = !this.isProfileMenuOpen;
    },
    closeProfileMenu() {
      this.isProfileMenuOpen = false;
    },
    isPagesMenuOpen: false,
    togglePagesMenu() {
      this.isPagesMenuOpen = !this.isPagesMenuOpen;
    },
    // Modal
    isModalOpen: false,
    trapCleanup: null,
    openModal() {
      this.isModalOpen = true;
      this.trapCleanup = focusTrap(document.querySelector("#modal"));
    },
    closeModal() {
      this.isModalOpen = false;
      this.trapCleanup();
    },
  };
}
