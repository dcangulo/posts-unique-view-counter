current_version = ARGV[0]
new_version = ARGV[1]

raise ArgumentError, "Please enter the current version using the format X.X.X" unless ARGV[0]
raise ArgumentError, "Please enter the new version using the format X.X.X" unless ARGV[1]

repo = "posts-unique-view-counter"

system "svn copy https://plugins.svn.wordpress.org/#{repo}/trunk/ https://plugins.svn.wordpress.org/#{repo}/tags/#{current_version} -m \"chore: tag v#{current_version}\""
Dir.chdir ".."
system "svn checkout https://plugins.svn.wordpress.org/#{repo}/trunk/ #{repo}"
Dir.chdir repo
system "svn revert . -R"
system "git add ."
system "git stash"
system "rm -rf .git"
system "svn add --force ."
system "svn commit -m \"chore: release v#{new_version}\""
