import React from 'react';
import { Link } from '@inertiajs/react';
import { 
  LayoutDashboard, 
  Puzzle, 
  Palette, 
  Users, 
  Settings,
  FileText,
  MessageSquare,
  Activity,
  Heart,
  UserCheck
} from 'lucide-react';

const navigation = [
  { name: 'Dashboard', href: '/admin', icon: LayoutDashboard },
  
  // Content Management
  { name: 'Pages', href: '/admin/pages', icon: FileText },
  
  // Social Networking
  { 
    name: 'Social', 
    icon: Heart,
    children: [
      { name: 'Users', href: '/admin/social/users', icon: Users },
      { name: 'Posts', href: '/admin/social/posts', icon: MessageSquare },
      { name: 'Activities', href: '/admin/social/activities', icon: Activity },
      { name: 'Relationships', href: '/admin/social/relationships', icon: UserCheck },
    ]
  },
  
  // User Management
  { name: 'Users', href: '/admin/users', icon: Users },
  
  // Extensions
  { name: 'Plugins', href: '/admin/plugins', icon: Puzzle },
  { name: 'Themes', href: '/admin/themes', icon: Palette },
  
  // System
  { name: 'Settings', href: '/admin/settings', icon: Settings },
];

export default function Sidebar() {
  const [openSections, setOpenSections] = React.useState<string[]>(['Social']);

  const toggleSection = (name: string) => {
    setOpenSections(prev => 
      prev.includes(name) 
        ? prev.filter(section => section !== name)
        : [...prev, name]
    );
  };

  const renderNavItem = (item: any, depth = 0) => {
    const hasChildren = item.children && item.children.length > 0;
    const isOpen = openSections.includes(item.name);
    const paddingLeft = depth * 16 + 8;

    if (hasChildren) {
      return (
        <div key={item.name}>
          <button
            onClick={() => toggleSection(item.name)}
            className="group flex w-full items-center rounded-md px-2 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
            style={{ paddingLeft }}
          >
            <item.icon className="mr-3 h-5 w-5" />
            {item.name}
            <svg
              className={`ml-auto h-4 w-4 transition-transform ${isOpen ? 'rotate-90' : ''}`}
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fillRule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clipRule="evenodd"
              />
            </svg>
          </button>
          {isOpen && (
            <div className="space-y-1">
              {item.children.map((child: any) => renderNavItem(child, depth + 1))}
            </div>
          )}
        </div>
      );
    }

    return (
      <Link
        key={item.name}
        href={item.href}
        className="group flex items-center rounded-md px-2 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
        style={{ paddingLeft }}
      >
        <item.icon className="mr-3 h-5 w-5" />
        {item.name}
      </Link>
    );
  };

  return (
    <div className="flex h-screen w-64 flex-col bg-white shadow-lg dark:bg-gray-800">
      <div className="flex h-16 items-center justify-center border-b border-gray-200 dark:border-gray-700">
        <h1 className="text-xl font-bold text-gray-900 dark:text-white">
          Social CMS
        </h1>
      </div>
      
      <nav className="flex-1 space-y-1 p-4 overflow-y-auto">
        {navigation.map(item => renderNavItem(item))}
      </nav>
      
      <div className="border-t border-gray-200 p-4 dark:border-gray-700">
        <div className="text-xs text-gray-500 dark:text-gray-400">
          Laravel Social CMS
        </div>
        <div className="text-xs text-gray-400 dark:text-gray-500">
          v1.0.0
        </div>
      </div>
    </div>
  );
}
