<?php

namespace App\Traits;

use App\Models\UserAthlete;
use App\Models\UserDegree;
use App\Models\UserIndustry;
use App\Models\UserMatchWeight;
use App\Models\UserOrg;
use Illuminate\Support\Facades\DB;

trait MatchTrait
{
    /**
     * @param $uid1
     * @param $uid2
     * @return array
     *
     * Get total match percentage between user 1 and user 2
     */
    public function getMatchPercent($uid1, $uid2) {
        $weights = UserMatchWeight::where('uid', $uid1)->first();
        $ps = $this->getPSMatchRatio($uid1, $uid2) * $weights->ps;
        $cl = $this->getCLMatchRatio($uid1, $uid2) * $weights->cl;
        $percent = round($ps + $cl, 0);
        return $percent;
    }

    /**
     * @param $uid1
     * @param $uid2
     * @return double
     *
     * Get match percentage of Past School
     */
    private function getPSMatchRatio($uid1, $uid2) {
        $percents = [
            'degrees' => $this->getDegreeMatchRatio($uid1, $uid2),
            'orgs' => $this->getOrgsMatchRatio($uid1, $uid2),
            'athletes' => $this->getAthletesMatchRatio($uid1, $uid2)
        ];
        $percent = ($percents['degrees'] + $percents['orgs'] + $percents['athletes']) / 3;
        return $percent;
    }

    private function getDegreeMatchRatio($uid1, $uid2) {
        $degrees1 = UserDegree::where('uid', $uid1)->get();
        $degrees2 = UserDegree::where('uid', $uid2)->get();
        $count1 = count($degrees1); $count2 = count($degrees2);
        if ($count1 == 0 || $count2 == 0) {
            return 0;
        } else {
            if ($count2 > $count1) {
                $items1 = $degrees1;
                $items2 = $degrees2;
            } else {
                $items1 = $degrees2;
                $items2 = $degrees1;
            }
            $match_count = 0;
            $json_items2 = json_encode($items2);
            foreach ($items1 as $item) {
                $json_item = json_encode($item);
                if (strpos($json_items2, $json_item) !== false) {
                    $match_count++;
                }
            }
            $percent = $match_count / ($count1 + $count2) * 2;
            return $percent;
        }
    }

    private function getOrgsMatchRatio($uid1, $uid2) {
        $orgs1 = UserOrg::where('uid', $uid1)->get();
        $orgs2 = UserOrg::where('uid', $uid2)->get();
        $count1 = count($orgs1); $count2 = count($orgs2);
        if ($count1 == 0 || $count2 == 0) {
            return 0;
        } else {
            if ($count2 > $count1) {
                $items1 = $orgs1;
                $items2 = $orgs2;
            } else {
                $items1 = $orgs2;
                $items2 = $orgs1;
            }
            $match_count = 0;
            $json_items2 = json_encode($items2);
            foreach ($items1 as $item) {
                $json_item = json_encode($item);
                if (strpos($json_items2, $json_item) !== false) {
                    $match_count++;
                }
            }
            $percent = $match_count / ($count1 + $count2) * 2;
            return $percent;
        }
    }

    private function getAthletesMatchRatio($uid1, $uid2) {
        $athletes1 = UserAthlete::where('uid', $uid1)->first();
        $athletes2 = UserAthlete::where('uid', $uid2)->first();

        if (is_null($athletes1) || is_null($athletes2) || $athletes1->member !== $athletes2->member) {
            return 0;
        } else if ($athletes1->member === 1) {
            $match_count = 1;
            if ($athletes1->athlete === $athletes2->athlete && !is_null($athletes1->athlete)) {
                $match_count++;
            }
            if ($athletes1->position === $athletes2->position && !is_null($athletes1->position)) {
                $match_count++;
            }
            return $match_count / 3;
        } else {
            return 1;
        }
    }

