//
//  SingleReportViewController.h
//  open311
//
//  Created by Cliff Ingham on 10/6/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SingleReportViewController : UIViewController <UIAlertViewDelegate> {
    NSString *service_request_id;
    
    UILabel *serviceName;
    UILabel *submissionDate;
    UILabel *status;
    UILabel *address;
    UILabel *department;
    UIImageView *imageView;
}

@property (nonatomic, retain) IBOutlet UILabel *serviceName;
@property (nonatomic, retain) IBOutlet UILabel *submissionDate;
@property (nonatomic, retain) IBOutlet UILabel *status;
@property (nonatomic, retain) IBOutlet UILabel *address;
@property (nonatomic, retain) IBOutlet UILabel *department;
@property (nonatomic, retain) IBOutlet UIImageView *imageView;

- (id)initWithServiceRequestId:(NSString *)request_id;

@end
