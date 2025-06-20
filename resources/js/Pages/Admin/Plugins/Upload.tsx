import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AdminLayout from '@/Components/Admin/Layout/AdminLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Upload } from 'lucide-react';

export default function PluginUpload() {
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!file) return;

    setUploading(true);
    
    const formData = new FormData();
    formData.append('plugin', file);

    router.post(route('admin.plugins.store'), formData, {
      onFinish: () => setUploading(false),
    });
  };

  return (
    <AdminLayout title="Upload Plugin">
      <div className="max-w-2xl mx-auto">
        <Card>
          <CardHeader>
            <CardTitle>Upload Plugin</CardTitle>
            <CardDescription>
              Upload a new plugin ZIP file to install it on your site.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <Input
                  type="file"
                  accept=".zip"
                  onChange={(e) => setFile(e.target.files?.[0] || null)}
                  required
                />
                <p className="text-sm text-gray-500 mt-1">
                  Select a ZIP file containing your plugin
                </p>
              </div>
              
              <div className="flex gap-4">
                <Button type="submit" disabled={!file || uploading}>
                  <Upload className="mr-2 h-4 w-4" />
                  {uploading ? 'Uploading...' : 'Upload Plugin'}
                </Button>
                
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => router.visit(route('admin.plugins.index'))}
                >
                  Cancel
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  );
}
