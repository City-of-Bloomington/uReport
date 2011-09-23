//
//  Settings.h
//  open311
//
//  Created by Cliff Ingham on 8/31/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface Settings : NSObject {
@public
    NSDictionary *availableServers;
    NSMutableArray *myServers;
}
@property (nonatomic, retain) NSDictionary *availableServers;
@property (nonatomic, retain) NSMutableArray *myServers;

@property (nonatomic, retain) NSDictionary *currentServer;

+ (Settings *) sharedSettings;

- (void)load;
- (void)save;
- (void)switchToServer:(NSDictionary *)server;

@end
