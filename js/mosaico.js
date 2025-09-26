  document.addEventListener("DOMContentLoaded", () => {
    new Masonry('#dashboard', {
      itemSelector: '.publicacion',
      columnWidth: '.grid-sizer',
      percentPosition: true,
      gutter: 20
    });
  });