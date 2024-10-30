async function fetchRandomSongs() {
    alert('ok')
    try {
        const response = await fetch('http://localhost:8000/api/song-random'); // Thay đổi đường dẫn API thực tế
        const songs = await response.json();

        // Chọn 3 bài hát ngẫu nhiên từ danh sách
        const randomSongs = songs.sort(() => 0.5 - Math.random()).slice(0, 3);

        // Cập nhật danh sách bài hát trong HTML
        const playlist = document.querySelector('.option-all__playlist-list');
        playlist.innerHTML = ''; // Xóa nội dung hiện tại

        randomSongs.forEach(song => {
            const songItem = document.createElement('li');
            songItem.className = 'option-all__playlist-item';
            songItem.innerHTML = `
                <div class="option-all__playlist-item-img-wrapper">
                    <div class="option-all__playlist-item-img-wrapper-action">
                        <i class="fas fa-play option-all__playlist-item-img-wrapper-action-icon2"></i>
                    </div>
                    <div class="option-all__playlist-item-img option-all__playlist-item-img-mv" style="background-image: url(${song.image});"></div>
                </div>
                <div class="option-all__playlist-item-content-mv">
                    <img src="${song.icon}" alt="${song.artist}" class="option-all__playlist-item-content-img">
                    <div class="option-all__playlist-item-content-name">
                        <div class="option-all__playlist-item-content-name1 js__main-color">${song.title}</div>
                        <div class="option-all__playlist-item-content-name2 js__sub-color">${song.artist}</div>
                    </div>
                </div>
            `;
            playlist.appendChild(songItem);
        });
    } catch (error) {
        console.error('Error fetching songs:', error);
    }
}

// Gọi hàm khi tải trang
document.addEventListener('DOMContentLoaded', fetchRandomSongs);