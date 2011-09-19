//
//  Locator.h
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <CoreLocation/CoreLocation.h>
#import <Foundation/Foundation.h>

@interface Locator : NSObject <CLLocationManagerDelegate> {
@public
    CLLocationManager *locationManager;
    CLLocation *currentLocation;
    bool locationAvailable;
}
@property (nonatomic, retain) CLLocationManager *locationManager;
@property (nonatomic, retain) CLLocation *currentLocation;
@property (nonatomic) bool locationAvailable;

+ (Locator *)sharedLocator;
- (void)start;
- (void)stop;
@end
