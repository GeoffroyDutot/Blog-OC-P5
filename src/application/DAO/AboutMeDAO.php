<?php


namespace App\DAO;


use App\DTO\AboutMeDTO;

class AboutMeDAO extends DAO
{
    // Get AboutMe Object
    public function getAboutMe(): AboutMeDTO
    {
    $req = $this->db->query('SELECT * FROM `about_me` LIMIT 1');

    return $req->fetchObject('App\DTO\AboutMeDTO');
    }

    // Creates or Updates AboutMe
    public function save(AboutMeDTO $aboutMe): bool
    {
        // If AboutMe doesn't exists Insert else Updates
        if (!$aboutMe->getId()) {
            // Prepare the request
            $req = $this->db->prepare('INSERT INTO `about_me`(`firstname`, `lastname`, `slogan`, `bio`, `profil_picture`, `cv_pdf`, `picture`, `twitter_link`, `linkedin_link`, `github_link`) VALUES(:firstname, :lastname, :slogan, :bio, :profil_picture, :cv_pdf, :picture, :twitter_link, :linkedin_link, :github_link)');

            // Inserts new data
            $aboutMe = $req->execute(['firstname' => $aboutMe->getFirstname(), 'lastname' => $aboutMe->getLastname(), 'slogan' => $aboutMe->getSlogan(), 'bio' => $aboutMe->getBio(), 'profil_picture' => $aboutMe->getProfilPicture(), 'cv_pdf' => $aboutMe->getCvPdf(), 'picture' => $aboutMe->getPicture(), 'twitter_link' => $aboutMe->getTwitterLink(), 'linkedin_link' => $aboutMe->getLinkedinLink(), 'github_link' => $aboutMe->getGithubLink()]);
        } else {
            // Prepares the request
            $req = $this->db->prepare('UPDATE about_me SET firstname=:firstname, lastname=:lastname, slogan=:slogan, bio=:bio, profil_picture=:profil_picture, cv_pdf=:cv_pdf, picture=:picture, twitter_link=:twitter_link, linkedin_link=:linkedin_link, github_link=:github_link WHERE id = \''.$aboutMe->getId().'\'');

            // Inserts Updated Data
            $aboutMe = $req->execute(['firstname' => $aboutMe->getFirstname(), 'lastname' => $aboutMe->getLastname(), 'slogan' => $aboutMe->getSlogan(), 'bio' => $aboutMe->getBio(), 'profil_picture' => $aboutMe->getProfilPicture(), 'cv_pdf' => $aboutMe->getCvPdf(), 'picture' => $aboutMe->getPicture(), 'twitter_link' => $aboutMe->getTwitterLink(), 'linkedin_link' => $aboutMe->getLinkedinLink(), 'github_link' => $aboutMe->getGithubLink()]);
        }

        return $aboutMe;
    }
}