<form id="form-comment" novalidate="" enctype="multipart/form-data">
    @csrf
    {{-- @method('POST') --}}
    <input type="hidden" name="user_id" value="{{ Auth::id() }}" />
    <input type="hidden" name="task_id" id="id_task_comment" />
    <div class="mb-3">
        <label for="name" class="font-weight-bold">Nama Task</label>
        <input type="disabled" class="form-control" id="title_task">
    </div>
    <div class="mb-3">
        <label for="description">Comment</label>
        <textarea class="form-control" name="comment" placeholder="Comment"></textarea>
    </div>
    <div class="mt-2">
        <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
    </div>
</form>
