@include('layout.chatsHeader')


<div class="main-container">
    <div class="center-container">
    </div>
</div>

<!-- Sidebar del reto-->
<div id="sidebar2" class="position-fixed bottom-0 end-0 bg-purple text-white p-4" style="width: 260px; height: 92.5vh; z-index: 1040;">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <ul class="list-unstyled">
        <li class="titulo_reto">Reto del dia</li>
        <li class="desc_reto">Descripcion del reto: ndsandjaosjdnsaiubcijunsaiunx ijbkjsanicnsaiuxnijsncinaicijnij</li>

        <button type="submit" class="btn btn-warning w-100 rounded-pill skip-btn mb-3">
            Skip
            <span class="triangle"></span>
            <span class="triangle tight"></span>
        </button>

        <a href="{{ route('user.friendchat') }}" class="text-decoration-none">
            <button type="button" class="btn btn-warning w-100 rounded-pill friend-btn">
                Amigos
                <span class="triangle"></span>
                <span class="triangle tight"></span>
            </button>
        </a>
    </ul>
</div>

<style>
.friend-btn {
    background-color: #9147ff;
    color: white;
    border: none;
    transition: all 0.3s ease;
}

.friend-btn:hover {
    background-color: #7a30dd;
    color: white;
}
</style>