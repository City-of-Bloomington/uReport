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

- (void)reload:(NSURL *)url
{
    self.endpoint = nil;
    self.baseURL = nil;
    self.services = nil;

    // Load the discovery data
    NSLog(@"Open311:reload:%@",[url absoluteString]);
    NSURL *discoveryURL = [url URLByAppendingPathComponent:@"discovery.json"];
    NSLog(@"Loading URL: %@",discoveryURL);
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
        NSLog(@"Loading URL: %@", servicesURL);
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
        NSLog(@"Loaded %u services",[self.services count]);
    }
}

@end
