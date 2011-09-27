//
//  NumberFieldViewController.m
//  open311
//
//  Created by Cliff Ingham on 9/26/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import "NumberFieldViewController.h"

@implementation NumberFieldViewController

- (void)dealloc
{
    [input release];
    [input release];
    [super dealloc];
}

#pragma mark - Button handling functions
- (void)done
{
    NSError *error = NULL;
    NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:@"[^0-9]" options:0 error:&error];
    NSString *cleanedString = [regex stringByReplacingMatchesInString:input.text options:0 range:NSMakeRange(0, input.text.length) withTemplate:@""];

    [[self.reportForm objectForKey:@"data"] setObject:cleanedString forKey:self.fieldname];
    [super done];
}

#pragma mark - View lifecycle

- (void)viewDidUnload
{
    [input release];
    input = nil;
    [input release];
    input = nil;
    [super viewDidUnload];
}

- (void)viewWillAppear:(BOOL)animated
{
    input.text = [[self.reportForm objectForKey:@"data"] objectForKey:self.fieldname];
    [super viewWillAppear:animated];
}

@end
