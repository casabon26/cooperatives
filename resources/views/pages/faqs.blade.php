@extends('layouts.app')

@section('styles')
  .accordion .accordion-item { border-radius: 10px; overflow: hidden; margin-bottom: 12px; border: 1px solid rgba(0,0,0,0.06); }
  .accordion-button { transition: background-color .28s ease, box-shadow .28s ease, transform .15s ease; padding: 1rem 1.25rem; }
  .accordion-button::after { transition: transform .28s ease; font-size: 1.15rem; }
  .accordion-button:not(.collapsed) {
    background: linear-gradient(90deg, rgba(200,16,46,0.06), rgba(227,6,19,0.03));
    box-shadow: 0 8px 28px rgba(200,16,46,0.06);
    color: var(--primary);
    font-weight: 700;
    border-left: 6px solid var(--primary);
  }
  .accordion-button:hover { transform: translateY(-2px); }
  .accordion-collapse { transition: height .36s cubic-bezier(.2,.8,.2,1); }
  .accordion-body { transition: opacity .22s ease, padding .22s ease; }
  /* Reduce abrupt radius when nested */
  .accordion .accordion-item + .accordion-item { margin-top: 8px; }
@endsection

@section('content')
  <div class="py-4">
    <h1 class="mb-3">FAQs</h1>
    <p class="text-muted">Frequently Asked Questions</p>

    <div class="accordion mt-4" id="faqsAccordion">
      @if(!empty($items) && $items->count())
        @foreach($items as $i => $it)
          <div class="accordion-item">
            <h2 class="accordion-header" id="faqHeader{{ $i }}">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="false" aria-controls="faq{{ $i }}">
                {{ $it->question }}
              </button>
            </h2>
            <div id="faq{{ $i }}" class="accordion-collapse collapse" aria-labelledby="faqHeader{{ $i }}" data-bs-parent="#faqsAccordion">
              <div class="accordion-body">{!! nl2br(e($it->answer)) !!}</div>
            </div>
          </div>
        @endforeach
      @else
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
      @endif
    </div>
  </div>
@endsection
