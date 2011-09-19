//
//  MapViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <CoreLocation/CoreLocation.h>
#import <MapKit/MapKit.h>
#import <MapKit/MKAnnotation.h>

@interface MapViewController : UIViewController {
    
    MKMapView *map;
}
@property (nonatomic, retain) IBOutlet MKMapView *map;

@end
