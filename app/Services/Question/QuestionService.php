<?php

namespace App\Services\Question;

use App\Models\Answer;
use App\Models\Question;

class QuestionService{

    public function createQuestion($data) {
       return Question::create($data);
    }

    public function readQuestions() {
        return Question::all();
    }

    public function readQuestion($questionId) {
        return Question::find($questionId);
    }

    public function questionWithAnswers($questionId) {
        $answers = Question::find($questionId)::with('answers');

        return $answers;
    }

    public function toogleFaq($questionId) {
        $question = Question::find($questionId);
        $question->is_faq = !$question->is_faq;
        $question->save();

        return $question;
    }

    public function faq() {
        return Question::where('is_faq', true)->get();
      
    }

    public function answerQuestion($data) {
        $answer =  Answer::create($data);
        return $answer->load('question');
    }


}