import type { LinkComponentBaseProps } from '@inertiajs/core';
import { page } from '@inertiajs/svelte';
import { toUrl } from '@/lib/utils';

export type CurrentUrlState = {
    readonly currentUrl: string;
    isCurrentUrl: (
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
    ) => boolean;
    isCurrentOrParentUrl: (
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
    ) => boolean;
    whenCurrentUrl: <TIfTrue, TIfFalse>(
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
        ifTrue: TIfTrue,
        ifFalse: TIfFalse,
    ) => TIfTrue | TIfFalse;
};

export function currentUrlState(): CurrentUrlState {
    const currentUrl = $derived.by(() => {
        const origin =
            typeof window === 'undefined'
                ? 'http://localhost'
                : window.location.origin;

        try {
            return new URL(page.url, origin).pathname;
        } catch {
            return page.url;
        }
    });

    function isCurrentUrl(
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
    ): boolean {
        const resolved = toUrl(urlToCheck);

        if (typeof resolved !== 'string') {
            return false;
        }

        return currentUrl === resolved;
    }

    function isCurrentOrParentUrl(
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
    ): boolean {
        const resolved = toUrl(urlToCheck);

        if (typeof resolved !== 'string') {
            return false;
        }

        return currentUrl.startsWith(resolved);
    }

    function whenCurrentUrl<TIfTrue, TIfFalse>(
        urlToCheck: NonNullable<LinkComponentBaseProps['href']>,
        ifTrue: TIfTrue,
        ifFalse: TIfFalse,
    ): TIfTrue | TIfFalse {
        return isCurrentUrl(urlToCheck) ? ifTrue : ifFalse;
    }

    return {
        get currentUrl() {
            return currentUrl;
        },
        isCurrentUrl,
        isCurrentOrParentUrl,
        whenCurrentUrl,
    };
}
