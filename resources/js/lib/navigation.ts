import BookOpen from 'lucide-svelte/icons/book-open';
import FolderGit2 from 'lucide-svelte/icons/folder-git-2';
import LayoutGrid from 'lucide-svelte/icons/layout-grid';
import { externalLinks } from '@/lib/links';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

export const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: externalLinks.github,
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: externalLinks.docs,
        icon: BookOpen,
    },
];

export const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: externalLinks.github,
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: externalLinks.docs,
        icon: BookOpen,
    },
];
