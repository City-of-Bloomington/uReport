//
//  Settings.m
//  open311
//
//  Created by Cliff Ingham on 8/31/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "Settings.h"
#import "SynthesizeSingleton.h"


@implementation Settings
SYNTHESIZE_SINGLETON_FOR_CLASS(Settings);

@synthesize availableServers,myServers;
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
    [myServers release];
    [availableServers release];
    [currentServer release];
    [super dealloc];
}

/**
 * Loads data from three sources
 *
 * We're using three sources:
 * Available servers come from a plist in the main bundle
 * My Servers are saved out to a plist in the user domain
 * All the rest of the global variables come in from the Settings bundle
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
    
    self.currentServer = [[NSUserDefaults standardUserDefaults] objectForKey:@"currentServer"];
}

/**
 * Saves data back for two of our sources
 *
 * We only need to save two of our sources (My Servers and Settings)
 * This is because data in Available Servers should never change
 */
- (void) save
{
    [myServers writeToFile:[[NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0] stringByAppendingPathComponent:@"MyServers.plist"] atomically:TRUE];
    
    [[NSUserDefaults standardUserDefaults] setObject:self.currentServer forKey:@"currentServer"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}
   
@end
