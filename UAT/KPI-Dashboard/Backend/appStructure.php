<?php
$expires='2016-01-25 18:51:24';
$appStructure=['Main'=>['KPIReporting\Controllers\DaysController'=>['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],'getProjectAllocatedDaysPage'=>['customRoute'=>['uri'=>'projects/int/allocatedDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectAllocatedDaysPage',],'getExtensionReasons'=>['customRoute'=>['uri'=>'extensionReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getExtensionReasons',],'getResetReasons'=>['customRoute'=>['uri'=>'resetReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getResetReasons',],'getParkReasons'=>['customRoute'=>['uri'=>'parkReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getParkReasons',],'getAvailableDays'=>['customRoute'=>['uri'=>'projects/int/availableDates',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getAvailableDays',],'changeDayDate'=>['customRoute'=>['uri'=>'days/int/changeDate','bindingParams'=>['KPIReporting\BindingModels\DatDateChangeBindingModel'=>['newDate'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/changeDayDate',],'extendProjectDuration'=>['customRoute'=>['uri'=>'projects/int/extendDuration','bindingParams'=>['KPIReporting\BindingModels\ExtendDurationBindingModel'=>['duration'=>['required'=>'1',],'extensionReasons'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/extendProjectDuration',],'overrideProjectConfiguration'=>['customRoute'=>['uri'=>'projects/int/overrideConfiguration',],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/overrideProjectConfiguration',],'stopProjectExecution'=>['customRoute'=>['uri'=>'projects/int/stopExecution','bindingParams'=>['KPIReporting\BindingModels\StopExecutionBindingModel'=>['reason'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/stopProjectExecution',],'resumeProjectExecution'=>['customRoute'=>['uri'=>'projects/int/resumeExecution',],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/resumeProjectExecution',],'deleteProjectDay'=>['customRoute'=>['uri'=>'projects/int/days/int/delete',],'method'=>'DELETE','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/deleteProjectDay',],],'KPIReporting\Controllers\ProjectsController'=>['getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'getActiveConfig'=>['customRoute'=>['uri'=>'projects/int/config',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getActiveConfig',],'syncProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/sync',],'method'=>'GET','authorize'=>'','admin'=>'','defaultRoute'=>'main/projects/syncProjectTestCases',],],'KPIReporting\Controllers\SetupController'=>['getProjectSetupPage'=>['customRoute'=>['uri'=>'projects/int/setup',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/getProjectSetupPage',],'saveProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/save','bindingParams'=>['KPIReporting\BindingModels\SetupBindingModel'=>['duration'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'activeUsers'=>['required'=>'1',],'expectedTCPD'=>['required'=>'1',],'actualTCPD'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/saveProjectSetup',],'clearProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/clear','bindingParams'=>['KPIReporting\BindingModels\ResetBindingModel'=>['reason'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/clearProjectSetup',],],'KPIReporting\Controllers\StatusesController'=>['getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],],'KPIReporting\Controllers\TestCasesController'=>['getProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/testCases',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getProjectTestCases',],'getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'projects/int/testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'projects/int/testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'projects/int/testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],],'KPIReporting\Controllers\UsersController'=>['login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],'getUsersLoad'=>['customRoute'=>['uri'=>'users/load',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUsersLoad',],'getUserLoadByDays'=>['customRoute'=>['uri'=>'users/int/load',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUserLoadByDays',],'getUserById'=>['customRoute'=>['uri'=>'user/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUserById',],'expandUserDay'=>['customRoute'=>['uri'=>'user/int/date/mixed',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/expandUserDay',],],],];
$actionsStructure=['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],'getProjectAllocatedDaysPage'=>['customRoute'=>['uri'=>'projects/int/allocatedDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectAllocatedDaysPage',],'getExtensionReasons'=>['customRoute'=>['uri'=>'extensionReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getExtensionReasons',],'getResetReasons'=>['customRoute'=>['uri'=>'resetReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getResetReasons',],'getParkReasons'=>['customRoute'=>['uri'=>'parkReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getParkReasons',],'getAvailableDays'=>['customRoute'=>['uri'=>'projects/int/availableDates',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getAvailableDays',],'changeDayDate'=>['customRoute'=>['uri'=>'days/int/changeDate','bindingParams'=>['KPIReporting\BindingModels\DatDateChangeBindingModel'=>['newDate'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/changeDayDate',],'extendProjectDuration'=>['customRoute'=>['uri'=>'projects/int/extendDuration','bindingParams'=>['KPIReporting\BindingModels\ExtendDurationBindingModel'=>['duration'=>['required'=>'1',],'extensionReasons'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/extendProjectDuration',],'overrideProjectConfiguration'=>['customRoute'=>['uri'=>'projects/int/overrideConfiguration',],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/overrideProjectConfiguration',],'stopProjectExecution'=>['customRoute'=>['uri'=>'projects/int/stopExecution','bindingParams'=>['KPIReporting\BindingModels\StopExecutionBindingModel'=>['reason'=>['required'=>'1',],],],],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/stopProjectExecution',],'resumeProjectExecution'=>['customRoute'=>['uri'=>'projects/int/resumeExecution',],'method'=>'PUT','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/resumeProjectExecution',],'deleteProjectDay'=>['customRoute'=>['uri'=>'projects/int/days/int/delete',],'method'=>'DELETE','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/deleteProjectDay',],'getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'getActiveConfig'=>['customRoute'=>['uri'=>'projects/int/config',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getActiveConfig',],'syncProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/sync',],'method'=>'GET','authorize'=>'','admin'=>'','defaultRoute'=>'main/projects/syncProjectTestCases',],'getProjectSetupPage'=>['customRoute'=>['uri'=>'projects/int/setup',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/getProjectSetupPage',],'saveProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/save','bindingParams'=>['KPIReporting\BindingModels\SetupBindingModel'=>['duration'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'activeUsers'=>['required'=>'1',],'expectedTCPD'=>['required'=>'1',],'actualTCPD'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/saveProjectSetup',],'clearProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/clear','bindingParams'=>['KPIReporting\BindingModels\ResetBindingModel'=>['reason'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/clearProjectSetup',],'getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],'getProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/testCases',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getProjectTestCases',],'getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'projects/int/testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'projects/int/testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'projects/int/testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],'login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],'getUsersLoad'=>['customRoute'=>['uri'=>'users/load',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUsersLoad',],'getUserLoadByDays'=>['customRoute'=>['uri'=>'users/int/load',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUserLoadByDays',],'getUserById'=>['customRoute'=>['uri'=>'user/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getUserById',],'expandUserDay'=>['customRoute'=>['uri'=>'user/int/date/mixed',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/expandUserDay',],];