<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Share;
use App\Entity\Event;
use App\Entity\Subscribe;
use App\Entity\UserEvent;
use App\Entity\ReqUserPrivate;
use App\Entity\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/feed/posts", name="ajax_feed_posts")
     */
    public function ajaxFeedPosts(Request $request, UserInterface $user)
    {  
        if ($request->isXmlHttpRequest()) {
            $date = $request->request->get('date');
            $offset = $request->request->get('offset');

            $posts = $this->getDoctrine()->getManager()->getRepository(Post::class)->findFeedPosts($user->getId(), $date, $offset, 5);    

            $response = new Response($this->renderView('ajax/fetch_posts.html.twig', ['posts' => $posts]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/user/posts", name="ajax_user_posts")
     */
    public function ajaxUserPosts(Request $request, UserInterface $user)
    {  
        if ($request->isXmlHttpRequest()) {
            $date = $request->request->get('date');
            $offset = $request->request->get('offset');
            $userProfile = $request->request->get('user');

            $posts = $this->getDoctrine()->getManager()->getRepository(Post::class)->findUserPosts($userProfile, $date, $offset, 5);        
            $response = new Response($this->renderView('ajax/fetch_posts.html.twig', ['posts' => $posts]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/deletepost", name="ajax_delete_post")
     */
    public function ajaxDeletePost(Request $request, UserInterface $user)
    {  
        if ($request->isXmlHttpRequest()) {
            $idPost = $request->request->get('id');

            $em = $this->getDoctrine()->getManager();

            $post = $em->getRepository(Post::class)->find($idPost);     
            
            if ($post == null) {
                return;
            }

            if ($post->getAuthor() != $user) {
                return;
            }

            $em->remove($post);
            $em->flush();

            $response = new Response(1);
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/deletecomment", name="ajax_delete_comment")
     */
    public function ajaxDeleteComment(Request $request, UserInterface $user)
    {  
        if ($request->isXmlHttpRequest()) {
            $idComment = $request->request->get('id');
            $idPost = $request->request->get('post');

            $em = $this->getDoctrine()->getManager();

            $comment = $em->getRepository(Comment::class)->find($idComment);  
            $post = $em->getRepository(Post::class)->find($idPost);    
            
            if ($comment == null || $post == null) {
                return;
            }

            if ($comment->getAuthor() != $user && $post->getAuthor() != $user) {
                return;
            }

            if ($post->getId() != $comment->getPost()->getId()) {
                return;
            }

            $em->remove($comment);
            $em->flush();

            $response = new Response(1);
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

     /**
     * @Route("/ajax/fetchcomments", name="ajax_fetch_comments")
     */
    public function ajaxFetchComments(Request $request)
    {   
        if ($request->isXmlHttpRequest()) {
            $idPost = $request->request->get('idPost');
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');

            $comments = $this->getDoctrine()->getManager()->getRepository(Comment::class)->findComments($idPost, $offset, 5, $date); 

            $response = new Response($this->renderView('ajax/fetch_comments.html.twig', ['comments' => $comments]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/sendcomment", name="ajax_send_comment")
     */
    public function ajaxSendComment(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $idPost = $request->request->get('idPost');
            $content = urldecode($request->request->get('content'));

            $post = $this->getDoctrine()->getManager()->getRepository(Post::class)->find($idPost); 

            $comment = new Comment(); 
            $comment->setPost($post); 
            $comment->setAuthor($user); 
            $comment->setContent($content);  

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $commentSend = $this->getDoctrine()->getManager()->getRepository(Comment::class)->find($comment);

            $response = new Response($this->renderView('ajax/new_comment.html.twig', ['comment' => $commentSend]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/sendlike", name="ajax_send_like")
     */
    public function ajaxSendLike(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $idPost = $request->request->get('idPost');

            $post = $this->getDoctrine()->getManager()->getRepository(Post::class)->find($idPost); 

            $em = $this->getDoctrine()->getManager();
            
            $searchLike = $this->getDoctrine()->getManager()->getRepository(Like::class)->searchLike($user->getId(), $post->getId());
            $like = "";

            foreach ($searchLike as $l) {
                $like = $l->getId();
            }

            if ($searchLike == null) {
                $like = new Like(); 
                $like->setPost($post); 
                $like->setUser($user);   
    
                $em->persist($like);
                $em->flush();

                return new Response(1);
            } else {
                $deleteLike = $em->getRepository(Like::class)->find($like);
                $em->remove($deleteLike);
                $em->flush();

                return new Response(0);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/sharepost", name="ajax_share_post")
     */
    public function ajaxSharePost(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $idPost = $request->request->get('idPost');

            $postSend = $this->getDoctrine()->getManager()->getRepository(Post::class)->find($idPost); 

            $em = $this->getDoctrine()->getManager();

            $share = new Share(); 
            $share->setPost($postSend); 
            $share->setUser($user);   

            $em->persist($share);
            $em->flush();

            $post = new Post();

            $post->setAuthor($user);
            $post->setShared($share);

            $em->persist($post);
            $em->flush();

            return;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/subscribe", name="ajax_subscribe")
     */
    public function ajaxSubscribe(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');

            $userSelect = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id); 

            $em = $this->getDoctrine()->getManager();

            if ($id == $user->getId()) {
                return;
            }

            $searchSubscribe = $this->getDoctrine()->getManager()->getRepository(Subscribe::class)->searchSubscribe($user->getId(), $userSelect->getId());
            $subscribe = "";

            foreach ($searchSubscribe as $s) {
                $subscribe = $s->getId();
            }

            $searchReq = $this->getDoctrine()->getManager()->getRepository(ReqUserPrivate::class)->searchReq($user->getId(), $userSelect->getId());
            $req = "";

            foreach ($searchReq as $r) {
                $req = $r->getId();
            }

            if ($searchSubscribe == null && $userSelect->getPrivate() == 0) {
                $subscribe = new Subscribe(); 
                $subscribe->setUser($user); 
                $subscribe->setSubscription($userSelect);   
    
                $em->persist($subscribe);
                $em->flush();

                return new Response(1);
            } else if ($searchReq == null && $userSelect->getPrivate() == 1 && $searchSubscribe == null) {
                $req = new ReqUserPrivate();

                $req->setUser($user);
                $req->setUserTarget($userSelect);

                $em->persist($req);
                $em->flush();

                return new Response(1);
            } else if ($searchSubscribe != null) {
                $deleteSubscribe = $em->getRepository(Subscribe::class)->find($subscribe);
                $em->remove($deleteSubscribe);
                $em->flush();

                return new Response(0);
            } else {
                $deleteReq = $em->getRepository(ReqUserPrivate::class)->find($req);
                $em->remove($deleteReq);
                $em->flush();

                return new Response(0);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/participate", name="ajax_participate")
     */
    public function ajaxParticipate(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');

            $event = $this->getDoctrine()->getManager()->getRepository(Event::class)->find($id); 

            $em = $this->getDoctrine()->getManager();

            if ($event == null) {
                return;
            }

            if ($event->getUser() == $user) {
                return;
            }

            $searchParticipate = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->searchParticipate($user->getId(), $event->getId());
            $participate = "";

            foreach ($searchParticipate as $p) {
                $participate = $p->getId();
            }

            if ($searchParticipate == null) {
                $userEvent = new UserEvent(); 
                $userEvent->setParticipant($user); 
                $userEvent->setEvent($event);   
    
                $em->persist($userEvent);
                $em->flush();

                return new Response(1);
            } else {
                $deleteParticipate = $em->getRepository(UserEvent::class)->find($participate);
                $em->remove($deleteParticipate);
                $em->flush();

                return new Response(0);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/reqprvresponse", name="req_prv_response")
     */
    public function ajaxReqPrvResponse(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $response = $request->request->get('response');

            $userSelect = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id); 

            $searchReq = $this->getDoctrine()->getManager()->getRepository(ReqUserPrivate::class)->searchReq($userSelect->getId(), $user->getId());
            $req = "";

            $em = $this->getDoctrine()->getManager();
            
            foreach ($searchReq as $r) {
                $req = $r->getId();
            }

            if ($searchReq != null && $response == 1) {
                $subscribe = new Subscribe(); 
                $subscribe->setUser($userSelect); 
                $subscribe->setSubscription($user);   
    
                $em->persist($subscribe);
                $em->flush();

                $deleteReq = $em->getRepository(ReqUserPrivate::class)->find($req);
                $em->remove($deleteReq);
                $em->flush();

                return $this->redirectToRoute('profile', array('username' => $userSelect->getUsername()));
            } else if ($searchReq != null && $response == 0) {
                $deleteReq = $em->getRepository(ReqUserPrivate::class)->find($req);
                $em->remove($deleteReq);
                $em->flush();

                return $this->redirectToRoute('profile', array('username' => $userSelect->getUsername()));
            } else {
                return;
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchsearchs", name="ajax_fetch_searchs")
     */
    public function ajaxFetchSearchs(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $content = urldecode($request->request->get('content'));

            $results = $this->getDoctrine()->getManager()->getRepository(User::class)->searchUsers('%'.$content.'%', $offset, 10); 

            $response = new Response($this->renderView('ajax/fetch_searchs.html.twig', ['results' => $results]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchfollowers", name="ajax_fetch_followers")
     */
    public function ajaxFetchFollowers(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');
            $userSelect = $request->request->get('user');

            $followers = $this->getDoctrine()->getManager()->getRepository(Subscribe::class)->findFollowers($userSelect, $offset, 10, $date); 

            $response = new Response($this->renderView('ajax/fetch_followers.html.twig', ['followers' => $followers]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchfollowings", name="ajax_fetch_followings")
     */
    public function ajaxFetchFollowings(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');
            $userSelect = $request->request->get('user');

            $followings = $this->getDoctrine()->getManager()->getRepository(Subscribe::class)->findFollowings($userSelect, $offset, 10, $date); 

            $response = new Response($this->renderView('ajax/fetch_followings.html.twig', ['followings' => $followings]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchdiscussions", name="ajax_fetch_discussions")
     */
    public function ajaxFetchDiscussions(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');

            $req = $this->getDoctrine()->getManager()->getRepository(Message::class)->findDiscussions($user->getId(), $offset, 10, $date); 
            
            $results = [];
            foreach ($req as $r) {
                $req2 = $this->getDoctrine()->getManager()->getRepository(Message::class)->findLastMessage($user->getId(), $r->getUser()->getId(), 1, $date); 
                array_push($results, $req2[0]);
            }

            $response = new Response($this->renderView('ajax/fetch_discussions.html.twig', ['discussions' => $results]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchmyevents", name="ajax_fetch_myevents")
     */
    public function ajaxFetchMyEvents(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');
            $userEvent = $request->request->get('user');

            $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->findMyEvents($userEvent, $offset, 5, $date); 

            $response = new Response($this->renderView('ajax/fetch_events.html.twig', ['events' => $events]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchevents", name="ajax_fetch_events")
     */
    public function ajaxFetchEvents(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');

            $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->findEvents($offset, 5, $date); 

            $response = new Response($this->renderView('ajax/fetch_events.html.twig', ['events' => $events]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchmessages", name="ajax_fetch_messages")
     */
    public function ajaxFetchMessages(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $userSelect = $request->request->get('user');
            $offset = $request->request->get('offset');
            $date = $request->request->get('date');

            $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findMessages($user->getId(), $userSelect, $offset, 10, $date); 

            $response = new Response($this->renderView('ajax/fetch_messages.html.twig', ['messages' => $messages]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchbeforemessages", name="ajax_fetch_before_messages")
     */
    public function ajaxFetchBeforeMessages(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $userSelect = $request->request->get('user');
            $offset = $request->request->get('offset');
            $first = $request->request->get('first');

            $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findBeforeMessages($user->getId(), $userSelect, $offset, 10, $first); 

            $response = new Response($this->renderView('ajax/fetch_messages.html.twig', ['messages' => $messages]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/fetchmessagesuserselect", name="ajax_fetch_messages_userselect")
     */
    public function ajaxFetchMessagesUserSelect(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $userSelect = $request->request->get('id');
            $last = $request->request->get('last');

            $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findMessagesUserSelect($user->getId(), $userSelect, $last); 

            $response = new Response($this->renderView('ajax/fetch_messages.html.twig', ['messages' => $messages]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/sendmessage", name="ajax_send_message")
     */
    public function ajaxSendMessage(Request $request, UserInterface $user)
    {   
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $content = urldecode($request->request->get('content'));

            $userSelect = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id); 

            $message = new Message(); 
            $message->setUser($user); 
            $message->setUserReceiveMsg($userSelect); 
            $message->setContent($content);  

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $messageSend = $this->getDoctrine()->getManager()->getRepository(Message::class)->find($message);

            $response = new Response($this->renderView('ajax/new_message.html.twig', ['message' => $messageSend]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/ajax/mapevents", name="ajax_map_events")
     */
    public function ajaxMapEvents(Request $request)
    {   
        if ($request->isXmlHttpRequest()) {
            $verifFetch = time() + 604800;
            $events = $this->getDoctrine()->getManager()->getRepository(Event::class)->mapEvents($verifFetch); 

            $response = new Response($this->renderView('ajax/map_events.html.twig', ['events' => $events]));
            return $response;
        } else {
            return $this->redirectToRoute('home');
        }
    }
}
