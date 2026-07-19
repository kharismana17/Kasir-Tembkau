@extends('layouts.admin')

@section('title', 'Kategori - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#A3978D]">
          Master Data
        </p>

        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#292522]">
          Kategori
        </h1>

        <p class="mt-2 text-sm text-[#8A8179]">
          Kelola kategori produk toko tembakau.
        </p>
      </div>

      <a
        href="{{ route('admin.categories.create') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#C68B59] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#C68B59]/15 transition hover:bg-[#B87948]"
      >
        <span class="text-lg leading-none">+</span>
        Tambah Kategori
      </a>

    </div>


    {{-- SUCCESS ALERT --}}
    @if (session('success'))

      <div class="flex items-start gap-3 rounded-2xl border border-[#D8C3AF] bg-[#F6EEE7] p-4">

        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#C68B59] text-xs font-bold text-white">
          ✓
        </div>

        <p class="text-sm font-medium text-[#6B4F3A]">
          {{ session('success') }}
        </p>

      </div>

    @endif


    {{-- ERROR ALERT --}}
    @if ($errors->has('category'))

      <div class="flex items-start gap-3 rounded-2xl border border-[#E7C4BD] bg-[#FBF0EE] p-4">

        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#A94442] text-xs font-bold text-white">
          !
        </div>

        <p class="text-sm font-medium text-[#A94442]">
          {{ $errors->first('category') }}
        </p>

      </div>

    @endif


    {{-- CATEGORY TABLE --}}
    <div class="overflow-hidden rounded-3xl border border-[#E7E1D9] bg-white shadow-sm">

      {{-- TABLE HEADER --}}
      <div class="flex flex-col gap-2 border-b border-[#E7E1D9] bg-[#FCFBF9] px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

        <div>
          <h2 class="text-base font-semibold text-[#292522]">
            Daftar Kategori
          </h2>

          <p class="mt-1 text-sm text-[#8A8179]">
            Kelola kategori produk yang tersedia di sistem.
          </p>
        </div>

        <span class="w-fit rounded-full bg-[#F3E8DE] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
          {{ $categories->count() }} Kategori
        </span>

      </div>


      <div class="overflow-x-auto">

        <table class="w-full min-w-[700px] text-left">

          <thead class="border-b border-[#E7E1D9] bg-[#F7F5F0]">

            <tr>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                No
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Nama Kategori
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Slug
              </th>

              <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Jumlah Produk
              </th>

              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-[#A3978D]">
                Aksi
              </th>

            </tr>

          </thead>


          <tbody class="divide-y divide-[#F0ECE7]">

            @forelse ($categories as $category)

              <tr class="transition hover:bg-[#FCFBF9]">

                {{-- NO --}}
                <td class="px-6 py-5 text-sm text-[#A3978D]">
                  {{ $loop->iteration }}
                </td>


                {{-- CATEGORY --}}
                <td class="px-6 py-5">

                  <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F3E8DE] text-sm font-bold text-[#6B4F3A]">
                      {{ strtoupper(substr($category->name, 0, 1)) }}
                    </div>

                    <div>

                      <p class="font-semibold text-[#292522]">
                        {{ $category->name }}
                      </p>

                      @if ($category->description)

                        <p class="mt-1 max-w-sm text-sm text-[#8A8179]">
                          {{ $category->description }}
                        </p>

                      @endif

                    </div>

                  </div>

                </td>


                {{-- SLUG --}}
                <td class="px-6 py-5">

                  <span class="rounded-lg bg-[#F7F5F0] px-3 py-1.5 font-mono text-xs text-[#8A8179]">
                    {{ $category->slug }}
                  </span>

                </td>


                {{-- PRODUCT COUNT --}}
                <td class="px-6 py-5">

                  <span class="inline-flex items-center rounded-full bg-[#F3E8DE] px-3 py-1.5 text-xs font-semibold text-[#6B4F3A]">
                    {{ $category->products_count }} produk
                  </span>

                </td>


                {{-- ACTION --}}
                <td class="px-6 py-5">

                  <div class="flex justify-end gap-2">

                    <a
                      href="{{ route('admin.categories.edit', $category) }}"
                      class="rounded-xl border border-[#E1D5C8] bg-white px-4 py-2 text-sm font-semibold text-[#6B4F3A] transition hover:border-[#C68B59] hover:bg-[#F7F5F0]"
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
                        class="rounded-xl border border-[#E7C4BD] bg-[#FBF0EE] px-4 py-2 text-sm font-semibold text-[#A94442] transition hover:bg-[#F8E2DE]"
                      >
                        Hapus
                      </button>

                    </form>

                  </div>

                </td>

              </tr>

            @empty

              <tr>

                <td
                  colspan="5"
                  class="px-6 py-16 text-center"
                >

                  <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F3E8DE] text-[#6B4F3A]">
                    <span class="text-2xl">◇</span>
                  </div>

                  <p class="mt-4 font-semibold text-[#292522]">
                    Belum ada kategori
                  </p>

                  <p class="mt-1 text-sm text-[#8A8179]">
                    Tambahkan kategori pertama untuk mulai mengelola produk.
                  </p>

                </td>

              </tr>

            @endforelse

          </tbody>

        </table>

      </div>

    </div>

  </div>
@endsection