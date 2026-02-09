<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && ($this->user()->role === 'gov_admin' || $this->user()->role === 'cooperative_admin');
    }

    public function rules()
    {
        return [
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,zip',
            'document_type' => 'required|string|max:191',
            'visibility' => 'required|in:public,private,restricted',
            'cooperative_id' => 'nullable|exists:cooperatives,id',
        ];
    }
}
