//
//  open311AppDelegate.m
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "open311AppDelegate.h"
#import "HomeViewController.h"
#import "ReportViewController.h"
#import "MapViewController.h"
#import "MyReportsViewController.h"
#import "MyServersViewController.h"
#import "Settings.h"
#import "Locator.h"

@implementation open311AppDelegate


@synthesize window=_window;
@synthesize tabBarController=_tabBarController;

- (void)dealloc
{
    [_tabBarController release];
    [_window release];
    [super dealloc];
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    HomeViewController* home = [[HomeViewController alloc] init];
    ReportViewController* report = [[ReportViewController alloc] init];
    MyReportsViewController* myReports = [[MyReportsViewController alloc] init];
    MyServersViewController* servers = [[MyServersViewController alloc] init];
    //MapViewController* map = [[MapViewController alloc] init];
    
    UINavigationController* reportNav = [[UINavigationController alloc] initWithRootViewController:report];
    UINavigationController* serverNav = [[UINavigationController alloc] initWithRootViewController:servers];
    UINavigationController* issueNav = [[UINavigationController alloc] initWithRootViewController:myReports];
    issueNav.navigationBar.barStyle = UIBarStyleBlack;
    reportNav.navigationBar.barStyle = UIBarStyleBlack;
    serverNav.navigationBar.barStyle = UIBarStyleBlack;

    self.tabBarController = [[UITabBarController alloc] init];
    self.tabBarController.viewControllers = [NSArray arrayWithObjects:home, reportNav, issueNav, serverNav, nil];

    if (![[Settings sharedSettings] currentServer]) {
        self.tabBarController.selectedViewController = serverNav;
    }
    
    [self.window addSubview:self.tabBarController.view];
    [self.window makeKeyAndVisible];
    
    [home release];
    [report release];
    [myReports release];
    //[map release];
    [servers release];
    [reportNav release];
    //[issueNav release];
    [serverNav release];
    [self.tabBarController release];
    
    // Start up the location services.
    // Do it here, so we should have a position by the time we need it.
    [[Locator sharedLocator] start];
    
    return YES;
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    /*
     Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
     Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
     */
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    /*
     Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
     If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
     */
    [[Settings sharedSettings] save];
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    /*
     Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
     */
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    /*
     Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
     */
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    /*
     Called when the application is about to terminate.
     Save data if appropriate.
     See also applicationDidEnterBackground:.
     */
    [[Settings sharedSettings] save];
}

@end
