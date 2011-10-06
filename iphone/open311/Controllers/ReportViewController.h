//
//  ReportViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MapKit/MapKit.h>

@interface ReportViewController : UIViewController <UINavigationControllerDelegate,
                                                    UITableViewDelegate,
                                                    UITableViewDataSource,
                                                    UIImagePickerControllerDelegate,
                                                    MKReverseGeocoderDelegate> {
    
    IBOutlet UITableView *reportTableView;
}
@property (nonatomic, retain) NSString *previousServerURL;
@property (nonatomic, retain) NSDictionary *currentService;
@property (nonatomic, retain) NSDictionary *service_definition;
@property (nonatomic, retain) NSMutableDictionary *reportForm;

- (void)chooseService;
- (void)initReportForm;
- (void)didSelectService:(NSNumber *)selectedIndex:(id)element;
- (void)loadServiceDefinition:(NSString *)service_code;

@end
