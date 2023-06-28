<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class zohoAPIController extends Controller
{
    public function generateAccessToken()
    {
        $response = Http::post('https://accounts.zoho.com/oauth/v2/token?refresh_token=1000.fe0d9bb4313e0eece06d167a9a013a5e.0f4412fac6ffb157ef93e773395e3e44&client_id=1000.LILWNN31MJOJW4B68SN9TWUYLQO5IF&client_secret=79edd42890ed9017422c9ec5a45de439fa7905852f&grant_type=refresh_token');
        $res = $response->body();
        $accessToken = json_decode($res,true) ;
        return $accessToken['access_token'];

    }
    public function verifyTokenForZoho($zohoAccessCode,$clientSecretKey)
    {
        $response = Http::withHeaders([
                              'Authorization' => 'Zoho-oauthtoken ' . $zohoAccessCode
                          ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/All_Bluewire_Partners?Secret_Key='.$clientSecretKey);

        if($response['code'] == 3000){
            return true ;
        }
        else{
            return false ;
        }
    }

    public function getAllCompanies(Request $request)
    {
        // check the token is received or not
        if(isset($request->clientToken) == false){
            $returnJson = array("code"=>3001,"response"=>"Client token is not available.");
            return json_encode($returnJson) ;
        }

        // generate new Access Token
        $accessTokenRes = $this->generateAccessToken() ;

//         verify client token in zoho
        $verifyTokenRes = $this->verifyTokenForZoho($accessTokenRes,$request->clientToken);

        if($verifyTokenRes == false){
             $returnJson = array("code"=>3001,"response"=>"Unauthorized access. Client token is not corrent.");
             return json_encode($returnJson) ;
        }

        //get all the companies of a partner
        $getAllCompanyOfPartner = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/All_Bluewire_Partners?Secret_Key='.$request->clientToken);


        $companyArray = array();

        foreach ($getAllCompanyOfPartner['data'][0]['Companies'] as $company) {
          $companyArray[] = array("display_value"=>$company['display_value'],'ID'=>$company['ID']) ;
        }

            $dataArray = array("Partner_Name"=>$getAllCompanyOfPartner['data'][0]['Partner_Name'],"Companies"=>$companyArray);
            $returnJson = array("code"=>3000,"data"=>$dataArray);
            return json_encode($returnJson) ;
    }

    public function getAllCompanyDOT(Request $request)
    {
        // check the token is received or not
        if(isset($request->clientToken) == false){
            $returnJson = array("code"=>3001,"response"=>"Client token is not available.");
            return json_encode($returnJson) ;
        }

    // check the company ID is received or not
        if(isset($request->companyID) == false){
            $returnJson = array("code"=>3001,"response"=>"Company ID is not available.");
            return json_encode($returnJson) ;
        }

        // generate new Access Token
        $accessTokenRes = $this->generateAccessToken() ;

//         verify client token in zoho
        $verifyTokenRes = $this->verifyTokenForZoho($accessTokenRes,$request->clientToken);

        if($verifyTokenRes == false){
             $returnJson = array("code"=>3001,"response"=>"Unauthorized access. Client token is not corrent.");
             return json_encode($returnJson) ;
        }

        //get company by id to see the company is available or not
        $getCompanyByID = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Admin_All_Companies/'.$request->companyID);

        if($getCompanyByID['code'] != 3000){
            $returnJson = array("code"=>3001,"response"=>"Company ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the companies of a partner for company id validation
        $getAllCompanyOfPartner = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/All_Bluewire_Partners?Secret_Key='.$request->clientToken);

        $isCompanyIDaPartOfPartner = false ;
        foreach ($getAllCompanyOfPartner['data'][0]['Companies'] as $company) {
            if($company['ID'] == $request->companyID){
                $isCompanyIDaPartOfPartner = true ;
            }
        }

        if($isCompanyIDaPartOfPartner == false){
            $returnJson = array("code"=>3001,"response"=>"Company ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the DOT of a Company
        $getAllDOTOfPartner = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Admin_DOTs_API?Company.ID='.$request->companyID);

        $dotArray = array();

        foreach ($getAllDOTOfPartner['data'] as $dot) {
          $dotArray[] = array("display_value"=>$dot['Name'],'DOT_Number'=>$dot['DOT_Number'],'ID'=>$dot['ID']) ;
        }

        $dataArray = array("DOTs"=>$dotArray);
        $returnJson = array("code"=>3000,"data"=>$dataArray);
        return json_encode($returnJson) ;
    }

    public function getCompanyScore(Request $request)
    {
        // check the token is received or not
        if(isset($request->clientToken) == false){
            $returnJson = array("code"=>3001,"response"=>"Client token is not available.");
            return json_encode($returnJson) ;
        }

    // check the company ID is received or not
        if(isset($request->companyID) == false){
            $returnJson = array("code"=>3001,"response"=>"Company ID is not available.");
            return json_encode($returnJson) ;
        }

        // generate new Access Token
        $accessTokenRes = $this->generateAccessToken() ;

//         verify client token in zoho
        $verifyTokenRes = $this->verifyTokenForZoho($accessTokenRes,$request->clientToken);

        if($verifyTokenRes == false){
             $returnJson = array("code"=>3001,"response"=>"Unauthorized access. Client token is not corrent.");
             return json_encode($returnJson) ;
        }

        //get company by id to see the company is available or not
        $getCompanyByID = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Admin_All_Companies/'.$request->companyID);

        if($getCompanyByID['code'] != 3000){
            $returnJson = array("code"=>3001,"response"=>"Company ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the companies of a partner for company id validation
        $getAllCompanyOfPartner = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/All_Bluewire_Partners?Secret_Key='.$request->clientToken);

        $isCompanyIDaPartOfPartner = false ;
        foreach ($getAllCompanyOfPartner['data'][0]['Companies'] as $company) {
            if($company['ID'] == $request->companyID){
                $isCompanyIDaPartOfPartner = true ;
            }
        }

        if($isCompanyIDaPartOfPartner == false){
            $returnJson = array("code"=>3001,"response"=>"Company ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the attack vector score of a Company
        $companyAttackVectorScore = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Attack_vector_multi_DOT_Score_API?Company.ID='.$request->companyID);

        // get company total score
        $companyTotalScore = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Company_Total_Reptile_Index_Score_API?Company.ID='.$request->companyID);

        $attackVectorScoreArray = array();

        foreach ($companyAttackVectorScore['data'] as $attackVector) {
          $attackVectorScoreArray[] = array("Attack_Vector_Name"=>$attackVector['Attack_Vector']['display_value'],'Reptile_Theory_Index_0_100'=>$attackVector['Reptile_Theory_Index_0_100']) ;
        }


        $dataArray = array("Company"=>$companyTotalScore['data'][0]['Company']['display_value'],"Company_ID"=>$companyTotalScore['data'][0]['Company']['ID'],"Reptile_Theory_Index_0_100"=>$companyTotalScore['data'][0]['Reptile_Theory_Index_0_100'],"Attack_Vector"=>$attackVectorScoreArray);
        $returnJson = array("code"=>3000,"data"=>$dataArray);
        return json_encode($returnJson) ;
    }

    public function getDOTScore(Request $request)
    {
        // check the token is received or not
        if(isset($request->clientToken) == false){
            $returnJson = array("code"=>3001,"response"=>"Client token is not available.");
            return json_encode($returnJson) ;
        }

    // check the company ID is received or not
        if(isset($request->dotID) == false){
            $returnJson = array("code"=>3001,"response"=>"DOT ID is not available.");
            return json_encode($returnJson) ;
        }

        // generate new Access Token
        $accessTokenRes = $this->generateAccessToken() ;

//         verify client token in zoho
        $verifyTokenRes = $this->verifyTokenForZoho($accessTokenRes,$request->clientToken) ;

        if($verifyTokenRes == false){
             $returnJson = array("code"=>3001,"response"=>"Unauthorized access. Client token is not corrent.") ;
             return json_encode($returnJson) ;
        }

        //get DOT by id to see the DOT is available or not
        $getDOTByID = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/Admin_DOTs/'.$request->dotID);

        if($getDOTByID['code'] != 3000){
            $returnJson = array("code"=>3001,"response"=>"DOT ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the companies of a partner for company id validation
        $getAllCompanyOfPartner = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/All_Bluewire_Partners?Secret_Key='.$request->clientToken);

        $isCompanyIDaPartOfPartner = false ;
        foreach ($getAllCompanyOfPartner['data'][0]['Companies'] as $company) {
            if($company['ID'] == $getDOTByID['data']['Company']['ID']){
                $isCompanyIDaPartOfPartner = true ;
            }
        }

        if($isCompanyIDaPartOfPartner == false){
            $returnJson = array("code"=>3001,"response"=>"DOT ID is incorrect.");
            return json_encode($returnJson) ;
        }

        //get all the attack vector score of a DOT
        $dotAttackVectorScore = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/DOT_Attack_Vector_Result_API?DOT.ID='.$request->dotID);

        // get DOT total score
        $dotTotalScore = Http::withHeaders([
                                      'Authorization' => 'Zoho-oauthtoken ' . $accessTokenRes
                                  ])->get('https://creatorapp.zoho.com/api/v2/bluewire/customer-portal/report/DOT_Total_Reptile_Index_Score_API?DOT.ID='.$request->dotID);

        $attackVectorScoreArray = array();

        foreach ($dotAttackVectorScore['data'] as $attackVector) {
          $attackVectorScoreArray[] = array("Attack_Vector_Name"=>$attackVector['Attack_Vector']['display_value'],'Reptile_Theory_Index_0_100'=>$attackVector['Reptile_Theory_Index_0_100']) ;
        }


        $dataArray = array("DOT_Name"=>$dotTotalScore['data'][0]['DOT']['display_value'],"Company_ID"=>$dotTotalScore['data'][0]['DOT']['ID'],"Reptile_Theory_Index_0_100"=>$dotTotalScore['data'][0]['Reptile_Theory_Index_0_100'],"Attack_Vector"=>$attackVectorScoreArray);
        $returnJson = array("code"=>3000,"data"=>$dataArray);
        return json_encode($returnJson) ;
    }
}
