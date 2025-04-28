<?php


namespace App\Calendar;

use App\Entity\Conge;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmployeeCalendarSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvent::class => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendarEvent): void
    {
        $filters = $calendarEvent->getFilters();
        $userId = $filters['userId'] ?? null;

        if (!$userId) {
            return; // Si pas d'ID employé, on ne montre rien
        }

        $start = $calendarEvent->getStart();
        $end = $calendarEvent->getEnd();

        $conges = $this->em->getRepository(Conge::class)->createQueryBuilder('c')
            ->where('c.employe = :employe')
            ->andWhere('c.statut = :statut')
            ->andWhere('c.dateDebut BETWEEN :start AND :end OR c.dateFin BETWEEN :start AND :end')
            ->setParameter('employe', $userId)
            ->setParameter('statut', Conge::STATUT_APPRUVE)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        foreach ($conges as $conge) {
            $event = new Event(
                $conge->getTypeConge(),
                $conge->getDateDebut(),
                $conge->getDateFin()
            );

            $color = match ($conge->getStatut()) {
                Conge::STATUT_APPRUVE => 'green',
                Conge::STATUT_REFUSE => 'red',
                Conge::STATUT_EN_ATTENTE => 'blue',
                default => 'gray',
            };

            $event->setOptions([
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => 'white',
            ]);

            $calendarEvent->addEvent($event);
        }
    }
}
