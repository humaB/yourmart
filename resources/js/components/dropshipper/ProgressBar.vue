<template>
    <div class="progress-container">
      <div
        v-for="(item, index) in levels"
        :key="index"
        class="level-step"
      >
        <div data-placement="top"
          :title="getLevelTooltip(item)"
          class="icon-wrapper"
          :class="{
            completed: index < currentLevel,
            active: index == currentLevel,
            locked: index > currentLevel,
          }"

          :style="{ backgroundColor: getLevelColor(item) }"
        >
          <i
            class="text-white"
            :class="{
              'fas fa-check': index < currentLevel,
              'fas fa-crosshairs': index === currentLevel,
              'fas fa-lock': index > currentLevel
            }"
          ></i>
        </div>
        <div class="level-label">  {{ formatLevel(item) }}</div>
        <div
          v-if="index < levels.length - 1"
          class="line"
          :class="{
            completed: index < currentLevel
          }"
        ></div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: ['currentLevel'],
    data() {
      return {
        levels: [
            'New Seller',
            'Level 01',
            'Level 02',
            'Level 03',
            'Top Rated Seller'
        ],
      };
    },
    methods: {
        formatLevel(item) {
           // Check for Level with leading zero
            const match = item.match(/^Level 0(\d)$/);
            if (match) {
            return `Level ${match[1]} Seller`; // e.g., Level 1 Seller
            }
            return item;
        },
        getLevelColor(level) {
            switch (level) {
            case 'New Seller':
                return '#6c757d'; // Grey
            case 'Level 01':
                return '#007bff'; // Blue
            case 'Level 02':
                return '#28a745'; // Green
            case 'Level 03':
                return '#ffc107'; // Gold
            case 'Top Rated Seller':
                return '#e97bb8'; // Purple
            default:
                return '#dee2e6'; // Default light grey
            }
        },
        getLevelTooltip(level) {
            switch (level) {
                case 'Level 01':
                    return `CRITERIA\n✓ Orders 100\n✓ Success Rate 80%\n✓ Revenue > 20k \n\nREWARDS\n✓ Social Media Coverage\n✓ Certificate\n`;
                case 'Level 02':
                    return `CRITERIA\n✓ Orders 400\n✓ Success Rate 80%\n✓ Revenue > 60k \n\nREWARDS\n✓ Social Media Coverage\n✓ Certificate\n✓ Gift`;
                case 'Level 03':
                    return `CRITERIA\n✓ Orders 1,000\n✓ Success Rate 85%\n✓ Revenue > 160k \n\nREWARDS\n✓ Social Media Coverage\n✓ Certificate\n✓ Gift\n✓ 1-To-1 Support`;
                case 'Top Rated Seller':
                    return `CRITERIA\n✓ Orders 2,500\n✓ Success Rate 90%\n✓ Revenue > 500k \n\nREWARDS\n✓ Social Media Coverage\n✓ Certificate\n✓ Gift\n✓ 1-To-1 Support\n✓ Shield of Honor\n✓ Membership of Advisory Team`;
                default:
                    return `CRITERIA\n✓ Orders < 100\n✓ Success Rate < 80%\n✓ Revenue < 20k \n\nREWARDS\n✓ Weekly Payouts`;
            }
        }
        }
  };
  </script>

  <style scoped>
  .progress-container {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 5px 0;
  }

  .level-step {
    display: flex;
    align-items: center;
    position: relative;
  }

  .icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: background-color 0.3s ease;
  }

  .icon-wrapper.completed {
    background-color: #00bcd4; /* Cyan for completed */
    color: #fff;
  }

  .icon-wrapper.active {
    background-color: #2196f3; /* Blue for active */
    color: #fff;
  }

  .icon-wrapper.locked {
    background-color: #e0e0e0; /* Grey for locked */
    color: #888;
  }

  .level-label {
    text-align: center;
    font-size: 11px;
    color: #333;
    position: absolute;
    top: 50px;
    left: 10%;
    transform: translateX(-50%);
    width: 100px;
    font-weight: 600;
  }

  .line {
    height: 2px;
    width: 150px;
    background-color: #ccc;
    margin: 0 5px;
    z-index: 1;
  }

  .line.completed {
    background-color: #00bcd4;
  }
  </style>
