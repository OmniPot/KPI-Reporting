<?php
$expires='2015-11-27 22:29:21';
$appStructure=['Main'=>['KPIReporting\Controllers\DaysController'=>['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],],'KPIReporting\Controllers\ProjectsController'=>['getAll'=>['customRoute'=>['uri'=>'projects/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getAll',],'getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'checkIfProjectIsAllocated'=>['customRoute'=>['uri'=>'projects/int/check',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/checkIfProjectIsAllocated',],],'KPIReporting\Controllers\StatusesController'=>['getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],],'KPIReporting\Controllers\TestCasesController'=>['getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatusId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],],'KPIReporting\Controllers\UsersController'=>['login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'register'=>['customRoute'=>['uri'=>'user/register','bindingParams'=>['KPIReporting\BindingModels\RegisterBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],'confirm'=>['required'=>'1',],'email'=>['required'=>'',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/register',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],],],];
$actionsStructure=['getProjectRemainingDays'=>['customRoute'=>['uri'=>'projects/int/remainingDays',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/days/getProjectRemainingDays',],'getAll'=>['customRoute'=>['uri'=>'projects/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getAll',],'getById'=>['customRoute'=>['uri'=>'projects/int',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/getById',],'checkIfProjectIsAllocated'=>['customRoute'=>['uri'=>'projects/int/check',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/projects/checkIfProjectIsAllocated',],'getAllStatuses'=>['customRoute'=>['uri'=>'statuses/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/statuses/getAllStatuses',],'getTestCaseEvents'=>['customRoute'=>['uri'=>'testCases/int/events',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/getTestCaseEvents',],'changeStatus'=>['customRoute'=>['uri'=>'testCases/changeStatus','bindingParams'=>['KPIReporting\BindingModels\ChangeStatusBindingModel'=>['userId'=>['required'=>'1',],'testCaseId'=>['required'=>'1',],'oldStatusId'=>['required'=>'1',],'newStatusId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'1','admin'=>'','defaultRoute'=>'main/testCases/changeStatus',],'changeUser'=>['customRoute'=>['uri'=>'testCases/changeUser','bindingParams'=>['KPIReporting\BindingModels\ChangeUserBindingModel'=>['testCaseId'=>['required'=>'1',],'oldUserId'=>['required'=>'1',],'newUserId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/testCases/changeUser',],'changeDate'=>['customRoute'=>['uri'=>'testCases/changeDate','bindingParams'=>['KPIReporting\BindingModels\ChangeDayBindingModel'=>['testCaseId'=>['required'=>'1',],'oldDayId'=>['required'=>'1',],'newDayId'=>['required'=>'1',],'reasonId'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/testCases/changeDate',],'login'=>['customRoute'=>['uri'=>'user/login','bindingParams'=>['KPIReporting\BindingModels\LoginBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/login',],'register'=>['customRoute'=>['uri'=>'user/register','bindingParams'=>['KPIReporting\BindingModels\RegisterBindingModel'=>['username'=>['required'=>'1',],'password'=>['required'=>'1',],'confirm'=>['required'=>'1',],'email'=>['required'=>'',],],],],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/register',],'logout'=>['customRoute'=>['uri'=>'user/logout',],'method'=>'POST','authorize'=>'','admin'=>'','defaultRoute'=>'main/users/logout',],'getAllUsers'=>['customRoute'=>['uri'=>'users/all',],'method'=>'GET','authorize'=>'1','admin'=>'','defaultRoute'=>'main/users/getAllUsers',],];