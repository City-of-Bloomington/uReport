//
//  ReportViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface ReportViewController : UIViewController <UINavigationControllerDelegate,
                                                    UITableViewDelegate,
                                                    UITableViewDataSource,
                                                    UIImagePickerControllerDelegate> {
    
    IBOutlet UITableView *reportTableView;
}
@property (nonatomic, retain) NSString *previousServerURL;
@property (nonatomic, retain) NSDictionary *currentService;
@property (nonatomic, retain) NSMutableDictionary *reportForm;

- (void)chooseService;
- (void)initReportForm;
- (void)didSelectService:(NSNumber *)selectedIndex:(id)element;

@end
