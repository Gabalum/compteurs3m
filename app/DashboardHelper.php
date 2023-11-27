<?php
namespace App;

use Carbon\Carbon;

class DashboardHelper
{
    public static function alert($message = '', $color = '', $link = '', $linkLabel = '')
    {
        $mainClass = 'bg-'.$color.'-100 text-'.$color.'-700';
        return '
            <div class="mb-5 p-2 md:p-3 md:px-20 w-full rounded '.$mainClass.'">
                <div class="flex justify-between">
                    <div class="flex space-x-3 grow">
                        <i class="fas fa-check-circle pt-1 hidden sm:inline-flex"></i>
                        <div class="flex-1 leading-tight text-base sm:text-lg">
                            '.$message.'
                        </div>
                        <div class="shrink-0 self-start ml-2 text-sm">
                            <a href="'.$link.'" class="underline">'.$linkLabel.'</a>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    public static function displayRanking($ranking = [], $rankingTitle = '', $lastValue = 0, $alterColor = false, $isMonth = false)
    {
        $retour = '
            <div class="bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">'.$rankingTitle.'</h4>
                </header>
                <ul class="my-1">';
        $i = 1;
        if($alterColor){
            $color = 'bg-amber-100';
        }else{
            $color = 'bg-green-100';
        }
        if(count($ranking) > 0){
            foreach($ranking as $date => $value){
                if(is_array($value)){ // par semaine ou mois
                    if($isMonth){
                        $date = Helper::frenchMonth($value['num'], false).' '.$value['year'];
                    }else{
                        $tmpDate = Carbon::now();
                        $tmpDate->setISODate($value['year'], $value['num']);
                        $date = $tmpDate->startOfWeek()->format('d').' au '.$tmpDate->endOfWeek()->format('d').' '.Helper::frenchMonth($tmpDate->endOfWeek()->format('m'), false).' '.$tmpDate->endOfWeek()->format('Y');
                    }
                    $rValue = $value['sum'];
                    $value = Helper::nf($value['sum']);
                }else{
                    $rValue = $value;
                    $value = Helper::nf($value);
                }
                $medal = 'bg-white-600 text-black text-sm';
                if($i === 1){
                    $medal = 'bg-yellow-500 text-gray-900 text-base';
                }elseif($i === 2){
                    $medal = 'bg-gray-400 text-neutral-900 text-base';
                }elseif($i === 3){
                    $medal = 'bg-yellow-900 text-white text-base';
                }
                $retour .= '
                    <li class="flex px-2 hover:bg-gray-200 '.($lastValue == $rValue ? $color : '').'">
                        <div class="w-6 h-6 rounded-full shrink-0 my-2 mr-3 text-center '.$medal.'">
                            '.$i.'
                        </div>
                        <div class="grow flex items-center border-b border-gray-100 text-sm py-1">
                            <div class="grow flex justify-between">
                                <div class="self-center '.($i < 4 ? 'text-lg' : '').' '.($lastValue == $rValue ? 'font-bold' : 'font-normal').'">
                                    '.$value.'
                                </div>
                                <div class="shrink-0 self-start ml-2 text-gray-400 text-sm">
                                    '.$date.'
                                </div>
                            </div>
                        </div>
                    </li>';
                $i++;
            }
        }else{
            $retour .= '<li class="pl-5 text-gray-400 text-sm"><em>Aucune donnée pour le moment</em></li>';
        }
        $retour .= '
                </ul>
            </div>
        ';
        return $retour;
    }
}
