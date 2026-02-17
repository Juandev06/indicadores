/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./resources/js/dashboard.js ***!
  \***********************************/
Chart.register(ChartDataLabels); // INDICADOR 01

var ctx01 = document.getElementById('ind01');
var ind01 = new Chart(ctx01, {
  type: tipoItem1,
  data: dataInd01,
  options: {
    interaction: {
      intersect: false,
      mode: 'index'
    },
    responsive: true,
    plugins: {
      datalabels: dataLabelConf1
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
}); // INDICADOR 02

var ctx02 = document.getElementById('ind02');
var ind02 = new Chart(ctx02, {
  type: tipoItem2,
  data: dataInd02,
  options: {
    interaction: {
      intersect: false,
      mode: 'index'
    },
    responsive: true,
    plugins: {
      datalabels: dataLabelConf2
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
}); // INDICADOR 03

var ctx03 = document.getElementById('ind03');
var ind03 = new Chart(ctx03, {
  type: tipoItem3,
  data: dataInd03,
  options: {
    responsive: true,
    plugins: {
      datalabels: dataLabelConf3
    }
  }
}); // INDICADOR 04

var ctx04 = document.getElementById('ind04');
var ind04 = new Chart(ctx04, {
  type: tipoItem4,
  data: dataInd04,
  options: {
    interaction: {
      intersect: false,
      mode: 'index'
    },
    responsive: true,
    plugins: {
      datalabels: dataLabelConf4
    }
  }
});
document.addEventListener('DOMContentLoaded', function () {
  window.livewire.on('update-chart', function (id) {
    select = document.getElementById('tipoItem' + id);
    tipoChart = select.options[select.selectedIndex].value;
    dataLabelConfAct = tipoChart == "pie" || tipoChart == "doughnut" ? dataLabelConf.pie : dataLabelConf["default"];

    if (id == 1) {
      ind01.destroy();
      ind01 = new Chart(ctx01, {
        type: tipoChart,
        data: dataInd01,
        options: {
          plugins: {
            datalabels: dataLabelConfAct
          }
        }
      });
    }

    if (id == 2) {
      ind02.destroy();
      ind02 = new Chart(ctx02, {
        type: tipoChart,
        data: dataInd02,
        options: {
          plugins: {
            datalabels: dataLabelConfAct
          }
        }
      });
    }

    if (id == 3) {
      ind03.destroy();
      ind03 = new Chart(ctx03, {
        type: tipoChart,
        data: dataInd03,
        options: {
          plugins: {
            datalabels: dataLabelConfAct
          }
        }
      });
    }

    if (id == 4) {
      ind04.destroy();
      ind04 = new Chart(ctx04, {
        type: tipoChart,
        data: dataInd04,
        options: {
          plugins: {
            datalabels: dataLabelConfAct
          }
        }
      });
    }
  });
});
/******/ })()
;