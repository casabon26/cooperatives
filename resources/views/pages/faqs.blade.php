@extends('layouts.app')

@section('content')
  <div class="py-4">
    <h1 class="mb-3">FAQs</h1>
    <p class="text-muted">Frequently Asked Questions — add Q&amp;A entries below when available.</p>

    <div class="accordion mt-4" id="faqsAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqOneHeader">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="false" aria-controls="faqOne">
            Sample question: How do I join a cooperative?
          </button>
        </h2>
        <div id="faqOne" class="accordion-collapse collapse" aria-labelledby="faqOneHeader" data-bs-parent="#faqsAccordion">
          <div class="accordion-body">Answer placeholder: Provide a step-by-step guide or link to the membership form.</div>
        </div>
      </div>
    </div>
  </div>
@endsection
