<?php 
	namespace App\Http\Controllers\API;

	use Illuminate\HTTP\Request;
	use App\HTTP\Controllers\Controller;
	use App\User;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Validator;
	use Hash;

	class UserController extends Controller
	{
	   public function register(Request $request){
	   	    try{
		   	    if($request->isMethod('post')){
					$validator  = Validator::make($request->all(),[
						'full_name'=>'required',
						'phone'=>'required',
						'password'=>'required',
						'c_password'=>'required|same:password',
					]);
					if($validator->fails()){
						return response()->json(['status'=>0,'message'=>$validator->errors()]);
					}
					$input  = $request->all();
					$checkalreadyPhoneNumber  =  User::where(['phone'=>$input['phone']])->count();
					if($checkalreadyPhoneNumber < 1){
						$input['password']    = Hash::make($input['password']);
						$otp  =  User::randomNumber(4);
						$message  =  "Verfication code for registration:"."".$otp;
					    User::SendOtp($message,$request->phone);
					    $input['otp']  = $otp;
					    $user  =  User::create($input);
						$success['token']   =  $user->createToken('MyApp')->accessToken;
						return response()->json(['status'=>1,'data'=>$success,'message'=>'Verfication code sent to your phone number']);
					} else{
						return response()->json(['status'=>0,'message'=>'This phone number already used']);					
					}
				} else{
					return response()->json(['status'=>0,'message'=>'Your request method was wrong']);
				}
			}catch (\Exception $e) {
				return response()->json(['status'=>0,'message'=>$e->getMessage()]);
            }
		}
	   
	    public function login(Request $request)
	    {
	    	try{
		        $credentials = [
		            'phone' => $request->phone,
		            'password' => $request->password
		        ];

		        $validator  = Validator::make($request->all(),[
					'phone'=>'required',
					'password'=>'required',
					'otp'=>'required',
				]);
				if($validator->fails()){
					return response()->json(['status'=>0,'message'=>$validator->errors()]);
				}
		 
		        if (auth()->attempt($credentials)) {
		        	$user  = Auth::user();
		        	if($user->otp == $request->otp){
		            	$token = auth()->user()->createToken('MyApp')->accessToken;
		            	return response()->json(['status'=>1,'data'=>$token,'Login Successfully']);
		            } else{
		            	return response()->json(['status'=>0,'message'=>'Your OTP wrong']);
		            }
		        } else {
		            return response()->json(['status'=>0,'message'=>'UnAuthorised']);
		        } 
		    }catch (\Exception $e) {
				return response()->json(['status'=>0,'message'=>$e->getMessage()]);
            }
	    }

	    public function getUserDetails(){
	    	try{
				$user  = Auth::user(); 
				return response()->json(['status'=>1,'data'=>$user]);
			}  catch (\Exception $e) {
				return response()->json(['status'=>0,'message'=>$e->getMessage()]);
            }
		}
	}
?>