//
//  DateFieldViewController.m
//  open311
//
//  Created by Cliff Ingham on 10/3/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "DateFieldViewController.h"

@implementation DateFieldViewController

- (void)dealloc {
    [datePicker release];
    [super dealloc];
}

- (void)done
{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateStyle:kCFDateFormatterMediumStyle];
    [[self.reportForm objectForKey:@"data"] setObject:[dateFormatter stringFromDate:datePicker.date] forKey:self.fieldname];
    [dateFormatter release];
    [super done];
}

#pragma mark - View lifecycle

- (void)viewWillAppear:(BOOL)animated
{
    NSString *date = [[self.reportForm objectForKey:@"data"] objectForKey:self.fieldname];
    if (date) {
        NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
        [dateFormatter setDateStyle:kCFDateFormatterMediumStyle];
        datePicker.date = [dateFormatter dateFromString:date];
        [dateFormatter release];
    }
    [super viewWillAppear:animated];
}

- (void)viewDidUnload
{
    [datePicker release];
    datePicker = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


@end
