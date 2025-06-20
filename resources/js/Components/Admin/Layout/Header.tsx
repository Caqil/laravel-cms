import React from 'react';
import { Link, router } from '@inertiajs/react';
import { Bell, User, LogOut } from 'lucide-react';
import { Button } from '@/Components/ui/button';

export default function Header() {
  const handleLogout = () => {
    router.post('/logout');
  };

  return (
    <header className="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6 dark:border-gray-700 dark:bg-gray-800">
      <div className="flex items-center">
        <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
          Admin Dashboard
        </h2>
      </div>
      
      <div className="flex items-center space-x-4">
        <Button variant="ghost" size="icon">
          <Bell className="h-5 w-5" />
        </Button>
        
        <Button variant="ghost" size="icon">
          <User className="h-5 w-5" />
        </Button>
        
        <Button variant="ghost" size="icon" onClick={handleLogout}>
          <LogOut className="h-5 w-5" />
        </Button>
      </div>
    </header>
  );
}
