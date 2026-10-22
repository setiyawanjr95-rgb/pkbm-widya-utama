<x-site.layout title="Pendaftaran Online">
    <x-site.page-header eyebrow="Pendaftaran" title="Pendaftaran Warga Belajar Baru" subtitle="Isi formulir di bawah, tim kami akan menghubungi lewat WhatsApp untuk langkah berikutnya." />

    <section class="container-site grid gap-10 py-12 lg:grid-cols-[1fr_20rem] lg:py-16">
        @if (! $site['registration_open'])
            <div class="card p-10 text-center">
                <x-heroicon-o-lock-closed class="mx-auto size-12 text-brand-300" />
                <h2 class="mt-4 text-xl font-bold">Pendaftaran sedang ditutup</h2>
                <p class="mt-2 text-muted">Silakan hubungi kami untuk informasi pembukaan pendaftaran berikutnya.</p>
                <a href="{{ route('site.contact') }}" class="btn-primary mt-6">Hubungi kami</a>
            </div>
        @else
            <form method="POST" action="{{ route('site.register.store') }}" class="card space-y-8 p-6 sm:p-10">
                @csrf
                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-bold">Ada data yang perlu diperbaiki:</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <h2 class="text-lg font-extrabold text-ink">1. Pilih program</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($programs as $program)
                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 has-[:checked]:ring-4 has-[:checked]:ring-brand-100">
                                <input type="radio" name="program_id" value="{{ $program->id }}" class="mt-1 accent-brand-600" required
                                    @checked((string) old('program_id', $selected) === (string) $program->id)>
                                <span>
                                    <span class="block font-bold text-ink">{{ $program->name }}</span>
                                    <span class="text-sm text-muted">{{ $program->level }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-extrabold text-ink">2. Data diri</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="field-label" for="name">Nama lengkap <span class="text-red-500">*</span></label>
                            <input id="name" name="name" class="field" value="{{ old('name') }}" required maxlength="255" placeholder="Sesuai KTP / akta kelahiran">
                        </div>
                        <div>
                            <label class="field-label" for="nik">NIK</label>
                            <input id="nik" name="nik" class="field" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" placeholder="16 digit (opsional)">
                        </div>
                        <div>
                            <label class="field-label">Jenis kelamin <span class="text-red-500">*</span></label>
                            <div class="flex gap-3">
                                @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $value => $label)
                                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                                        <input type="radio" name="gender" value="{{ $value }}" class="accent-brand-600" required @checked(old('gender') === $value)> {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="field-label" for="birth_place">Tempat lahir <span class="text-red-500">*</span></label>
                            <input id="birth_place" name="birth_place" class="field" value="{{ old('birth_place') }}" required maxlength="100">
                        </div>
                        <div>
                            <label class="field-label" for="birth_date">Tanggal lahir <span class="text-red-500">*</span></label>
                            <input id="birth_date" type="date" name="birth_date" class="field" value="{{ old('birth_date') }}" required max="{{ now()->subDay()->toDateString() }}">
                        </div>
                        <div>
                            <label class="field-label" for="last_education">Pendidikan terakhir</label>
                            <select id="last_education" name="last_education" class="field">
                                <option value="">Pilih...</option>
                                @foreach (\App\Models\Registration::EDUCATION as $value => $label)
                                    <option value="{{ $value }}" @selected(old('last_education') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-extrabold text-ink">3. Kontak</h2>
                    <div class="mt-4 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="phone">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                            <input id="phone" type="tel" name="phone" class="field" value="{{ old('phone') }}" required maxlength="20" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="field-label" for="email">Email</label>
                            <input id="email" type="email" name="email" class="field" value="{{ old('email') }}" maxlength="255" placeholder="Opsional">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="address">Alamat lengkap <span class="text-red-500">*</span></label>
                            <textarea id="address" name="address" rows="3" class="field" required maxlength="1000">{{ old('address') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="notes">Catatan / pertanyaan</label>
                            <textarea id="notes" name="notes" rows="3" class="field" maxlength="1000" placeholder="Misal: hanya bisa belajar sore hari">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Jebakan bot: dibiarkan kosong oleh manusia --}}
                <div class="hidden" aria-hidden="true"><input name="website" tabindex="-1" autocomplete="off"></div>

                <button type="submit" class="btn-primary w-full !py-4 text-base">Kirim Pendaftaran</button>
                <p class="text-center text-xs text-muted">Data kamu hanya dipakai untuk keperluan pendaftaran di {{ $site['site_name'] }}.</p>
            </form>
        @endif

        <aside class="space-y-5">
            <div class="rounded-3xl bg-brand-700 p-6 text-white">
                <p class="font-extrabold">Alur pendaftaran</p>
                <ol class="mt-4 space-y-4 text-sm">
                    @foreach (['Isi formulir online ini', 'Tim kami menghubungi lewat WhatsApp', 'Datang ke PKBM membawa berkas', 'Mulai belajar & dapat akun aplikasi'] as $i => $step)
                        <li class="flex gap-3"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-gold-500 text-xs font-bold text-ink">{{ $i + 1 }}</span>{{ $step }}</li>
                    @endforeach
                </ol>
            </div>
            @if ($site['registration_info'])
                <div class="card p-6">
                    <p class="font-extrabold text-ink">Berkas yang disiapkan</p>
                    <p class="mt-3 text-sm leading-relaxed whitespace-pre-line text-muted">{{ $site['registration_info'] }}</p>
                </div>
            @endif
        </aside>
    </section>
</x-site.layout>
