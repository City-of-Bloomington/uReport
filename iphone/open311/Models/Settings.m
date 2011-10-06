//
//  Settings.m
//  open311
//
//  Created by Cliff Ingham on 8/31/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "Settings.h"
#import "SynthesizeSingleton.h"
#import "Open311.h"

@implementation Settings
SYNTHESIZE_SINGLETON_FOR_CLASS(Settings);

@synthesize availableServers,myServers,myRequests;
@synthesize currentServer;


- (id) init
{
    self = [super init];
    if (self) {
        [self load];
    }
    return self;
}

- (void) dealloc
{
    [myRequests release];
    [myServers release];
    [availableServers release];
    [currentServer release];
    [super dealloc];
}

/**
 * Loads all the stored data
 */
- (void) load
{
    self.availableServers = [[NSDictionary alloc] initWithContentsOfFile:[[NSBundle mainBundle] pathForResource:@"AvailableServers" ofType:@"plist"]];
    
    NSString *plistPath = [[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:@"MyServers.plist"];
    if (![[NSFileManager defaultManager] fileExistsAtPath:plistPath]) {
        self.myServers = [[NSMutableArray alloc] init];
    }
    else {
        self.myServers = [NSMutableArray arrayWithContentsOfFile:plistPath];
    }
    
    plistPath = [[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:@"MyRequests.plist"];
    if (![[NSFileManager defaultManager] fileExistsAtPath:plistPath]) {
        self.myRequests = [[NSMutableArray alloc] init];
    }
    else {
        self.myRequests = [NSMutableArray arrayWithContentsOfFile:plistPath];
    }
    
    self.currentServer = [[NSUserDefaults standardUserDefaults] objectForKey:@"currentServer"];
}

/**
 * Helper function for populating data
 *
 * Will load data from the given plist if the file exists.
 * Otherwise, it just creates an empty NSMutableArray that can later
 * be saved as the desired filename, so it's ready next time
 */
- (void)loadPlistIntoArray:(NSMutableArray *)array plistFilename:(NSString *)plistFilename
{
    NSString *plistPath = [[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:plistFilename];
    
    if (![[NSFileManager defaultManager] fileExistsAtPath:plistPath]) {
        array = [[NSMutableArray alloc] init];
    }
    else {
        array = [NSMutableArray arrayWithContentsOfFile:plistPath];
    }
}

/**
 * Saves all the data we've collected
 *
 * We can ignore Available Servers, because that data should never change
 */
- (void) save
{
    [self.myServers writeToFile:[[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:@"MyServers.plist"] atomically:TRUE];
    [self.myRequests writeToFile:[[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:@"MyRequests.plist"] atomically:TRUE];
    
    [[NSUserDefaults standardUserDefaults] setObject:self.currentServer forKey:@"currentServer"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

/**
 * Resets Open311 to point to a different server
 *
 * Open311 needs to load the discovery and services for the new server
 * The list of servers is stored in AvailableServers.plist
 * The user will choose one, and we'll take it's dictionary and put it
 * into self.currentServer
 *
 * @param NSDictionary server Server should have keys for name and url
 */
- (void)switchToServer:(NSDictionary *)server
{
    Open311 *open311 = [Open311 sharedOpen311];
    DLog(@"Switching to server: %@",[server objectForKey:@"URL"]);
    [open311 reload:[NSURL URLWithString:[server objectForKey:@"URL"]]];
    self.currentServer = server;
}
@end
