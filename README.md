# gp-brief

Wordpress plugin for collect briefs for object shooting and notify via telegram. Using React on front-end.
For use insert shortcode \[gp_brief\] on target page. 

When lead open target page, new brief creats on server. Data is collected continuously, even if the user has not completed the checkout and clicked the submit button.
Empty briefs are automatically removed according to the schedule. 

For build React App used yarn from Node docker container by [build.sh](front/object-shooting/build.sh)
