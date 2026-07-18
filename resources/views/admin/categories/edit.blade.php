@extends('layouts.admin')

@section('title', 'Edit Kategori - Kasir Tembakau')

@section('content')
  <div class="mx-auto max-w-3xl">
    <div class="mb-8">
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Master Data
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Edit Kategori
      </h1>

      <p class="mt-2 text-slate-500">
        Perbarui informasi kategori.
      </p>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
          <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            Nama Kategori
          </label>

          <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $category->name) }}"
            required
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >

          @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
            Deskripsi
          </label>

          <textarea
            id="description"
            name="description"
            rows="4"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          >{{ old('description', $category->description) }}</textarea>

          @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-end gap-3">
          <a
            href="{{ route('admin.categories.index') }}"
            class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
          >
            Batal
          </a>

          <button
            type="submit"
            class="rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
          >
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection