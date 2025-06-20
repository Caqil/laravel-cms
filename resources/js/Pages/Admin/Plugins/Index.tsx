import React from 'react';
import { Link, router } from '@inertiajs/react';
import AdminLayout from '@/Components/Admin/Layout/AdminLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Switch } from '@/Components/ui/switch';
import { Badge } from '@/Components/ui/badge';
import { Trash2, Upload, Settings } from 'lucide-react';
import { Plugin } from '@/Types';

interface Props {
  plugins: {
    data: Plugin[];
  };
}

export default function PluginsIndex({ plugins }: Props) {
  const handleToggleActive = (plugin: Plugin) => {
    const action = plugin.is_active ? 'deactivate' : 'activate';
    router.post(route(`admin.plugins.${action}`, plugin.id));
  };

  const handleDelete = (plugin: Plugin) => {
    if (confirm(`Are you sure you want to delete the plugin "${plugin.name}"?`)) {
      router.delete(route('admin.plugins.destroy', plugin.id));
    }
  };

  return (
    <AdminLayout title="Plugins">
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-3xl font-bold">Plugins</h1>
            <p className="text-gray-600 dark:text-gray-400">
              Manage your installed plugins
            </p>
          </div>
          <Link href={route('admin.plugins.upload')}>
            <Button>
              <Upload className="mr-2 h-4 w-4" />
              Upload Plugin
            </Button>
          </Link>
        </div>

        <div className="grid gap-6">
          {plugins.data.map((plugin) => (
            <Card key={plugin.id}>
              <CardHeader>
                <div className="flex justify-between items-start">
                  <div>
                    <CardTitle className="flex items-center gap-2">
                      {plugin.name}
                      <Badge variant={plugin.is_active ? 'default' : 'secondary'}>
                        {plugin.is_active ? 'Active' : 'Inactive'}
                      </Badge>
                    </CardTitle>
                    <CardDescription>
                      {plugin.description}
                    </CardDescription>
                    <p className="text-sm text-gray-500 mt-1">
                      Version {plugin.version} by {plugin.author}
                    </p>
                  </div>
                  <div className="flex items-center gap-2">
                    <Switch
                      checked={plugin.is_active}
                      onCheckedChange={() => handleToggleActive(plugin)}
                    />
                    <Button variant="outline" size="icon">
                      <Settings className="h-4 w-4" />
                    </Button>
                    <Button
                      variant="outline"
                      size="icon"
                      onClick={() => handleDelete(plugin)}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </CardHeader>
            </Card>
          ))}
        </div>

        {plugins.data.length === 0 && (
          <Card>
            <CardContent className="text-center py-12">
              <p className="text-gray-500 dark:text-gray-400">
                No plugins installed yet.
              </p>
              <Link href={route('admin.plugins.upload')} className="mt-4 inline-block">
                <Button>Upload Your First Plugin</Button>
              </Link>
            </CardContent>
          </Card>
        )}
      </div>
    </AdminLayout>
  );
}
