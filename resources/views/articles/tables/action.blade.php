<div class="flex space-x-2">
    <a href="{{ route('dashboard', ['id' => $id]) }}" class="btn-green-gradient">
        <svg class='w-5' fill='currentColor' xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'>
            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h560v-280h80v280q0 33-23.5 56.5T760-120H200Zm188-212-56-56 372-372H560v-80h280v280h-80v-144L388-332Z"/>
        </svg>
    </a>

    <a href="{{ route('dashboard', ['id' => $id]) }}" class="btn-blue-gradient">
        <svg class='w-5' fill='currentColor' xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'>
            <path d="M200-200h56l345-345-56-56-345 345v56Zm572-403L602-771l56-56q23-23 56.5-23t56.5 23l56 56q23 23 24 55.5T829-660l-57 57Zm-58 59L290-120H120v-170l424-424 170 170Zm-141-29-28-28 56 56-28-28Z"/>
        </svg>
    </a>

    <form action="{{ route('articles.destroy', $id) }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn-red-gradient">
            <svg class='w-5' fill='currentColor' xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'>
                <path d='m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104 56 56Zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z'/>
            </svg>
        </button>
    </form>
</div>