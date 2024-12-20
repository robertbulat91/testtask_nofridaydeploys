<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/companies', name: 'restapi_company')]
class CompanyRestController extends AbstractController
{
    #[Route('/', name: 'project_index', methods:['get'] )]
    public function index(CompanyRepository $companyRepository): JsonResponse
    {

        $companies = $companyRepository
            ->findAll();

        $data = [];

        /**
         * @var Company $company
         */
        foreach ($companies as $company) {
            $data[] = [
                'name' => $company->getName(),
                'taxerpayerIdentificationNumber' => $company->getTaxpayerIdentificationNumber(),
                'address' => $company->getAddress(),
                'city' => $company->getCity(),
                'zipCode' => $company->getZipCode()
            ];
        }

        return $this->json($data);
    }


    #[Route('/-company/add', name: 'company_add', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        if(!isset($requestContent['company'])){
            return $this->json(['error' => 'Data doesn\'t exist']);
        }

        $company = new Company();

        $company->setName($requestContent['company']['name']);
        $company->setTaxpayerIdentificationNumber($requestContent['company']['taxpayerIdentificationNumber']);
        $company->setAddress($requestContent['company']['address']);
        $company->setCity($requestContent['company']['city']);
        $company->setPostalCode($requestContent['company']['zipCode']);

        $entityManager->persist($company);
        $entityManager->flush();

        $data =  [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'taxerpayerIdentificationNumber' => $company->getTaxpayerIdentificationNumber(),
            'address' => $company->getAddress(),
            'city' => $company->getCity(),
            'zipCode' => $company->getPostalCode()
        ];

        return $this->json($data);
    }

    #[Route('/company/{id}', name: 'company_show', methods:['get'] )]
    public function show(CompanyRepository $companyRepository, int $id): JsonResponse
    {
        $company = $companyRepository->find($id);

        if (!$project) {
            return $this->json('No company found for id ' . $id, 404);
        }

        $data =  [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'taxerpayerIdentificationNumber' => $company->getTaxpayerIdentificationNumber(),
            'address' => $company->getAddress(),
            'city' => $company->getCity(),
            'zipCode' => $company->getPostalCode()
        ];

        return $this->json($data);
    }
}