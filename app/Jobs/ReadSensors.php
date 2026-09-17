<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Sensor;
use App\Models\SensorData;
use Exception;
use Log;

class ReadSensors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private function parseValue($output) {
        // Parse the output to extract the value part
        if (preg_match('/value: (.*)/', $output, $matches)) {
            $hexValues = explode(' ', $matches[1]);
            return array_map('hexdec', $hexValues); // Convert hex values to decimal
        }
        return null;
    }
    
    private function readUInt8($value) {
        // Convert the first byte into an unsigned 8-bit integer
        return $value[0];
    }
    
    private function readInt16LE($values) {
        // Combine two bytes (little-endian) into a signed 16-bit integer
        $binData = pack('C*', $values[0], $values[1]);
        $data = unpack('s', $binData); // 's' for signed 16-bit (little-endian)
        return $data[1];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $sensors = Sensor::all();

      Log::info('Starting to read BLE sensors');

      foreach ($sensors as $sensor) {
          try {
              $mac = escapeshellarg($sensor->mac);

              // Start the BLE device discovery
              $output = shell_exec("gatttool -b {$mac} --primary 2>&1");
              if ($output === null || strpos($output, 'error') !== false || strpos($output, 'connect to') !== false) {
                  throw new Exception("Failed to discover services for sensor {$sensor->name} (MAC: {$sensor->mac})");
              }

              // Extract service and characteristic handles
              $batteryHandle = "00002a19-0000-1000-8000-00805f9b34fb";
              $temperatureHandle = "00002a1f-0000-1000-8000-00805f9b34fb";
              $humidityHandle = "00002a6f-0000-1000-8000-00805f9b34fb";

              // Reading the battery value
              $batteryOutput = shell_exec("gatttool -b {$mac} --char-read --uuid={$batteryHandle} 2>&1");
              $batteryValue = $this->parseValue($batteryOutput);
              if ($batteryValue) {
                  $battery = $this->readUInt8($batteryValue);
              } else {
                  throw new Exception("Failed to read battery data for sensor {$sensor->name} (MAC: {$sensor->mac})");
              }

              // Reading the temperature value
              $temperatureOutput = shell_exec("gatttool -b {$mac} --char-read --uuid={$temperatureHandle} 2>&1");
              $temperatureValue = $this->parseValue($temperatureOutput);
              if ($temperatureValue) {
                  $temperature = $this->readInt16LE($temperatureValue) / 10;
              } else {
                  throw new Exception("Failed to read temperature data for sensor {$sensor->name} (MAC: {$sensor->mac})");
              }

              // Reading the humidity value
              $humidityOutput = shell_exec("gatttool -b {$mac} --char-read --uuid={$humidityHandle} 2>&1");
              $humidityValue = $this->parseValue($humidityOutput);
              if ($humidityValue) {
                  $humidity = $this->readInt16LE($humidityValue) / 100;
              } else {
                  throw new Exception("Failed to read humidity data for sensor {$sensor->name} (MAC: {$sensor->mac})");
              }

              // Store the sensor data in the database
              SensorData::create([
                  'sensor_id' => $sensor->id,
                  'battery' => $battery,
                  'temperature' => $temperature,
                  'humidity' => $humidity,
              ]);
              $sensor->reachable = true;
              $sensor->save();

              Log::info("Sensor data for {$sensor->name} (MAC: {$sensor->mac}) recorded successfully.");

          } catch (Exception $e) {
              $sensor->reachable = false;
              $sensor->save();
              Log::error($e->getMessage());
          }
      }
    }
}
