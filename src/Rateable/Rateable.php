<?php namespace willvincent\Rateable;

trait Rateable
{
    /**
     * This model has many ratings.
     *
     * @return Rating
     */
    public function ratings()
    {
        return $this->morphMany('willvincent\Rateable\Rating', 'rateable');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function sumRating()
    {
        return $this->ratings()->sum('rating');
    }

    public function userAverageRating()
    {
        return $this->ratings()->where('user_id', \Auth::id())->avg('rating');
    }

    public function userSumRating()
    {
        return $this->ratings()->where('user_id', \Auth::id())->sum('rating');
    }

    public function ratingPercent($max = 5)
    {
        $quantity = $this->ratings()->count();
        $total = $this->sumRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    public function getSumRatingAttribute()
    {
        return $this->sumRating();
    }

    public function getUserAverageRatingAttribute()
    {
        return $this->userAverageRating();
    }

    public function getUserSumRatingAttribute()
    {
        return $this->userSumRating();
    }

    /**
     * Add a rate for this record by the given user.
     * @param $userId mixed - If null will use currently logged in user.
     * @param $rating int - user rate
     */
    public function rate($userId=null, $rating=1)
    {
        if(is_null($userId)) {
            $userId = $this->loggedInUserId();
        }
        
        if(is_null($rating)){
            $rating = 1;
        }
        
        if($userId) {
            $rate = $this->ratings()
                ->where('user_id', '=', $userId)
                ->first();
    
            //update rating
            if($rate){
                $rate->rating = $rating;
                $rate->save();
            }
            else{
                $rate = new Rating();
                $rate->user_id = $userId;
                $rate->rating = $rating;
                $this->ratings()->save($rate);
            }
        }
        else{
            return;
        }
    }
}
