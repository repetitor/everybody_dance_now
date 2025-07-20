#!/bin/bash
# todo translate
# Function to detect OS
detect_os() {
  case "$(uname -s)" in
    Linux*)  os=linux ;;
    Darwin*) os=mac ;;
    CYGWIN*|MINGW*|MSYS*) os=windows ;;
    *)       os=unknown ;;
  esac
  echo "$os"
}

# Memory detection
get_total_memory() {
  case "$(detect_os)" in
    linux) #todo test (в др функциях тоже)
      if grep -q 'MemTotal' /proc/meminfo; then
        echo $(($(grep -Po '(?<=MemTotal:)\s+\K\d+' /proc/meminfo) / 1024))
      elif command -v free &> /dev/null; then
        echo $(($(free -m | awk '/Mem:/ {print $2}')))
      else
        echo 8192 # Fallback value (8GB)
      fi
      ;;
    mac)
      echo $(($(sysctl -n hw.memsize) / 1024 / 1024))
      ;;
    windows) #todo test (в др функциях тоже)
      # Для WSL2 и нативного Windows
      if [ -f /proc/meminfo ]; then
        echo $(($(grep -Po '(?<=MemTotal:)\s+\K\d+' /proc/meminfo) / 1024))
      else
        # Для чистого Windows (требует доработки для продакшена)
        echo 8192 # Fallback value
      fi
      ;;
    *)
      echo 8192 # Default fallback
      ;;
  esac
}

# CPU detection
get_total_cpus() {
  case "$(detect_os)" in
    linux|windows)
      nproc
      ;;
    mac)
      sysctl -n hw.ncpu
      ;;
    *)
      echo 4 # Fallback
      ;;
  esac
}

# Calculate safe limits
calculate_limits() {
  local total_mem=$(get_total_memory)
  local total_cpus=$(get_total_cpus)

  # Safe limits (adjust percentages as needed)
  local php_mem=$((total_mem * 20 / 100))  # 20% for PHP
  local mysql_mem=$((total_mem * 30 / 100)) # 30% for MySQL

  # Round to nearest standard value
  round_to_standard() {
    local value=$1
    if [ $value -lt 512 ]; then
      echo 256
    elif [ $value -lt 1024 ]; then
      echo 512
    elif [ $value -lt 2048 ]; then
      echo 1024
    elif [ $value -lt 4096 ]; then
      echo 2048
    else
      echo 4096
    fi
  }

  php_mem=$(round_to_standard $php_mem)
  mysql_mem=$(round_to_standard $mysql_mem)

  local php_cpu=1 # 1 ядро
  local mysql_cpu=$((total_cpus > 4 ? 2 : 1)) # 1-2 ядра

  # Альтернатива с дробными через умножение (если нужно 0.5):
  # local php_cpu=$((total_cpus > 2 ? 2 : 1))  # Затем делите на 2 при использовании

  echo "PHP_MEMORY_LIMIT=${php_mem}M"
  echo "MYSQL_MEMORY_LIMIT=${mysql_mem}M"
  echo "PHP_CPU_LIMIT=${php_cpu}"
  echo "MYSQL_CPU_LIMIT=${mysql_cpu}"
}

# Main execution
if [ "$1" = "--test" ]; then
  # Test mode
  echo "OS: $(detect_os)"
  echo "Total Memory: $(get_total_memory)MB"
  echo "Total CPUs: $(get_total_cpus)"
  echo "Calculated Limits:"
  calculate_limits
else #todo test
  # Production mode - update .env file
  env_file=".env"
  temp_file=".env.tmp"

  # Create backup
  cp "$env_file" "$env_file.bak" 2>/dev/null || true

  # Process .env file
  while IFS= read -r line || [ -n "$line" ]; do
    if [[ "$line" =~ ^(PHP|MYSQL)_(MEMORY_LIMIT|CPU_LIMIT)= ]]; then
      # Skip existing resource lines - we'll add them at the end
      continue
    fi
    echo "$line" >> "$temp_file"
  done < "$env_file"

  # Add new resource limits
  calculate_limits >> "$temp_file"

  # Replace original file
  mv "$temp_file" "$env_file"

  echo "Resource limits updated in $env_file"
fi