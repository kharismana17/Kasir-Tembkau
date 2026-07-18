@extends('layouts.admin')

@section('title', 'Kategori - Kasir Tembakau')

@section('content')
  <div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
          Master Data
        </p>

        <h1 class="mt-2 text-3xl font-semibold text-slate-900">
          Kategori
        </h1>

        <p class="mt-2 text-slate-500">
          Kelola kategori produk toko tembakau.
        </p>
      </div>

      <a
        href="{{ route('admin.categories.create') }}"
        class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-800"
      >
        + Tambah Kategori
      </a>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    @if ($errors->has('category'))
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
        {{ $errors->first('category') }}
      </div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[700px]">
          <thead class="border-b border-slate-200 bg-slate-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                No
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Nama Kategori
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Slug
              </th>

              <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                Jumlah Produk
              </th>

              <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-100">
            @forelse ($categories as $category)
              <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-sm text-slate-500">
                  {{ $loop->iteration }}
                </td>

                <td class="px-6 py-4">
                  <p class="font-semibold text-slate-900">
                    {{ $category->name }}
                  </p>

                  @if ($category->description)
                    <p class="mt-1 text-sm text-slate-500">
                      {{ $category->description }}
                    </p>
                  @endif
                </td>

                <td class="px-6 py-4 text-sm text-slate-500">
                  {{ $category->slug }}
                </td>

                <td class="px-6 py-4">
                  <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-medium text-emerald-800">
                    {{ $category->products_count }} produk
                  </span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex justify-end gap-2">
                    <a
                      href="{{ route('admin.categories.edit', $category) }}"
                      class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                      Edit
                    </a>

                    <form
                      method="POST"
                      action="{{ route('admin.categories.destroy', $category) }}"
                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
                    >
                      @csrf
                      @method('DELETE')

                      <button
                        type="submit"
                        class="rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-200"
                      >
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                  Belum ada kategori.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection