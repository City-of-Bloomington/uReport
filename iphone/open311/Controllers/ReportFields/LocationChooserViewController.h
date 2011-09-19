//
//  LocationChooserViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MapKit/MapKit.h>

@interface LocationChooserViewController : UIViewController {
    
    IBOutlet MKMapView *map;
}
@property (nonatomic, retain) IBOutlet MKMapView *map;

@end
