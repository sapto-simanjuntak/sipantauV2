 <div class="sidebar-wrapper" data-simplebar="true">
     <div class="sidebar-header">
         <div>
             <img src="{{ asset('theme/assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
         </div>
         <div>
             <h4 class="logo-text font-weight-bold ">SI PANTAU</h4>
         </div>
         <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
         </div>
     </div>
     <!--navigation-->
     <ul class="metismenu" id="menu">
         <li class="menu-label">Dashboard</li>
         <li>
             <a href="{{ route('dashboard') }}">
                 <div class="parent-icon"><i class='bx bx-home-alt'></i>
                 </div>
                 <div class="menu-title">Dashboard</div>
             </a>
         </li>
         <li class="menu-label">Hardware / Jaringan / Software</li>
         <li>
             <a href="{{ route('service.index') }}">
                 <div class="parent-icon"><i class='bx bx-book'></i>
                 </div>
                 <div class="menu-title">Request Perbaikan</div>
             </a>
         </li>
         <li class="menu-label">Pembuatan / Pengembangan Sistem / Data</li>

         <li>
             <a href="{{ route('formulir.index') }}">
                 <div class="parent-icon"><i class='bx bx-book'></i>
                 </div>
                 <div class="menu-title">Form Pengajuan</div>
             </a>
         </li>
         <li>
             <a href="{{ route('projects.index') }}">
                 <div class="parent-icon"><i class='bx bx-book'></i>
                 </div>
                 <div class="menu-title">Project</div>
             </a>
         </li>
         <li class="menu-label">Settings</li>

         <li>
             <a href="{{ route('user.index') }}">
                 <div class="parent-icon"><i class='bx bx-user'></i>
                 </div>
                 <div class="menu-title">Users</div>
             </a>
         </li>
         <li>
             <a href="{{ route('permission.index') }}">
                 <div class="parent-icon"><i class='bx bx-user'></i>
                 </div>
                 <div class="menu-title">Permission</div>
             </a>
         </li>
         <li>
             <a href="{{ route('role.index') }}">
                 <div class="parent-icon"><i class='bx bx-user'></i>
                 </div>
                 <div class="menu-title">Roles</div>
             </a>
         </li>

         <li>
             <a href="{{ route('user.show', Auth::id()) }}">
                 <div class="parent-icon"><i class='bx bx-user'></i>
                 </div>
                 <div class="menu-title">My Profile</div>
             </a>
         </li>
         <li>
             <a href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 <div class="parent-icon"><i class='bx bx-undo'></i>
                 </div>
                 <div class="menu-title">{{ __('Logout') }}</div>
             </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                 @csrf
             </form>
         </li>
         {{-- <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon"><i class='bx bx-cart'></i>
                 </div>
                 <div class="menu-title">User</div>
             </a>
             <ul>
                 <li> <a href="ecommerce-products.html"><i class='bx bx-radio-circle'></i>List User</a>
                 </li>
                 <li> <a href="ecommerce-products-details.html"><i class='bx bx-radio-circle'></i>Product
                         Details</a>
                 </li>
                 <li> <a href="ecommerce-add-new-products.html"><i class='bx bx-radio-circle'></i>Add New
                         Products</a>
                 </li>
                 <li> <a href="ecommerce-orders.html"><i class='bx bx-radio-circle'></i>Orders</a>
                 </li>
             </ul>
         </li> --}}


         {{-- <li class="menu-label">UI Elements</li>
         <li>
             <a href="widgets.html">
                 <div class="parent-icon"><i class='bx bx-cookie'></i>
                 </div>
                 <div class="menu-title">Widgets</div>
             </a>
         </li>
         <li>
             <a href="javascript:;" class="has-arrow">
                 <div class="parent-icon"><i class='bx bx-cart'></i>
                 </div>
                 <div class="menu-title">eCommerce</div>
             </a>
             <ul>
                 <li> <a href="ecommerce-products.html"><i class='bx bx-radio-circle'></i>Products</a>
                 </li>
                 <li> <a href="ecommerce-products-details.html"><i class='bx bx-radio-circle'></i>Product
                         Details</a>
                 </li>
                 <li> <a href="ecommerce-add-new-products.html"><i class='bx bx-radio-circle'></i>Add New
                         Products</a>
                 </li>
                 <li> <a href="ecommerce-orders.html"><i class='bx bx-radio-circle'></i>Orders</a>
                 </li>
             </ul>
         </li> --}}

     </ul>
     <!--end navigation-->
 </div>
