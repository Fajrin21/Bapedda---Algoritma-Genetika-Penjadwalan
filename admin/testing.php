<?php
class ScheduleGA {
    private $population;
    private $populationSize = 50;
    private $generations = 100;
    private $fitnessScores = [];
    private $mutationRate = 0.1;
    private $crossoverRate = 0.9;
    private $eliteSize = 5;
    private $randomSeed;

    public function __construct($data, $seed = null) {
        $this->populationSize = max(10, count($data));
        $this->randomSeed = $seed;
        if ($this->randomSeed !== null) {
            mt_srand($this->randomSeed);  // Seed the random number generator
        }
        $this->initializePopulation($data);
    }

    private function initializePopulation($data) {
        $this->population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $shuffledData = $data;
            foreach ($shuffledData as &$meeting) {
                $meeting['waktu'] = $this->generateRandomTime($meeting['tanggalinput'], $meeting['deadline']);
            }
            $this->population[] = $shuffledData;
        }
    }

    private function generateRandomTime($start, $end) {
        $startDate = strtotime($start . ' 08:00:00');
        $endDate = strtotime($end . ' 17:00:00');
        $randomTimestamp = mt_rand($startDate, $endDate);
        return $this->roundToNearestHalfHour($randomTimestamp);
    }

    public function run() {
        for ($gen = 0; $gen < $this->generations; $gen++) {
            $this->calculateFitness();
            $this->selection();
            $this->crossover();
            $this->mutation();
        }
        return $this->bestSchedule();
    }

    private function selection() {
        array_multisort($this->fitnessScores, SORT_DESC, $this->population);
        $this->population = array_slice($this->population, 0, $this->eliteSize);
    }

    private function calculateFitness() {
        $this->fitnessScores = array_map(function($chromosome) {
            $score = 0;
            foreach ($chromosome as $meeting) {
                $time = strtotime($meeting['waktu']);
                $start = strtotime($meeting['tanggalinput']);
                $end = strtotime($meeting['deadline']);
                if ($time >= $start && $time <= $end) {
                    $score += 50;
                } else {
                    $score -= 100;
                }
            }
            return $score;
        }, $this->population);
    }

    private function crossover() {
        $newPopulation = [];
        while (count($newPopulation) < $this->populationSize) {
            $parent1 = $this->population[array_rand($this->population)];
            $parent2 = $this->population[array_rand($this->population)];
            $child1 = $parent1;
            $child2 = $parent2;
            $cutPoint = mt_rand(1, count($parent1) - 1);
            for ($i = $cutPoint; $i < count($parent1); $i++) {
                $temp = $child1[$i]['waktu'];
                $child1[$i]['waktu'] = $child2[$i]['waktu'];
                $child2[$i]['waktu'] = $temp;
            }
            $newPopulation[] = $child1;
            $newPopulation[] = $child2;
        }
        $this->population = $newPopulation;
    }

    private function mutation() {
        foreach ($this->population as &$individual) {
            if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                $mutateIndex = mt_rand(0, count($individual) - 1);
                $individual[$mutateIndex]['waktu'] = $this->generateRandomTime($individual[$mutateIndex]['tanggalinput'], $individual[$mutateIndex]['deadline']);
            }
        }
    }

    private function roundToNearestHalfHour($timestamp) {
        $minute = date('i', $timestamp);
        if ($minute < 30) {
            $minute = '00';
        } else {
            $minute = '30';
        }
        $time = date('Y-m-d H:' . $minute . ':00', $timestamp);
        $hour = date('H', strtotime($time));
        if ($hour < 8) {
            return date('Y-m-d 08:' . $minute . ':00', $timestamp);
        } elseif ($hour >= 17) {
            return date('Y-m-d 17:' . $minute . ':00', $timestamp - 3600); // Adjust to last valid half-hour
        }
        return $time;
    }

    private function bestSchedule() {
        $bestIndex = array_keys($this->fitnessScores, max($this->fitnessScores));
        return $this->population[$bestIndex[0]];
    }
}

// Randomly generate meeting data for testing
function generateRandomMeetingData($numMeetings = 10) {
    $data = [];
    for ($i = 0; $i < $numMeetings; $i++) {
        $startDate = date('Y-m-d', strtotime("+".mt_rand(0, 30)." days"));
        $endDate = date('Y-m-d', strtotime($startDate . "+".mt_rand(1, 10)." days"));
        $data[] = [
            'nama' => 'Meeting ' . ($i + 1),
            'kabkota' => 'City ' . mt_rand(1, 100),
            'tempat' => 'Place ' . mt_rand(1, 100),
            'tanggalinput' => $startDate,
            'deadline' => $endDate,
            'waktu' => ''
        ];
    }
    return $data;
}

$meetingData = generateRandomMeetingData(20); // Generate 20 random meetings
$ga = new ScheduleGA($meetingData);
$schedule = $ga->run();

echo "<pre>";
print_r($schedule);
echo "</pre>";

?>