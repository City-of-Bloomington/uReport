//
//  MapViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "MapViewController.h"
#import "Settings.h"
#import "Locator.h"

@implementation MapViewController
@synthesize map;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.tabBarItem = [[UITabBarItem alloc] initWithTitle:@"Issues" image:[UIImage imageNamed:@"map.png"] tag:0];
    }
    return self;
}

- (void)dealloc
{
    [map release];
    [super dealloc];
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    [[Locator sharedLocator] start];

    // If the user hasn't chosen a server yet, send them to the MyServers tab
    if (![[Settings sharedSettings] currentServer]) {
        self.tabBarController.selectedIndex = 3;
    }
}

- (void)viewDidUnload
{
    [self setMap:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [self zoomToGpsLocation:animated];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}


- (void)zoomToGpsLocation:(BOOL)animated
{
    Locator *locator = [Locator sharedLocator];
    if (locator.locationAvailable) {
        MKCoordinateRegion region;
        region.center.latitude = locator.currentLocation.coordinate.latitude;
        region.center.longitude = locator.currentLocation.coordinate.longitude;
        MKCoordinateSpan span;
        span.latitudeDelta = 0.0025; // arbitrary value seems to look OK
        span.longitudeDelta = 0.0025; // arbitrary value seems to look OK
        region.span = span;
        [self.map setRegion:region animated:animated];
    }
}
@end
