//
//  Locator.m
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "Locator.h"
#import "SynthesizeSingleton.h"

@implementation Locator
SYNTHESIZE_SINGLETON_FOR_CLASS(Locator);

@synthesize locationManager;
@synthesize locationAvailable;
@synthesize currentLocation;


- (id) init
{
    self = [super init];
    if (self) {
        self.locationAvailable = NO;
        self.currentLocation = [[CLLocation alloc] init];
        self.locationManager = [[CLLocationManager alloc] init];
        self.locationManager.delegate = self;
        self.locationManager.desiredAccuracy = kCLLocationAccuracyNearestTenMeters;
        [self start];
    }
    return self;
}

- (void)dealloc
{
    [currentLocation release];
    [locationManager release];
    [super dealloc];
}

- (void)start
{
    [self.locationManager startUpdatingLocation];
}

- (void)stop
{
    [self.locationManager startUpdatingLocation];
}

- (void)locationManager:(CLLocationManager *)manager didUpdateToLocation:(CLLocation *)newLocation fromLocation:(CLLocation *)oldLocation
{
	if ( abs([newLocation.timestamp timeIntervalSinceDate: [NSDate date]]) < 120) {             
        self.currentLocation = newLocation;
        self.locationAvailable = YES;
        
        if (newLocation.horizontalAccuracy <= locationManager.desiredAccuracy) {
            [self stop];
        }
    }
}

- (void)locationManager:(CLLocationManager *)manager didFailWithError:(NSError *)error
{
    self.currentLocation = nil;
    self.locationAvailable = NO;
}

@end