    /**
     * @param $uid1
     * @param $uid2
     * @return double
     *
     * Get match percentage of current life
     */
    private function getCLMatchRatio($uid1, $uid2) {
        $sum = 0;
        $sum += array_sum($this->getGenderAgeMatchRatio($uid1, $uid2));
        $sum += $this->getEthnicityMatchRatio($uid1, $uid2);
        $sum += $this->getSpeakMatchRatio($uid1, $uid2);
        $sum += $this->getLearnMatchRatio($uid1, $uid2);
        $sum += $this->getReligionMatchRatio($uid1, $uid2);
        $sum += $this->getRelationshipMatchRatio($uid1, $uid2);
        $sum += $this->getWorkMatchRatio($uid1, $uid2);
        $sum += $this->getHomeBaseMatchRatio($uid1, $uid2);
        $sum += $this->getHometownMatchRatio($uid1, $uid2);
        $sum += $this->getHobbiesMatchRatio($uid1, $uid2);
        $sum += $this->getCausesMatchRatio($uid1, $uid2);
        $sum += $this->getSchoolRelatedMatchRatio($uid1, $uid2);
        return $sum / 12;
    }

    private function getGenderAgeMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_gender_ages')->where('uid', $uid1)->first();
        $item2 = DB::table('user_gender_ages')->where('uid', $uid2)->first();
        if (is_null($item1) || is_null($item2)) {
            return [0, 0];
        }
        $percents = [
            $item1->gender !== $item1->gender ? 0 : 1,
            $item1->age !== $item1->age ? 0 : 1
        ];
        return $percents;
    }

    private function getEthnicityMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_ethnicities')->where('uid', $uid1)->first();
        $item2 = DB::table('user_ethnicities')->where('uid', $uid2)->first();
        return !is_null($item1) && !is_null($item2) && $item1->ethnicity === $item2->ethnicity ? 1 : 0;
    }

    private function getSpeakMatchRatio($uid1, $uid2) {
        $items1 = DB::table('user_speak_languages')->where('uid', $uid1)->pluck('language')->toArray();
        $items2 = DB::table('user_speak_languages')->where('uid', $uid2)->pluck('language')->toArray();
        if (count($items1) > 0 && count($items2)) {
            $similar = array_intersect($items1, $items2);
            return count($similar) / (count($items1) + count($items2)) * 2;
        } else {
            return 0;
        }
    }

    private function getLearnMatchRatio($uid1, $uid2) {
        $items1 = DB::table('user_learn_languages')->where('uid', $uid1)->pluck('fluent', 'language')->toArray();
        $items2 = DB::table('user_learn_languages')->where('uid', $uid2)->pluck('fluent', 'language')->toArray();
        if (count($items1) > 0 && count($items2)) {
            $similar_fluent = array_intersect($items1, $items2);
            $similar_lang = array_intersect_key($items1, $items2);
            $language_ratio = (count($similar_fluent) + count($similar_lang)) / (count($items1) + count($items2));
            return $language_ratio;
        } else {
            return 0;
        }
    }

    private function getReligionMatchRatio($uid1, $uid2) {
        $religion1 = DB::table('user_religions')->where('uid', $uid1)->first();
        $religion2 = DB::table('user_religions')->where('uid', $uid2)->first();

        if (is_null($religion1) || is_null($religion2)) {
            return 0;
        } else {
            $match_ratio = 0;
            if ($religion1->religion === $religion2->religion) {
                $match_ratio += 0.5;
            }
            if (!is_null($religion1->year) && !is_null($religion2->year) && $religion1->year === $religion2->year) {
                $match_ratio += 0.5;
            }
            return $match_ratio;
        }
    }

    private function getRelationshipMatchRatio($uid1, $uid2) {
        $relationship1 = DB::table('user_relationships')->where('uid', $uid1)->first();
        $relationship2 = DB::table('user_relationships')->where('uid', $uid2)->first();
        if (is_null($relationship1) || is_null($relationship2) || $relationship1->relationship !== $relationship2->relationship) {
            return 0;
        } else {
            return 1;
        }
    }

    private function getWorkMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_work_careers')->where('uid', $uid1)->first();
        $item2 = DB::table('user_work_careers')->where('uid', $uid2)->first();

        if (is_null($item1) || is_null($item2)) {
            return 0;
        } else {
            $match_count = 0;
            if (!is_null($item1->work_for) && !is_null($item2->work_for) && $item1->work_for === $item2->work_for) {
                $match_count++;
            }
            if (!is_null($item1->employment_status) && !is_null($item2->employment_status) && $item1->employment_status === $item2->employment_status) {
                $match_count++;
            }
            if (!is_null($item1->work_title) && !is_null($item2->work_title) && $item1->work_title === $item2->work_title) {
                $match_count++;
            }
            if (!is_null($item1->hire_full) && !is_null($item2->hire_full) && $item1->hire_full === $item2->hire_full) {
                if (!$item1->hire_full) {
                    $match_count++;
                } else {
                    $match_count += 0.25;
                    if (!is_null($item1->hire_full_count) && !is_null($item2->hire_full_count) && $item1->hire_full_count === $item2->hire_full_count) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->hire_full_looking) && !is_null($item2->hire_full_looking) && $item1->hire_full_looking === $item2->hire_full_looking) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->hire_full_for) && !is_null($item2->hire_full_for) && $item1->hire_full_for === $item2->hire_full_for) {
                        $match_count += 0.25;
                    }
                }
            }
            if (!is_null($item1->hire_gig) && !is_null($item2->hire_gig) && $item1->hire_gig === $item2->hire_gig) {
                if (!$item1->hire_gig) {
                    $match_count++;
                } else {
                    $match_count += 0.5;
                    if (!is_null($item1->hire_gig_count) && !is_null($item2->hire_gig_count) && $item1->hire_gig_count === $item2->hire_gig_count) {
                        $match_count += 0.5;
                    }
                }
            }
            if (!is_null($item1->hire_intern) && !is_null($item2->hire_intern) && $item1->hire_intern === $item2->hire_intern) {
                if (!$item1->hire_intern) {
                    $match_count++;
                } else {
                    $match_count += 0.25;
                    if (!is_null($item1->hire_intern_count) && !is_null($item2->hire_intern_count) && $item1->hire_intern_count === $item2->hire_intern_count) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->hire_intern_looking) && !is_null($item2->hire_intern_looking) && $item1->hire_intern_looking === $item2->hire_intern_looking) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->hire_intern_for) && !is_null($item2->hire_intern_for) && $item1->hire_intern_for === $item2->hire_intern_for) {
                        $match_count += 0.25;
                    }
                }
            }
            if (!is_null($item1->own_business) && !is_null($item2->own_business) && $item1->own_business === $item2->own_business) {
                if ($item1->own_business !== 0) {
                    $match_count++;
                } else {
                    $match_count += 0.25;
                    if (!is_null($item1->seeking_investment) && !is_null($item2->seeking_investment) && $item1->seeking_investment === $item2->seeking_investment) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->buying_stuff) && !is_null($item2->buying_stuff) && $item1->buying_stuff === $item2->buying_stuff) {
                        $match_count += 0.25;
                    }
                    if (!is_null($item1->customer) && !is_null($item2->customer) && $item1->customer === $item2->customer) {
                        $match_count += 0.25;
                    }
                }
            }
            if (!is_null($item1->investor) && !is_null($item2->investor) && $item1->investor === $item2->investor) {
                if (!$item1->investor) {
                    $match_count++;
                } else {
                    $match_count += 0.333;
                    if (!is_null($item1->wealth) && !is_null($item2->wealth) && $item1->wealth === $item2->wealth) {
                        $match_count += 0.333;
                    }
                    if (!is_null($item1->review_plan) && !is_null($item2->review_plan) && $item1->review_plan === $item2->review_plan) {
                        $match_count += 0.333;
                    }
                }
            }

            $industries1 = DB::table('user_industries')->where('uid', $uid1)->pluck('industry')->toArray();
            $industries2 = DB::table('user_industries')->where('uid', $uid2)->pluck('industry')->toArray();
            if (count($industries1) > 0 && count($industries2) > 0) {
                $similar_industries = array_intersect($industries1, $industries2);
                $match_count += count($similar_industries) / (count($industries1) + count($industries2)) * 2;
            }

            $percent = $match_count / 9;
            return $percent;
        }
    }

    private function getHomeBaseMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_homes')->where('uid', $uid1)->first();
        $item2 = DB::table('user_homes')->where('uid', $uid2)->first();
        if (is_null($item1) || is_null($item2)) {
            return 0;
        } else {
            $match_count = 0;
            if (!is_null($item1->country) && !is_null($item2->country) && $item1->country === $item2->country) {
                $match_count += 1;
            }
            if (!is_null($item1->state) && !is_null($item2->state) && $item1->state === $item2->state) {
                $match_count += 1;
            }
            if (!is_null($item1->zip) && !is_null($item2->zip) && $item1->zip === $item2->zip) {
                $match_count += 1;
            }
            return $match_count / 3;
        }
    }

    private function getHometownMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_hometowns')->where('uid', $uid1)->first();
        $item2 = DB::table('user_hometowns')->where('uid', $uid2)->first();
        if (is_null($item1) || is_null($item2)) {
            return 0;
        } else {
            $match_count = 0;
            if (!is_null($item1->country) && !is_null($item2->country) && $item1->country === $item2->country) {
                $match_count += 1;
            }
            if (!is_null($item1->state) && !is_null($item2->state) && $item1->state === $item2->state) {
                $match_count += 1;
            }
            if (!is_null($item1->zip) && !is_null($item2->zip) && $item1->zip === $item2->zip) {
                $match_count += 1;
            }
            return $match_count / 3;
        }
    }

    private function getHobbiesMatchRatio($uid1, $uid2) {
        $hobbies1 = DB::table('user_hobbies')->where('uid', $uid1)->pluck('hobby')->toArray();
        $hobbies2 = DB::table('user_hobbies')->where('uid', $uid2)->pluck('hobby')->toArray();
        if (count($hobbies1) > 0 && count($hobbies2) > 0) {
            $similar = array_intersect($hobbies1, $hobbies2);
            return count($similar) / (count($hobbies1) + count($hobbies2)) * 2;
        } else {
            return 0;
        }
    }

    private function getCausesMatchRatio($uid1, $uid2) {
        $causes1 = DB::table('user_causes')->where('uid', $uid1)->pluck('cause')->toArray();
        $causes2 = DB::table('user_causes')->where('uid', $uid2)->pluck('cause')->toArray();
        if (count($causes1) > 0 && count($causes2) > 0) {
            $similar = array_intersect($causes1, $causes2);
            return count($similar) / (count($causes1) + count($causes2)) * 2;
        } else {
            return 0;
        }
    }

    private function getSchoolRelatedMatchRatio($uid1, $uid2) {
        $item1 = DB::table('user_schools')->where('uid', $uid1)->first();
        $item2 = DB::table('user_schools')->where('uid', $uid2)->first();
        if (is_null($item1) || is_null($item2)) {
            return 0;
        } else {
            $match_ratio = 0;
            if (!is_null($item1->member) && !is_null($item2->member) && $item1->member === $item2->member) {
                $match_ratio += 0.5;
            }
            if (!is_null($item1->satis_level) && !is_null($item2->satis_level) && $item1->satis_level === $item2->satis_level) {
                $match_ratio += 0.5;
            }
            return $match_ratio;
        }
    }
}