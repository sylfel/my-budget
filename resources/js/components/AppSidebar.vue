<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import MonthsSelector from './filter/MonthsSelector.vue';
import SidebarGroup from './ui/sidebar/SidebarGroup.vue';
import SidebarGroupContent from './ui/sidebar/SidebarGroupContent.vue';

//const mainNavItems: NavItem[] = [];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="none">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupContent>
                    <MonthsSelector />
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <!-- <NavUser /> -->
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
