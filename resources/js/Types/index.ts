export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  is_active: boolean;
  last_login_at?: string;
  created_at: string;
  updated_at: string;
  roles?: Role[];
}

export interface Role {
  id: number;
  name: string;
  guard_name: string;
}

export interface Plugin {
  id: number;
  name: string;
  slug: string;
  description?: string;
  version: string;
  author?: string;
  author_url?: string;
  plugin_url?: string;
  file_path: string;
  config?: any;
  is_active: boolean;
  auto_activate: boolean;
  dependencies?: string[];
  minimum_php_version: string;
  minimum_laravel_version: string;
  created_at: string;
  updated_at: string;
}

export interface Theme {
  id: number;
  name: string;
  slug: string;
  description?: string;
  version: string;
  author?: string;
  author_url?: string;
  theme_url?: string;
  screenshot?: string;
  file_path: string;
  config?: any;
  is_active: boolean;
  type: string;
  customization_options?: any;
  created_at: string;
  updated_at: string;
}

export interface Page {
  id: number;
  title: string;
  slug: string;
  content: string;
  excerpt?: string;
  status: string;
  template?: string;
  meta?: any;
  sort_order: number;
  author_id: number;
  author?: User;
  published_at?: string;
  created_at: string;
  updated_at: string;
}

export interface PageProps<T extends Record<string, unknown> = Record<string, unknown>> {
  auth: {
    user: User;
  };
  flash: {
    success?: string;
    error?: string;
  };
}

// Global route function
declare global {
  function route(name: string, params?: any): string;
  interface Window {
    route: typeof route;
  }
}
