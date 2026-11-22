<?php

namespace App\Services;

class SaopQuestionnaire
{
    public const QUESTIONS = [
        1 => ['Équipement informatique et capacité technique', 'Quelle est la configuration technique complète de l’ordinateur (portable ou de bureau) que vous utiliserez quotidiennement durant votre formation à l’EVC ?', 'Précisez le type d’ordinateur, le processeur, la mémoire RAM, la capacité de stockage, le système d’exploitation et si cet équipement est personnel ou partagé.'],
        2 => ['Connexion Internet et environnement de travail', 'De quel type de connexion Internet disposez-vous pour suivre les formations, télécharger les ressources pédagogiques et transmettre vos projets pratiques dans les délais impartis ?', 'Précisez si vous utilisez un Wi-Fi personnel, une connexion mobile, un cybercafé ou une autre solution. Indiquez le niveau de stabilité de votre connexion et les solutions alternatives prévues en cas de coupure.'],
        3 => ['Disponibilité et investissement personnel', 'En dehors des heures officielles de formation, combien d’heures par semaine pouvez-vous réellement consacrer aux travaux pratiques, aux recherches personnelles et au perfectionnement de vos compétences ?', 'Indiquez un volume horaire réaliste et expliquez votre organisation en tenant compte de vos obligations personnelles, académiques ou professionnelles.'],
        4 => ['Motivation et objectif professionnel', 'Pourquoi avez-vous choisi l’École Virtuelle des Créatifs et quel projet professionnel précis souhaitez-vous concrétiser à l’issue de votre formation ?', 'Expliquez vos motivations, les compétences souhaitées et leur usage concret : recherche d’emploi, entrepreneuriat, reconversion professionnelle, freelance ou évolution de carrière.'],
        5 => ['Autodiscipline, autonomie et organisation personnelle', 'Quelles stratégies concrètes comptez-vous adopter pour maintenir votre autodiscipline, éviter la procrastination et respecter vos engagements pédagogiques ?', 'Décrivez votre organisation, votre gestion des distractions et les dispositions prises pour travailler régulièrement, même sans supervision directe.'],
        6 => ['Résistance à la pression et capacité d’amélioration', 'Comment réagiriez-vous si plusieurs de vos réalisations étaient jugées insuffisantes et devaient être entièrement reprises dans un délai limité ?', 'Expliquez comment vous géreriez la pression, les critiques constructives, les difficultés techniques et vos priorités pour améliorer vos productions sans abandonner.'],
        7 => ['Compétences numériques préalables et capacité d’adaptation', 'Quel est votre niveau actuel de maîtrise des outils informatiques essentiels : fichiers et dossiers, navigation Internet, téléchargements, installation de logiciels, plateformes numériques et résolution des difficultés courantes ?', 'Précisez les opérations réalisées de manière autonome, vos difficultés et votre capacité à apprendre de nouveaux outils numériques.'],
        8 => ['Intégrité, honnêteté et responsabilité professionnelle', 'Un projet important est à remettre dans quelques heures, mais vous n’avez réalisé que la moitié du travail. Un étudiant vous propose de copier intégralement sa réalisation pour respecter le délai et obtenir une bonne évaluation. Quelle décision prendriez-vous et pourquoi ?', 'Expliquez comment concilier honnêteté, respect des délais, responsabilité personnelle et qualité attendue par l’EVC.'],
        9 => ['Implication institutionnelle et disponibilité', 'Dans quelle mesure êtes-vous disponible et disposé à participer aux événements pédagogiques et institutionnels de l’EVC : rencontres professionnelles, activités collectives, présentations de projets et cérémonies de remise des certificats ?', 'Si vous résidez en Côte d’Ivoire, précisez votre capacité à vous déplacer si nécessaire. À l’étranger, indiquez vos possibilités de participation à distance.'],
        10 => ['Engagement administratif et financier', 'Si votre candidature est retenue, serez-vous en mesure de finaliser les formalités administratives et financières dans un délai de 72 heures pour confirmer votre place dans la prochaine cohorte ?', 'Précisez votre disponibilité immédiate, votre besoin d’un délai supplémentaire ou d’un accompagnement administratif.'],
        11 => ['Déclaration d’engagement et acceptation des conditions réglementaires', 'Avez-vous pris connaissance des conditions d’éligibilité, du règlement intérieur, des exigences pédagogiques, des modalités de certification et des dispositions administratives et financières de l’EVC ? Expliquez votre engagement personnel à les respecter.', 'Rédigez votre déclaration personnelle. Précisez les documents effectivement reçus et lus, ainsi que toute question ou information manquante. Ne déclarez pas avoir lu un document qui ne vous a pas été communiqué.'],
    ];

    public const DECLARATIONS = [
        'Je certifie que les informations fournies dans mon dossier sont exactes, complètes et sincères.',
        'Je reconnais avoir pris connaissance des conditions d’éligibilité et d’admission et comprends que ma préinscription ne constitue pas une admission définitive.',
        'Je reconnais avoir lu le règlement intérieur et m’engage à respecter la discipline, l’assiduité, la ponctualité et la conduite professionnelle.',
        'Je comprends que la pédagogie repose sur la pratique, les projets, les évaluations continues et la validation des compétences.',
        'Je m’engage à réaliser personnellement mes travaux, à respecter les délais et à ne pas présenter les réalisations d’autrui comme les miennes.',
        'Je reconnais que le paiement des frais ne garantit pas automatiquement un certificat ou une attestation de compétences.',
        'Je comprends que le certificat dépend des exigences pédagogiques et administratives, de la validation des travaux, évaluations, projets et dispositifs pratiques applicables à ma formation.',
        'Je reconnais avoir pris connaissance des modalités de rattrapage et des éventuels frais supplémentaires définis dans les documents officiels.',
        'Je reconnais avoir pris connaissance des conditions financières, des échéances et des modalités d’inscription applicables à ma formation.',
        'Je confirme avoir pris connaissance de la politique de confidentialité relative au traitement de mes données personnelles.',
    ];

    public const ENGAGEMENT = 'Je déclare sur l’honneur avoir fourni des informations sincères et reconnais avoir pris connaissance des exigences de l’École Virtuelle des Créatifs (EVC). Je comprends que mon admission, ma progression pédagogique et l’obtention de mon certificat dépendent du respect des conditions établies par l’institution. Je m’engage à faire preuve de discipline, d’intégrité, de persévérance et de responsabilité tout au long de mon parcours de formation.';
}
