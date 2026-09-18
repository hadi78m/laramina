export const TableRenderer = {

    renderPagination(meta, table) {

        const container = table.container.querySelector('.admin-pagination')
        if (!container) return

        const total = Number(meta.total) || 0
        // per_page ممکن است در meta نباشد؛ از config جدول استفاده می‌کنیم
        const perPage = Number(meta.per_page) || Number(table?.perPage) || total || 1
        const totalPages = Number(meta.last_page) || Math.ceil(total / perPage)
        const current = Number(meta.current_page) || 1

        if (total === 0 || totalPages <= 1) {
            container.innerHTML = ''
            return
        }

        // از from/to سرور استفاده می‌کنیم تا با نتیجه‌ی واقعی query هم‌خوان باشد
        const start = meta.from ?? (current - 1) * perPage + 1
        const end = meta.to ?? Math.min(current * perPage, total)

        const prev = Math.max(1, current - 1)
        const next = Math.min(totalPages, current + 1)

        // اگر per_page فعلی در لیست پیش‌فرض نباشد، آن را اضافه می‌کنیم
        // تا مقدار انتخاب‌شده در select درست نمایش داده شود
        const perPageOptions = [10, 25, 50]
        if (!perPageOptions.includes(perPage)) {
            perPageOptions.push(perPage)
            perPageOptions.sort((a, b) => a - b)
        }

        container.innerHTML = `
        <div class="flex items-center justify-between text-sm">

            <div class="text-gray-600">
                نمایش ${start} تا ${end} از ${total}
            </div>

            <div class="flex items-center gap-1">

                <button data-page="1" ${current === 1 ? 'disabled' : ''}
                    class="px-2 py-1 border rounded">«</button>

                <button data-page="${prev}" ${current === 1 ? 'disabled' : ''}
                    class="px-2 py-1 border rounded">‹</button>

                ${this.pages(current, totalPages)}

                <button data-page="${next}" ${current === totalPages ? 'disabled' : ''}
                    class="px-2 py-1 border rounded">›</button>

                <button data-page="${totalPages}" ${current === totalPages ? 'disabled' : ''}
                    class="px-2 py-1 border rounded">»</button>

                <select data-perpage class="border ml-2 px-2 py-1 rounded">
                    ${perPageOptions.map(n =>
            `<option value="${n}" ${perPage == n ? 'selected' : ''}>${n}</option>`
        ).join('')}
                </select>

            </div>
        </div>
        `
    },

    pages(current, total) {

        let html = ''
        const start = Math.max(1, current - 2)
        const end = Math.min(total, current + 2)

        for (let i = start; i <= end; i++) {
            html += `
            <button
                data-page="${i}"
                class="px-3 py-1 border rounded
                ${i === current ? 'bg-blue-500 text-white' : ''}">
                ${i}
            </button>`
        }

        return html
    }
}
