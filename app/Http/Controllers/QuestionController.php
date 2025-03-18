<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Services\Question\QuestionService;

class QuestionController extends BaseController
{   
    protected $questionService;

    public function __construct(QuestionService $questionService) {
        $this->questionService = $questionService;

    }

    public function createQuestion(Request $request) {
        $question = $this->questionService->createQuestion($request->validated());
        $this->sendResponse($question, "question sent", 201);
    }

    public function readQuestions(Request $reqeust) {
        $questions =$this->questionService->readQuestions();
        $this->sendResponse($questions, "All Questions", 201);

    }

    public function faq() {
        $questions =$this->questionService->faq();
        $this->sendResponse($questions, "frequently AskedQuestion", 201);
    }

    public function toogleFaq($questionId) {
        $questions = $this->questionService->toogleFaq($questionId);
        $this->sendResponse($questions, "faq state has be updated", 201);

    }

    public function answerQuestion(Request $request, $questionId) {
      

        // $answer = Answer::create([
        //     "user_id" => $request->user()->id,
        //     "question_id" => $question->id,
        //     "answer_text" => $request->input("answer_text")
        // ]);

        $answer = $this->questionService->answerQuestion($request->validated());
        $this->sendResponse($answer, "Answer", 201);
    }

    public function questionWithAnswers(Request $request, $questionId) {
        $questionWithAnswers = $this->questionService->questionWithAnswers($questionId);
        $this->sendResponse($questionWithAnswers, "All Questions", 200);
    }


}
