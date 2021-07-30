function deleteHandle(event) {
    event.preventDefault();

    if(window.confirm('削除してもよろしいでしょうか？')) {
        document.getElementById('delete-form').submit();
    } else {
        alert('キャンセルしました');
    }
}