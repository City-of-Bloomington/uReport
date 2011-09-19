//
//  TextFieldViewController.h
//  open311
//
//  Created by Cliff Ingham on 9/15/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface TextFieldViewController : UIViewController {
    
    IBOutlet UILabel *label;
    IBOutlet UITextView *textarea;
}
@property (nonatomic, retain) NSString *fieldname;
@property (nonatomic, retain) NSMutableDictionary *reportForm;

- (id)initWithFieldname:(NSString *)field report:(NSMutableDictionary *)report;
@end
