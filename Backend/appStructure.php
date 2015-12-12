<?php
$expires='2015-12-12 22:15:56';
$appStructure=['Main'=>['KPIReporting\Controllers\DaysController'=>['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],'getProjectAllocatedDaysPage'=>['customRoute'=>['uri'=>'projects/int/allocatedDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectAllocatedDaysPage',],'getExtensionReasons'=>['customRoute'=>['uri'=>'extensionReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getExtensionReasons',],'extendProjectDuration'=>['customRoute'=>['uri'=>'projects/int/extendDuration','bindingParams'=>['KPIReporting\BindingModels\ExtendDurationBindingModel'=>['startDate'=>['required'=>'1',],'startDuration'=>['required'=>'1',],'endDuration'=>['required'=>'1',],'expectedTestCases'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'extensionReasons'=>['required'=>'1',],'planRenew'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/extendProjectDuration',],],'KPIReporting\Controllers\ProjectsController'=>['getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'getActiveConfig'=>['customRoute'=>['uri'=>'projects/int/config',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getActiveConfig',],],'KPIReporting\Controllers\SetupController'=>['getProjectSetupPage'=>['customRoute'=>['uri'=>'projects/int/setup',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/getProjectSetupPage',],'saveProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/save','bindingParams'=>['KPIReporting\BindingModels\SetupBindingModel'=>['duration'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'activeUsers'=>['required'=>'1',],'expectedTCPD'=>['required'=>'1',],'actualTCPD'=>['required'=>'1',],'planRenew'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/saveProjectSetup',],],'KPIReporting\Controllers\StatusesController'=>['getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],],'KPIReporting\Controllers\TestCasesController'=>['getProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/testCases',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getProjectTestCases',],'getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'projects/int/testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatusId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'projects/int/testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'projects/int/testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],],'KPIReporting\Controllers\UsersController'=>['login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],],],];
$actionsStructure=['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],'getProjectAllocatedDaysPage'=>['customRoute'=>['uri'=>'projects/int/allocatedDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectAllocatedDaysPage',],'getExtensionReasons'=>['customRoute'=>['uri'=>'extensionReasons',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getExtensionReasons',],'extendProjectDuration'=>['customRoute'=>['uri'=>'projects/int/extendDuration','bindingParams'=>['KPIReporting\BindingModels\ExtendDurationBindingModel'=>['startDate'=>['required'=>'1',],'startDuration'=>['required'=>'1',],'endDuration'=>['required'=>'1',],'expectedTestCases'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'extensionReasons'=>['required'=>'1',],'planRenew'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/extendProjectDuration',],'getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'getActiveConfig'=>['customRoute'=>['uri'=>'projects/int/config',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getActiveConfig',],'getProjectSetupPage'=>['customRoute'=>['uri'=>'projects/int/setup',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/getProjectSetupPage',],'saveProjectSetup'=>['customRoute'=>['uri'=>'projects/int/setup/save','bindingParams'=>['KPIReporting\BindingModels\SetupBindingModel'=>['duration'=>['required'=>'1',],'algorithm'=>['required'=>'1',],'activeUsers'=>['required'=>'1',],'expectedTCPD'=>['required'=>'1',],'actualTCPD'=>['required'=>'1',],'planRenew'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/setup/saveProjectSetup',],'getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],'getProjectTestCases'=>['customRoute'=>['uri'=>'projects/int/testCases',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getProjectTestCases',],'getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'projects/int/testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatusId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'projects/int/testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'projects/int/testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'externalStatus'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],'login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],];