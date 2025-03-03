<?php

namespace App\Controller;

use App\Entity\Diagnostic;
use App\Form\DiagnosticType;
use App\Repository\DiagnosticRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/diagnostic')]
final class DiagnosticController extends AbstractController
{
    #[Route(name: 'app_diagnostic_index', methods: ['GET'])]
    public function index(DiagnosticRepository $diagnosticRepository): Response
    {
        return $this->render('diagnostic/index.html.twig', [
            'diagnostics' => $diagnosticRepository->findAll(),
        ]);
    }

    #[Route('/DE', name: 'app_diagnosticM_index', methods: ['GET'])]
    public function indexM(DiagnosticRepository $diagnosticRepository): Response
    {
        return $this->render('diagnostic/indexM.html.twig', [
            'diagnostics' => $diagnosticRepository->findAll(),
        ]);
    }

    #[Route('/new/{patientId}', name: 'app_diagnostic_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, int $patientId): Response
    {
        $diagnostic = new Diagnostic();
        
        // Create form with patientId option
        $form = $this->createForm(DiagnosticType::class, $diagnostic, [
            'patient_id' => $patientId,
        ]);
        $form->handleRequest($request);

        // Validation de la saisie
        $errors = $validator->validate($diagnostic);

        if ($form->isSubmitted() && $form->isValid() && count($errors) === 0) {
            $entityManager->persist($diagnostic);
            $entityManager->flush();

            return $this->redirectToRoute('app_diagnosticM_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('diagnostic/new.html.twig', [
            'diagnostic' => $diagnostic,
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'app_diagnostic_show', methods: ['GET'])]
    public function show(Diagnostic $diagnostic): Response
    {
        return $this->render('diagnostic/show.html.twig', [
            'diagnostic' => $diagnostic,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_diagnostic_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Diagnostic $diagnostic, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(DiagnosticType::class, $diagnostic);
        $form->handleRequest($request);

        // Validation de la saisie
        $errors = $validator->validate($diagnostic);

        if ($form->isSubmitted() && $form->isValid() && count($errors) === 0) {
            $entityManager->flush();

            return $this->redirectToRoute('app_diagnosticM_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('diagnostic/edit.html.twig', [
            'diagnostic' => $diagnostic,
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'app_diagnostic_delete', methods: ['POST'])]
    public function delete(Request $request, Diagnostic $diagnostic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$diagnostic->getId(), $request->get('_token'))) {
            $entityManager->remove($diagnostic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_diagnostic_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate-pdf', name: 'app_diagnostic_generate_pdf')]
    public function generatePdf(Diagnostic $diagnostic): Response
    {
        $html = $this->renderView('diagnostic/pdf_report.html.twig', [
            'diagnostic' => $diagnostic,
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="rapport_diagnostic.pdf"',
        ]);
    }
}