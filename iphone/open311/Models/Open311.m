//
//  Open311.m
//  open311
//
//  Created by Cliff Ingham on 9/7/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "Open311.h"
#import "ASIHTTPRequest.h"
#import "SBJson.h"
#import "SynthesizeSingleton.h"
#import "Settings.h"
#import "ActionSheetPicker.h"

@implementation Open311
SYNTHESIZE_SINGLETON_FOR_CLASS(Open311);

@synthesize endpoint=_endpoint;
@synthesize baseURL=_baseURL;
@synthesize services=_services;

- (id)init
{
    self = [super init];
    if (self) {
        if ([[Settings sharedSettings] currentServer]) {
            [self reload:[NSURL URLWithString:[[[Settings sharedSettings] currentServer] objectForKey:@"URL"]]];
        }
    }
    return self;
}

- (void) dealloc
{
    [_baseURL release];
    [_endpoint release];
    [_services release];
    [super dealloc];
}

/**
 * Clears out all the current data and reloads Open311 data from the provided URL
 */
- (void)reload:(NSURL *)url
{
    self.endpoint = nil;
    self.baseURL = nil;
    self.services = nil;

    // Load the discovery data
    DLog(@"Open311:reload:%@",[url absoluteString]);
    NSURL *discoveryURL = [url URLByAppendingPathComponent:@"discovery.json"];
    DLog(@"Loading URL: %@",discoveryURL);
    ASIHTTPRequest *request = [ASIHTTPRequest requestWithURL:discoveryURL];
    [request startSynchronous];
    if (![request error] && [request responseStatusCode]==200) {
        NSDictionary *discovery = [[request responseString] JSONValue];
        for (NSDictionary *ep in [discovery objectForKey:@"endpoints"]) {
            if ([[ep objectForKey:@"specification"] isEqualToString:@"http://wiki.open311.org/GeoReport_v2"]) {
                self.endpoint = ep; 
                self.baseURL = [NSURL URLWithString:[ep objectForKey:@"url"]];
            }
        }
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Could not load discovery" message:[discoveryURL absoluteString] delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
        [alert show];
        [alert release];
    }
    
    // Load all the service definitions
    if (self.baseURL) {
        NSURL *servicesURL = [self.baseURL URLByAppendingPathComponent:@"services.json"];
        DLog(@"Loading URL: %@", servicesURL);
        request = [ASIHTTPRequest requestWithURL:servicesURL];
        [request startSynchronous];
        if (![request error]) {
            self.services = [[request responseString] JSONValue];
        }
        else {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Could not load services" message:[servicesURL absoluteString] delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            [alert release];
        }
        DLog(@"Loaded %u services",[self.services count]);
    }
}
/**
 * Opens the picker for the user to choose a service from the current server
 *
 * We're using ActionSheetPicker written by Tim Cinel
 * It requires us to pass in a plain NSArray of strings to choose from
 */
- (void)chooseServiceForView:(UIView *)view target:(id)target action:(SEL)action
{
    if (self.services) {
        NSMutableArray *data = [NSMutableArray array];
        for (NSDictionary *service in self.services) {
            [data addObject:[service objectForKey:@"service_name"]];
        }
        [ActionSheetPicker displayActionPickerWithView:view data:data selectedIndex:0 target:target action:action title:@"Choose Service"];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"No services" message:@"No services.  Please choose a different server" delegate:self cancelButtonTitle:@"Ok" otherButtonTitles:nil];
        [alert show];
        [alert release];
    }
}


@end
