<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

// storage untuk melakukan store atau upload data gambar ke dalam server
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        // mengurutkan datanya berdasarkan terbaru dengan method latest() dan membatasi data yang ditampilkan sejumlah 5 data perhalaman.
        $product = Product::latest()->paginate(5);

        return view('product.index', compact('product'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request) {
    $this->validate($request, [
        'nama'     => 'required',
        'merk'   => 'required',
        'harga_beli'   => 'required',
        'harga_jual'   => 'required',
        'stok'   => 'required',
        'image'     => 'required|image|mimes:png,jpg,jpeg',
        'tanggal_input'   => 'required',
    ]);

    //upload image
    $image = $request->file('image');
    $image->storeAs('public/product', $image->hashName());

    $product = Product::create([
        'nama'     => $request->nama,
        'merk'   => $request->merk,
        'harga_beli'   => $request->harga_beli,
        'harga_jual'   => $request->harga_jual,
        'stok'   => $request->stok,
        'image'     => $image->hashName(),
        'tanggal_input'   => $request->tanggal_input,
    ]);

    if($product){
        //redirect dengan pesan sukses
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('products.index')->with(['error' => 'Data Gagal Disimpan!']);
    }
}

public function edit(Product $product)
{
    return view('product.edit', compact('product'));
}

//di dalam function ini kita memiliki sebuah parameter, yaitu Product $product, yang artinya parameter tersebut adalah model PRODUCT yang diambil datanya sesuai dengan ID yang di dapatkan dari URL.

public function update(Request $request, Product $product)
{
    $this->validate($request, [
        'nama'     => 'required',
        'merk'   => 'required',
        'harga_beli'   => 'required',
        'harga_jual'   => 'required',
        'stok'   => 'required',
        'tanggal_input'   => 'required',
    ]);

    //get data Product by ID
    $product = Product::findOrFail($product->id);

    if($request->file('image') == "") {

        $product->update([
            'nama'     => $request->nama,
            'merk'   => $request->merk,
            'harga_beli'   => $request->harga_beli,
            'harga_jual'   => $request->harga_jual,
            'stok'   => $request->stok,
            'tanggal_input'   => $request->tanggal_input,
        ]);

    } else {

        //hapus image lama
        Storage::disk('local')->delete('public/product/'.$product->image);

        //upload new image
        $image = $request->file('image');
        $image->storeAs('public/product', $image->hashName());

        $product->update([
            'nama'     => $request->nama,
            'merk'   => $request->merk,
            'harga_beli'   => $request->harga_beli,
            'harga_jual'   => $request->harga_jual,
            'stok'   => $request->stok,
            'tanggal_input'   => $request->tanggal_input,
            'image'     => $image->hashName(),
        ]);

    }

    if($product){
        //redirect dengan pesan sukses
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diupdate!']);
    }else{
        //redirect dengan pesan error
        return redirect()->route('products.index')->with(['error' => 'Data Gagal Diupdate!']);
    }
}

public function destroy($id)
{
  $product = Product::findOrFail($id);
  Storage::disk('local')->delete('public/product/'.$product->image);
  $product->delete();

  if($product){
     //redirect dengan pesan sukses
     return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
  }else{
    //redirect dengan pesan error
    return redirect()->route('products.index')->with(['error' => 'Data Gagal Dihapus!']);
  }
}

}
