//
//  BaseFieldViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface BaseFieldViewController : UIViewController {

    IBOutlet UILabel *label;
}
@property (nonatomic, retain) NSString *fieldname;
@property (nonatomic, retain) NSString *previousText;
@property (nonatomic, retain) NSMutableDictionary *reportForm;

- (id)initWithFieldname:(NSString *)field report:(NSMutableDictionary *)report;
- (void)cancel;
- (void)done;

@end
