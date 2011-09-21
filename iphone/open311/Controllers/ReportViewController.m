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
#import "TextFieldViewController.h"
#import "LocationChooserViewController.h"

@implementation ReportViewController

@synthesize previousServerURL;
@synthesize currentService;
@synthesize reportForm;

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
    [reportForm release];
    [currentService release];
    [previousServerURL release];
    [reportTableView release];
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
    [reportForm release];
    [reportTableView release];
    reportTableView = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    Settings *settings = [Settings sharedSettings];
    Open311 *open311 = [Open311 sharedOpen311];
    
    // If the user has changed servers, we need to load the new server's discovery information
    // All the data from the server will be stored in the Open311 singleton
    if (![self.previousServerURL isEqualToString:[[settings currentServer] objectForKey:@"URL"]]) {
        self.currentService = nil;
        self.previousServerURL = [[settings currentServer] objectForKey:@"URL"];
    }
    
    if (open311.services) {
        if (!self.currentService) {
            [self chooseService];
        }
        [reportTableView reloadData];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"No services" message:@"No services to report to.  Please choose a different server" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
        [alert show];
        [alert release];
    }
    
    [super viewWillAppear:animated];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

#pragma mark - Report functions
/**
 * Wipes and reloads the reportForm
 *
 * Clears out both the form data and any custom fields created for the service
 * Reads the service definition and creates custom fields for attributes
 */
- (void)initReportForm
{
    NSError *error = nil;
    NSPropertyListFormat format;
    NSData *data = [[NSFileManager defaultManager] contentsAtPath:[[NSBundle mainBundle] pathForResource:@"Report" ofType:@"plist"]];
    self.reportForm = (NSMutableDictionary *)[NSPropertyListSerialization propertyListWithData:data options:NSPropertyListMutableContainersAndLeaves format:&format error:&error];
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
    [self initReportForm];
}

/**
 * Sets the user-chosen service and loads it's service definition
 */
- (void)didSelectService:(NSNumber *)selectedIndex :(id)element
{
    self.currentService = [[[Open311 sharedOpen311] services] objectAtIndex:[selectedIndex integerValue]];
    [self.navigationItem setTitle:[self.currentService objectForKey:@"service_name"]];
    
    [self initReportForm];
     
    [reportTableView reloadData];
}

#pragma mark - Table View Handlers
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [[self.reportForm objectForKey:@"fields"] count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cell"];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:@"cell"] autorelease];
    }
    
    NSString *fieldname = [[self.reportForm objectForKey:@"fields"] objectAtIndex:indexPath.row];

    cell.textLabel.text = [[self.reportForm objectForKey:@"labels"] objectForKey:fieldname];
    cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
    
    // Populate the user-provided data
    if ([fieldname isEqualToString:@"media"]) {
        
    }
    else if ([fieldname isEqualToString:@"address_string"]) {
        
    }
    else {
        cell.detailTextLabel.text = [[self.reportForm objectForKey:@"data"] objectForKey:fieldname];
    }
    
    
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    
    // Find out what data type the row is and display the appropriate view
    NSString *fieldname = [[self.reportForm objectForKey:@"fields"] objectAtIndex:indexPath.row];
    NSString *type = [[self.reportForm objectForKey:@"types"] objectForKey:fieldname];
    
    if ([type isEqualToString:@"media"]) {
        if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera] == YES) {
            UIImagePickerController *picker = [[UIImagePickerController alloc] init];
            picker.delegate = self;
            picker.allowsEditing = NO;
            picker.sourceType = UIImagePickerControllerSourceTypeCamera;
            [self presentModalViewController:picker animated:YES];
            [picker release];
        }
    }
    if ([type isEqualToString:@"location"]) {
        LocationChooserViewController *chooseLocation = [[LocationChooserViewController alloc] init];
        [self.navigationController pushViewController:chooseLocation animated:YES];
        [chooseLocation release];
    }
    if ([type isEqualToString:@"text"]) {
        TextFieldViewController *editTextController = [[TextFieldViewController alloc] initWithFieldname:fieldname report:self.reportForm];
        [self.navigationController pushViewController:editTextController animated:YES];
        [editTextController release];
    }
}

#pragma mark - Image Choosing Functions

- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker
{
    [[picker parentViewController] dismissModalViewControllerAnimated:YES];
    [picker release];
}

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
{
    UIImage *image = (UIImage *)[info objectForKey:UIImagePickerControllerOriginalImage];
    [[self.reportForm objectForKey:@"data"] setObject:image forKey:@"media"];
    
    [[picker parentViewController] dismissModalViewControllerAnimated:YES];
    [picker release];
}
@end
