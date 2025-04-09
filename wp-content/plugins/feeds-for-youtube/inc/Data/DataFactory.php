<?php

namespace SmashBalloon\YouTubeFeed\Data;


use SmashBalloon\YouTubeFeed\Container;

class DataFactory {
	public function create($class) {
		return Container::get_instance()->get($class);
	}
}