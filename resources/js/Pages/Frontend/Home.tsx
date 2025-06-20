import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Page } from '@/Types';

interface Props {
  pages: Page[];
}

export default function Home({ pages }: Props) {
  return (
    <>
      <Head title="Home" />
      
      <div className="min-h-screen bg-gray-50">
        {/* Header */}
        <header className="bg-white shadow">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex justify-between items-center py-6">
              <div className="flex items-center">
                <Link href="/" className="text-2xl font-bold text-gray-900">
                  Laravel CMS
                </Link>
              </div>
              <div className="flex items-center space-x-4">
                <Link href={route('login')}>
                  <Button variant="outline">Login</Button>
                </Link>
                <Link href={route('register')}>
                  <Button>Register</Button>
                </Link>
              </div>
            </div>
          </div>
        </header>

        {/* Hero Section */}
        <div className="bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div className="text-center">
              <h1 className="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                Welcome to Laravel CMS
              </h1>
              <p className="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                A modern, WordPress-like content management system built with Laravel 11, React, and Shadcn UI.
              </p>
              <div className="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                <div className="rounded-md shadow">
                  <Link href={route('login')}>
                    <Button size="lg">
                      Get Started
                    </Button>
                  </Link>
                </div>
                <div className="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                  <Button variant="outline" size="lg">
                    Learn More
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Pages Section */}
        {pages.length > 0 && (
          <div className="py-12">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <div className="text-center">
                <h2 className="text-3xl font-extrabold text-gray-900">
                  Latest Pages
                </h2>
                <p className="mt-4 text-lg text-gray-500">
                  Check out our latest content
                </p>
              </div>
              <div className="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                {pages.map((page) => (
                  <Card key={page.id}>
                    <CardHeader>
                      <CardTitle>
                        <Link
                          href={route('page.show', page.slug)}
                          className="hover:text-indigo-600"
                        >
                          {page.title}
                        </Link>
                      </CardTitle>
                      {page.excerpt && (
                        <CardDescription>{page.excerpt}</CardDescription>
                      )}
                    </CardHeader>
                    <CardContent>
                      <Link href={route('page.show', page.slug)}>
                        <Button variant="outline" size="sm">
                          Read More
                        </Button>
                      </Link>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* Footer */}
        <footer className="bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div className="text-center text-gray-500">
              <p>&copy; 2024 Laravel CMS. Built with ❤️ using Laravel 11, React & Shadcn UI.</p>
            </div>
          </div>
        </footer>
      </div>
    </>
  );
}
