const buttonLogic = require('../../www/htdocs/scripts/button.generation.logic');
window.alert = () => {};  // an empty implementation for window.alert for jsdom alert

// basic with max depths and no locked worksheets

test('test max depths wks button percentage 50%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"wks1\">1 50%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths wks button percentage 100%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-success\" id=\"wks1\">1 100%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths wks button percentage 0%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"wks1\">1 0%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths question button percentage 50%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"q1\">1 50%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths question button percentage 100%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-success\" id=\"q1\">1 100%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths question button percentage 0%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"q1\">1 0%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths part button percentage 50%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"p1\">1 50%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths part button percentage 100%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-success\" id=\"p1\">1 100%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test max depths part button percentage 0%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = true;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"p1\">1 0%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


// without max depths and no locked worksheets

test('test no max depths wks button percentage 50%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"wks1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths wks button percentage 100%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"wks1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths wks button percentage 0%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"wks1\">1 <i class='fa fa-times'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths question button percentage 50%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"q1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths question button percentage 100%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"q1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths question button percentage 0%', () => {
    var buttonType = "q";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = "wks1";
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"q1\">1 <i class='fa fa-times'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths part button percentage 50%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-warning\" id=\"p1\">1 50%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths part button percentage 100%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-success\" id=\"p1\">1 100%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test no max depths part button percentage 0%', () => {
    var buttonType = "p";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = "wks1";
    var selectedQuestion = "q1";
    var isMaxDepth = false;
    var lockedWorksheets = [];

    var expectedVal = "<button type=\"button\"  class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"p1\">1 0%</button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


// locked worksheets -- relevant for worksheet buttons only

test('test locked wks button percentage 50%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,50]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [1,2];

    var expectedVal = "<button type=\"button\" disabled class=\"btn border-secondary signoffbuttons btn-warning\" id=\"wks1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test locked wks button percentage 100%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [[1,1,1,100]];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [1,2];

    var expectedVal = "<button type=\"button\" disabled class=\"btn border-secondary signoffbuttons btn-warning\" id=\"wks1\">1 <i class='fa fa-minus'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});


test('test locked wks button percentage 0%', () => {
    var buttonType = "wks";
    var buttonNumber = 1;
    var classStructureArrays = [[5,5],[2,1]];
    var signOffsList = [];
    var selectedWorksheet = null;
    var selectedQuestion = null;
    var isMaxDepth = false;
    var lockedWorksheets = [1,2];

    var expectedVal = "<button type=\"button\" disabled class=\"btn border-secondary signoffbuttons btn-outline-secondary\" id=\"wks1\">1 <i class='fa fa-times'></i></button>";
    expect(buttonLogic(buttonType, buttonNumber, classStructureArrays, signOffsList, selectedWorksheet, selectedQuestion, lockedWorksheets, isMaxDepth)).toEqual(expectedVal);
});

