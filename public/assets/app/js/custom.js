function hljsElements(root) {
    root.querySelectorAll('pre > [class^="language-"]').forEach((el) => {
        hljs.highlightElement(el);
    })
}

document.addEventListener('DOMContentLoaded', (event) => {
    hljsElements(document)
})

function editable(textarea) {
    const code = textarea.parentElement.querySelector('.editable-content > code');
    code.innerHTML = textarea.value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    hljs.highlightElement(code);
}

function editableSyncScroll(textarea) {
    const pre = textarea.parentElement.querySelector('.editable-content');
    pre.scrollTop = textarea.scrollTop;
    pre.scrollLeft = textarea.scrollLeft;
}
