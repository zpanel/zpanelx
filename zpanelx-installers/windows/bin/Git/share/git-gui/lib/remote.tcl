# git-gui remote management
# Copyright (C) 2006, 2007 Shawn Pearce

set some_heads_tracking 0;  # assume not

proc is_tracking_branch {name} {
	global tracking_branches
	foreach spec $tracking_branches {
		set t [lindex $spec 0]
		if {$t eq $name || [string match $t $name]} {
			return 1
		}
	}
	return 0
}

proc all_tracking_branches {} {
	global tracking_branches

	set all [list]
	set pat [list]
	set cmd [list]

	foreach spec $tracking_branches {
		set dst [lindex $spec 0]
		if {[string range $dst end-1 end] eq {/*}} {
			lappend pat $spec
			lappend cmd [string range $dst 0 end-2]
		} else {
			lappend all $spec
		}
	}

	if {$pat ne {}} {
		set fd [eval git_read for-each-ref --format=%(refname) $cmd]
		while {[gets $fd n] > 0} {
			foreach spec $pat {
				set dst [string range [lindex $spec 0] 0 end-2]
				set len [string length $dst]
				if {[string equal -length $len $dst $n]} {
					set src [string range [lindex $spec 2] 0 end-2]
					set spec [list \
						$n \
						[lindex $spec 1] \
						$src[string range $n $len end] \
						]
					lappend all $spec
				}
			}
		}
		close $fd
	}

	return [lsort -index 0 -unique $all]
}

proc load_all_remotes {} {
	global repo_config
	global all_remotes tracking_branches some_heads_tracking
	global remote_url

	set some_heads_tracking 0
	set all_remotes [list]
	set trck [list]

	set rh_str refs/heads/
	set rh_len [string length $rh_str]
	set rm_dir [gitdir remotes]
	if {[file isdirectory $rm_dir]} {
		set all_remotes [glob \
			-types f \
			-tails \
			-nocomplain \
			-directory $rm_dir *]

		foreach name $all_remotes {
			catch {
				set fd [open [file join $rm_dir $name] r]
				while {[gets $fd line] >= 0} {
					if {[regexp {^URL:[ 	]*(.+)$} $line line url]} {
						set remote_url($name) $url
						continue
					}
					if {![regexp {^Pull:[ 	]*([^:]+):(.+)$} \
						$line line src dst]} continue
					if {[string index $src 0] eq {+}} {
						set src [string range $src 1 end]
					}
					if {![string equal -length 5 refs/ $src]} {
						set src $rh_str$src
					}
					if {![string equal -length 5 refs/ $dst]} {
						set dst $rh_str$dst
					}
					if {[string equal -length $rh_len $rh_str $dst]} {
						set some_heads_tracking 1
					}
					lappend trck [list $dst $name $src]
				}
				close $fd
			}
		}
	}

	foreach line [array names repo_config remote.*.url] {
		if {![regexp ^remote\.(.*)\.url\$ $line line name]} continue
		lappend all_remotes $name
		set remote_url($name) $repo_config(remote.$name.url)

		if {[catch {set fl $repo_config(remote.$name.fetch)}]} {
			set fl {}
		}
		foreach line $fl {
			if {![regexp {^([^:]+):(.+)$} $line line src dst]} continue
			if {[string index $src 0] eq {+}} {
				set src [string range $src 1 end]
			}
			if {![string equal -length 5 refs/ $src]} {
				set src $rh_str$src
			}
			if {![string equal -length 5 refs/ $dst]} {
				set dst $rh_str$dst
			}
			if {[string equal -length $rh_len $rh_str $dst]} {
				set some_heads_tracking 1
			}
			lappend trck [list $dst $name $src]
		}
	}

	set tracking_branches [lsort -index 0 -unique $trck]
	set all_remotes [lsort -unique $all_remotes]
}

proc populate_fetch_menu {} {
	global all_remotes repo_config

	set remote_m .mbar.remote
	set fetch_m $remote_m.fetch
	set prune_m $remote_m.prune

	foreach r $all_remotes {
		set enable 0
		if {![catch {set a $repo_config(remote.$r.url)}]} {
			if {![catch {set a $repo_config(remote.$r.fetch)}]} {
				set enable 1
			}
		} else {
			catch {
				set fd [open [gitdir remotes $r] r]
				while {[gets $fd n] >= 0} {
					if {[regexp {^Pull:[ \t]*([^:]+):} $n]} {
						set enable 1
						break
					}
				}
				close $fd
			}
		}

		if {$enable} {
			if {![winfo exists $fetch_m]} {
				menu $prune_m
				$remote_m insert 0 cascade \
					-label [mc "Prune from"] \
					-menu $prune_m

				menu $fetch_m
				$remote_m insert 0 cascade \
					-label [mc "Fetch from"] \
					-menu $fetch_m
			}

			$fetch_m add command \
				-label $r \
				-command [list fetch_from $r]
			$prune_m add command \
				-label $r \
				-command [list prune_from $r]
		}
	}
}

proc populate_push_menu {} {
	global all_remotes repo_config

	set remote_m .mbar.remote
	set push_m $remote_m.push

	foreach r $all_remotes {
		set enable 0
		if {![catch {set a $repo_config(remote.$r.url)}]} {
			if {![catch {set a $repo_config(remote.$r.push)}]} {
				set enable 1
			}
		} else {
			catch {
				set fd [open [gitdir remotes $r] r]
				while {[gets $fd n] >= 0} {
					if {[regexp {^Push:[ \t]*([^:]+):} $n]} {
						set enable 1
						break
					}
				}
				close $fd
			}
		}

		if {$enable} {
			if {![winfo exists $push_m]} {
				menu $push_m
				$remote_m insert 0 cascade \
					-label [mc "Push to"] \
					-menu $push_m
			}

			$push_m add command \
				-label $r \
				-command [list push_to $r]
		}
	}
}
