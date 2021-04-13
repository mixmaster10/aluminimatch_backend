<?php
namespace App\Http\Controllers\Api;

use App\Events\PostReaction;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\MatchTrait;
use App\Traits\DistanceTrait;
use App\Models\User;
use App\Models\UserCoords;
use App\Models\UserHobby;
use App\Models\UserDegree;
use App\Models\College;
use App\Models\Post;
use App\Models\PostType;
use App\Models\PostCategory;
use App\Models\PostLikes;
use App\Models\PostComments;
use App\Models\PostReports;
use App\Events\Tagged;

use config\constants;

class PostController extends Controller
{
    use MatchTrait;
    use DistanceTrait;

    public function getPostType(Request $request) {
        $typeId = $request->route('typeId');
        $types = $typeId ? PostType::find($typeId) : PostType::all();
        return $this->successResponse($types,"Post Types Listed successfully");
    }

    public function createOrUpdatePostType(Request $request) {
        $typeId = $request->route('typeId');
        $validator = $this->_validatePostTypeCategories();
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $isPostTypeCreated = PostType::updateOrCreate(['id' => $typeId],$request->all());
        $message = $typeId ? "Post Type Updated successfully" : "Post Type Created successfully";
        return $this->successResponse(null,$message);
    }

    public function getPostTypeCategories(Request $request) {
        $typeId = $request->route('typeId');
        $categories = $typeId ? PostCategory::where("postTypeId",$typeId)->get() : PostCategory::all();
        return $this->successResponse($categories,"Post Type Categories Listed successfully");
    }

    public function createOrUpdatePostCategory(Request $request) {
        $categoryId = $request->route('categoryId');
        $validator = $this->_validatePostTypeCategories(true);
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $isPostTypeCategoryCreated = PostCategory::updateOrCreate(['id' => $categoryId],$request->all());
        $message = $categoryId ? "Post Type Category updated successfully" : "Post Type Category created successfully";
        return $this->successResponse(null,$message);
    }

    public function getAllPosts(Request $request){
        $postId = $request->route('postId');
        $authUserId = $request->user()->id;
        $isAuthPost = $request->isAuthPost;
        $query = Post::query();
        
        if($isAuthPost === true){
            $query->where("userId",'=',$authUserId);
        } 

        if($postId){
            $query->whereId($postId);
        }
        
        
        $query->with('user')->with('type')->with('category')->withCount('likes')->withCount("comments")->orderBy('created_at', 'desc');

        if($postId){
            $posts = $query->first();
        }
        else {
            $posts = $query->get();
            $posts = $posts->map(function ($post) use ($authUserId) {
                $post->match = $this->getMatchPercent($authUserId, $post->userId);
                $post->college = json_decode(User::where('id','=',$post->userId)->with('college')->first()->college, true)['primary'];
                $flag = false;
                $colleges = json_decode(User::where('id','=',$authUserId)->with('college')->first()->college, true);
                foreach($colleges as $college) {
                    if($post->college == $college)
                        $flag = true;
                }
                if($flag) {
                    $post->college = College::where('id','=',$post->college)->first();
                    return $post;
                }
                else{
                    return null;
                }
        });
        $posts = $posts->filter()->flatten(1);
        }

        

        

        return $this->successResponse($posts,"All Posts listed successfully");
    }

    public function createOrUpdatePost(Request $request){
        $postId = $request->route('postId');
        $tagged = $request['tagged'];
        unset($request['tagged']);
        
        $authUserId = $request->user()->id;
        
        $validator = $this->_validatePost(true);
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $post = Arr::add($request->all(), 'userId', $authUserId);
        
        if(isset($post['photoUrl'])){
            if (preg_match('/^data:image\/(\w+);base64,/', $post['photoUrl'])) {
                $data = substr($post['photoUrl'], strpos($post['photoUrl'], ',') + 1);
                $data = base64_decode($data);
                $fileName = uniqid($authUserId.'_post_').'.jpg';
                Storage::disk('avatar')->put($fileName, $data);
                $post['photoUrl'] = config('app.url').'/images/avatar/'.$fileName;;
            }
        }
        //Regex Match for all valid youtube URLS, returns the last instance of a matched key, the video's unique code.
        //Might as well be black magic. Edit 2/5/21: I'm ashamed to say I understand Regex now, and it's STILL black magic.
        if(preg_match('/(https?\:\/\/)?((www\.)?youtube\.com|youtu\.?be)\/((embed\/)|(watch\?v=))?(?<key>[a-zA-Z0-9\_\-]+)/', $post['description'], $matches)) {
            $post = Arr::add($post,'embed','youtube.com/embed/'.$matches['key']);
        }
        $isPostCreated = Post::updateOrCreate(['id' => $postId],$post);
        $message = $postId ? "Post updated successfully!" : "Post created successfully!";
        
        if($tagged){
            $data = [
                'uid' => $authUserId,
                'tagged' => $tagged
            ];
            event(new Tagged($data));
        }

        return $this->successResponse(null, $message);
    }

