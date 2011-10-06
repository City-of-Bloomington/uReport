//
//  SingleReportViewController.m
//  open311
//
//  Created by Cliff Ingham on 10/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "SingleReportViewController.h"
#import "Settings.h"
#import "Open311.h"
#import "ASIHTTPRequest.h"
#import "SBJson.h"

@implementation SingleReportViewController
@synthesize serviceName;
@synthesize submissionDate;
@synthesize status;
@synthesize address;
@synthesize department;
@synthesize imageView;

- (id)initWithServiceRequestId:(NSString *)request_id
{
    self = [super init];
    if (self) {
        service_request_id = request_id;
    }
    return self;
}

- (void)dealloc {
    [service_request_id release];
    [serviceName release];
    [submissionDate release];
    [status release];
    [address release];
    [department release];
    [imageView release];
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
    NSString *path = [NSString stringWithFormat:@"requests/%@.json",service_request_id];
    NSURL *url = [[NSURL URLWithString:[[[Open311 sharedOpen311] endpoint] objectForKey:@"url"]] URLByAppendingPathComponent:path];
    ASIHTTPRequest *request = [ASIHTTPRequest requestWithURL:url];
    DLog(@"Loading %@", url);
    [request startSynchronous];
    if (![request error] && [request responseStatusCode]==200) {
        DLog(@"Loaded single report %@", [request responseString]);
        NSArray *data = [[request responseString] JSONValue];
        NSDictionary *service_request = [data objectAtIndex:0];
        if (service_request) {
            [self.navigationItem setTitle:[service_request objectForKey:@"service_name"]];
            serviceName.text = [service_request objectForKey:@"service_name"];
            submissionDate.text = [service_request objectForKey:@"requested_datetime"];
            status.text = [service_request objectForKey:@"status"];
            address.text = [service_request objectForKey:@"address"];
            department.text = [service_request objectForKey:@"agency_responsible"];
        }
        else {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Report was garbled" message:[url absoluteString] delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            [alert release];
        }
    }
    else {
        NSString *message = [url absoluteString];
        if ([request responseString]) {
            DLog(@"%@",[request responseString]);
            NSArray *errors = [[request responseString] JSONValue];
            NSString *description = [[errors objectAtIndex:0] objectForKey:@"description"];
            if (description) {
                message = description;
            }
        }
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Could not load report" message:message delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
        [alert show];
        [alert release];
    }
}

- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)viewDidUnload
{
    [self setServiceName:nil];
    [self setSubmissionDate:nil];
    [self setStatus:nil];
    [self setAddress:nil];
    [self setDepartment:nil];
    [self setImageView:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}


@end
