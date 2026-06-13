<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'maiden_name' => $this->maiden_name,
            
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'place_of_birth' => $this->place_of_birth,
            'age' => $this->age,
            
            'status' => $this->status,
            'date_of_death' => $this->date_of_death?->format('Y-m-d'),
            'place_of_death' => $this->place_of_death,
            
            'generation_number' => $this->generation_number,
            
            // Relationships
            'clan' => $this->whenLoaded('clan', function () {
                return [
                    'id' => $this->clan->id,
                    'name' => $this->clan->name,
                ];
            }),
            
            'family' => $this->whenLoaded('family', function () {
                return [
                    'id' => $this->family->id,
                    'name' => $this->family->name,
                    'surname' => $this->family->surname,
                ];
            }),
            
            'branch' => $this->whenLoaded('branch', function () {
                return $this->branch ? [
                    'id' => $this->branch->id,
                    'name' => $this->branch->name,
                ] : null;
            }),
            
            'father' => $this->whenLoaded('father', function () {
                return $this->father ? [
                    'id' => $this->father->id,
                    'full_name' => $this->father->full_name,
                    'status' => $this->father->status,
                ] : null;
            }),
            
            'mother' => $this->whenLoaded('mother', function () {
                return $this->mother ? [
                    'id' => $this->mother->id,
                    'full_name' => $this->mother->full_name,
                    'status' => $this->mother->status,
                ] : null;
            }),
            
            'spouse' => $this->whenLoaded('spouse', function () {
                return $this->spouse ? [
                    'id' => $this->spouse->id,
                    'full_name' => $this->spouse->full_name,
                    'status' => $this->spouse->status,
                ] : null;
            }),
            
            'children_count' => $this->when(isset($this->children_count), $this->children_count),
            
            // Contact Information
            'contact' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
            ],
            
            // Media
            'profile_photo_url' => $this->profile_photo_url,
            
            // Additional Info
            'biography' => $this->biography,
            'occupation' => $this->occupation,
            'notes' => $this->notes,
            
            // Timestamps
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
