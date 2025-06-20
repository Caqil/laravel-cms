import React from 'react';
import { Head } from '@inertiajs/react';
import Header from './Header';
import Sidebar from './Sidebar';

interface AdminLayoutProps {
  title: string;
  children: React.ReactNode;
}

export default function AdminLayout({ title, children }: AdminLayoutProps) {
  return (
    <>
      <Head title={title} />
      <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
        <div className="flex">
          <Sidebar />
          <div className="flex-1">
            <Header />
            <main className="p-6">
              {children}
            </main>
          </div>
        </div>
      </div>
    </>
  );
}
