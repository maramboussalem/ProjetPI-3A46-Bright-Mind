<?php

namespace App\Controller;

use App\Repository\MedicamentRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        // Validation de la quantité
        if (!isset($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }

        $session->set('cart', $cart);

        // 🔹 Message de succès
        $this->addFlash('success', 'Médicament ajouté avec succès');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart', name: 'cart_show')]
    public function showCart(Request $request, MedicamentRepository $medicamentRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $cartData = [];
        $totalPanier = 0;

        foreach ($cart as $id => $quantity) {
            // Vérifier que la quantité est valide (positive et non nulle)
            if ($quantity <= 0) {
                continue;
            }

            $medicament = $medicamentRepository->find($id);
            if ($medicament) {
                $subtotal = $medicament->getPrix() * $quantity;
                $totalPanier += $subtotal; // Ajoute au total

                $cartData[] = [
                    'medicament' => $medicament,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
            } else {
                // Gestion de l'erreur si le médicament n'est pas trouvé
                $this->addFlash('error', 'Le médicament avec l\'ID ' . $id . ' n\'a pas été trouvé.');
            }
        }

        return $this->render('cart/show.html.twig', [
            'cart' => $cartData,
            'totalPanier' => $totalPanier
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);

            // 🔹 Message de succès
            $this->addFlash('success', 'Médicament supprimé avec succès');
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clearCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->remove('cart');

        // 🔹 Message de succès
        $this->addFlash('success', 'Le panier a été vidé avec succès');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/increase/{id}', name: 'cart_increase')]
    public function increaseQuantity(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]++;
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
    public function decreaseQuantity(Request $request, int $id): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/imprimer', name: 'cart_imprimer')]
    public function imprimerPanier(Pdf $pdf, Request $request, MedicamentRepository $medicamentRepository, Environment $twig): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return new Response("Le panier est vide, impossible d'imprimer la facture.", 400);
        }

        $cartDetails = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $medicament = $medicamentRepository->find($id);

            if (!$medicament) {
                continue;
            }

            $subtotal = $medicament->getPrix() * $quantity;
            $total += $subtotal;

            $cartDetails[] = [
                'NomMedicament' => $medicament->getNomMedicament(),
                'Prix' => $medicament->getPrix(),
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
        }

        if (empty($cartDetails)) {
            return new Response("Tous les produits du panier ont été supprimés ou sont indisponibles.", 400);
        }

        $html = $twig->render('cart/pdf.html.twig', [
            'cart' => $cartDetails,
            'total' => $total
        ]);

        $pdfContent = $pdf->getOutputFromHtml($html);

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture_panier.pdf"'
        ]);
    }
}
