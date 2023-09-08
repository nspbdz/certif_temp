@extends('layouts.app')
@section('content')
<div class="container mt-xl-50 mt-sm-30 mt-15">

  <div class="row">
    <div class="col-9">
      <form action="{{route ('backend.sample.store') }}" method="post" onsubmit="return snippetName(event)">
        @csrf
        subject
        <input name="subject">
        <br><br>
        <input type="hidden" id="characterCount" name="characterCount" value="">

        <textarea name="wysiwyg" id="myeditorinstance">
             </textarea>

        <button id="CharButton" type="submit">Submit</button>
      </form>

      <!-- <button type="submit">Submit</button> -->
    </div>

    <div class="col-2">
      <p id="snippetName">$$Name</p>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
  $(document).ready(function() {

    function snippetName() {
      event.preventDefault();
      // var name = document.getElementById('nameSnippet').value
      // console.log(name, 'snipetname')
      // var ed = tinyMCE.get('myeditorinstance');
      let editorBody = tinymce.activeEditor.getBody();
      console.log(editorBody);
      tinymce.activeEditor.selection.setContent("$$name");
      console.log(tinymce.activeEditor.selection, 'log ');

    }
    var buttonCount = document.getElementById('snippetName');
    buttonCount.addEventListener('click', snippetName, false);

    function characterCount() {
      event.preventDefault();

      // var max = 1000;
      var max = 10;

      const wordCount = tinymce.activeEditor.plugins.wordcount;
      const numChars = wordCount.body.getCharacterCount()
      console.log(wordCount.body.getCharacterCount())

      //    const characterToRequest= document.getElementById('characterCount');
      // const characterToRequest = document.getElementById("characterCount").setAttribute('value', numChars);
      // console.log(wordCount.body)
      // console.log(wordCount.body.getCharacterCount())

      if (numChars > max) {
        alert("Maximum " + max + " characters allowed.");
        event.preventDefault();
        return false;
      } else {
        event.target.submit();
      }
      // event.target.submit();
    }
    var buttonCount = document.getElementById('CharButton');
    buttonCount.addEventListener('click', characterCount, false);

  });
</script>
@endsection