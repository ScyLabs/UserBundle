(function(){
    document.addEventListener("DOMContentLoaded", function(event) {
        var container = document.getElementById('user-profile-container');

        container.querySelector('.switchPassWord').onclick = function(e){
            e.preventDefault();
            this.classList.toggle('active');
            let passwordInput = this.parentNode.querySelector('input#password');

            if('password' === passwordInput.getAttribute('type')){
                passwordInput.setAttribute('type','text');
            }else{
                passwordInput.setAttribute('type','password');
            }
        }
    });
})();