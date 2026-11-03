<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageProduct;
use App\Models\VillageProductFile;
use App\Models\VillageProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مدیریت محصولات روستا (جهت فروش).
 *
 * ادمین روستا می‌تواند محصول اضافه/ویرایش/حذف کند، چند تصویر و
 * فایل ضمیمه (کاتالوگ، مشخصات، مجوز) بارگذاری کند.
 */
class ProductController extends Controller
{
    private const DISK = 'public';

    /** حداکثر حجم هر تصویر: ۱۰ مگابایت */
    private const MAX_IMAGE_KB = 10240;

    /** حداکثر حجم هر فایل ضمیمه: ۲۰ مگابایت */
    private const MAX_FILE_KB = 20480;

    // ══════════════════════════════════════════════════════
    // فهرست
    // ══════════════════════════════════════════════════════

    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $products = VillageProduct::with(['images', 'files'])
            ->forVillage($village->id)
            ->ordered()
            ->paginate(25);

        return view('admin.village.products.index', compact('village', 'products'));
    }

    // ══════════════════════════════════════════════════════
    // افزودن
    // ══════════════════════════════════════════════════════

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);

        return view('admin.village.products.form', [
            'village'    => $village,
            'product'    => new VillageProduct(),
            'categories' => VillageProduct::CATEGORIES,
            'maxImageKb' => self::MAX_IMAGE_KB,
            'maxFileKb'  => self::MAX_FILE_KB,
            'suggestPhone' => $village->phone,
        ]);
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $data = $this->validateProduct($request);

        $payload = $this->buildPayload($data, $village, $request);

        $payload['slug'] = VillageProduct::uniqueSlug($village->id, $data['title']);

        $product = VillageProduct::create($payload);

        $this->storeImages($request, $product, $village);
        $this->storeFiles($request, $product, $village);

        return redirect()
            ->route('admin.village.products.index', $village->id)
            ->with('success', 'محصول «' . $product->title . '» با موفقیت ثبت شد.');
    }

    // ══════════════════════════════════════════════════════
    // ویرایش
    // ══════════════════════════════════════════════════════

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $product->load(['images', 'files']);

        return view('admin.village.products.form', [
            'village'    => $village,
            'product'    => $product,
            'categories' => VillageProduct::CATEGORIES,
            'maxImageKb' => self::MAX_IMAGE_KB,
            'maxFileKb'  => self::MAX_FILE_KB,
            'suggestPhone' => $village->phone,
        ]);
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $data = $this->validateProduct($request);

        $payload = $this->buildPayload($data, $village, $request);

        // اگر عنوان عوض شد، اسلاگ هم بازسازی می‌شود
        if ($product->title !== $data['title']) {
            $payload['slug'] = VillageProduct::uniqueSlug($village->id, $data['title'], $product->id);
        }

        $product->update($payload);

        $this->storeImages($request, $product, $village);
        $this->storeFiles($request, $product, $village);

        return redirect()
            ->route('admin.village.products.index', $village->id)
            ->with('success', 'تغییرات محصول ذخیره شد.');
    }

    // ══════════════════════════════════════════════════════
    // حذف
    // ══════════════════════════════════════════════════════

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $product->load(['images', 'files']);

        // پاک کردن فایل‌های فیزیکی
        foreach ($product->images as $img) {
            if ($img->image && Storage::disk(self::DISK)->exists($img->image)) {
                Storage::disk(self::DISK)->delete($img->image);
            }
        }
        foreach ($product->files as $f) {
            if ($f->file_path && Storage::disk(self::DISK)->exists($f->file_path)) {
                Storage::disk(self::DISK)->delete($f->file_path);
            }
        }

        $title = $product->title;

        // رکوردهای وابسته با حذف دستی (FK cascade نداریم)
        VillageProductImage::where('product_id', $product->id)->delete();
        VillageProductFile::where('product_id', $product->id)->delete();
        $product->delete();

        return redirect()
            ->route('admin.village.products.index', $village->id)
            ->with('success', 'محصول «' . $title . '» حذف شد.');
    }

    public function destroyImage($villageId, $id, $imageId)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $img = VillageProductImage::where('product_id', $product->id)->findOrFail($imageId);

        if ($img->image && Storage::disk(self::DISK)->exists($img->image)) {
            Storage::disk(self::DISK)->delete($img->image);
        }
        $img->delete();

        return back()->with('success', 'تصویر حذف شد.');
    }

    public function destroyFile($villageId, $id, $fileId)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $file = VillageProductFile::where('product_id', $product->id)->findOrFail($fileId);

        if ($file->file_path && Storage::disk(self::DISK)->exists($file->file_path)) {
            Storage::disk(self::DISK)->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'فایل حذف شد.');
    }

    /** تغییر سریع وضعیت فعال/غیرفعال */
    public function toggleActive($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $product = $this->findInVillage($village, $id);

        $product->is_active = ! $product->is_active;
        $product->save();

        return back()->with('success', $product->is_active
            ? 'محصول فعال شد و در سایت نمایش داده می‌شود.'
            : 'محصول غیرفعال شد.');
    }

    // ══════════════════════════════════════════════════════
    // کمکی‌ها
    // ══════════════════════════════════════════════════════

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);

        if (! auth()->user()->canManageVillage($village)) {
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }

        return $village;
    }

    private function findInVillage(Village $village, $id): VillageProduct
    {
        return VillageProduct::where('village_id', $village->id)->findOrFail($id);
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'category'          => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:300'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'price'             => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'unit'              => ['nullable', 'string', 'max:40'],
            'quantity'          => ['nullable', 'string', 'max:60'],
            'seller_name'       => ['nullable', 'string', 'max:120'],
            'seller_phone'      => ['nullable', 'string', 'max:30'],
            'delivery_info'     => ['nullable', 'string', 'max:300'],
            'sort_order'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'images'            => ['nullable', 'array', 'max:8'],
            'images.*'          => ['nullable', 'image', 'max:' . self::MAX_IMAGE_KB],
            'files'             => ['nullable', 'array', 'max:5'],
            'files.*'           => ['nullable', 'file', 'max:' . self::MAX_FILE_KB],
            'file_titles'       => ['nullable', 'array'],
            'file_titles.*'     => ['nullable', 'string', 'max:200'],
        ], [
            'title.required' => 'نام محصول را وارد کنید.',
            'price.numeric'  => 'قیمت باید عدد باشد.',
            'images.*.image' => 'فایل‌های تصویر باید از نوع عکس باشند.',
            'images.*.max'   => 'حجم هر تصویر حداکثر ۱۰ مگابایت است.',
            'files.*.max'    => 'حجم هر فایل حداکثر ۲۰ مگابایت است.',
            'images.max'     => 'حداکثر ۸ تصویر مجاز است.',
            'files.max'      => 'حداکثر ۵ فایل مجاز است.',
        ]);
    }

    private function buildPayload(array $data, Village $village, Request $request): array
    {
        return [
            'village_id'        => $village->id,
            'user_id'           => auth()->id(),
            'title'             => $data['title'],
            'category'          => $data['category'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'description'       => $data['description'] ?? null,
            'price'             => $data['price'] ?? null,
            'unit'              => $data['unit'] ?? null,
            'quantity'          => $data['quantity'] ?? null,
            'seller_name'       => $data['seller_name'] ?? null,
            'seller_phone'      => $data['seller_phone'] ?? null,
            'delivery_info'     => $data['delivery_info'] ?? null,
            'is_active'         => $request->boolean('is_active'),
            'is_featured'       => $request->boolean('is_featured'),
            'is_available'      => $request->boolean('is_available', true),
            'sort_order'        => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function storeImages(Request $request, VillageProduct $product, Village $village): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $start = (int) VillageProductImage::where('product_id', $product->id)->max('sort_order');

        foreach ($request->file('images') as $i => $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('village-products/' . $village->id, self::DISK);

            VillageProductImage::create([
                'product_id' => $product->id,
                'image'      => $path,
                'sort_order' => $start + $i + 1,
            ]);
        }
    }

    private function storeFiles(Request $request, VillageProduct $product, Village $village): void
    {
        if (! $request->hasFile('files')) {
            return;
        }

        $titles = (array) $request->input('file_titles', []);
        $start  = (int) VillageProductFile::where('product_id', $product->id)->max('sort_order');
        $i      = 0;

        foreach ($request->file('files') as $key => $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('village-products/' . $village->id . '/files', self::DISK);

            $title = trim((string) ($titles[$key] ?? ''));
            if ($title === '') {
                $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'فایل ضمیمه';
            }

            VillageProductFile::create([
                'product_id' => $product->id,
                'title'      => Str::limit($title, 200, ''),
                'file_path'  => $path,
                'file_name'  => $file->getClientOriginalName(),
                'file_size'  => (int) $file->getSize(),
                'mime_type'  => $file->getClientMimeType(),
                'sort_order' => $start + (++$i),
            ]);
        }
    }
}
