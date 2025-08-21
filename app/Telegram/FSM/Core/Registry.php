<?php

namespace App\Telegram\FSM\Core;

use App\Telegram\FSM\Contracts\ConfigurableStateInterface;
use App\Telegram\FSM\Contracts\ScenarioInterface;
use App\Telegram\FSM\Contracts\StateInterface;
use Illuminate\Support\Str;

final class Registry
{
    /** @var array<string, StateInterface> key = scenario.state */
    private array $states = [];

    /** @var array<string, array<string>> key = scenario, value = ordered state names */
    private array $scenarioStateOrder = [];

    public function registerScenario(ScenarioInterface $scenario): void
    {
        $states = [];
        $stateOrder = [];

        foreach ($scenario->registerStates() as $stateKey => $state) {
            // Поддержка обоих форматов:
            // 1. ['start' => StartState::class] (ключ - имя состояния)
            // 2. [StartState::class] (без ключа)

            if (is_string($stateKey)) {
                // Формат с ключом: 'start' => StartState::class
                $stateName = $stateKey;
            } else {
                // Формат без ключа: StartState::class
                // Извлекаем имя состояния из класса или используем порядковый номер
                $stateName = $this->extractStateName($state, $stateKey);
            }

            // Создаем экземпляр состояния
            $stateInstance = $this->createStateInstance($state, $scenario->name(), $stateName);

            $states[$stateInstance->id()->key()] = $stateInstance;
            $stateOrder[] = $stateName;
        }

        // Сохраняем состояния
        foreach ($states as $key => $state) {
            $this->states[$key] = $state;
        }

        // Сохраняем порядок состояний для сценария
        $this->scenarioStateOrder[$scenario->name()] = $stateOrder;
    }

    private function createStateInstance($state, string $scenarioName, string $stateName): StateInterface
    {
        // Если передан класс (строка), создаем экземпляр через контейнер
        if (is_string($state)) {

            $stateInstance = app()->make($state);

            if ($stateInstance instanceof ConfigurableStateInterface) {
                $stateInstance->configure($scenarioName, $stateName);
            }

        } elseif (is_object($state)) {
            $stateInstance = $state;
        } elseif (is_callable($state)) {
            $stateInstance = $state();
        } else {
            throw new \InvalidArgumentException('Invalid state type provided');
        }

        if (!$stateInstance instanceof StateInterface) {
            throw new \InvalidArgumentException('State must implement StateInterface');
        }

        return $stateInstance;
    }

    private function extractStateName($state, $defaultKey): string
    {
        if (is_string($state)) {
            $stateName = Str::snake(
                    str_replace('State', '', 
                        class_basename($state)));
            return $stateName ?? "state_{$defaultKey}";
        }

        return "state_{$defaultKey}";
    }

    public function get(StateId $id): ?StateInterface
    {
        return $this->states[$id->key()] ?? null;
    }

    public function getNextState(StateId $currentState): ?StateId
    {
        $scenarioName = $currentState->scenario;
        $currentStateName = $currentState->state;

        if (!isset($this->scenarioStateOrder[$scenarioName])) {
            return null;
        }

        $order = $this->scenarioStateOrder[$scenarioName];
        $currentIndex = array_search($currentStateName, $order);

        if ($currentIndex === false || $currentIndex >= count($order) - 1) {
            return null;
        }

        $nextStateName = $order[$currentIndex + 1];
        return new StateId($scenarioName, $nextStateName);
    }

}