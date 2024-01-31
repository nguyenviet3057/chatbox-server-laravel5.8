<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        // Kiểm tra xem request có chứa file ảnh không
        if ($request->hasFile('file')) {
            // Lấy file ảnh từ request
            $image = $request->file('file');

            // Tạo tên file mới để lưu
            $imageName = time() . Str::random(16) . '.' . $image->getClientOriginalExtension();

            // Lưu file vào thư mục public/images
            $image->move(public_path('images'), $imageName);

            // Trả về đường dẫn đến ảnh
            $imagePath = url('/') . '/images/' . $imageName;
            return response()->json(['url' => $imagePath], 200);
        } else {
            // Nếu không có file ảnh, trả về thông báo lỗi
            return response()->json(['message' => 'Không tìm thấy file ảnh.'], 400);
        }
    }
}
