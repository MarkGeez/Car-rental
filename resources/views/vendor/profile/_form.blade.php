<!-- Business Name -->
<div>
    <label for="business_name" class="mb-2 block text-sm font-semibold text-slate-700">
        Business Name <span class="text-red-500">*</span>
    </label>
    <input
        id="business_name"
        name="business_name"
        type="text"
        value="{{ old('business_name', $vendor->business_name ?? '') }}"
        placeholder="Enter your business name"
        required
        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
    >
</div>

<div>
    <label for="payment_qr_code" class="mb-2 block text-sm font-semibold text-slate-700">
        Payment QR Code
    </label>
    @if (!empty($vendor?->payment_qr_code))
        <img src="{{ asset('storage/'.$vendor->payment_qr_code) }}" alt="Current payment QR code" class="mb-3 max-h-48 rounded-xl border border-slate-200 object-contain">
    @endif
    <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-5">
        <input id="payment_qr_code" name="payment_qr_code" type="file" accept="image/jpeg,image/png" class="block w-full cursor-pointer text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:font-semibold file:text-white">
        <p class="mt-3 text-xs text-slate-500">Upload the QR code customers should use for payment. JPG or PNG, maximum 5 MB.</p>
    </div>
</div>

<!-- Description -->
<div>
    <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">
        Business Description
    </label>
    <textarea
        id="description"
        name="description"
        rows="4"
        placeholder="Tell customers about your business..."
        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
    >{{ old('description', $vendor->description ?? '') }}</textarea>
</div>

<!-- Address and City -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <label for="address" class="mb-2 block text-sm font-semibold text-slate-700">
            Business Address <span class="text-red-500">*</span>
        </label>
        <input
            id="address"
            name="address"
            type="text"
            value="{{ old('address', $vendor->address ?? '') }}"
            placeholder="Street, building, etc."
            required
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
        >
    </div>

    <div>
        <label for="city" class="mb-2 block text-sm font-semibold text-slate-700">
            City <span class="text-red-500">*</span>
        </label>
        <input
            id="city"
            name="city"
            type="text"
            value="{{ old('city', $vendor->city ?? '') }}"
            placeholder="Enter your city"
            required
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
        >
    </div>
</div>

<!-- Business Permit -->
<div>
    <label for="business_permit" class="mb-2 block text-sm font-semibold text-slate-700">
        Business Permit
    </label>

    <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 transition hover:border-indigo-400 hover:bg-indigo-50/30">
        <input
            id="business_permit"
            name="business_permit"
            type="file"
            accept="image/jpeg,image/png"
            @if ($permitRequired ?? false) required @endif
            class="block w-full cursor-pointer text-sm text-slate-600 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700"
        >

        <p class="mt-3 text-xs text-slate-500">
            Upload a JPG, JPEG, or PNG image. Maximum file size: 10 MB.
        </p>
    </div>
</div>