import { Route, Routes, Navigate } from 'react-router-dom';
import { Connections } from '@screens/connections/index.js';
import { Logs } from '@screens/logs/index.js';
import { Dashboard } from '@screens/dashboard/index.js';
import { Notifications } from '@screens/notifications';
import { Settings } from '@screens/settings/index.js';

const ContentArea = () => {
	return (
		<div className="content-area w-full">
			<Routes>
				<Route path="/connections" element={ <Connections /> } />
				<Route path="/logs" element={ <Logs /> } />
				<Route path="/dashboard" element={ <Dashboard /> } />
				<Route path="/settings" element={ <Settings /> } />
				<Route path="/notifications" element={ <Notifications /> } />
				<Route
					path="/"
					element={ <Navigate to="/dashboard" replace /> }
				/>
			</Routes>
		</div>
	);
};

export default ContentArea;