    public function postReaction(Request $request) {
        $validator = $this->_validatePostReaction(true);
        if($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $userId = $request->userId ? $request->userId : $request->user()->id;

        if ($request->type === "comment") {
            $reaction = new PostComments;
            $reaction->commentBy = $userId;
            $reaction->comment = $request->comment;
        } elseif ($request->type === "like") {
            $reaction = PostLikes::firstOrNew(
                [
                    'likedBy' => $userId,
                    'postId' => $request->postId
                ]
            );
            $reaction->likedBy = $userId;
        } elseif ($request->type === "report") {
            $reaction = PostReports::firstOrNew(
                [
                    'reportedBy' => $userId,
                    'postId' => $request->postId
                ]
            );
            $reaction->reportedBy = $userId;
            $reaction->reason = $request->reason;
        } elseif ($request->type === "unlike") {
            $reaction = PostLikes::where('likedBy','=',$userId)->where('postId','=',$request->postId)->delete();
        }
        
        if($request->type !== "unlike"){
            $reaction->postId = $request->postId;
            
            $isPostReactionSaved = $reaction->save();
            $data = [
                'uid' => $userId,
                'loggedUserId' => $request->user()->id,
                'postId' => $request->postId,
                'reactionType' => $request->type
            ];
                event(new PostReaction($data));
        }
        switch ($request->type){
            case "comment":
                $message = "Commented Successfully";
                break;
            case "like" :
                $message = "Liked Successfully";
                break;
            case "unlike" :
                $isPostReactionSaved = true;
                $message = "Unliked Successfully";
                break;
        }
        
        return $isPostReactionSaved ? $this->successResponse(null, $message) : $this->errorResponse("Error in post reaction");
    }

    public function getPostLike(Request $request) {
        $uid = $request->user()->id;
        $likes = new PostLikes;
        $postId = $request->route('postId');
        $types = $postId ? $likes->where("postId",$postId) : $likes;
        $postLikes = $types->with("likeBy")->get();

        if($postId) {
            foreach($postLikes as $like){
                $like->match = $this->getMatchPercent($request->user()->id, $like->likedBy);
            }
        }
        
        return $this->successResponse($postLikes,"post likes list successfully");
    }

    public function getPostComments(Request $request) {
        $postComments = PostComments::where("postId",$request->route('postId'))->with("commentUser")->get();
        return $this->successResponse($postComments,"post comment list successfully");
    }

    public function removeReportedPosts($id) {
        // dd($id);
        $postReports = PostReports::where('id',$id)->first();
        // dd($postReports);
        $postReports->delete();

        return redirect('/home');
    }

    public function _validatePostReaction() {
        $rule = [
            "postId" => "required|numeric",
            "audience" =>  "in:like,comment"
        ];
        return Validator::make(request()->all(), $rule);
    }

    public function _validatePost() {
        $rule = [
            "title" => "required|string|max:255",
            "description" => "required|",
            "postTypeId" => "required|numeric",
            "postCategoryId" => "required|numeric",
            "photoUrl" => "string|nullable",
            // "audience" =>  "in:distance,friend,everywhere"
        ];
        return Validator::make(request()->all(), $rule);
    }

    public function _validatePostTypeCategories($isCategory=false){
        $rule = [
            'name' => 'required|string|max:255',
            'shortDescription' => 'string|max:144|nullable',
            'icon' => 'string|nullable',
        ];
        if($isCategory){
            $rule = Arr::add($rule, 'postTypeId', 'required|numeric');
        }
        return Validator::make(request()->all(), $rule);
    }

    public function getPostsByCategory(Request $request) {
        $postCategoryIds = $request->postCategoryIds;
        $postTypeIds = $request->postTypeIds;
        if (is_null($postCategoryIds) && is_null($postTypeIds)) {
            $response = $this->getAllPosts($request)->getData();
            $posts = $response->data;
        } else {
            // whereIn('postCatagoryId',$postCategoryIds)->whereIn('postTypeId',$postTypeIds)
            $posts = Post::with('user')->with('type')->with('category')->withCount('likes')->withCount("comments")->orderBy('created_at', 'desc');
            if (!is_null($postCategoryIds)) {
                $posts->whereIn('postCategoryId',$postCategoryIds);
            }
            if (!is_null($postTypeIds)) {
                $posts->whereIn('postTypeId',$postTypeIds);
            }
        }        
        if (is_null($posts))
            return $this->errorResponse("Posts Not Found",422);
        else
            return $this->successResponse($posts->get(),"Posts by Category");
    }

    public function getAllReportedPosts() {
        $reported_posts = PostReports::orderBy('created_at')->get();
        return $this->successResponse(($reported_posts));
    }

    public function deleteAllPosts(Request $request) {
        $uid = $request->user()->id;
        Posts::where("userId",'=',$uid)->delete();

        return response()->json(true);
    }

    public function deletePost(Request $request) {
        $postId = $request->postId;
        Post::where('id','=',$postId)->first()->delete();

        return response()->json(true);
    }

    public function getRelevantPosts(Request $request) {
        $uid = $request->user()->id;
        $user = $request->get('user');
        $query = Post::query();
        $authUserId = $request->user()->id;


        $query->where('userId','!=',$uid)
        ->with('user')
        ->with('type')
        ->with('category')
        ->withCount('likes')
        ->withCount("comments")
        ->orderBy('created_at', 'desc');

        $posts = $query->get();
            $posts = $posts->map(function ($post) use ($authUserId) {
                $post->match = $this->getMatchPercent($authUserId, $post->userId);
                $post->college = json_decode(User::where('id','=',$post->userId)->with('college')->first()->college, true)['primary'];
                $flag = false;
                $colleges = json_decode(User::where('id','=',$authUserId)->with('college')->first()->college, true);
                foreach($colleges as $college) {
                    if($post->college == $college)
                        $flag = true;
                }
                if($flag) {
                    $post->college = College::where('id','=',$post->college)->first();
                    return $post;
                }
                else{
                    return null;
                }
        });
        $posts = $posts->filter()->flatten(1);


        $posts = $posts->sortByDesc('match')->flatten(1);

        return $this->successResponse($posts,"Relevant Posts");
    }

    public function getPostsByCollege(Request $request) {
        $user = $request->get('user');
        $uid = $user->id;
        $collegeID = $request->route('collegeID');
        $query = Post::query();

        $query->where('userId','!=',$uid)
        ->with('user')
        ->with('type')
        ->with('category')
        ->withCount('likes')
        ->withCount("comments")
        ->orderBy('created_at', 'desc');

        $posts = $query->get();
        $posts = $posts->map(function ($post) use ($user) {
            $post->match = $this->getMatchPercent($user->id, $post->userId);
            $post->college = json_decode(User::where('id','=',$post->userId)->with('college')->first()->college, true)['primary'];
            if($post->college == $collegeID) {
                return $post;
            }
            else{
                return null;
            }
        });
        $posts = $posts->filter()->flatten(1);

        return $this->successResponse($posts,"Posts Listed By College");
    }

    public function getNewUserPost(Request $request) {
        $user = $request->get('user');
        $postId = $request->postId;
        $post = Post::where('id','=',$postId)->first();
        $alumni = User::where('id','=',$post->userId)->first();
        $acollege = College::where('id','=',json_decode($alumni->college,true)['primary'])->first();
        $coords = UserCoords::where('uid','=',$user->id);
        $acoords = UserCoords::where('uid','=',$alumni->id);
        $hobbies = UserHobby::where('uid','=',$user->id)->get();
        $ahobbies = UserHobby::where('uid','=',$alumni->id)->get();

        $hobmatch = 0;
        foreach($hobbies as $hobby){
            foreach($ahobbies as $ahobby){
                if($hobby->hobby == $ahobby->hobby)
                    $hobmatch++;
            }
        }

        $adegree = UserDegree::where('uid','=',$alumni->id)->first();
        switch($adegree->type){
            case 0:
                $adegree->type = "Bachelors";
                break;
            case 1:
                $adegree->type = "Masters";
            case 2:
                $adegree->type = "Doctorate";
                break;
        }

        $adegree->degree = constants::Degrees()[$adegree->degree - 1];

        if($alumni && $post) {
            $post->description = "They're just starting to build their ".$acollege->name." network. Say hi!";
            $post->college = $acollege;

            if($coords && $acoords){
                $distance = $this->getDistance($user->id,$alumni->id);
                $distance = (string)$distance;
                $post->description .="\n They are ".$distance." miles away!";
            }

            if($hobmatch > 0){
                $post->description .="\n They also share ".$hobmatch." hobbies with you! Check out their profile!";
            }
            
            $post->description .="\n They've got a ".$adegree->type." in ".$adegree->degree." they earned back in ".$adegree->year.".";
        }

        return $this->successResponse($post,"NewJoinPost Customised.");
    }
}

