<?php
class Blog_model extends CI_Model{
/*************************************************
 投稿
 ・トップページ(取得5件制限)
 ・記事一覧(post_list)
 ・記事詳細
 ・投稿に紐づいたカテゴリの取得 (記事表示用)
**************************************************/
  //トップページ
  public function get_post_list_limit(){
    $query = $this->db->query("
      SELECT *
      ,date_format(post_date,'%Y年%m月%d日') AS post_date
      FROM post
      ORDER BY post_id DESC
      limit 5");
    return $query->result_array();
  }

  //一覧記事用(post_list)
  public function get_post_list(){
    if(empty($_GET['per_page'])){
      $_GET['per_page'] = 0;
    }
    $query=
      $this->db
      ->select("*,date_format(post_date,'%Y年%m月%d日') AS post_date")
      ->order_by("post_date DESC")
      ->get("post", 10, $_GET['per_page']);//表示件数はControllerと合わせる
    return $query->result_array();
  }

  //記事詳細
  public function get_post_detail($post_id){
    $query = $this->db->query("
      SELECT *
      , date_format(post_date,'%Y年%m月%d日') AS post_date
      FROM post
      WHERE post_id=$post_id"
    );
    return $query->row_array();
  }

  //月別記事
  public function get_post_archive(){
    $post_date = $this->input->get('post_date');//x年x月を$_GET

    if(empty($_GET['per_page'])){
      $_GET['per_page'] = 0;
    }
    $query=
      $this->db
      ->select("*,date_format(post_date,'%Y年%m月%d日') AS post_date")
      ->where("date_format(post_date,'%Y-%m')='".$post_date."'")
      ->order_by("post_date DESC")
      ->get("post", 2, $_GET['per_page']);//表示件数はControllerと合わせる

    return $query->result_array();
  }

  //月別記事(サイドバー用)
  public function get_post_archive_sidebar(){
    $query = $this->db->query("
      SELECT *
      ,date_format(post_date,'%Y年%m月') AS post_date
      ,COUNT(*) as count
      ,date_format(post_date,'%Y-%m') AS post_date_month
      FROM post
      GROUP BY DATE_FORMAT(post_date, '%Y年%m')
      ORDER BY post_id DESC");
    return $query->result_array();
  }
/*************************************************
 投稿とカテゴリ
 ・投稿に紐づいたカテゴリの取得 (記事表示用)
 ・カテゴリに紐づいた記事を取得 (記事表示用)
**************************************************/
  //投稿に紐づいたカテゴリの取得 (記事表示用)
  public function get_show_check_category(){
    $query = $this->db->query("
      SELECT *
      FROM category
      inner join post_category ON(category.cat_id=post_category.cat_id)");
     return $query->result_array();
   }

  //カテゴリに紐づいた記事を取得 (記事表示用)
  public function get_post_category(){
    $cat_slug = $this->input->get("cat_slug");
    if(empty($_GET['per_page'])){
      $_GET['per_page'] = 0;
    }
    $query=
      $this->db
      ->select("*,date_format(post_date,'%Y年%m月%d日') AS post_date")
      ->join('category', 'post_category.cat_id=category.cat_id')
      ->join('post', 'post.post_id=post_category.post_id')
      ->where("cat_slug='".$cat_slug."'")
      ->order_by("post_date DESC")
      ->get("post_category", 2, $_GET['per_page']);//表示件数はControllerと合わせる
    return $query->result_array();
   }
/*************************************************
 カテゴリ
 ・カテゴリを取得
**************************************************/
  //カテゴリ取得
  public function get_category(){
   $query =  $this->db->get('category');
   return $query->result_array();
  }
}