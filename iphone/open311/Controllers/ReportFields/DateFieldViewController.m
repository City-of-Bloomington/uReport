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
    [[self.reportForm objectForKey:@"data"] setObject:datePicker.date forKey:self.fieldname];
    [super done];
}

#pragma mark - View lifecycle

- (void)viewDidUnload
{
    [datePicker release];
    datePicker = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


@end
