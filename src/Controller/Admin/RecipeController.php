<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/admin/recettes', name: 'admin.recipe.')]
#[IsGranted('ROLE_ADMIN')]
class RecipeController extends AbstractController
{

   #[Route('/', name: 'index')]
   // #[IsGranted('ROLE_USER')]
   public function index(RecipeRepository $repository, Request $request): Response
   {
      // $recipes = $repository->findWithDurationLowerThan(20);
      $page = $request->query->getInt('page', 1);
      $limit = 1;
      $recipes = $repository->paginateRecipes($page);
      // $maxPage = ceil($recipes->count() / $limit);
      // $maxPage = ceil($recipes->getTotalItemCount() / $limit);
      return $this->render('admin/recipe/index.html.twig', [
         'recipes' => $recipes,
         // 'maxPage' => $maxPage,
         // 'page' => $page
      ]);
   }

   #[Route('/create', name: 'create')]
   public function create(Request $request, EntityManagerInterface $em)
   {
      $recipe = new Recipe();
      $form = $this->createForm(RecipeType::class, $recipe);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
         $recipe-> setCreatedAt(new DateTimeImmutable());
         $recipe-> setUpdatedAt(new DateTimeImmutable());
         $em->persist($recipe);
         $em->flush();
         $this->addFlash('success', 'La recette a bien été créée');
         return $this->redirectToRoute('admin.recipe.index');
      }
      return $this->render('admin/recipe/create.html.twig', [
         "form" => $form
      ]);
   }

   // #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
   // public function show(Request $request, string $slug, int $id, RecipeRepository $repo): Response
   // {
   //    $recipe = $repo->find($id);
   //    if ($recipe->getSlug() !== $slug) {
   //       return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
   //    }
   //    return $this->render('recipe/show.html.twig', [
   //       'recipe' => $recipe
   //    ]);
   // }

   #[Route('/{id}', name: 'edit', methods:['GET', 'POST'], requirements:['id' => Requirement::DIGITS])]
   public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em)
   {
      $form = $this->createForm(RecipeType::class, $recipe);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
         $recipe-> setUpdatedAt(new DateTimeImmutable());
         // /** @var UploadedFile $file */
         // $file = $form->get('thumbnailFile')->getData();
         // $filename=$recipe->getId() . '.' . $file->getClientOriginalExtension();
         // $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images',$filename);
         // $recipe->setThumbnail($filename);
         $em->flush();
         $this->addFlash('success', 'La recette a bien été modifiée');
         return $this->redirectToRoute('admin.recipe.index');
      }
      return $this->render('admin/recipe/edit.html.twig', [
         'recipe' => $recipe,
         "form" => $form
      ]);
   }

   #[Route('/{id}', name: 'delete', methods:['DELETE'], requirements:['id' => Requirement::DIGITS])]
   public function remove(Recipe $recipe, EntityManagerInterface $em, Request $request){
      $recipeId = $recipe->getId();
      $message ='La recette a bien été supprimée';
      $em->remove($recipe);
      $em->flush();
      if($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT){
         $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
         return $this->render('admin/recipe/delete.html.twig', ['recipeId' => $recipeId, 'message' => $message]);
      }
      $this->addFlash('success', $message);
      return $this->redirectToRoute('admin.recipe.index');
   }
}
