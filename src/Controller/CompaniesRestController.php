<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompaniesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/companies', name: 'restapi_company')]
class CompaniesRestController extends AbstractController
{
    #[Route('/', name: 'companies_index', methods:['get'] )]
    public function index(CompaniesRepository $companyRepository): JsonResponse
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


    #[Route('/company/add', name: 'company_add', methods:['post'] )]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        if(!isset($requestContent['company'])){
            return $this->json(['error' => 'Data doesn\'t exist']);
        }

        $company = new Company();

        isset($requestContent['company']['name']) ?? $company->setName($requestContent['company']['name']);
        isset($requestContent['company']['taxpayerIdentificationNumber']) ?? $company->setTaxpayerIdentificationNumber($requestContent['company']['taxpayerIdentificationNumber']);
        isset($requestContent['company']['address']) ?? $company->setAddress($requestContent['company']['address']);
        isset($requestContent['company']['city']) ?? $company->setCity($requestContent['company']['city']);
        isset($requestContent['company']['zipCode']) ?? $company->setPostalCode($requestContent['company']['zipCode']);

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
    public function show(CompaniesRepository $companyRepository, int $id): JsonResponse
    {
        $company = $companyRepository->find($id);

        if (!$company) {
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

    #[Route('/company/{id}', name: 'company_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json('No company found for id ' . $id, 404);
        }
        $requestContent = json_decode($request->getContent(), true);

        if(!isset($requestContent['company'])){
            return $this->json(['error' => 'Data doesn\'t exist']);
        }

        $company = new Company();

        isset($requestContent['company']['name']) ?? $company->setName($requestContent['company']['name']);
        isset($requestContent['company']['taxpayerIdentificationNumber']) ?? $company->setTaxpayerIdentificationNumber($requestContent['company']['taxpayerIdentificationNumber']);
        isset($requestContent['company']['address']) ?? $company->setAddress($requestContent['company']['address']);
        isset($requestContent['company']['city']) ?? $company->setCity($requestContent['company']['city']);
        isset($requestContent['company']['zipCode']) ?? $company->setPostalCode($requestContent['company']['zipCode']);

        $entityManager->persist($company);
        $entityManager->flush();

        $data =  [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'description' => $company->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/company/{id}', name: 'company_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            return $this->json('No company found for id ' . $id, 404);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json('Deleted a company successfully with id ' . $id);
    }
}