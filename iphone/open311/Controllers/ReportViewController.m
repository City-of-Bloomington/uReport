//
//  ReportViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//
// This is the screen where the user is creating a report to send to
// an open311 server.  We need to know what service the user wants to
// report to on that server.  We'll get the list of attributes for that
// service and let the user enter data for each one of the attributes.

#import <AddressBook/AddressBook.h>
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
    self.navigationItem.leftBarButtonItem = [[UIBarButtonItem alloc] initWithTitle:@"Service" style:UIBarButtonItemStylePlain target:self action:@selector(chooseService)];

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
    // If the user has changed servers, the previous service they were using is no longer valid
    NSString *currentServerURL = [[[Settings sharedSettings] currentServer] objectForKey:@"URL"];
    if (![self.previousServerURL isEqualToString:currentServerURL]) {
        self.currentService = nil;
        self.previousServerURL = currentServerURL;
        [self chooseService];
    }
    
    [reportTableView reloadData];
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
 * The template for the report is Report.plist in the bundle.
 * We clear out the report by reloading it from the template.
 * Then, we add in any custom attributes defined in the service.
 */
- (void)initReportForm
{
    NSError *error = nil;
    NSPropertyListFormat format;
    NSData *data = [[NSFileManager defaultManager] contentsAtPath:[[NSBundle mainBundle] pathForResource:@"Report" ofType:@"plist"]];
    self.reportForm = (NSMutableDictionary *)[NSPropertyListSerialization propertyListWithData:data options:NSPropertyListMutableContainersAndLeaves format:&format error:&error];
    [reportTableView reloadData];
}

/**
 * Opens the picker for the user to choose a service from the current server
 *
 * We're using ActionSheetPicker written by Tim Cinel
 * It requires us to pass in a plain NSArray of strings to choose from
 */
- (void)chooseService
{
    self.currentService = nil;
    
    Open311 *open311 = [Open311 sharedOpen311];
    if (open311.services) {
        NSMutableArray *data = [NSMutableArray array];
        for (NSDictionary *service in [[Open311 sharedOpen311] services]) {
            [data addObject:[service objectForKey:@"service_name"]];
        }
        [ActionSheetPicker displayActionPickerWithView:self.view data:data selectedIndex:0 target:self action:@selector(didSelectService::) title:@"Choose Service"];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"No services" message:@"No services to report to.  Please choose a different server" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
        [alert show];
        [alert release];
    }
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
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:@"cell"];
    }
    
    NSString *fieldname = [[self.reportForm objectForKey:@"fields"] objectAtIndex:indexPath.row];

    cell.textLabel.text = [[self.reportForm objectForKey:@"labels"] objectForKey:fieldname];
    cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
    
    // Populate the user-provided data
    NSMutableDictionary *data = [self.reportForm objectForKey:@"data"];
    if ([fieldname isEqualToString:@"media"]) {
        [cell.imageView setFrame:CGRectMake(0, 0, 64, 40)];
        [cell.imageView setContentMode:UIViewContentModeScaleAspectFit];
        cell.imageView.image = [data objectForKey:@"media"];
    }
    else if ([fieldname isEqualToString:@"address_string"]) {
        NSString *address = [data objectForKey:@"address_string"];
        NSString *latitude = [data objectForKey:@"lat"];
        NSString *longitude = [data objectForKey:@"long"];
        if ([address length]!=0) {
            cell.detailTextLabel.text = address;
        }
        else if ([latitude length]!=0 && [longitude length]!=0) {
            cell.detailTextLabel.text = [NSString stringWithFormat:@"%@, %@",latitude,longitude];
            CLLocation *location = [[CLLocation alloc] initWithLatitude:[latitude doubleValue] longitude:[longitude doubleValue]];
            MKReverseGeocoder *geocoder = [[MKReverseGeocoder alloc] initWithCoordinate:location.coordinate];
            geocoder.delegate = self;
            [geocoder start];
            [location release];
        }
    }
    else {
        cell.detailTextLabel.text = [data objectForKey:fieldname];
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
        }
    }
    if ([type isEqualToString:@"location"]) {
        LocationChooserViewController *chooseLocation = [[LocationChooserViewController alloc] initWithReport:self.reportForm];
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
    UIImage *image = [info objectForKey:UIImagePickerControllerOriginalImage];
    [[self.reportForm objectForKey:@"data"] setObject:image forKey:@"media"];

    [[picker parentViewController] dismissModalViewControllerAnimated:YES];
    [picker release];
}

#pragma mark - Reverse Geocoder Delegate Functions

- (void)reverseGeocoder:(MKReverseGeocoder *)geocoder didFindPlacemark:(MKPlacemark *)placemark
{
    NSString *address = [[placemark.addressDictionary objectForKey:@"FormattedAddressLines"] objectAtIndex:0];
    [[reportForm objectForKey:@"data"] setObject:address forKey:@"address_string"];
    [reportTableView reloadData];
}

- (void)reverseGeocoder:(MKReverseGeocoder *)geocoder didFailWithError:(NSError *)error
{
    // Just ignore any errors.  We don't really need an address string.
    // We're only displaying it to be all fancy.
    // The only thing that matters on submission will be the lat/long
}

@end
