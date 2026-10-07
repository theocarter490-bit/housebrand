<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FooterWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $widgets = ['Company', 'Useful Links', 'Client Support'];
        foreach ($widgets as $widget) {
            $wid = new \App\Models\FooterWidget();
            $wid->title = $widget;
            $wid->save();
        }

        $users = User::whereIn('role_id',[Role::SUPER_ADMIN, Role::DESIGNER])->get();

        foreach ($users as $key => $user) {
            foreach (Page::pages as $page) {
                $pg = new \App\Models\Page();
                $pg->title = $page;
                if ($page == "About Us") {
                    $slug = Str::slug('About');
                }elseif($page == "Contact Us"){
                    $slug = Str::slug('Contact');
                }else{
                    $slug = Str::slug($page);
                }
                $pg->slug = $slug;
                $pg->short_desc = 'This is '.$page.' page';
                $pg->content = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>';
                $pg->meta_title = $page;
                $pg->meta_description = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>';
                if ($page == 'About Us' || $page == 'Our Story' || $page == 'Contact Us') {
                    $pg->footer_widget_id = 1;
                } elseif ( $page == 'Team' || $page == 'Blog') {
                    $pg->footer_widget_id = 2;
                } else {
                    $pg->footer_widget_id = 3;
                }
                $pg->user_id = $user->id;
                $pg->save();
            }
        }
    }
}
