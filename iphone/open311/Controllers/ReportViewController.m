//
//  ReportViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "ReportViewController.h"
#import "Settings.h"
#import "Open311.h"
#import "ActionSheetPicker.h"

@implementation ReportViewController

@synthesize previousServerURL;
@synthesize currentService;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        self.tabBarItem = [[UITabBarItem alloc] initWithTitle:@"Report" image:[UIImage imageNamed:@"report.png"] tag:0];
    }
    return self;
}

- (void)dealloc
{
    [currentService release];
    [previousServerURL release];
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
    [self.navigationItem setTitle:@"New Issue"];
    self.navigationItem.leftBarButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Service" style:UIBarButtonItemStylePlain target:self action:@selector(openCategoryChooser:)];

    // If the user hasn't chosen a server yet, send them to the MyServers tab
    if (![[Settings sharedSettings] currentServer]) {
        self.tabBarController.selectedIndex = 3;
    }
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    // If the user has changed servers, we need to load the new server's discovery information
    // All the data from the server will be stored in the Open311 singleton
    if (![self.previousServerURL isEqualToString:[[[Settings sharedSettings] currentServer] objectForKey:@"URL"]]) {
        self.currentService = nil;
        self.previousServerURL = [[[Settings sharedSettings] currentServer] objectForKey:@"URL"];
        [[Open311 sharedOpen311] reload:[NSURL URLWithString:self.previousServerURL]];
    }
    if (!currentService) {
        [self chooseService];
    }
    
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

/**
 * Opens the picker for the user to choose a service from the current server
 *
 * We're using ActionSheetPicker written by Tim Cinel
 * It requires us to pass in a plain NSArray of strings to choose from
 */
- (void)chooseService
{
    NSMutableArray *data = [NSMutableArray array];
    for (NSDictionary *service in [[Open311 sharedOpen311] services]) {
        [data addObject:[service objectForKey:@"service_name"]];
    }
    [ActionSheetPicker displayActionPickerWithView:self.view data:data selectedIndex:0 target:self action:@selector(didSelectService::) title:@"Choose Service"];
}

- (void)didSelectService:(NSNumber *)selectedIndex :(id)element
{
    self.currentService = [[[Open311 sharedOpen311] services] objectAtIndex:[selectedIndex integerValue]];
    [self.navigationItem setTitle:[self.currentService objectForKey:@"service_name"]];
}



- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if (section == 1)
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
}

@end
