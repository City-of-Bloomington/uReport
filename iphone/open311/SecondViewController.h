//
//  SecondViewController.h
//  open311
//
//  Created by Cliff Ingham on 8/24/11.
//  Copyright 2011 City of Bloomington. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface SecondViewController : UIViewController <UIWebViewDelegate> {
    
    UIWebView *webview;
}
@property (nonatomic, retain) IBOutlet UIWebView *webview;

@end
