import React from 'react';
import AdminLayout from '@/Components/Admin/Layout/AdminLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Users, Puzzle, Palette, FileText } from 'lucide-react';

interface Props {
  stats: {
    users: number;
    plugins: number;
    active_plugins: number;
    themes: number;
    pages: number;
  };
  recentPlugins: any[];
  recentThemes: any[];
}

export default function Dashboard({ stats, recentPlugins, recentThemes }: Props) {
  return (
    <AdminLayout title="Dashboard">
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold">Dashboard</h1>
          <p className="text-gray-600 dark:text-gray-400">
            Welcome to your CMS admin panel
          </p>
        </div>

        <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Users</CardTitle>
              <Users className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.users}</div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Plugins</CardTitle>
              <Puzzle className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.plugins}</div>
              <p className="text-xs text-muted-foreground">
                {stats.active_plugins} active
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Themes</CardTitle>
              <Palette className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.themes}</div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Pages</CardTitle>
              <FileText className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{stats.pages}</div>
            </CardContent>
          </Card>
        </div>

        <div className="grid gap-6 md:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle>Recent Plugins</CardTitle>
              <CardDescription>Latest plugin installations</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentPlugins.map((plugin) => (
                  <div key={plugin.id} className="flex items-center space-x-4">
                    <div className="flex-1">
                      <p className="text-sm font-medium">{plugin.name}</p>
                      <p className="text-xs text-muted-foreground">v{plugin.version}</p>
                    </div>
                    <div className={`text-xs px-2 py-1 rounded ${
                      plugin.is_active 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-gray-100 text-gray-800'
                    }`}>
                      {plugin.is_active ? 'Active' : 'Inactive'}
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Recent Themes</CardTitle>
              <CardDescription>Latest theme installations</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {recentThemes.map((theme) => (
                  <div key={theme.id} className="flex items-center space-x-4">
                    <div className="flex-1">
                      <p className="text-sm font-medium">{theme.name}</p>
                      <p className="text-xs text-muted-foreground">v{theme.version}</p>
                    </div>
                    <div className={`text-xs px-2 py-1 rounded ${
                      theme.is_active 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-gray-100 text-gray-800'
                    }`}>
                      {theme.is_active ? 'Active' : 'Inactive'}
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </AdminLayout>
  );
}
