//
//  LocationChooserViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MapKit/MapKit.h>
#import "BaseMapViewController.h"

@interface LocationChooserViewController : BaseMapViewController {
    
}

@property (nonatomic, retain) NSMutableDictionary *reportForm;

- (id)initWithReport:(NSMutableDictionary *)report;
- (IBAction)handleZoomButton:(id)sender;
- (void)didChooseLocation;

@end
