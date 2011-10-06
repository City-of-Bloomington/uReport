//
//  Open311.h
//  open311
//
//  Created by Cliff Ingham on 9/7/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface Open311 : NSObject {
@public
    NSDictionary *endpoint;
    NSURL *baseURL;
    NSArray *services;
}
@property (nonatomic, retain) NSDictionary *endpoint;
@property (nonatomic, retain) NSURL *baseURL;
@property (nonatomic, retain) NSArray *services;

+ (Open311 *) sharedOpen311;

- (void)reload:(NSURL *)url;
- (void)chooseServiceForView:(UIView *)view target:(id)target action:(SEL)action;

@end
