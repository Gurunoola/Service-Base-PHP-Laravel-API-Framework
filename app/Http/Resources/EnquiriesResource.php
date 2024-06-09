<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EnquiriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'phone_number' => $this->phone_number,
            'alternate_phone_number' => $this->alternate_phone_number,
            'email' => $this->email,
            'address' => $this->address,
            'qualification' => $this->qualification,
            'course' => $this->course,
            'optional_subject' => $this->optional_subject,
            'attempts_given' => $this->attempts_given,
            'referral_source' => $this->referral_source,
            'counseling_satisfaction' => $this->counseling_satisfaction,
            'contact_preference' => $this->contact_preference,
            'status' => $this->status,
            'rescheduled_date' => $this->rescheduled_date,
            'remarks' => $this->remarks,
            'dp_path' => $this->dp_path ? Storage::url($this->dp_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
        ];
    }
}
